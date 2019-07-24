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
        echo "\n===\n";

        try {
            $pairModel = PairModel::whereId($this->pair_id)->firstOrFail();

            $buyOrderModel = OrderModel::where('pair_id', $this->pair_id)->whereIn('status', [OrderModel::STATUS_NEW, OrderModel::STATUS_ACTIVE, OrderModel::STATUS_PARTIAL])->whereType(OrderModel::TYPE_BUY)->orderBy('price', 'DESC')->first();
            $sellOrderModel = OrderModel::where('pair_id', $this->pair_id)->whereIn('status', [OrderModel::STATUS_NEW, OrderModel::STATUS_ACTIVE, OrderModel::STATUS_PARTIAL])->whereType(OrderModel::TYPE_SELL)->orderBy('price', 'ASC')->first();

            if ($buyOrderModel && $sellOrderModel) {
                echo "Counter orders found {$buyOrderModel->price} - {$sellOrderModel->price}\n";

                $pair = new Pair(
                    new Asset($pairModel->primary->code), //primary asset
                    new Asset($pairModel->secondary->code) //secondary asset
                );

                $primaryAsset = $pairModel->primary;
                $secondaryAsset = $pairModel->secondary;

                $buyerBalance = new BuyerBalance(
                    ($primaryAsset->format2($buyOrderModel->user->getBalance($primaryAsset)) + $primaryAsset->format2($buyOrderModel->cost_remain)),
                    $secondaryAsset->format2($buyOrderModel->user->getBalance($secondaryAsset))
                );

                $sellerBalance = new SellerBalance(
                    $primaryAsset->format2($sellOrderModel->user->getBalance($primaryAsset)),
                    ($secondaryAsset->format2($sellOrderModel->user->getBalance($secondaryAsset)) + $secondaryAsset->format2($sellOrderModel->quantity_remain))
                );

                $buyOrder = new BuyOrder(
                    $pair,
                    $secondaryAsset->format2($buyOrderModel->quantity_remain),
                    $primaryAsset->format2($buyOrderModel->price),
                    $buyerBalance,
                    $buyOrderModel->id
                );

                $sellOrder = new SellOrder(
                    $pair,
                    $secondaryAsset->format2($sellOrderModel->quantity_remain),
                    $primaryAsset->format2($sellOrderModel->price),
                    $sellerBalance,
                    $sellOrderModel->id
                );

                $matcher = new Matcher($buyOrder, $sellOrder);

                if ($deal = $matcher->matching()) {
                    echo "Deal - successfull\n";
                    \DB::transaction(function() use ($pairModel, $buyOrderModel, $sellOrderModel, $buyerBalance, $sellerBalance, $buyOrder, $sellOrder, $deal) {
                        $primaryAsset = $pairModel->primary;
                        $secondaryAsset = $pairModel->secondary;

                        $dealModel = new DealModel;
                        $dealModel->fill([
                            'pair_id' => $pairModel->id,
                            'buyer_user_id' => $buyOrderModel->user->id,
                            'seller_user_id' => $sellOrderModel->user->id,
                            'ask_id' => $buyOrderModel->id,
                            'bid_id' => $sellOrderModel->id,
                            'quantity' => $deal->getQuantity()*$secondaryAsset->subunits,
                            'price' => ($deal->getPrice() * $primaryAsset->subunits),
                            'cost' => ($deal->getQuantity() * $deal->getPrice()) * $primaryAsset->subunits,
                            'type' => $deal->getType()
                        ]);

                        if ($dealModel->save()) {
                            $buyOrderModel->user->income($secondaryAsset, $dealModel, $dealModel->quantity);
                            $sellOrderModel->user->income($primaryAsset, $dealModel, $dealModel->cost);

                            $buyOrderModel->quantity_remain = $buyOrder->getQuantityRemain()*$secondaryAsset->subunits;
                            $buyOrderModel->status = $buyOrder->getStatus();
                            $buyOrderModel->cost_remain = $buyOrder->getCostRemain()*$primaryAsset->subunits;
                            $buyOrderModel->save();

                            $sellOrderModel->quantity_remain = $sellOrder->getQuantityRemain()*$secondaryAsset->subunits;
                            $sellOrderModel->status = $sellOrder->getStatus();
                            $sellOrderModel->cost_remain = $sellOrder->getCostRemain()*$primaryAsset->subunits;
                            $sellOrderModel->save();
                        }
                    });
                } else {
                    echo "No deal \n";
                }
            }

            echo implode("\n", Logger::list());
            echo "\n____________________\n";

            return true;
        } catch (\Throwable $e) {
            echo "[Error]: " . $e->getMessage() . "\n";
            return false;
        }
    }
}
