<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use GuzzleHttp\Client;

class ReCaptcha implements Rule
{
    public function passes($attribute, $value)
    {
    
        $client = new Client();
    
        $response = $client->post(
            'https://www.google.com/recaptcha/api/siteverify',
            ['form_params'=>
                [
                    'secret' => env('GOOGLE_RECAPTCHA_SECRET'),
                    'response' => $value
                 ]
            ]
        );
    
        $body = json_decode((string)$response->getBody());
        
        return $body->success;
    }

    public function message()
    {
        return 'Invalid reCaptcha';
    }
}