<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use GuzzleHttp\Client;

class Proxy extends Model
{
    public function getContent($url, $agent)
    {
        $client = new Client();
        $response = $client->get($this->url . '?sq=' . urlencode($url) . '&ua=' . $agent->name);

        $this->last_check = date('Y-m-d H:i:s');
        $this->save();
    
        return $response;
    }
}
