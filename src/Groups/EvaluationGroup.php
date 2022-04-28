<?php
/**
 * Created by PhpStorm.
 * User: r.a.freitas
 * Date: 26/05/2019
 * Time: 00:30
 */

use App\Basics\Account;
use App\Basics\Evaluation;
use App\Basics\Resume;
use App\Basics\Meeting;
use App\Basics\AuditedAreas;
use App\Basics\Diary;
use App\Basics\ControlVisualizationDimensions;
use App\Utils\WorkOut;
use App\Controller\EvaluationController;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

$app->group('/evaluation', function () {

    $this->get('/list/{typeEvaluation}/{classification}/{type}', function (ServerRequestInterface $request, ResponseInterface $response, $args) {

        $workOut = new WorkOut();
        $managerRequestToken = $workOut->managerRequestToken($request, $response);
        if ($managerRequestToken['status'] !== 200) return $managerRequestToken['response'];

        $user = $managerRequestToken['token']->data;

        $account = new Account();
        $account->setId(isset($user->ac_id) ? $user->ac_id : null);
        $account->setGroupId(isset($user->gr_id) ? $user->gr_id : null);
        $userId = $user->us_id;

        $typeEvaluation = $args['typeEvaluation'];
        $type = $args['type'];
        $classification = $args['classification'];

        $evaluationController = new EvaluationController();
        $return = $evaluationController->listForType($account, $userId, $typeEvaluation, $type, $classification);
        return $workOut->managerResponse($response, $return, 'result');
    });

    $this->get('/report/{evaluationId}', function (ServerRequestInterface $request, ResponseInterface $response, $args) {

        $workOut = new WorkOut();
        $managerRequestToken = $workOut->managerRequestToken($request, $response, [Account::GROUP_ADMIN]);
        if ($managerRequestToken['status'] !== 200) return $managerRequestToken['response'];

        $user = $managerRequestToken['token']->data;

        $account = new Account();
        $account->setId(isset($user->ac_id) ? $user->ac_id : null);
        $account->setGroupId(isset($user->gr_id) ? $user->gr_id : null);
        $userId = $user->us_id;

        $evaluation = new Evaluation(null);
        $evaluation->setId($args['evaluationId']);

        $evaluationController = new EvaluationController();
        $return = $evaluationController->report($account, $userId, $evaluation);
        return $workOut->managerResponse($response, $return, 'result');
    });

    $this->get('/report/{evaluationId}/preview', function (ServerRequestInterface $request, ResponseInterface $response, $args) {

        $workOut = new WorkOut();
        $managerRequestToken = $workOut->managerRequestToken($request, $response, [Account::GROUP_ADMIN, Account::GROUP_EVALUATOR]);
        if ($managerRequestToken['status'] !== 200) return $managerRequestToken['response'];

        $account = new Account();
        $user = $managerRequestToken['token']->data;
        $userId = $user->us_id;
        $account->setId(isset($user->ac_id) ? $user->ac_id : null);
        $account->setGroupId(isset($user->gr_id) ? $user->gr_id : null);

        $evaluation = new Evaluation(null);
        $evaluation->setId($args['evaluationId']);

        $evaluationController = new EvaluationController();
        $return = $evaluationController->report($account, $userId, $evaluation, true);
        return $workOut->managerResponse($response, $return, 'result');
    });

    $this->get('/resume/{evaluationId}', function (ServerRequestInterface $request, ResponseInterface $response, $args) {

        $workOut = new WorkOut();
        $managerRequestToken = $workOut->managerRequestToken($request, $response, [Account::GROUP_ADMIN, Account::GROUP_EVALUATOR]);
        if ($managerRequestToken['status'] !== 200) return $managerRequestToken['response'];

        $evaluation = new Evaluation(null);
        $evaluation->setId($args['evaluationId']);

        $evaluationController = new EvaluationController();
        $return = $evaluationController->listResume($evaluation);
        return $workOut->managerResponse($response, $return, 'result');
    });

    $this->put('/resume', function (ServerRequestInterface $request, ResponseInterface $response) {

        $workOut = new WorkOut();
        $managerRequestToken = $workOut->managerRequestToken($request, $response, [Account::GROUP_ADMIN, Account::GROUP_EVALUATOR]);
        if ($managerRequestToken['status'] !== 200) return $managerRequestToken['response'];

        $data = $request->getParsedBody();
        $resume = new Resume();
        $resume->setId(isset($data['id']) ? $data['id'] : null);
        $resume->setStartDay(isset($data['startDay']) ? $data['startDay'] : null);
        $resume->setEndDay(isset($data['endDay']) ? $data['endDay'] : null);
        $resume->setMonth(isset($data['month']) ? $data['month'] : null);
        $resume->setYear(isset($data['year']) ? $data['year'] : null);
        $resume->setCustomText(isset($data['customText']) ? $data['customText'] : null);
        $resume->setIsFit(isset($data['isFit']) ? $data['isFit'] : null);
        $resume->setIsRemote(isset($data['isRemote']) ? $data['isRemote'] : null);
        $resume->setLevel(isset($data['level']) ? $data['level'] : null);

        $evaluationController = new EvaluationController();
        $return = $evaluationController->updateResume($resume);
        return $workOut->managerResponse($response, $return, 'result');
    });

    $this->put('/date-start-end', function (ServerRequestInterface $request, ResponseInterface $response) {

        $workOut = new WorkOut();
        $managerRequestToken = $workOut->managerRequestToken($request, $response, [Account::GROUP_ADMIN, Account::GROUP_EVALUATOR]);
        if ($managerRequestToken['status'] !== 200) return $managerRequestToken['response'];

        $data = $request->getParsedBody();
        $evaluation = new Evaluation(null);
        $evaluation->setId(isset($data['id']) ? $data['id'] : null);
        $dateStart = isset($data['dateStart']) ? $data['dateStart'] : null;
        $dateEnd = isset($data['dateEnd']) ? $data['dateEnd'] : null;

        $evaluationController = new EvaluationController();
        $return = $evaluationController->dateStart($evaluation, $dateStart, $dateEnd, false);
        return $workOut->managerResponse($response, $return, 'result');
    });

    $this->put('/validity-date-start-end', function (ServerRequestInterface $request, ResponseInterface $response) {

        $workOut = new WorkOut();
        $managerRequestToken = $workOut->managerRequestToken($request, $response, [Account::GROUP_ADMIN, Account::GROUP_EVALUATOR]);
        if ($managerRequestToken['status'] !== 200) return $managerRequestToken['response'];

        $data = $request->getParsedBody();
        $evaluation = new Evaluation(null);
        $evaluation->setId(isset($data['id']) ? $data['id'] : null);
        $dateStart = isset($data['dateStart']) ? $data['dateStart'] : null;
        $dateEnd = isset($data['dateEnd']) ? $data['dateEnd'] : null;

        $evaluationController = new EvaluationController();
        $return = $evaluationController->dateStart($evaluation, $dateStart, $dateEnd, true);
        return $workOut->managerResponse($response, $return, 'result');
    });

    $this->put('/company-fields', function (ServerRequestInterface $request, ResponseInterface $response) {

        $workOut = new WorkOut();
        $managerRequestToken = $workOut->managerRequestToken($request, $response, [Account::GROUP_ADMIN, Account::GROUP_EVALUATOR]);
        if ($managerRequestToken['status'] !== 200) return $managerRequestToken['response'];

        $data = $request->getParsedBody();
        $evaluation = new Evaluation(null);
        $evaluation->setId(isset($data['id']) ? $data['id'] : null);
        $evaluation->setCompanyPort(isset($data['companyPort']) ? $data['companyPort'] : null);
        $evaluation->setCompanyIdss(isset($data['companyIdss']) ? $data['companyIdss'] : null);
        $evaluation->setCompanyNumberOfBeneficiaries(isset($data['companyNumberOfBeneficiaries']) ? $data['companyNumberOfBeneficiaries'] : null);
        $evaluation->setCompanyNumberOfEmployees(isset($data['companyNumberOfEmployees']) ? $data['companyNumberOfEmployees'] : null);

        $evaluationController = new EvaluationController();
        $return = $evaluationController->companyFields($evaluation);
        return $workOut->managerResponse($response, $return, 'result');
    });

    $this->put('/meeting', function (ServerRequestInterface $request, ResponseInterface $response) {

        $workOut = new WorkOut();
        $managerRequestToken = $workOut->managerRequestToken($request, $response, [Account::GROUP_ADMIN, Account::GROUP_EVALUATOR]);
        if ($managerRequestToken['status'] !== 200) return $managerRequestToken['response'];

        $data = $request->getParsedBody();
        $meeting = new Meeting(null);
        $meeting->setId(isset($data['id']) ? $data['id'] : null);
        $meeting->setDate(isset($data['date']) ? $data['date'] : null);
        $meeting->setPlace(isset($data['place']) ? $data['place'] : null);
        $meeting->setSchedule(isset($data['schedule']) ? $data['schedule'] : null);

        $participants = isset($data['participants']) ? $data['participants'] : null;

        $evaluationController = new EvaluationController();
        $return = $evaluationController->updateMeeting($meeting, $participants);
        return $workOut->managerResponse($response, $return, 'result');
    });

    $this->put('/people-interviewed', function (ServerRequestInterface $request, ResponseInterface $response) {

        $workOut = new WorkOut();
        $managerRequestToken = $workOut->managerRequestToken($request, $response, [Account::GROUP_ADMIN, Account::GROUP_EVALUATOR]);
        if ($managerRequestToken['status'] !== 200) return $managerRequestToken['response'];

        $data = $request->getParsedBody();
        $evaluationId = isset($data['evaluationId']) ? $data['evaluationId'] : null;
        $people = isset($data['people']) ? $data['people'] : null;

        $evaluationController = new EvaluationController();
        $return = $evaluationController->updatePeopleInterviewed($evaluationId, $people);
        return $workOut->managerResponse($response, $return, 'result');
    });

    $this->post('/audited-areas', function (ServerRequestInterface $request, ResponseInterface $response) {

        $workOut = new WorkOut();
        $managerRequestToken = $workOut->managerRequestToken($request, $response, [Account::GROUP_ADMIN, Account::GROUP_EVALUATOR]);
        if ($managerRequestToken['status'] !== 200) return $managerRequestToken['response'];

        $data = $request->getParsedBody();

        $auditedAreas = new AuditedAreas();
        $auditedAreas->setEvaluation(isset($data['evaluationId']) ? $data['evaluationId'] : null);
        $auditedAreas->setDimension(isset($data['dimension']) ? $data['dimension'] : null);
        $auditedAreas->setName(isset($data['name']) ? $data['name'] : null);

        $evaluationController = new EvaluationController();
        $return = $evaluationController->createAuditedAreas($auditedAreas);
        return $workOut->managerResponse($response, $return, 'result');
    });

    $this->delete('/audited-areas/{id}', function (ServerRequestInterface $request, ResponseInterface $response, $args) {

        $workOut = new WorkOut();
        $managerRequestToken = $workOut->managerRequestToken($request, $response, [Account::GROUP_ADMIN, Account::GROUP_EVALUATOR]);
        if ($managerRequestToken['status'] !== 200) return $managerRequestToken['response'];

        $auditedAreas = new AuditedAreas();
        $auditedAreas->setId($args['id']);

        $evaluationController = new EvaluationController();
        $return = $evaluationController->removeAuditedAreas($auditedAreas);
        return $workOut->managerResponse($response, $return, 'result');
    });

    $this->put('/upload-diary', function (ServerRequestInterface $request, ResponseInterface $response) {

        $workOut = new WorkOut();
        $managerRequestToken = $workOut->managerRequestToken($request, $response, [Account::GROUP_ADMIN, Account::GROUP_EVALUATOR]);
        if ($managerRequestToken['status'] !== 200) return $managerRequestToken['response'];

        $data = $request->getParsedBody();
        $diary = new Diary();
        $diary->setEvaluation(isset($data['evaluationId']) ? $data['evaluationId'] : null);
        $diary->setEvaluator(isset($data['evaluator']) ? $data['evaluator'] : null);
        $diary->setStartDate(isset($data['startDate']) ? $data['startDate'] : null);
        $diary->setEndDate(isset($data['endDate']) ? $data['endDate'] : null);
        $diary->setTitle(isset($data['title']) ? $data['title'] : null);
        $diary->setPublicId(isset($data['publicId']) ? $data['publicId'] : null);

        $replicate = $data['replicate'];
        $evaluators = $data['evaluators'];

        $type = isset($data['type']) ? $data['type'] : null;

        $evaluationController = new EvaluationController();
        $return = $evaluationController->updateDiary($diary, $type, $replicate, $evaluators);
        return $workOut->managerResponse($response, $return, 'result');
    });

    $this->put('/upload-evaluators', function (ServerRequestInterface $request, ResponseInterface $response) {

        $workOut = new WorkOut();
        $managerRequestToken = $workOut->managerRequestToken($request, $response, [Account::GROUP_ADMIN, Account::GROUP_EVALUATOR]);
        if ($managerRequestToken['status'] !== 200) return $managerRequestToken['response'];

        $data = $request->getParsedBody();

        $evaluation = new Evaluation(null);

        $evaluation->setId(isset($data['evaluationId']) ? $data['evaluationId'] : null);
        $evaluation->setEvaluator(isset($data['evaluators']) ? $data['evaluators'] : null);
        $evaluation->setLeaderApproval(isset($data['leader']) ? $data['leader'] : null);

        $evaluationController = new EvaluationController();
        $return = $evaluationController->updateEvaluators($evaluation);
        return $workOut->managerResponse($response, $return, 'result');
    });

    $this->put('/control-visualization', function (ServerRequestInterface $request, ResponseInterface $response) {

        $workOut = new WorkOut();
        $managerRequestToken = $workOut->managerRequestToken($request, $response, [Account::GROUP_ADMIN, Account::GROUP_EVALUATOR]);
        if ($managerRequestToken['status'] !== 200) return $managerRequestToken['response'];

        $data = $request->getParsedBody();
        $controlVisualization = new ControlVisualizationDimensions();
        $controlVisualization->setEvaluation(isset($data['evaluationId']) ? $data['evaluationId'] : null);
        $controlVisualization->setEvaluator(isset($data['evaluator']) ? $data['evaluator'] : null);
        $controlVisualization->setDimension(isset($data['dimension']) ? $data['dimension'] : null);
        $controlVisualization->setRequirement(isset($data['requirement']) ? $data['requirement'] : null);
        $type = isset($data['type']) ? $data['type'] : null;

        $evaluationController = new EvaluationController();
        $return = $evaluationController->updateControlVisualization($controlVisualization, $type);
        return $workOut->managerResponse($response, $return, 'result');
    });

    $this->put('/submitAnalysis', function (ServerRequestInterface $request, ResponseInterface $response) {

        $workOut = new WorkOut();
        $managerRequestToken = $workOut->managerRequestToken($request, $response, [Account::GROUP_ADMIN, Account::GROUP_EVALUATOR]);
        if ($managerRequestToken['status'] !== 200) return $managerRequestToken['response'];

        $user = $managerRequestToken['token']->data;
        $data = $request->getParsedBody();
        $evaluation = new Evaluation(Evaluation::TYPE_RN_452);
        $evaluation->setId(isset($data['id']) ? $data['id'] : null);

        $account = new Account();
        $account->setId(isset($user->ac_id) ? $user->ac_id : null);
        $account->setGroupId(isset($user->gr_id) ? $user->gr_id : null);
        $userId = $user->us_id;

        $evaluationController = new EvaluationController();
        $return = $evaluationController->submitAnalysis($evaluation, $account, $userId);

        return $workOut->managerResponse($response, $return, 'result');
    });

    $this->put('/submitFinishedSupervision', function (ServerRequestInterface $request, ResponseInterface $response) {

        $workOut = new WorkOut();
        $managerRequestToken = $workOut->managerRequestToken($request, $response, [Account::GROUP_ADMIN, Account::GROUP_EVALUATOR]);
        if ($managerRequestToken['status'] !== 200) return $managerRequestToken['response'];

        $user = $managerRequestToken['token']->data;
        $data = $request->getParsedBody();
        $evaluation = new Evaluation(Evaluation::TYPE_RN_452);
        $evaluation->setId(isset($data['id']) ? $data['id'] : null);

        $account = new Account();
        $account->setId(isset($user->ac_id) ? $user->ac_id : null);
        $account->setGroupId(isset($user->gr_id) ? $user->gr_id : null);
        $userId = $user->us_id;

        $evaluationController = new EvaluationController();
        $return = $evaluationController->submitFinishedSupervision($evaluation, $account, $userId);

        return $workOut->managerResponse($response, $return, 'result');
    });

    $this->put('/submitFinished', function (ServerRequestInterface $request, ResponseInterface $response) {

        $workOut = new WorkOut();
        $managerRequestToken = $workOut->managerRequestToken($request, $response, [Account::GROUP_ADMIN, Account::GROUP_EVALUATOR, Account::GROUP_COMPANY_USER]);
        if ($managerRequestToken['status'] !== 200) return $managerRequestToken['response'];

        $data = $request->getParsedBody();
        $evaluation = new Evaluation(Evaluation::TYPE_RN_452);
        $evaluation->setId(isset($data['id']) ? $data['id'] : null);

        $user = $managerRequestToken['token']->data;
        $account = new Account();
        $account->setId(isset($user->ac_id) ? $user->ac_id : null);
        $account->setGroupId(isset($user->gr_id) ? $user->gr_id : null);
        $userId = $user->us_id;

        $evaluationController = new EvaluationController();
        $return = $evaluationController->submitFinished($evaluation, $account, $userId);

        return $workOut->managerResponse($response, $return, 'result');
    });

    $this->put('/submitFeedback', function (ServerRequestInterface $request, ResponseInterface $response) {

        $workOut = new WorkOut();
        $managerRequestToken = $workOut->managerRequestToken($request, $response, [Account::GROUP_COMPANY_USER]);
        if ($managerRequestToken['status'] !== 200) return $managerRequestToken['response'];

        $data = $request->getParsedBody();
        $evaluation = new Evaluation(Evaluation::TYPE_RN_452);
        $evaluation->setId(isset($data['id']) ? $data['id'] : null);

        $evaluationController = new EvaluationController();
        $return = $evaluationController->submitFeedback($evaluation);

        return $workOut->managerResponse($response, $return, 'result');
    });

    $this->put('/populate', function (ServerRequestInterface $request, ResponseInterface $response) {

        $workOut = new WorkOut();
        $managerRequestToken = $workOut->managerRequestToken($request, $response, [Account::GROUP_ADMIN]);
        if ($managerRequestToken['status'] !== 200) return $managerRequestToken['response'];

        $data = $request->getParsedBody();
        $evaluation = new Evaluation(null);
        $evaluation->setId(isset($data['id']) ? $data['id'] : null);

        $evaluationController = new EvaluationController();
        $return = $evaluationController->populate($evaluation);

        return $workOut->managerResponse($response, $return, 'result');
    });

    $this->put('/delete', function (ServerRequestInterface $request, ResponseInterface $response) {

        $workOut = new WorkOut();
        $managerRequestToken = $workOut->managerRequestToken($request, $response, [Account::GROUP_ADMIN]);
        if ($managerRequestToken['status'] !== 200) return $managerRequestToken['response'];

        $data = $request->getParsedBody();
        $user = $managerRequestToken['token']->data;
        $account = new Account();
        $account->setId(isset($user->ac_id) ? $user->ac_id : null);
        $account->setPass(isset($data['password']) ? $data['password'] : null);

        $evaluation = new Evaluation(null);
        $evaluation->setId(isset($data['id']) ? $data['id'] : null);

        $evaluationController = new EvaluationController();
        $return = $evaluationController->deleteEvaluation($evaluation, $account);

        return $workOut->managerResponse($response, $return, 'result');
    });

});