<?php

namespace App\DAO;
use App\Basics\Account;
use App\Basics\Admin;
use App\Utils\WorkOut;
use App\Service\Email;
use App\Config\Doctrine;

class UserAdminDAO
{
    public function findAll()
    {
        try {
            $doctrine = new Doctrine();
            $entityManager = $doctrine->getEntityManager();

            $obj = $entityManager
                ->getRepository(Admin::class)
                ->findBy([], ['name' => 'ASC']);

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

    public function validateBeforePersist(Admin $admin, Account $account, $isEdit = false, $oldCpf = null, $oldEmail = null)
    {
        try {
            $authDAO = new AuthDAO();
            $checkEmail = $authDAO->checkEmail($account, $isEdit, $oldEmail);
            if ($checkEmail['status'] !== 200) return $checkEmail;

            $workOut = new WorkOut();
            $checkCpf = $workOut->checkCPF($admin->getCpf(), Account::GROUP_ADMIN, $isEdit, $oldCpf);
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

    public function insert(Admin $admin, Account $account)
    {
        try {
            $doctrine = new Doctrine();
            $entityManager = $doctrine->getEntityManager();

            $validate = $this->validateBeforePersist($admin, $account);
            if ($validate['status'] !== 200) return $validate;

            $newPass = $account->getPass();
            $account->setPass(strtoupper(sha1($newPass)));

            $entityManager->persist($account);

            $admin->setAccount($account);

            $entityManager->persist($admin);
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

    public function update(Admin $admin, Account $account)
    {
        try {
            $doctrine = new Doctrine();
            $entityManager = $doctrine->getEntityManager();

            $accountObj = $entityManager->find(Account::class, $account->getId());
            $adminObj = $entityManager->find(
                Admin::class,
                $admin->getId()
            );

            $validate = $this->validateBeforePersist($admin, $account, true, $adminObj->getCpf(), $accountObj->getEmail());
            if ($validate['status'] !== 200) return $validate;

            $accountObj->setEmail($account->getEmail());

            $adminObj->setName($admin->getName());
            $adminObj->setPhone($admin->getPhone());
            $adminObj->setCpf($admin->getCpf());

            $adminObj->setUniversityGraduate($admin->getUniversityGraduate());
            $adminObj->setPostGraduate($admin->getPostGraduate());

            $adminObj->setMinimumExperienceInBusinessAudit($admin->getMinimumExperienceInBusinessAudit());
            $adminObj->setMinimumExperienceInControllership($admin->getMinimumExperienceInControllership());
            $adminObj->setMinimumExperienceInHealthAccreditation($admin->getMinimumExperienceInHealthAccreditation());
            $adminObj->setMinimumExperienceInHealthAaudit($admin->getMinimumExperienceInHealthAaudit());

            if ($admin->getSubscription()) {
                $adminObj->setSubscription($admin->getSubscription());
            }

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

            $obj->setAtivo($account->getActive());

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