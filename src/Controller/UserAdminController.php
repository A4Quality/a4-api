<?php

namespace App\Controller;

use App\Basics\Account;
use App\Basics\Admin;
use App\DAO\UserAdminDAO;
use App\Utils\WorkOut;

class UserAdminController
{
    public function findAll(){
        $userAdminDAO = new UserAdminDAO();
        return $userAdminDAO->findAll();
    }

    public function validateAccount(Admin $admin, Account $account, $isInsert = true){
        if (empty($admin->getName())) {
            return [
                'status' => 400,
                'message' => "ERROR",
                'result' => 'Nome não informado!',
            ];
        }
        else if (empty($admin->getPhone())) {
            return [
                'status' => 400,
                'message' => "ERROR",
                'result' => 'Telefone não informada!',
            ];
        }
        else if (empty($admin->getCpf())) {
            return [
                'status' => 400,
                'message' => "ERROR",
                'result' => 'CPF não informado!',
            ];
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

    public function insert(Admin $admin, Account $account)
    {
        if (is_null($admin->getSubscription())) {
            return [
                'status' => 400,
                'message' => "ERROR",
                'result' => 'Assinatura não informada!',
            ];
        }

        $result = $this->validateAccount($admin, $account);
        if ($result['status'] !== 200) return $result;

        $workOut = new WorkOut();

        $admin->setPhone($workOut->removeMask($admin->getPhone(), 'phone'));
        $admin->setCpf($workOut->removeMask($admin->getCpf(), 'cpf'));

        $adminDAO = new UserAdminDAO();
        return $adminDAO->insert($admin, $account);
    }

    public function update(Admin $admin, Account $account)
    {
        if (empty($account->getId())) {
            return [
                'status' => 400,
                'message' => "ERROR",
                'result' => 'ID não informado!',
            ];
        }
        $result = $this->validateAccount($admin, $account, false);
        if ($result['status'] !== 200) return $result;

        $workOut = new WorkOut();
        $admin->setPhone($workOut->removeMask($admin->getPhone(), 'phone'));
        $admin->setCpf($workOut->removeMask($admin->getCpf(), 'cpf'));

        $admin->setMinimumExperienceInBusinessAudit($admin->getMinimumExperienceInBusinessAudit() === '1');
        $admin->setMinimumExperienceInControllership($admin->getMinimumExperienceInControllership() === '1');
        $admin->setMinimumExperienceInHealthAccreditation($admin->getMinimumExperienceInHealthAccreditation() === '1');
        $admin->setMinimumExperienceInHealthAaudit($admin->getMinimumExperienceInHealthAaudit() === '1');

        $adminDAO = new UserAdminDAO();
        return $adminDAO->update($admin, $account);
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

        $adminDAO = new UserAdminDAO();
        return $adminDAO->enabled($account);
    }
}