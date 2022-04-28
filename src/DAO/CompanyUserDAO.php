<?php

namespace App\DAO;
use App\Basics\Account;
use App\Basics\Company;
use App\Basics\CompanyUser;
use App\Utils\WorkOut;
use App\Service\Email;
use App\Config\Doctrine;

class CompanyUserDAO
{
    public function findAll($idCompany)
    {
        try {
            $doctrine = new Doctrine();
            $entityManager = $doctrine->getEntityManager();

            $obj = $entityManager
                ->getRepository(CompanyUser::class)
                ->findBy(['company' => $idCompany], ['name' => 'ASC']);

            $workOut = new WorkOut();
            return $workOut->prepareListUserActiveInactive($obj);


        } catch (\Exception $ex) {
            return [
                'status' => 500,
                'message' => "ERROR",
                'result' => 'Erro na execução da instrução!',
                'CODE' => $ex->getCode(),
                'Exception' => $ex->getMessage(),
            ];
        }
    }

    public function validateBeforePersist(CompanyUser $companyUser, Account $account, $isEdit = false, $oldCpf = null, $oldEmail = null)
    {
        try {
            $authDAO = new AuthDAO();
            $checkEmail = $authDAO->checkEmail($account, $isEdit, $oldEmail);
            if ($checkEmail['status'] !== 200) return $checkEmail;

            $workOut = new WorkOut();
            $checkCpf = $workOut->checkCPF($companyUser->getCpf(), Account::GROUP_COMPANY_USER, $isEdit, $oldCpf);
            if ($checkCpf['status'] !== 200) return $checkCpf;

            return ['status' => 200];

        } catch (\Exception $ex) {
            return [
                'status' => 500,
                'message' => "ERROR",
                'result' => 'Erro na execução da instrução!',
                'CODE' => $ex->getCode(),
                'Exception' => $ex->getMessage(),
            ];
        }
    }

    public function insert(CompanyUser $companyUser, Account $account)
    {
        try {
            $doctrine = new Doctrine();
            $entityManager = $doctrine->getEntityManager();

            $validate = $this->validateBeforePersist($companyUser, $account);
            if ($validate['status'] !== 200) return $validate;

            $companyObj = $entityManager->find(Company::class, $companyUser->getCompany());

            $newPass = $account->getPass();
            $account->setPass(strtoupper(sha1($newPass)));

            $entityManager->persist($account);
            $companyUser->setAccount($account);
            $companyUser->setCompany($companyObj);

            $entityManager->persist($companyUser);
            $entityManager->flush();

            $email = new Email('');
            $email->newPass($account, false, $newPass);

            return [
                'status' => 200,
                'message' => "SUCCESS",
                'result' => 'Usuário cadastrado!',
            ];
        } catch (\Exception $ex) {
            return [
                'status' => 500,
                'message' => "ERROR",
                'result' => 'Erro na execução da instrução!',
                'CODE' => $ex->getCode(),
                'Exception' => $ex->getMessage(),
            ];
        }
    }

    public function update(CompanyUser $companyUser, Account $account)
    {
        try {
            $doctrine = new Doctrine();
            $entityManager = $doctrine->getEntityManager();

            $accountObj = $entityManager->find(Account::class, $account->getId());
            $companyUserObj = $entityManager->find(
                CompanyUser::class,
                $companyUser->getId()
            );

            $validate = $this->validateBeforePersist($companyUser, $account, true, $companyUserObj->getCpf(), $accountObj->getEmail());
            if ($validate['status'] !== 200) return $validate;

            $accountObj->setEmail($account->getEmail());
            $companyUserObj->setName($companyUser->getName());
            $companyUserObj->setPhone($companyUser->getPhone());
            $companyUserObj->setCpf($companyUser->getCpf());
            $companyUserObj->setType($companyUser->getType());
            $entityManager->flush();

            return [
                'status' => 200,
                'message' => "SUCCESS",
                'result' => 'Usuário atualizado!',
            ];
        } catch (\Exception $ex) {
            return [
                'status' => 500,
                'message' => "ERROR",
                'result' => 'Erro na execução da instrução!',
                'CODE' => $ex->getCode(),
                'Exception' => $ex->getMessage(),
            ];
        }
    }

    public function enabled(Account $account)
    {
        try {
            $doctrine = new Doctrine();
            $entityManager = $doctrine->getEntityManager();

            $text = $account->getActive() ? 'ativado' : 'desativado';

            $obj = $entityManager->find(Account::class, $account->getId());

            $obj->setActive($account->getActive());

            $entityManager->flush();

            return [
                'status' => 200,
                'message' => "SUCCESS",
                'result' => 'Usuário ' . $text . '!',
            ];
        } catch (\Exception $ex) {
            return [
                'status' => 500,
                'message' => "ERROR",
                'result' => 'Erro na execução da instrução!',
                'CODE' => $ex->getCode(),
                'Exception' => $ex->getMessage(),
            ];
        }
    }
}