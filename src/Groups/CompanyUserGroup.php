<?php
/**
 * Created by PhpStorm.
 * User: r.a.freitas
 * Date: 20/10/2018
 * Time: 12:47
 */
use App\Basics\Account;
use App\Basics\CompanyUser;
use App\Utils\WorkOut;
use App\Controller\CompanyUserController;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

$app->group('/companyUser', function () {

    $this->get('/{id}', function (ServerRequestInterface $request, ResponseInterface $response, $args) {

        $workOut = new WorkOut();
        $managerRequestToken = $workOut->managerRequestToken($request, $response, Account::GROUP_ADMIN);
        if ($managerRequestToken['status'] !== 200) return $managerRequestToken['response'];

        $companyUserController = new CompanyUserController;
        $workOut = new WorkOut();

        $return = $companyUserController->findAll($args['id']);
        return $workOut->managerResponse($response, $return, 'result');
    });

    $this->post('', function (ServerRequestInterface $request, ResponseInterface $response, $args) {

        $workOut = new WorkOut();
        $managerRequestToken = $workOut->managerRequestToken($request, $response, Account::GROUP_ADMIN);
        if ($managerRequestToken['status'] !== 200) return $managerRequestToken['response'];

        $data = $request->getParsedBody();

        $companyUser = new CompanyUser();
        $companyUser->setName(isset($data['name']) ? $data['name'] : null);
        $companyUser->setCpf(isset($data['cpf']) ? $workOut->removeMask($data['cpf'], 'cpf'): null);
        $companyUser->setPhone(isset($data['phone']) ? $workOut->removeMask($data['phone'], 'phone'): null);
        $companyUser->setCompany(isset($data['company']) ? $data['company']: null);
        $companyUser->setType(isset($data['type']) ? $data['type']: null);

        $account = new Account();
        $account->setEmail(isset($data['account']['email']) ? $data['account']['email'] : null);
        $account->setPass(strtoupper(uniqid()));
        $account->setGroupId(Account::GROUP_COMPANY_USER);
        $account->setActive(true);

        $companyUserController = new CompanyUserController();
        $return = $companyUserController->insert($companyUser, $account);

        return $workOut->managerResponse($response, $return, 'result');
    });

    $this->put('', function (ServerRequestInterface $request, ResponseInterface $response, $args) {

        $workOut = new WorkOut();
        $managerRequestToken = $workOut->managerRequestToken($request, $response, Account::GROUP_ADMIN);
        if ($managerRequestToken['status'] !== 200) return $managerRequestToken['response'];

        $data = $request->getParsedBody();

        $companyUser = new CompanyUser();
        $companyUser->setId(isset($data['id']) ? $data['id'] : null);
        $companyUser->setName(isset($data['name']) ? $data['name'] : null);
        $companyUser->setCpf(isset($data['cpf']) ? $workOut->removeMask($data['cpf'], 'cpf'): null);
        $companyUser->setPhone(isset($data['phone']) ? $workOut->removeMask($data['phone'], 'phone'): null);
        $companyUser->setType(isset($data['type']) ? $data['type']: null);

        $account = new Account();
        $account->setId(isset($data['account']['id']) ? $data['account']['id'] : null);
        $account->setEmail(isset($data['account']['email']) ? $data['account']['email'] : null);

        $companyUserController = new CompanyUserController();
        $return = $companyUserController->update($companyUser, $account);

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

        $companyUserController = new CompanyUserController();
        $return = $companyUserController->enabled($account);

        return $workOut->managerResponse($response, $return, 'result');
    });
});