<?php
/**
 * Created by PhpStorm.
 * User: r.a.freitas
 * Date: 26/05/2019
 * Time: 00:30
 */

use App\Basics\Account;
use App\Basics\Evaluation;
use App\Basics\RN440\RN440;
use App\Basics\RN440\RN440RequirementsItems;
use App\Controller\RN440Controller;
use App\Utils\WorkOut;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

$app->group('/rn-440', function () {

    $this->get('/requirements/{id}', function (ServerRequestInterface $request, ResponseInterface $response, $args) {

        $workOut = new WorkOut();
        $managerRequestToken = $workOut->managerRequestToken($request, $response, [Account::GROUP_ADMIN, Account::GROUP_EVALUATOR, Account::GROUP_COMPANY_USER]);
        if ($managerRequestToken['status'] !== 200) return $managerRequestToken['response'];

        $rn440  = new RN440();
        $rn440->setId($args['id']);

        $user = $managerRequestToken['token']->data;
        $group = $user->gr_id;

        $rn440Controller = new RN440Controller();
        $return = $rn440Controller->listRequirements($rn440, $group);
        return $workOut->managerResponse($response, $return, 'result');
    });

    $this->post('/create', function (ServerRequestInterface $request, ResponseInterface $response) {

        $workOut = new WorkOut();
        $managerRequestToken = $workOut->managerRequestToken($request, $response, Account::GROUP_ADMIN);
        if ($managerRequestToken['status'] !== 200) return $managerRequestToken['response'];

        $data = $request->getParsedBody();
        $rn440 = new RN440();
        $rn440->setType(isset($data['type']) ? $data['type'] : null);

        $evaluation = new Evaluation(Evaluation::TYPE_RN_440);

        $evaluation->setCompany(isset($data['company']) ? $data['company'] : null);
        $evaluation->setEvaluator(isset($data['evaluators']) ? $data['evaluators'] : null);
        $evaluation->setEvaluatorObserver(isset($data['observerEvaluators']) ? $data['observerEvaluators'] : []);
        $evaluation->setAnalysisUser(isset($data['director']) ? $data['director'] : null);
        $evaluation->setLeaderApproval(isset($data['leader']) ? $data['leader'] : null);

        $requirements = isset($data['requirementNumbers']) ? $data['requirementNumbers'] : null;

        $rn440Controller = new RN440Controller();
        $return = $rn440Controller->insert($evaluation, $rn440, $requirements);

        return $workOut->managerResponse($response, $return, 'result');
    });

    $this->put('/update/requirements', function (ServerRequestInterface $request, ResponseInterface $response) {

        $workOut = new WorkOut();
        $managerRequestToken = $workOut->managerRequestToken($request, $response, [Account::GROUP_ADMIN, Account::GROUP_EVALUATOR, Account::GROUP_COMPANY_USER]);
        if ($managerRequestToken['status'] !== 200) return $managerRequestToken['response'];

        $data = $request->getParsedBody();
        $requirementsItems = new RN440RequirementsItems();
        $requirementsItems->setId(isset($data['id']) ? $data['id'] : null);
        $requirementsItems->setScope(isset($data['scope']) ? $data['scope'] : null);
        $requirementsItems->setDeploymentTime(isset($data['time']) ? $data['time'] : null);

        $type = isset($data['type']) ? $data['type'] : null;

        $user = $managerRequestToken['token']->data;
        $groupId = isset($user->gr_id) ? $user->gr_id : null;
        $userId = isset($user->us_id) ? $user->us_id : null;

        $rn440Controller = new RN440Controller();
        $return = $rn440Controller->updateRequirementsItems($requirementsItems, $groupId, $userId, $type);

        return $workOut->managerResponse($response, $return, 'result');
    });

    $this->put('/update/requirements/comments', function (ServerRequestInterface $request, ResponseInterface $response) {

        $workOut = new WorkOut();
        $managerRequestToken = $workOut->managerRequestToken($request, $response, [Account::GROUP_ADMIN, Account::GROUP_EVALUATOR, Account::GROUP_COMPANY_USER]);
        if ($managerRequestToken['status'] !== 200) return $managerRequestToken['response'];

        $data = $request->getParsedBody();
        $rn440RequirementsItems = new RN440RequirementsItems();
        $rn440RequirementsItems->setId(isset($data['id']) ? $data['id'] : null);
        $rn440RequirementsItems->setComment(isset($data['comment']) ? $data['comment'] : null);
        $rn440RequirementsItems->setEvidence(isset($data['evidence']) ? $data['evidence'] : null);
        $rn440RequirementsItems->setFeedback(isset($data['feedback']) ? $data['feedback'] : null);
        $rn440RequirementsItems->setChangedPoint(isset($data['changedPoint']) ? $data['changedPoint'] : null);
        $rn440RequirementsItems->setImprovementOpportunity(isset($data['improvementOpportunity']) ? $data['improvementOpportunity'] : null);
        $rn440RequirementsItems->setStrongPoint(isset($data['strongPoint']) ? $data['strongPoint'] : null);
        $rn440RequirementsItems->setNonAttendance(isset($data['nonAttendance']) ? $data['nonAttendance'] : null);

        $files = isset($data['files']) ? $data['files'] : null;

        $user = $managerRequestToken['token']->data;
        $groupId = isset($user->gr_id) ? $user->gr_id : null;
        $userId = isset($user->us_id) ? $user->us_id : null;

        $rn440Controller = new RN440Controller();
        $return = $rn440Controller->updateRequirementsComments($rn440RequirementsItems, $files, $groupId, $userId);

        return $workOut->managerResponse($response, $return, 'result');
    });

    $this->put('/delete/requirements/files', function (ServerRequestInterface $request, ResponseInterface $response) {

        $workOut = new WorkOut();
        $managerRequestToken = $workOut->managerRequestToken($request, $response, [Account::GROUP_ADMIN, Account::GROUP_EVALUATOR, Account::GROUP_COMPANY_USER]);
        if ($managerRequestToken['status'] !== 200) return $managerRequestToken['response'];

        $data = $request->getParsedBody();

        $id = isset($data['id']) ? $data['id'] : null;

        $userAdminController = new RN440Controller();
        $return = $userAdminController->deleteFile($id);

        return $workOut->managerResponse($response, $return, 'result');
    });

});