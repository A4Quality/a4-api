<?php

namespace App\Config;

use Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;

define('SECRET_KEY', 'sua_senha');
define('ALGORITHM', 'HS256');
class Authorization
{
    public function createToken($obj, $notExpired = null)
    {

        $tokenId = base64_encode(random_bytes(32));
        $issuedAt = time();
        $notBefore = $issuedAt + 10;
        $expire = $notExpired ? $notBefore + 946080000 : $notBefore + 8640000;
        $serverName = 'http://api.a4quality.com.br/';
        ///
        $data = [
            'iat' => $issuedAt,
            'jti' => $tokenId,
            'iss' => $serverName,
            'nbf' => $notBefore,
            'exp' => $expire,
            'data' => $obj,
        ];

        $secretKey = SECRET_KEY;
        return JWT::encode(
            $data, //Data to be encoded in the JWT
            $secretKey,
            ALGORITHM
        );
    }

    public function validateToken($request) {
        $authorization = $request->getHeaderLine("Authorization");

        if (trim($authorization) == "") {
            return array('status' => 401, 'message' => 'ERROR', 'result' => 'Token não informado');
        } else {
            try {
                JWT::$leeway = 60;
                $token = JWT::decode($authorization, SECRET_KEY, array('HS256'));
                return array('status' => 200, 'token' => $token);
            } catch (ExpiredException $ex) {
                return array(
                    'status' => 401,
                    'result' => 'Acesso não autorizado',
                    'message' => $ex->getMessage()
                );
            }
        }
    }

    public function breakToken($hash)
    {
        try {
            JWT::$leeway = 60;
            $token = JWT::decode($hash, SECRET_KEY, ['HS256']);
            return ['status' => 200, 'token' => $token];
        } catch (ExpiredException $ex) {
            return [
                'status' => 401,
                'result' => 'Acesso não autorizado',
                'message' => $ex->getMessage(),
            ];
        }
    }
}
