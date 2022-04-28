<?php
/**
 * Created by PhpStorm.
 * User: r.a.freitas
 * Date: 26/05/2019
 * Time: 00:30
 */

use App\Basics\Account;
use App\Controller\AuthController;
use App\Service\Email;
use App\Utils\WorkOut;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

$app->group('/auth', function () {

    $this->post('', function (ServerRequestInterface $request, ResponseInterface $response, $args) {

        $data = $request->getParsedBody();
        $account = new Account();
        $account->setEmail(isset($data['email'])?$data['email']: null);
        $account->setPass(isset($data['pass']) ? strtoupper(sha1($data['pass'])) : null);
        $account->setActive(true);

        // $file = UPLOAD_CLI . 'logs/' . 'log.txt';
        // $WorkOut = new WorkOut();
        // file_put_contents($file, 'REQUEST GET = /login/ ' . " => "  .  $WorkOut->getData()  . PHP_EOL,FILE_APPEND);

        $authController = new AuthController();
        $workOut = new WorkOut();
        $return = $authController->login($account);

        return $workOut->managerResponseToken($response, $return, 'user');
    });

    $this->get('/checkEmail/{email}', function (ServerRequestInterface $request, ResponseInterface $response, $args) {

        $authController = new AuthController();
        $account = new Account();
        $account->setEmail($args['email']);

        $workOut = new WorkOut();

        $return = $authController->checkEmail($account);

        return $workOut->managerResponse($response, $return, 'result');
    });

    $this->post('/forgotIt', function (ServerRequestInterface $request, ResponseInterface $response, $args) {

        $data = $request->getParsedBody();
        $account = new Account();
        $account->setEmail(isset($data['email'])?$data['email']: null);

        $authController = new AuthController();
        $workOut = new WorkOut();

        $return = $authController->reset($account);
        return $workOut->managerResponse($response, $return, 'result');
    });

    $this->post('/changePass', function (ServerRequestInterface $request, ResponseInterface $response, $args) {

        $workOut = new WorkOut();
        $managerRequestToken = $workOut->managerRequestToken($request, $response);
        if ($managerRequestToken['status'] !== 200) return $managerRequestToken['response'];

        $data = $request->getParsedBody();
        $user = $managerRequestToken['token']->data;

        $account = new Account();
        $account->setId(isset($user->ac_id) ? $user->ac_id : null);
        $old = (isset($data['old_pass'])) ? $data['old_pass']: null;
        $new = (isset($data['new_pass'])) ? $data['new_pass']: null;

        $authController = new AuthController();
        $workOut = new WorkOut();

        $return = $authController->changePass($account, $old, $new);
        return $workOut->managerResponse($response, $return, 'result');
    });

    $this->get('/getProfile', function (ServerRequestInterface $request, ResponseInterface $response, $args) {

        $workOut = new WorkOut();
        $managerRequestToken = $workOut->managerRequestToken($request, $response);
        if ($managerRequestToken['status'] !== 200) return $managerRequestToken['response'];

        $user = $managerRequestToken['token']->data;

        $authController = new AuthController();
        $workOut = new WorkOut();
        $account = new Account();
        $account->setId(isset($user->ac_id) ? $user->ac_id : null);

        $return = $authController->getProfile($account);
        return $workOut->managerResponseToken($response, $return, 'user');
    });

    $this->put('/editProfile', function (ServerRequestInterface $request, ResponseInterface $response, $args) {

        $data = $request->getParsedBody();
        $workOut = new WorkOut();
        $managerRequestToken = $workOut->managerRequestToken($request, $response);
        if ($managerRequestToken['status'] !== 200) return $managerRequestToken['response'];

        $user = $managerRequestToken['token']->data;

        $authController = new AuthController();
        $account = new Account();
        $account->setId(isset($user->ac_id) ? $user->ac_id : null);
        $account->setGroupId(isset($user->gr_id) ? $user->gr_id : null);
        $userId = isset($user->us_id) ? $user->us_id : null;

        $return = $authController->editProfile($account, $userId, $data);
        return $workOut->managerResponse($response, $return, 'result');
    });

    $this->get('/emailTest/{email}', function (ServerRequestInterface $request, ResponseInterface $response, $args) {

        $workOut = new WorkOut();
        $account = new Account();

        $account->setEmail($args['email']);
        $email = new Email('');
        $teste = $email->newPass($account, false, 123456);

        var_dump('teste');
        var_dump($teste);
        die;

        $return = array('status' => 200, 'result' => 'E-mail enviado!');

        return $workOut->managerResponse($response, $return, 'result');
    });

});