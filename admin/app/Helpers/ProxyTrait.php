<?php

namespace App\Helpers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

trait ProxyTrait
{
    /** 创建Token
     * @param string $guard
     * @return mixed
     */
    public function authenticate($guard = '')
    {
        $client = new Client();
        try {
            $url = request()->root() . '/oauth/token';

            $params = [
                'grant_type' => config('public.oauth.default.grant_type', 'password'), // env('OAUTH_GRANT_TYPE'),
                'client_id' => config('public.oauth.default.client_id', 0), // env('OAUTH_CLIENT_ID'),
                'client_secret' => config('public.oauth.default.client_secret', ''), // env('OAUTH_CLIENT_SECRET'),
                'username' => request('mobile'),
                'password' => request('password'),
                'scope' => config('public.oauth.default.scope', '*'), // env('OAUTH_SCOPE')
            ];
            if ($guard) {
                $params = array_merge($params, [
                    'provider' => $guard,
                ]);
            }
            $respond = $client->request('POST', $url, ['form_params' => $params]);
        } catch (RequestException $exception) {
            return false;
        }

        if ($respond->getStatusCode() === 200) {
            return json_decode($respond->getBody()->getContents(), true);
        }
        return false;
    }

    /** 刷新token
     * @return mixed
     */
    public function getRefreshtoken()
    {
        $client = new Client();

        try {
            $url = request()->root() . '/oauth/token';

            $params = array_merge(config('passport.refresh_token'), [
                'refresh_token' => request('refresh_token'),
            ]);

            $respond = $client->request('POST', $url, ['form_params' => $params]);
        } catch (RequestException $exception) {
            return false;
        }

        if ($respond->getStatusCode() === 200) {
            return json_decode($respond->getBody(), true);
        }
        return false;
    }
}