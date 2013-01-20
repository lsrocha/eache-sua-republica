<?php
namespace core;

class Facebook
{
    private static $appID = '';
    private static $appSecret = '';
    private static $url = '';

    public static function login()
    {
        $_SESSION['state'] = md5(uniqid(rand(), true));

        header(
            'Location: https://www.facebook.com/dialog/oauth?client_id='
            .self::$appID.'&redirect_uri='.self::$url.'&state='
            .$_SESSION['state'].'&scope=email,user_birthday,user_location'
        );
    }

    public static function getToken($code)
    {
        $code = filter_var($code, FILTER_SANITIZE_STRING);

        $ch = curl_init(
            'https://graph.facebook.com/oauth/access_token?client_id='
            .self::$appID.'&redirect_uri='.self::$url.'&client_secret='
            .self::$appSecret.'&code='.$code
        );

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $response = curl_exec($ch);
        parse_str($response, $params);

        curl_close($ch);

        return $params['access_token'];
    }

    public static function apiCall($accessToken)
    {
        $accessToken = filter_var($accessToken, FILTER_SANITIZE_STRING);

        $ch = curl_init(
            'https://graph.facebook.com/me?access_token='.$accessToken
        );

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);

        curl_close($ch);

        return json_decode($response);
    }

    public static function revokeAcess($accessToken)
    {
        $accessToken = filter_var($accessToken, FILTER_SANITIZE_STRING);

        $ch = curl_init(
            'https://graph.facebook.com/me/permissions?method=delete'.
            '&access_token='.$accessToken
        );

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);

        curl_close($ch);

        return json_decode($response);
    }
}

