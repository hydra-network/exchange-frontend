<?php
namespace App\Helpers;

use App\Models\Pair;

class Formatter
{
    public static function formatObjects(Pair $asset, array $objects)
    {
        $result = [];

        foreach ($objects as $object) {
            $object = (array) $object;
            $formattedObject = $object;
            $formattedObject['quantity'] = $asset->secondary->format($formattedObject['quantity']);
            if (isset($formattedObject['quantity_remain'])) {
                $formattedObject['quantity_remain'] = $asset->secondary->format($formattedObject['quantity_remain']);
            }

            $formattedObject['price'] = $asset->primary->format($formattedObject['price']);
            $formattedObject['cost'] = $asset->primary->format($formattedObject['cost']);
            if (isset($formattedObject['cost_remain'])) {
                $formattedObject['cost_remain'] = $asset->primary->format($formattedObject['cost_remain']);
            }

            $result[] = $formattedObject;
        }

        return $result;
    }
}