<?php

namespace App\DAO;
use App\Basics\Account;
use App\Basics\Admin;
use App\Basics\Evaluator;
use App\Basics\CompanyUser;
use App\Service\Email;
use App\Config\Doctrine;
class AuthDAO
{
    public function login(Account $account) {

        try {

            $doctrine = new Doctrine();
            $entityManager = $doctrine->getEntityManager();

            $accountObj = $entityManager->getRepository(Account::class)->findBy(array(
                'email'  => $account->getEmail(),
                'pass'  => $account->getPass(),
                'active'  => $account->getActive(),
            ), array(
                'id' => 'ASC'
            ), 1);

            if (empty($accountObj)) {
                return array('status' => 401, 'message' => "ERROR", 'result' => 'Usuário não existe ou a senha está incorreta!');
            }else{
                return $this->getUserData($accountObj[0]);
            }

        } catch (\Doctrine\ORM\ORMException $ex) {
            return array(
                'status'    => 500,
                'message'   => "ERROR",
                'result'    => 'Erro na execução da instrução!',
                'CODE'      => $ex->getCode(),
                'Exception' => $ex->getMessage(),
            );
        }
    }

    public function checkEmail(Account $account, $isEdit = false, $oldEmail = null) {

        try {

            $doctrine = new Doctrine();
            $entityManager = $doctrine->getEntityManager();

            $accountObj = $entityManager->getRepository(Account::class)->findBy(array(
                'email'  => $account->getEmail(),
            ), array(
                'id' => 'ASC'
            ), 1);

            if (!$isEdit && !empty($accountObj))
                return array(
                    'status' => 400,
                    'message' => "ERROR",
                    'result' => 'Este e-mail já esta em uso por outra conta!',
                    'account' => $accountObj[0]->convertArray());

            if ($isEdit && !empty($accountObj) && $accountObj[0]->getEmail() !== $oldEmail)
                return array(
                    'status' => 400,
                    'message' => "ERROR",
                    'result' => 'Este e-mail já esta em uso por outra conta!',
                    'account' => $accountObj[0]->convertArray());

            return array('status' => 200, 'message' => "SUCCESS", 'result' => 'Este e-mail não está sendo usado por ninguém!');

        } catch (\Doctrine\ORM\ORMException $ex) {
            return array(
                'status'    => 500,
                'message'   => "ERROR",
                'result'    => 'Erro na execução da instrução!',
                'CODE'      => $ex->getCode(),
                'Exception' => $ex->getMessage(),
            );
        }
    }

    public function reset(Account $account) {

        try {

            $doctrine = new Doctrine();
            $entityManager = $doctrine->getEntityManager();

            $accountObj = $entityManager->getRepository(Account::class)->findBy(array(
                'email' => $account->getEmail()
            ), array(
                'id' => 'ASC'
            ), 1);

            if (empty($accountObj)) {
                return array('status' => 401, 'message' => "ERROR", 'result' => 'Usuário não localizado!');
            } elseif(!$accountObj[0]->getActive()) {
                return array('status' => 401, 'message' => "ERROR", 'result' => 'Conta desativada!');
            } else {

                $newPass = strtoupper(uniqid());
                $newPass = str_split($newPass, 6);

                $accountObj[0]->setPass(strtoupper(sha1($newPass[0])));

                $entityManager->flush();
                $email = new Email('');
                $email->newPass($accountObj[0], true, $newPass[0]);

                return array(
                    'status' => 200,
                    'message' => "SUCCESS",
                    'result' => 'Senha alterada!'
                );
            }

        } catch (\Doctrine\ORM\ORMException $ex) {
            return array(
                'status'    => 500,
                'message'   => "ERROR",
                'result'    => 'Erro na execução da instrução!',
                'CODE'      => $ex->getCode(),
                'Exception' => $ex->getMessage(),
            );
        }
    }

    public function changePass(Account $account, $old, $new) {

        try {

            $doctrine = new Doctrine();
            $entityManager = $doctrine->getEntityManager();

            $accountObj = $entityManager->find(Account::class, $account->getId());

            if (strtoupper(sha1($old)) !== $accountObj->getPass())
                return array('status' => 400, 'message' => "ERROR", 'result' => 'A senha informada não corresponde com a registrada!');

            $accountObj->setPass(strtoupper(sha1($new)));

            $entityManager->flush();

            return array(
                'status'    => 200,
                'message'   => "SUCCESS",
                'result'    => 'Senha alterada!',
            );

        } catch (\Doctrine\ORM\ORMException $ex) {
            return array(
                'status'    => 500,
                'message'   => "ERROR",
                'result'    => 'Erro na execução da instrução!',
                'CODE'      => $ex->getCode(),
                'Exception' => $ex->getMessage(),
            );
        }
    }

    public function getUserData(Account $account) {

        try {

            $doctrine = new Doctrine();
            $entityManager = $doctrine->getEntityManager();

            switch ($account->getGroupId()){
                case Account::GROUP_ADMIN:
                    $obj = $entityManager->getRepository(Admin::class)->findBy(array(
                        'account'  => $account->getId(),
                    ), array(
                        'id' => 'ASC'
                    ), 1);

                    break;
                case Account::GROUP_EVALUATOR:
                    $obj = $entityManager->getRepository(Evaluator::class)->findBy(array(
                        'account'  => $account->getId(),
                    ), array(
                        'id' => 'ASC'
                    ), 1);

                    break;
                case Account::GROUP_COMPANY_USER:
                    $obj = $entityManager->getRepository(CompanyUser::class)->findBy(array(
                        'account'  => $account->getId(),
                    ), array(
                        'id' => 'ASC'
                    ), 1);

                    break;
                default:
                    return array(
                        'status' => 400,
                        'message' => 'Grupo não informado!'
                    );
            }

            $entityManager->getConnection()->close();

            return array(
                'status' => 200,
                'message' => "SUCCESS",
                'user' => $obj[0]->convertArray()
            );

        } catch (\Doctrine\ORM\ORMException $ex) {
            return array(
                'status'    => 500,
                'message'   => "ERROR",
                'result'    => 'Erro na execução da instrução!',
                'CODE'      => $ex->getCode(),
                'Exception' => $ex->getMessage(),
            );
        }
    }

    public function getProfile(Account $account) {

        try {

            $doctrine = new Doctrine();
            $entityManager = $doctrine->getEntityManager();

            $accountObj = $entityManager->getRepository(Account::class)->findBy(array(
                'id'  => $account->getId(),
            ), array(
                'id' => 'ASC'
            ), 1);

            if (empty($accountObj)) {
                return array('status' => 401, 'message' => "ERROR", 'result' => 'Usuário não existe!');
            }else{
                return $this->getUserData($accountObj[0]);
            }

        } catch (\Doctrine\ORM\ORMException $ex) {
            return array(
                'status'    => 500,
                'message'   => "ERROR",
                'result'    => 'Erro na execução da instrução!',
                'CODE'      => $ex->getCode(),
                'Exception' => $ex->getMessage(),
            );
        }
    }
}