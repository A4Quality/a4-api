<?php
/**
 * Created by PhpStorm.
 * User: r.a.freitas
 * Date: 26/05/2019
 * Time: 00:30
 */

use App\Basics\Account;
use App\Basics\RN452\RN452;
use App\Basics\CompanyUser;
use App\Controller\GraphicsController;
use App\Utils\WorkOut;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

$app->group('/graphics', function () {

    $this->get('/dimensions/{typeEvaluation}/{classification}/{type}', function (ServerRequestInterface $request, ResponseInterface $response, $args) {

        $workOut = new WorkOut();
        $managerRequestToken = $workOut->managerRequestToken($request, $response, [Account::GROUP_COMPANY_USER]);
        if ($managerRequestToken['status'] !== 200) return $managerRequestToken['response'];

        $user = $managerRequestToken['token']->data;
        $companyUser = new CompanyUser();
        $companyUser->setId($user->us_id ?? null);

        $graphicsController = new GraphicsController();
        $return = $graphicsController->onlyDimension($args['typeEvaluation'], $args['classification'], $args['type'], $companyUser);
        return $workOut->managerResponse($response, $return, 'result');
    });

    $this->get('/home/{typeEvaluation}', function (ServerRequestInterface $request, ResponseInterface $response, $args) {

        $workOut = new WorkOut();
        $managerRequestToken = $workOut->managerRequestToken($request, $response, [Account::GROUP_ADMIN]);
        if ($managerRequestToken['status'] !== 200) return $managerRequestToken['response'];

        $typeEvaluation = $args['typeEvaluation'];

        $graphicsController = new GraphicsController();
        $return = $graphicsController->home($typeEvaluation);
        return $workOut->managerResponse($response, $return, 'result');
    });

    $this->post('/export/report', function (ServerRequestInterface $request, ResponseInterface $response, $args) {

        $workOut = new WorkOut();
        $managerRequestToken = $workOut->managerRequestToken($request, $response, [Account::GROUP_ADMIN]);
        if ($managerRequestToken['status'] !== 200) return $managerRequestToken['response'];

        $data = $request->getParsedBody();

        $companies = (isset($data['companies'])) ? $data['companies']: null;
        $RN452 = new RN452();
        $RN452->setType(isset($data['type']) ? $data['type'] : null);

        $graphicsController = new GraphicsController();
        $return = $graphicsController->exportReport($companies, $RN452);
        return $workOut->managerResponse($response, $return, 'result');
    });

    $this->post('/custom', function (ServerRequestInterface $request, ResponseInterface $response, $args) {

        $workOut = new WorkOut();
        $managerRequestToken = $workOut->managerRequestToken($request, $response, [Account::GROUP_ADMIN]);
        if ($managerRequestToken['status'] !== 200) return $managerRequestToken['response'];

        $data = $request->getParsedBody();

        $companies = (isset($data['companies'])) ? $data['companies']: null;
        $start = (isset($data['firstDay'])) ? $data['firstDay'] . ' 00:00:00': null;
        $end = (isset($data['lastDay'])) ? $data['lastDay'] . ' 23:59:59': null;
        $accreditation = new Accreditation();
        $accreditation->setType(isset($data['type']) ? $data['type'] : null);

        $graphicsController = new GraphicsController();
        $return = $graphicsController->custom($companies, $accreditation, $start, $end);
        return $workOut->managerResponse($response, $return, 'result');
    });

});