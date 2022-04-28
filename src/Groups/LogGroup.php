<?php
/**
 * Created by PhpStorm.
 * User: r.a.freitas
 * Date: 26/05/2019
 * Time: 00:30
 */

use App\Basics\Account;
use App\Basics\Logs;
use App\Controller\LogsController;
use App\Utils\WorkOut;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

$app->group('/logs', function () {

    $this->get('/{type}/{idType}', function (ServerRequestInterface $request, ResponseInterface $response, $args) {

        $workOut = new WorkOut();
        $managerRequestToken = $workOut->managerRequestToken($request, $response, [Account::GROUP_ADMIN]);
        if ($managerRequestToken['status'] !== 200) return $managerRequestToken['response'];

        $user = $managerRequestToken['token']->data;
        $groupId = isset($user->gr_id) ? $user->gr_id : null;
        $userId = isset($user->us_id) ? $user->us_id : null;

        $logs = new Logs($args['idType'], $userId, $groupId);
        $logs->setType($args['type']);

        $logsController = new LogsController();
        $return = $logsController->getLog($logs);
        return $workOut->managerResponse($response, $return, 'result');
    });


});