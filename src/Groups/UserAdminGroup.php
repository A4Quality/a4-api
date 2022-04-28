<?php
/**
 * Created by PhpStorm.
 * User: r.a.freitas
 * Date: 20/10/2018
 * Time: 12:47
 */
use App\Basics\Account;
use App\Basics\Admin;
use App\Utils\WorkOut;
use App\Controller\UserAdminController;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

$app->group('/userAdmin', function () {

    $this->get('', function (ServerRequestInterface $request, ResponseInterface $response, $args) {

        $workOut = new WorkOut();
        $managerRequestToken = $workOut->managerRequestToken($request, $response, [Account::GROUP_ADMIN, Account::GROUP_EVALUATOR]);
        if ($managerRequestToken['status'] !== 200) return $managerRequestToken['response'];

        $userAdminController = new UserAdminController();
        $workOut = new WorkOut();

        $return = $userAdminController->findAll();
        return $workOut->managerResponse($response, $return, 'result');
    });

    $this->post('', function (ServerRequestInterface $request, ResponseInterface $response, $args) {

        $workOut = new WorkOut();
        $managerRequestToken = $workOut->managerRequestToken($request, $response, Account::GROUP_ADMIN);
        if ($managerRequestToken['status'] !== 200) return $managerRequestToken['response'];

        $data = $request->getParsedBody();

        $admin = new Admin();
        $account = new Account();
        $admin->setName(isset($data['name']) ? $data['name'] : null);
        $admin->setPhone(isset($data['phone']) ? $data['phone'] : null);
        $admin->setCpf(isset($data['cpf']) ? $workOut->removeMask($data['cpf'], 'cpf'): null);

        $admin->setUniversityGraduate(isset($data['graduate']) ? $data['graduate']: null);
        $admin->setPostGraduate(isset($data['postGraduate']) ? $data['postGraduate']: null);

        $admin->setMinimumExperienceInBusinessAudit(isset($data['minimumExperienceInBusinessAudit']) ? $data['minimumExperienceInBusinessAudit']: null);
        $admin->setMinimumExperienceInControllership(isset($data['minimumExperienceInControllership']) ? $data['minimumExperienceInControllership']: null);
        $admin->setMinimumExperienceInHealthAccreditation(isset($data['minimumExperienceInHealthAccreditation']) ? $data['minimumExperienceInHealthAccreditation']: null);
        $admin->setMinimumExperienceInHealthAaudit(isset($data['minimumExperienceInHealthAaudit']) ? $data['minimumExperienceInHealthAaudit']: null);

        $admin->setSubscription($workOut->base64_to_jpeg($data['file']['base64'], '', 'subscriptions/'));

        $account->setEmail(isset($data['account']['email']) ? $data['account']['email'] : null);
        $account->setGroupId(Account::GROUP_ADMIN);
        $account->setPass(strtoupper(uniqid()));
        $account->setActive(true);

        $userAdminController = new UserAdminController();
        $return = $userAdminController->insert($admin, $account);

        return $workOut->managerResponse($response, $return, 'result');
    });

    $this->put('', function (ServerRequestInterface $request, ResponseInterface $response, $args) {

        $workOut = new WorkOut();
        $data = $request->getParsedBody();

        $managerRequestToken = $workOut->managerRequestToken($request, $response, Account::GROUP_ADMIN);
        if ($managerRequestToken['status'] !== 200) return $managerRequestToken['response'];

        $admin = new Admin();
        $account = new Account();

        $admin->setId(isset($data['id']) ? $data['id'] : null);
        $admin->setName(isset($data['name']) ? $data['name'] : null);
        $admin->setPhone(isset($data['phone']) ? $data['phone'] : null);
        $admin->setCpf(isset($data['cpf'])? $workOut->removeMask($data['cpf'], 'cpf'): null);

        $admin->setUniversityGraduate(isset($data['graduate']) ? $data['graduate']: null);
        $admin->setPostGraduate(isset($data['postGraduate']) ? $data['postGraduate']: null);

        $admin->setMinimumExperienceInBusinessAudit(isset($data['minimumExperienceInBusinessAudit']) ? $data['minimumExperienceInBusinessAudit']: null);
        $admin->setMinimumExperienceInControllership(isset($data['minimumExperienceInControllership']) ? $data['minimumExperienceInControllership']: null);
        $admin->setMinimumExperienceInHealthAccreditation(isset($data['minimumExperienceInHealthAccreditation']) ? $data['minimumExperienceInHealthAccreditation']: null);
        $admin->setMinimumExperienceInHealthAaudit(isset($data['minimumExperienceInHealthAaudit']) ? $data['minimumExperienceInHealthAaudit']: null);

        if ($data['file'] && $data['file']['base64']) {
            $admin->setSubscription($workOut->base64_to_jpeg($data['file']['base64'], '', 'subscriptions/'));
        }

        $account->setId(isset($data['account']['id']) ? $data['account']['id'] : null);
        $account->setEmail(isset($data['account']['email']) ? $data['account']['email'] : null);

        $userAdminController = new UserAdminController();
        $return = $userAdminController->update($admin, $account);

        return $workOut->managerResponse($response, $return, 'result');
    });

    $this->put('/enabled', function (ServerRequestInterface $request, ResponseInterface $response, $args) {

        $workOut = new WorkOut();
        $data = $request->getParsedBody();

        $managerRequestToken = $workOut->managerRequestToken($request, $response, Account::GROUP_ADMIN);
        if ($managerRequestToken['status'] !== 200) return $managerRequestToken['response'];

        $account = new Account();

        $account->setId(isset($data['id']) ? $data['id'] : null);
        $account->setActive(isset($data['status']) ? $data['status'] : null);

        $userAdminController = new UserAdminController();
        $return = $userAdminController->enabled($account);

        return $workOut->managerResponse($response, $return, 'result');
    });
});