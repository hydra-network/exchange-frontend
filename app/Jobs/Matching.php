<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Hydra\Exchange\Entities\{Pair, BuyOrder, SellOrder, SellerBalance, BuyerBalance, Asset};
use Hydra\Exchange\Libs\{Matcher, Logger};
use App\Models\Order as OrderModel;
use App\Models\Pair as PairModel;
use App\Models\Deal as DealModel;

class Matching implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $pair_id = null;
    
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($pair_id)
    {
        $this->pair_id = $pair_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $pairModel = PairModel::whereId($this->pair_id)->firstOrFail();

            $buyOrderModel = OrderModel::where('pair_id', $this->pair_id)->whereType(OrderModel::TYPE_BUY)->orderBy('price', 'DESC')->first();
            $sellOrderModel = OrderModel::where('pair_id', $this->pair_id)->whereType(OrderModel::TYPE_SELL)->orderBy('price', 'ASC')->first();

            if ($buyOrderModel && $sellOrderModel) {
                $pair = new Pair(
                    new Asset("BTC"), //primary asset
                    new Asset("ETH") //secondary asset
                );

                $primaryAsset = $pairModel->primary()->first();
                $secondaryAsset = $pairModel->secondary()->first();

                $buyerBalance = new BuyerBalance($buyOrderModel->user->getBalance($primaryAsset), $buyOrderModel->user->getBalance($secondaryAsset));
                $sellerBalance = new SellerBalance($sellOrderModel->user->getBalance($primaryAsset), $sellOrderModel->user->getBalance($secondaryAsset));

                $buyOrder = new BuyOrder($pair, 100, 10, $buyerBalance, 1);
                $sellOrder = new SellOrder($pair, 10, 9, $sellerBalance, 2);

                $matcher = new Matcher($buyOrder, $sellOrder);

                if ($deal = $matcher->matching()) {
                    \DB::transaction(function() use ($pairModel, $buyOrderModel, $sellOrderModel, $buyerBalance, $sellerBalance, $buyOrder, $sellOrder, $deal) {
                        $primaryAsset = $pairModel->primary()->first();
                        $secondaryAsset = $pairModel->secondary()->first();

                        $dealModel = new DealModel;
                        $dealModel->fill([
                            'pair_id' => $pairModel->id,
                            'buyer_user_id' => $buyOrderModel->user->id,
                            'seller_user_id' => $sellOrderModel->user->id,
                            'ask_id' => $buyOrderModel->id,
                            'bid_id' => $sellOrderModel->id,
                            'quantity' => $deal->getQuantity(),
                            'price' => $deal->getPrice(),
                            'cost' => $deal->getQuantity() * $deal->getPrice(),
                            'type' => $deal->getType()
                        ]);

                        if ($dealModel->save()) {
                            $buyOrderModel->user->transaction($primaryAsset, $dealModel, $buyerBalance->getPrimary());
                            $buyOrderModel->user->transaction($secondaryAsset, $dealModel, $buyerBalance->getSecondary());

                            $sellOrderModel->user->transaction($primaryAsset, $dealModel, $sellerBalance->getPrimary());
                            $sellOrderModel->user->transaction($secondaryAsset, $dealModel, $sellerBalance->getSecondary());

                            $buyOrderModel->quantity_remain = $buyOrder->getQuantityRemain();
                            $buyOrderModel->status = $buyOrder->getStatus();
                            $buyOrderModel->cost_remain = $buyOrder->getCostRemain();
                            $buyOrderModel->save();

                            $sellOrderModel->quantity_remain = $sellOrder->getQuantityRemain();
                            $sellOrderModel->status = $sellOrder->getStatus();
                            $sellOrderModel->cost_remain = $sellOrder->getCostRemain();
                            $sellOrderModel->save();

                            echo implode("\n", Logger::list());
                            echo "____________________\n";
                        }
                    });
                }
            }

            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }
}
