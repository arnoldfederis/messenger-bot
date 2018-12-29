<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

class MessengerController extends Controller
{
    public function index()
    {
    	// here we can verify the webhook.
    	// i create a method for that.
    	$this->verifyAccess();

        $user    = json_decode($this->getUser($id));
    	$input   = json_decode(file_get_contents('php://input'), true);
    	$id 	 = $input['entry'][0]['messaging'][0]['sender']['id'];
    	$message = $input['entry'][0]['messaging'][0]['message']['text'];

    	$response = [
    		'recipient'		=>	['id'   => $id ],
            'message'		=>	['text' => "Thanks for watching {$user->first_name} {$user->last_name}! :)"]
    	];

    	$this->sendMessage($response);
    }

    protected function sendMessage($response)
    {
    	// set our post
    	$ch = curl_init('https://graph.facebook.com/v2.6/me/messages?access_token=' . env('PAGE_ACCESS_TOKEN'));
    	curl_setopt($ch, CURLOPT_POST, 1);
    	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($response));
    	curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    	curl_exec($ch);
    	curl_close($ch);
    }

    protected function getUser($id = null)
    {
        $url = "https://graph.facebook.com/v2.6/{$id}?fields=first_name,last_name,profile_pic&access_token=" . env('PAGE_ACCESS_TOKEN');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $user = curl_exec($ch);
        curl_close($ch);

        return $user;
    }

    protected function verifyAccess()
    {
    	// FACEBOOK_MESSENGER_WEBHOOK_TOKEN is not exist yet.
    	// we can set that up in our .env file
    	$local_token = env('FACEBOOK_MESSENGER_WEBHOOK_TOKEN');
    	$hub_verify_token = request('hub_verify_token');

    	// condition if our local token is equal to hub_verify_token
    	if ($hub_verify_token === $local_token) {
    		// echo the hub_challenge in able to verify.
    		echo request('hub_challenge');
    		exit;
    	}
    }
}
