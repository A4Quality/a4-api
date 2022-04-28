<?php
/**
 * Created by PhpStorm.
 * User: r.a.freitas
 * Date: 20/10/2018
 * Time: 12:47
 */
use App\Basics\Account;
use App\Basics\Evaluator;
use App\Utils\WorkOut;
use App\Controller\EvaluatorsController;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

$app->group('/evaluators', function () {

    $this->get('', function (ServerRequestInterface $request, ResponseInterface $response, $args) {

        $workOut = new WorkOut();
        $managerRequestToken = $workOut->managerRequestToken($request, $response, [Account::GROUP_ADMIN, Account::GROUP_EVALUATOR]);
        if ($managerRequestToken['status'] !== 200) return $managerRequestToken['response'];

        $evaluatorsController = new EvaluatorsController;
        $workOut = new WorkOut();

        $return = $evaluatorsController->findAll();
        return $workOut->managerResponse($response, $return, 'result');
    });

    $this->post('', function (ServerRequestInterface $request, ResponseInterface $response, $args) {

        $workOut = new WorkOut();
        $managerRequestToken = $workOut->managerRequestToken($request, $response, Account::GROUP_ADMIN);
        if ($managerRequestToken['status'] !== 200) return $managerRequestToken['response'];

        $data = $request->getParsedBody();

        $evaluator = new Evaluator();
        $evaluator->setName(isset($data['name']) ? $data['name'] : null);
        $evaluator->setPhone(isset($data['phone']) ? $data['phone'] : null);
        $evaluator->setCpf(isset($data['cpf']) ? $workOut->removeMask($data['cpf'], 'cpf'): null);
        $evaluator->setUniversityGraduate(isset($data['graduate']) ? $data['graduate']: null);
        $evaluator->setPostGraduate(isset($data['postGraduate']) ? $data['postGraduate']: null);
        $evaluator->setMinimumExperienceInBusinessAudit(isset($data['minimumExperienceInBusinessAudit']) ? $data['minimumExperienceInBusinessAudit']: null);
        $evaluator->setMinimumExperienceInControllership(isset($data['minimumExperienceInControllership']) ? $data['minimumExperienceInControllership']: null);
        $evaluator->setMinimumExperienceInHealthAccreditation(isset($data['minimumExperienceInHealthAccreditation']) ? $data['minimumExperienceInHealthAccreditation']: null);
        $evaluator->setMinimumExperienceInHealthAaudit(isset($data['minimumExperienceInHealthAaudit']) ? $data['minimumExperienceInHealthAaudit']: null);
        $evaluator->setSubscription($workOut->base64_to_jpeg($data['file']['base64'], '', 'subscriptions/'));

        $account = new Account();
        $account->setEmail(isset($data['account']['email']) ? $data['account']['email'] : null);
        $account->setPass(strtoupper(uniqid()));
        $account->setGroupId(Account::GROUP_EVALUATOR);
        $account->setActive(true);

        $evaluatorsController = new EvaluatorsController();
        $return = $evaluatorsController->insert($evaluator, $account);

        return $workOut->managerResponse($response, $return, 'result');
    });

    $this->put('', function (ServerRequestInterface $request, ResponseInterface $response, $args) {

        $workOut = new WorkOut();
        $managerRequestToken = $workOut->managerRequestToken($request, $response, Account::GROUP_ADMIN);
        if ($managerRequestToken['status'] !== 200) return $managerRequestToken['response'];

        $data = $request->getParsedBody();

        $evaluator = new Evaluator();
        $evaluator->setId(isset($data['id']) ? $data['id'] : null);
        $evaluator->setName(isset($data['name']) ? $data['name'] : null);
        $evaluator->setPhone(isset($data['phone']) ? $data['phone'] : null);
        $evaluator->setCpf(isset($data['cpf']) ? $workOut->removeMask($data['cpf'], 'cpf'): null);
        $evaluator->setUniversityGraduate(isset($data['graduate']) ? $data['graduate']: null);
        $evaluator->setPostGraduate(isset($data['postGraduate']) ? $data['postGraduate']: null);
        $evaluator->setMinimumExperienceInBusinessAudit(isset($data['minimumExperienceInBusinessAudit']) ? $data['minimumExperienceInBusinessAudit']: null);
        $evaluator->setMinimumExperienceInControllership(isset($data['minimumExperienceInControllership']) ? $data['minimumExperienceInControllership']: null);
        $evaluator->setMinimumExperienceInHealthAccreditation(isset($data['minimumExperienceInHealthAccreditation']) ? $data['minimumExperienceInHealthAccreditation']: null);
        $evaluator->setMinimumExperienceInHealthAaudit(isset($data['minimumExperienceInHealthAaudit']) ? $data['minimumExperienceInHealthAaudit']: null);


        if ($data['file'] && $data['file']['base64']) {
            $evaluator->setSubscription($workOut->base64_to_jpeg($data['file']['base64'], '', 'subscriptions/'));
        }

        $account = new Account();
        $account->setId(isset($data['account']['id']) ? $data['account']['id'] : null);
        $account->setEmail(isset($data['account']['email']) ? $data['account']['email'] : null);

        $evaluatorsController = new EvaluatorsController();
        $return = $evaluatorsController->update($evaluator, $account);

        return $workOut->managerResponse($response, $return, 'result');
    });

    $this->put('/enabled', function (ServerRequestInterface $request, ResponseInterface $response, $args) {

        $workOut = new WorkOut();
        $data = $request->getParsedBody();

        $managerRequestToken = $workOut->managerRequestToken($request, $response, Account::GROUP_ADMIN);
        if ($managerRequestToken['status'] !== 200) return $managerRequestToken['response'];

        $account = new Account();

        $account->setId(isset($data['idAccount']) ? $data['idAccount'] : null);
        $account->setActive(isset($data['status']) ? $data['status'] : null);

        $evaluatorsController = new EvaluatorsController();
        $return = $evaluatorsController->enabled($account);

        return $workOut->managerResponse($response, $return, 'result');
    });

    $this->post('/confirmTerm', function (ServerRequestInterface $request, ResponseInterface $response, $args) {

        $workOut = new WorkOut();

        $managerRequestToken = $workOut->managerRequestToken($request, $response, Account::GROUP_EVALUATOR);
        if ($managerRequestToken['status'] !== 200) return $managerRequestToken['response'];

        $evaluator = new Evaluator();

        $user = $managerRequestToken['token']->data;
        $evaluator->setId($user->us_id);

        $evaluatorsController = new EvaluatorsController();
        $return = $evaluatorsController->confirmTerm($evaluator);

        return $workOut->managerResponse($response, $return, 'result');
    });
});