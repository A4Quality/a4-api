<?php

namespace App\DAO;
use App\Basics\Account;
use App\Basics\Evaluator;
use App\Utils\WorkOut;
use App\Service\Email;
use App\Config\Doctrine;

class EvaluatorsDAO
{
    public function findAll()
    {
        try {
            $doctrine = new Doctrine();
            $entityManager = $doctrine->getEntityManager();

            $obj = $entityManager
                ->getRepository(Evaluator::class)
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

    public function validateBeforePersist(Evaluator $evaluator, Account $account, $isEdit = false, $oldCpf = null, $oldEmail = null)
    {
        try {
            $authDAO = new AuthDAO();
            $checkEmail = $authDAO->checkEmail($account, $isEdit, $oldEmail);
            if ($checkEmail['status'] !== 200) return $checkEmail;

            $workOut = new WorkOut();
            $checkCpf = $workOut->checkCPF($evaluator->getCpf(), Account::GROUP_EVALUATOR, $isEdit, $oldCpf);
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

    public function insert(Evaluator $evaluator, Account $account)
    {
        try {

            $doctrine = new Doctrine();
            $entityManager = $doctrine->getEntityManager();

            $validate = $this->validateBeforePersist($evaluator, $account);
            if ($validate['status'] !== 200) return $validate;

            $newPass = $account->getPass();
            $account->setPass(strtoupper(sha1($newPass)));

            $entityManager->persist($account);

            $evaluator->setAccount($account);

            $entityManager->persist($evaluator);
            $entityManager->flush();

            $email = new Email('');
            $email->newPass($account, false, $newPass);

            return [
                'status' => 200,
                'message' => "SUCCESS",
                'result' => 'Avaliador cadastrado!',
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

    public function update(Evaluator $evaluator, Account $account)
    {
        try {
            $doctrine = new Doctrine();
            $entityManager = $doctrine->getEntityManager();

            $accountObj = $entityManager->find(Account::class, $account->getId());
            $evaluatorObj = $entityManager->find(
                Evaluator::class,
                $evaluator->getId()
            );

            $validate = $this->validateBeforePersist($evaluator, $account, true, $evaluatorObj->getCpf(), $accountObj->getEmail());
            if ($validate['status'] !== 200) return $validate;

            $accountObj->setEmail($account->getEmail());
            $evaluatorObj->setName($evaluator->getName());
            $evaluatorObj->setPhone($evaluator->getPhone());
            $evaluatorObj->setCpf($evaluator->getCpf());
            $evaluatorObj->setUniversityGraduate($evaluator->getUniversityGraduate());
            $evaluatorObj->setPostGraduate($evaluator->getPostGraduate());

            $evaluatorObj->setMinimumExperienceInBusinessAudit($evaluator->getMinimumExperienceInBusinessAudit());
            $evaluatorObj->setMinimumExperienceInControllership($evaluator->getMinimumExperienceInControllership());
            $evaluatorObj->setMinimumExperienceInHealthAccreditation($evaluator->getMinimumExperienceInHealthAccreditation());
            $evaluatorObj->setMinimumExperienceInHealthAaudit($evaluator->getMinimumExperienceInHealthAaudit());

            if ($evaluator->getSubscription()) {
                $evaluatorObj->setSubscription($evaluator->getSubscription());
            }

            $entityManager->flush();

            return [
                'status' => 200,
                'message' => "SUCCESS",
                'result' => 'Avaliador atualizado!',
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
                'result' => 'Avaliador ' . $text . '!',
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

    public function confirmTerm(Evaluator $evaluator)
    {
        try {
            $doctrine = new Doctrine();
            $entityManager = $doctrine->getEntityManager();

            $evaluatorObj = $entityManager->find(
                Evaluator::class,
                $evaluator->getId()
            );

            $evaluatorObj->setStatementOfResponsibility(new \DateTime());
            $entityManager->flush();

            return [
                'status' => 200,
                'message' => "SUCCESS",
                'result' => 'Termo de responsabilidade confirmado',
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