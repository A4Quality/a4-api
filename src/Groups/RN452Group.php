<?php
/**
 * Created by PhpStorm.
 * User: r.a.freitas
 * Date: 26/05/2019
 * Time: 00:30
 */

use App\Basics\Account;
use App\Basics\Evaluation;
use App\Basics\RN452\RN452;
use App\Basics\RN452\RN452Prerequisites;
use App\Basics\RN452\RN452MonitoredIndicators;
use App\Basics\RN452\RN452RequirementsItems;
use App\Controller\RN452Controller;
use App\Utils\WorkOut;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

$app->group('/rn-452', function () {

    $this->get('/requirements/{id}', function (ServerRequestInterface $request, ResponseInterface $response, $args) {

        $workOut = new WorkOut();
        $managerRequestToken = $workOut->managerRequestToken($request, $response, [Account::GROUP_ADMIN, Account::GROUP_EVALUATOR, Account::GROUP_COMPANY_USER]);
        if ($managerRequestToken['status'] !== 200) return $managerRequestToken['response'];

        $rn452  = new RN452();
        $rn452->setId($args['id']);

        $user = $managerRequestToken['token']->data;
        $group = $user->gr_id;

        $rn452Controller = new RN452Controller();
        $return = $rn452Controller->listRequirements($rn452, $group);
        return $workOut->managerResponse($response, $return, 'result');
    });

    $this->post('/create', function (ServerRequestInterface $request, ResponseInterface $response) {

        $workOut = new WorkOut();
        $managerRequestToken = $workOut->managerRequestToken($request, $response, Account::GROUP_ADMIN);
        if ($managerRequestToken['status'] !== 200) return $managerRequestToken['response'];

        $data = $request->getParsedBody();
        $rn452 = new RN452();
        $rn452->setType(isset($data['type']) ? $data['type'] : null);
        $rn452->setClassification(isset($data['classification']) ? $data['classification'] : null);

        $evaluation = new Evaluation(Evaluation::TYPE_RN_452);

        $evaluation->setCompany(isset($data['company']) ? $data['company'] : null);
        $evaluation->setEvaluator(isset($data['evaluators']) ? $data['evaluators'] : null);
        $evaluation->setEvaluatorObserver(isset($data['observerEvaluators']) ? $data['observerEvaluators'] : []);
        $evaluation->setAnalysisUser(isset($data['director']) ? $data['director'] : null);
        $evaluation->setLeaderApproval(isset($data['leader']) ? $data['leader'] : null);

        $dimensions = isset($data['dimensions']) ? $data['dimensions'] : null;

        $rN452Controller = new RN452Controller();
        $return = $rN452Controller->insert($evaluation, $rn452, $dimensions);

        return $workOut->managerResponse($response, $return, 'result');
    });

    $this->get('/prerequisites/{accreditationId}', function (ServerRequestInterface $request, ResponseInterface $response, $args) {

        $workOut = new WorkOut();
        $managerRequestToken = $workOut->managerRequestToken($request, $response, [Account::GROUP_ADMIN, Account::GROUP_EVALUATOR]);
        if ($managerRequestToken['status'] !== 200) return $managerRequestToken['response'];

        $RN452 = new RN452();
        $RN452->setId($args['id']);

        $RN452Controller = new RN452Controller();
        $return = $RN452Controller->listPrerequisites($RN452);
        return $workOut->managerResponse($response, $return, 'result');
    });

    $this->put('/prerequisites', function (ServerRequestInterface $request, ResponseInterface $response) {

        $workOut = new WorkOut();
        $managerRequestToken = $workOut->managerRequestToken($request, $response, [Account::GROUP_ADMIN, Account::GROUP_EVALUATOR]);
        if ($managerRequestToken['status'] !== 200) return $managerRequestToken['response'];

        $data = $request->getParsedBody();
        $accreditationPrerequisites = new RN452Prerequisites();
        $accreditationPrerequisites->setId(isset($data['id']) ? $data['id'] : null);
        $accreditationPrerequisites->setItHas(isset($data['itHas']) ? $data['itHas'] : null);

        $RN452Controller = new RN452Controller();
        $return = $RN452Controller->updatePrerequisites($accreditationPrerequisites);
        return $workOut->managerResponse($response, $return, 'result');
    });

    $this->put('/update/indicators', function (ServerRequestInterface $request, ResponseInterface $response) {

        $workOut = new WorkOut();
        $managerRequestToken = $workOut->managerRequestToken($request, $response, [Account::GROUP_ADMIN, Account::GROUP_EVALUATOR, Account::GROUP_COMPANY_USER]);
        if ($managerRequestToken['status'] !== 200) return $managerRequestToken['response'];

        $data = $request->getParsedBody();
        $monitoredIndicators = new RN452MonitoredIndicators();
        $monitoredIndicators->setId(isset($data['id']) ? $data['id'] : null);
        $monitoredIndicators->setItHas(isset($data['itHas']) ? $data['itHas'] : null);

        $user = $managerRequestToken['token']->data;
        $groupId = isset($user->gr_id) ? $user->gr_id : null;
        $userId = isset($user->us_id) ? $user->us_id : null;

        $rN452Controller = new RN452Controller();
        $return = $rN452Controller->updateMonitoredIndicators($monitoredIndicators, $groupId, $userId);

        return $workOut->managerResponse($response, $return, 'result');
    });

    $this->put('/update/requirements', function (ServerRequestInterface $request, ResponseInterface $response) {

        $workOut = new WorkOut();
        $managerRequestToken = $workOut->managerRequestToken($request, $response, [Account::GROUP_ADMIN, Account::GROUP_EVALUATOR, Account::GROUP_COMPANY_USER]);
        if ($managerRequestToken['status'] !== 200) return $managerRequestToken['response'];

        $data = $request->getParsedBody();
        $requirementsItems = new RN452RequirementsItems();
        $requirementsItems->setId(isset($data['id']) ? $data['id'] : null);
        $requirementsItems->setScope(isset($data['scope']) ? $data['scope'] : null);
        $requirementsItems->setDeploymentTime(isset($data['time']) ? $data['time'] : null);

        $type = isset($data['type']) ? $data['type'] : null;

        $user = $managerRequestToken['token']->data;
        $groupId = isset($user->gr_id) ? $user->gr_id : null;
        $userId = isset($user->us_id) ? $user->us_id : null;

        $rN452Controller = new RN452Controller();
        $return = $rN452Controller->updateRequirementsItems($requirementsItems, $groupId, $userId, $type);

        return $workOut->managerResponse($response, $return, 'result');
    });

    $this->put('/update/requirements/comments', function (ServerRequestInterface $request, ResponseInterface $response) {

        $workOut = new WorkOut();
        $managerRequestToken = $workOut->managerRequestToken($request, $response, [Account::GROUP_ADMIN, Account::GROUP_EVALUATOR, Account::GROUP_COMPANY_USER]);
        if ($managerRequestToken['status'] !== 200) return $managerRequestToken['response'];

        $data = $request->getParsedBody();
        $RN452RequirementsItems = new RN452RequirementsItems();
        $RN452RequirementsItems->setId(isset($data['id']) ? $data['id'] : null);
        $RN452RequirementsItems->setComment(isset($data['comment']) ? $data['comment'] : null);
        $RN452RequirementsItems->setEvidence(isset($data['evidence']) ? $data['evidence'] : null);
        $RN452RequirementsItems->setFeedback(isset($data['feedback']) ? $data['feedback'] : null);
        $RN452RequirementsItems->setChangedPoint(isset($data['changedPoint']) ? $data['changedPoint'] : null);
        $RN452RequirementsItems->setImprovementOpportunity(isset($data['improvementOpportunity']) ? $data['improvementOpportunity'] : null);
        $RN452RequirementsItems->setStrongPoint(isset($data['strongPoint']) ? $data['strongPoint'] : null);
        $RN452RequirementsItems->setNonAttendance(isset($data['nonAttendance']) ? $data['nonAttendance'] : null);

        $files = isset($data['files']) ? $data['files'] : null;

        $user = $managerRequestToken['token']->data;
        $groupId = isset($user->gr_id) ? $user->gr_id : null;
        $userId = isset($user->us_id) ? $user->us_id : null;

        $RN452Controller = new RN452Controller();
        $return = $RN452Controller->updateRequirementsComments($RN452RequirementsItems, $files, $groupId, $userId);

        return $workOut->managerResponse($response, $return, 'result');
    });

    $this->put('/delete/requirements/files', function (ServerRequestInterface $request, ResponseInterface $response) {

        $workOut = new WorkOut();
        $managerRequestToken = $workOut->managerRequestToken($request, $response, [Account::GROUP_ADMIN, Account::GROUP_EVALUATOR, Account::GROUP_COMPANY_USER]);
        if ($managerRequestToken['status'] !== 200) return $managerRequestToken['response'];

        $data = $request->getParsedBody();

        $id = isset($data['id']) ? $data['id'] : null;

        $userAdminController = new RN452Controller();
        $return = $userAdminController->deleteFile($id);

        return $workOut->managerResponse($response, $return, 'result');
    });

});