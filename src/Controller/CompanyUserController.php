<?php

namespace App\Controller;

use App\Basics\Account;
use App\Basics\CompanyUser;
use App\DAO\CompanyUserDAO;

class CompanyUserController
{
    public function findAll($idCompany){

        $companyUserDAO = new CompanyUserDAO();
        return $companyUserDAO->findAll($idCompany);
    }

    public function validateCompanyUser(CompanyUser $companyUser, Account $account, $isInsert = true){
        if (empty($companyUser->getId()) && !$isInsert) {
            return [
                'status' => 400,
                'message' => "ERROR",
                'result' => 'Nome não informado!',
            ];
        }
        else if (empty($companyUser->getName())) {
            return [
                'status' => 400,
                'message' => "ERROR",
                'result' => 'Nome não informado!',
            ];
        }
        else if (empty($companyUser->getType())) {
            return [
                'status' => 400,
                'message' => "ERROR",
                'result' => 'Tipo não informado!',
            ];
        }
        else if (empty($companyUser->getPhone())) {
            return [
                'status' => 400,
                'message' => "ERROR",
                'result' => 'Telefone não informada!',
            ];
        }
        else if (empty($companyUser->getCpf())) {
            return [
                'status' => 400,
                'message' => "ERROR",
                'result' => 'CPF não informada!',
            ];
        }
        else if (empty($companyUser->getCompany()) && $isInsert) {
            return [
                'status' => 400,
                'message' => "ERROR",
                'result' => 'Empresa não informada!',
            ];
        }
        else if (is_null($account->getId()) && !$isInsert) {
            return array('status' => 400, 'message' => "ERROR", 'result' => 'ID da conta não informado!');
        }
        else if (is_null($account->getEmail())) {
            return array('status' => 400, 'message' => "ERROR", 'result' => 'E-Mail não informado!');
        }
        else if (is_null($account->getPass()) && $isInsert) {
            return array('status' => 400, 'message' => "ERROR", 'result' => 'Senha não informada!');
        }
        else{
            return array('status' => 200);
        }
    }

    public function insert(CompanyUser $companyUser, Account $account)
    {
        $result = $this->validateCompanyUser($companyUser, $account);
        if ($result['status'] !== 200) return $result;

        $companyUserDAO = new CompanyUserDAO();
        return $companyUserDAO->insert($companyUser, $account);
    }

    public function update(CompanyUser $companyUser, Account $account)
    {
        $result = $this->validateCompanyUser($companyUser, $account, false);
        if ($result['status'] !== 200) return $result;

        $companyUserDAO = new CompanyUserDAO();
        return $companyUserDAO->update($companyUser, $account);
    }

    public function enabled(Account $account)
    {
        if (empty($account->getId())) {
            return [
                'status' => 400,
                'message' => "ERROR",
                'result' => 'Id da Conta não informado!',
            ];
        }

        if (is_null($account->getActive())) {
            return [
                'status' => 400,
                'message' => "ERROR",
                'result' => 'Status da conta não informado!',
            ];
        }

        if (is_string($account->getActive())) {
            $account->setActive($account->getActive() === 'true');
        }

        $companyUserDAO = new CompanyUserDAO();
        return $companyUserDAO->enabled($account);
    }
}