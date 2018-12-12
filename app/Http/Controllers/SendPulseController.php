<?php 
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\SendPulseToken;
use DateTime;
use DateInterval;
class SendPulseController extends Controller
{
    public function getTemplates(request $request){ 
        $token = self::getToken($request->user()->id);   
        $client = new Client();
        $res=$client->request('GET', 'https://api.sendpulse.com/templates/?owner=sendpulse', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ] 
        ]);
        // echo $res->getStatusCode();
        // // "200"
        // echo $res->getHeader('content-type');
        // // 'application/json; charset=utf8' 
        return $res->getBody();
    }

    /*Daulet
     Get the difference of current time and the expiration time of the token,
     if the token is not expired, then return the existing one,
     otherwise request new one, then save.
    */
    private function getToken($user_id){
        $tokenObj = SendPulseToken::where('id','=',$user_id)->first();
        if($tokenObj){
            $updated_at = $tokenObj -> updated_at;
            $expires_in = $tokenObj -> expires_in;
            $updated_at ->add(new DateInterval('PT1H'));
            $current_time = (new DateTime());
            $diff =  $current_time->diff($updated_at);  
            if($diff->invert == 0) return $tokenObj->token;
        }
        $client = new Client(); 
        $res = $client->request('POST', 'https://api.sendpulse.com/oauth/access_token', [
                'form_params' => [
                        'grant_type' => 'client_credentials',
                        'client_id' => '39ba7832d36974e133435551783d535f',
                    'client_secret' => '78206b934e16ca6aa04146ecf27624a0', 
            ]
        ]);
        //save the requested token to the server
        self::saveToken(json_decode($res->getBody()), $user_id); 
        return json_decode($res->getBody())->access_token;
    }
    private function saveToken($token, $user_id){
        $newToken = SendPulseToken::findOrNew($user_id);
        $newToken -> token  =  $token -> access_token;
        $newToken -> id  =  $user_id;
        $newToken -> expires_in  =  $token -> expires_in;
        $newToken->save();
    }

    public function sendEmail(request $request){
        $newMail = [
            "subject" => "Test",
            "template" => [
               "id"=> 812841,
               "variables"=> [ 
               ]
            ],
            "from" => [
               "name"=> "Mike",
               "email"=> "mike.johnson@domain.com"
            ],
            "to"=> [
               [
                  "email"=> "daulet.issatayev@nu.edu.kz",
                  "name"=> "Daulet Issatayev"
               ]
            ]
        ];
        $token = self::getToken($request->user()->id);   
        $client = new Client();
        $res=$client->request('POST', 'https://api.sendpulse.com/smtp/emails', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ], 
            'form_params' =>[
                'email' => $newMail,
            ]
        ]);
        // echo $res->getStatusCode();
        // // "200"
        // echo $res->getHeader('content-type');
        // // 'application/json; charset=utf8' 
        return $res->getBody();
    }
}
