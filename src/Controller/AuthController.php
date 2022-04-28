<?php

namespace App\Controller;

use App\Basics\Account;
use App\Basics\Admin;
use App\Basics\CompanyUser;
use App\Basics\Evaluator;
use App\DAO\AuthDAO;
use App\Utils\WorkOut;

class AuthController
{

    public function validateAccount(Account $account){
        if (is_null($account->getEmail())) {
            return array('status' => 400, 'message' => "ERROR", 'result' => 'E-Mail não informado!');
        }
        else if (is_null($account->getPass())) {
            return array('status' => 400, 'message' => "ERROR", 'result' => 'Senha não informada!');
        }
        else{
            return array('status' => 200);
        }
    }

    public function checkEmail(Account $account){
        if (is_null($account->getEmail())) {
            return array('status' => 400, 'message' => "ERROR", 'result' => 'E-mail não informado!');
        }

        $authDAO = new AuthDAO();
        return $authDAO->checkEmail($account);
    }

    public function login(Account $account)
    {
        $result = $this->validateAccount($account);
        if ($result['status'] !== 200) return $result;

        $authDAO = new AuthDAO();
        return $authDAO->login($account);
    }

    public function reset(Account $account)
    {
        if (empty($account->getEmail())) {
            return array('status' => 400, 'message' => "ERROR", 'result' => 'E-Mail não informado!');
        }

        $authDAO = new AuthDAO();
        return $authDAO->reset($account);

    }

    public function changePass(Account $account, $old, $new)
    {
        if (empty($account->getId())) {
            return array('status' => 400, 'message' => "ERROR", 'result' => 'ID não informado!');
        }
        if (empty($new)) {
            return array('status' => 400, 'message' => "ERROR", 'result' => 'Nova senha não informada!');
        }
        if (empty($old)) {
            return array('status' => 400, 'message' => "ERROR", 'result' => 'Senha anterior não informada!');
        }

        $authDAO = new AuthDAO();
        return $authDAO->changePass($account, $old, $new);

    }

    public function getProfile(Account $account)
    {
        if (empty($account->getId())) {
            return array('status' => 400, 'message' => "ERROR", 'result' => 'ID não informado!');
        }

        $authDAO = new AuthDAO();
        return $authDAO->getProfile($account);

    }

    public function editProfile(Account $account, $userId, $data)
    {
        if (is_null($account->getId())) {
            return array('status' => 400, 'message' => "ERROR", 'result' => 'Id não informado!');
        }
        else if (is_null($account->getGroupId())) {
            return array('status' => 400, 'message' => "ERROR", 'result' => 'Grupo não informado!');
        }
        else if (is_null($userId)) {
            return array('status' => 400, 'message' => "ERROR", 'result' => 'User_Id não informado!');
        }

        $workOut = new WorkOut();

        switch ($account->getGroupId()){
            case Account::GROUP_ADMIN:
                $admin = new Admin();

                $admin->setId($userId);
                $admin->setName(isset($data['name']) ? $data['name'] : null);
                $admin->setPhone(isset($data['phone']) ? $data['phone'] : null);
                $admin->setCpf(isset($data['cpf'])? $workOut->removeMask($data['cpf'], 'cpf'): null);
                $account->setEmail(isset($data['email']) ? $data['email'] : null);

                $userAdminController = new UserAdminController();
                return $userAdminController->update($admin, $account);

            case Account::GROUP_EVALUATOR:
                $evaluator = new Evaluator();

                $evaluator->setId($userId);
                $evaluator->setName(isset($data['name']) ? $data['name'] : null);
                $evaluator->setPhone(isset($data['phone']) ? $data['phone'] : null);
                $evaluator->setCpf(isset($data['cpf']) ? $workOut->removeMask($data['cpf'], 'cpf'): null);
                $evaluator->setUniversityGraduate(isset($data['graduate']) ? $data['graduate']: null);
                $evaluator->setPostGraduate(isset($data['postGraduate']) ? $data['postGraduate']: null);
                $account->setEmail(isset($data['email']) ? $data['email'] : null);

                $evaluatorsController = new EvaluatorsController();
                return $evaluatorsController->update($evaluator, $account);

            case Account::GROUP_COMPANY_USER:
                $companyUser = new CompanyUser();

                $companyUser->setId($userId);
                $companyUser->setName(isset($data['name']) ? $data['name'] : null);
                $companyUser->setCpf(isset($data['cpf']) ? $workOut->removeMask($data['cpf'], 'cpf'): null);
                $companyUser->setPhone(isset($data['phone']) ? $workOut->removeMask($data['phone'], 'phone'): null);
                $account->setEmail(isset($data['email']) ? $data['email'] : null);

                $companyUserController = new CompanyUserController();
                return $companyUserController->update($companyUser, $account);

            default:
                return array(
                    'status' => 400,
                    'message' => 'Grupo não informado!'
                );
        }
    }
}