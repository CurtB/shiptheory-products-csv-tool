<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use Illuminate\Support\Carbon;
use Session;

trait ShiptheoryAPI
{
    /*
    |--------------------------------------------------------------------------
    | Shiptheory API Trait
    |--------------------------------------------------------------------------
    |
    | This trait is used to call the Shiptheory API
    |
    */

    protected $api_url = 'https://api.shiptheory.com/';
    protected $api_version = 'v1';

    /**
     * Send a request to Shiptheory API
     *
     * @param string $method POST | GET | PUT
     * @param string $requestUrl
     * @param array $queryParams optional
     * @param array $formParams optional
     * @param array $credentials_provider optional
     *
     * @return string the response from the API
     */
    public function callShiptheory($method, $requestUrl, $queryParams = [], $formParams = [], $credentials_provider = null)
    {
        $this->baseUri = $this->api_url.$this->api_version;

        if($requestUrl != "token"){
            $headers['Authorization'] = 'bearer '.$this->getToken($credentials_provider);
        }
        $headers['Accept'] = 'application/json';
        $headers['Content-Type'] = 'application/json';
        $headers['Shiptheory-Partner-Tag'] = 'returnsshop';

        $requestUrl = $this->api_version.'/'.$requestUrl;

        return $this->makeRequest(
            $method,
            $requestUrl,
            $queryParams,
            $formParams,
            $headers,
            false);
    }

     /**
     * Checks if email and password can access Shiptheory API
     *
     * @param string $email Shiptheory email
     * @param string $password Shiptheory password
     *
     * @return array ['error' => $message] or ['success' => true]
     */
    private function check_shiptheory_credentials($email, $password)
    {
        $response = $this->callShiptheory('POST', 'token', [], [
            'email' => $email,
            'password' => $password]);
        if(!empty($response['error'])){ return $response; }
        if($data = json_decode($response)){
            return ['success' => true];
        }
        return ['error' => 'Failed to authenticate with Shiptheory.'];
    }

    /**
     * Get Shiptheory API token from cache or get a new one with credentials
     *
     * @return string Shiptheory API access token
     */
    private function getToken($email = null, $password = null)
    {
        $token = Session::get('ship_token');
        if(!empty($token)){
            $expires = Session::get('ship_token_expires');
            if(Carbon::now()->lessThan($expires)){
                return $token;
            }else{
                // Expired
                session(['ship_token' => null]);
                session(['ship_token_expires' => null]);
                return['error'=>'Login expired', 'expired' => true];
            }
        }else{
            if(!empty($email) && !empty($password)){
                return $this->refreshToken($email, $password);
            }else{
                return['error'=>'Email and password required to get token'];
            }
        }
    }

    /**
     * Get fresh Shiptheory API token with credentials. Cache it.
     *
     * @param string $email Shiptheory email
     * @param string $password Shiptheory password
     *
     * @return string Shiptheory API access token
     */
    private function refreshToken($email, $password)
    {
        $params['email'] = $email;
        $params['password'] = $password;

        $response = $this->callShiptheory('POST', 'token', [], $params);
        $data = json_decode($response);
        session(['ship_token_expires' =>  Carbon::now()->addMinutes(60)]);
        session(['ship_token' => $data->data->token]);
        return $data->data->token;
    }


    /**
     * Send a request to any service, should be called through another function that sets
     * $this->baseUri to the desired api destination. See $this->callShiptheory
     *
     * @param string $method POST | GET | PUT
     * @param string $requestUrl
     * @param array $queryParams optional
     * @param array $formParams optional
     * @param array $headers optional
     * @param bool $hasFile optional if TRUE will get files from post params
     *
     * @return string the response from the server
     */
    public function makeRequest($method, $requestUrl, $queryParams = [], $formParams = [], $headers = [], $hasFile = false)
    {

        $client = new Client([
            'base_uri' => $this->baseUri,
        ]);

        $bodyType = 'form_params';

        if(!empty($headers['Content-Type']) && strpos($headers['Content-Type'], 'json') !== false){
            $bodyType = 'json';
        }

        if ($hasFile) {
            $bodyType = 'multipart';

            $multipart = [];

            foreach ($formParams as $name => $contents) {
                $multipart[] = [
                    'name' => $name,
                    'contents' => $contents
                ];
            }
        }

        try {
            $response = $client->request($method, $requestUrl, [
                'query' => $queryParams,
                $bodyType => $hasFile ? $multipart : $formParams,
                'headers' => $headers,
                'debug' => false
            ]);
        }
        catch(\Exception $e)
        {
            $message = "Error calling external API";
            if(!empty($e->getResponse()) &&
                $response = $e->getResponse()->getBody()->getContents()){
                if($json = json_decode($response)){
                    if(!empty($json->message)){
                        $message .= '<br /><span style="font-weight: bold;">response:</span> '.$json->message;
                    }
                }
            }else if(!empty($e->getMessage())){
                $message .= '<br /><span style="font-weight: bold;">message:</span> ' .
                    $e->getMessage();
            }
            if(!empty($e->getRequest()->getRequestTarget())){
                $message .= '<br /><span style="font-weight: bold;">target:</span> '.
                    $e->getRequest()->getRequestTarget();
            }
            Log::error("ShiptheoryAPI.php makeRequest() ".strip_tags($message).
                ' baseUri: '.$this->baseUri.
                ' queryParams: '.  json_encode($queryParams).
                ' formParams: '.  json_encode($formParams).
                ' headers: '. json_encode($headers) );

            return ['error' => $message];
        }

        $response = $response->getBody()->getContents();

        return $response;
    }

}
