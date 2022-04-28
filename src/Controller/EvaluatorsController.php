<?php

namespace App\Controller;

use App\Basics\Account;
use App\Basics\Evaluator;
use App\Utils\WorkOut;
use App\DAO\EvaluatorsDAO;

class EvaluatorsController
{
    public function findAll(){
        $evaluatorsDAO = new EvaluatorsDAO();
        return $evaluatorsDAO->findAll();
    }

    public function validateEvaluator(Evaluator $evaluator, Account $account, $isInsert = true){
        if (is_null($evaluator->getName())) {
            return [
                'status' => 400,
                'message' => "ERROR",
                'result' => 'Nome não informado!',
            ];
        }
        else if (is_null($evaluator->getPhone())) {
            return [
                'status' => 400,
                'message' => "ERROR",
                'result' => 'Telefone não informada!',
            ];
        }
        else if (is_null($evaluator->getCpf())) {
            return [
                'status' => 400,
                'message' => "ERROR",
                'result' => 'CPF não informada!',
            ];
        }
        else if (is_null($evaluator->getMinimumExperienceInHealthAaudit())) {
            return [
                'status' => 400,
                'message' => "ERROR",
                'result' => 'Experiência mínina não informada!',
            ];
        }
        else if (is_null($evaluator->getMinimumExperienceInHealthAccreditation())) {
            return [
                'status' => 400,
                'message' => "ERROR",
                'result' => 'Experiência mínina não informada!',
            ];
        }

        else if (is_null($evaluator->getMinimumExperienceInControllership())) {
            return [
                'status' => 400,
                'message' => "ERROR",
                'result' => 'Experiência mínina não informada!',
            ];
        }

        else if (is_null($evaluator->getMinimumExperienceInBusinessAudit())) {
            return [
                'status' => 400,
                'message' => "ERROR",
                'result' => 'Experiência mínina não informada!',
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

    public function insert(Evaluator $evaluator, Account $account)
    {

        if (is_null($evaluator->getSubscription())) {
            return [
                'status' => 400,
                'message' => "ERROR",
                'result' => 'Assinatura não informada!',
            ];
        }

        $result = $this->validateEvaluator($evaluator, $account);
        if ($result['status'] !== 200) return $result;

        $workOut = new WorkOut();
        $evaluator->setPhone($workOut->removeMask($evaluator->getPhone(), 'phone'));
        $evaluator->setCpf($workOut->removeMask($evaluator->getCpf(), 'cpf'));
        $evaluatorDAO = new EvaluatorsDAO();
        return $evaluatorDAO->insert($evaluator, $account);
    }

    public function update(Evaluator $evaluator, Account $account)
    {
        if (is_null($account->getId())) {
            return [
                'status' => 400,
                'message' => "ERROR",
                'result' => 'ID não informado!',
            ];
        }
        $result = $this->validateEvaluator($evaluator, $account, false);
        if ($result['status'] !== 200) return $result;

        $workOut = new WorkOut();
        $evaluator->setCpf($workOut->removeMask($evaluator->getCpf(), 'cpf'));
        $evaluator->setPhone($workOut->removeMask($evaluator->getPhone(), 'phone'));

        $evaluator->setMinimumExperienceInBusinessAudit($evaluator->getMinimumExperienceInBusinessAudit() === '1');
        $evaluator->setMinimumExperienceInControllership($evaluator->getMinimumExperienceInControllership() === '1');
        $evaluator->setMinimumExperienceInHealthAccreditation($evaluator->getMinimumExperienceInHealthAccreditation() === '1');
        $evaluator->setMinimumExperienceInHealthAaudit($evaluator->getMinimumExperienceInHealthAaudit() === '1');

        $evaluatorDAO = new EvaluatorsDAO();
        return $evaluatorDAO->update($evaluator, $account);
    }

    public function enabled(Account $account)
    {
        if (is_null($account->getId())) {
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

        $evaluatorDAO = new EvaluatorsDAO();
        return $evaluatorDAO->enabled($account);
    }

    public function confirmTerm(Evaluator $evaluator){
        $evaluatorsDAO = new EvaluatorsDAO();
        return $evaluatorsDAO->confirmTerm($evaluator);
    }

}