<?php

namespace App\Controller;

use App\Basics\Account;
use App\Basics\AuditedAreas;
use App\Basics\ControlVisualizationDimensions;
use App\Basics\Diary;
use App\Basics\Evaluation;
use App\Basics\Meeting;
use App\Basics\Resume;
use App\Basics\RN440\RN440;
use App\DAO\EvaluationDAO;
use App\DAO\RN440DAO;
use App\DAO\RN452DAO;

class EvaluationController
{
    public function listForType(Account $account, $userId, $typeEvaluation, $type, $classification): array
    {
        switch ($typeEvaluation){
            case Evaluation::TYPE_RN_452:
                return $this->listRn452($account, $userId, $type, $classification);
            case Evaluation::TYPE_RN_440:
                return $this->listRn440($account, $userId, $type, $classification);
            default:
                return array(
                    'status' => 400,
                    'message' => 'Grupo não informado!'
                );
        }
    }

    public function listRn452(Account $account, $userId, $type, $classification): array
    {

        $RN452DAO = new RN452DAO();

        switch ($account->getGroupId()){
            case Account::GROUP_ADMIN:
                return $RN452DAO->listForAdmin($type, $classification);

            case Account::GROUP_EVALUATOR:
                return $RN452DAO->listForEvaluator($userId, $type, $classification);

            case Account::GROUP_COMPANY_USER:
                return $RN452DAO->listForCompanyUser($userId, $type, $classification);

            default:
                return array(
                    'status' => 400,
                    'message' => 'Grupo não informado!'
                );
        }
    }

    public function listRn440(Account $account, $userId, $type, $classification = RN440::CLASSIFICATION_APS): array
    {

        $RN440DAO = new RN440DAO();

        switch ($account->getGroupId()){
            case Account::GROUP_ADMIN:
                return $RN440DAO->listForAdmin($type, $classification);

            case Account::GROUP_EVALUATOR:
                return $RN440DAO->listForEvaluator($userId, $type, $classification);

            case Account::GROUP_COMPANY_USER:
                return $RN440DAO->listForCompanyUser($userId, $type, $classification);

            default:
                return array(
                    'status' => 400,
                    'message' => 'Grupo não informado!'
                );
        }
    }

    public function report(Account $account, $userId, Evaluation $evaluation, $isPreview = false)
    {
        $evaluationDAO = new EvaluationDAO();
        return $evaluationDAO->prepareReport($account, $userId, $evaluation, $isPreview);
    }

    public function listResume(Evaluation $evaluation)
    {

        if (is_null($evaluation->getId())) {
            return array('status' => 400, 'message' => "ERROR", 'result' => 'ID da acreditação não informado!');
        }

        $evaluationDAO = new EvaluationDAO();
        return $evaluationDAO->listResume($evaluation);
    }

    public function updateResume(Resume $resume)
    {

        if (is_null($resume->getId())) {
            return array('status' => 400, 'message' => "ERROR", 'result' => 'ID não informado!');
        }

        $evaluationDAO = new EvaluationDAO();
        return $evaluationDAO->updateResume($resume);
    }

    public function updateMeeting(Meeting $meeting, $participants)
    {

        if (is_null($meeting->getId())) {
            return array('status' => 400, 'message' => "ERROR", 'result' => 'ID não informado!');
        }

        if (!is_array($participants)) {
            return array('status' => 400, 'message' => "ERROR", 'result' => 'Participantes não é um array');
        }

        $evaluationDAO = new EvaluationDAO();
        return $evaluationDAO->updateMeeting($meeting, $participants);
    }

    public function dateStart(Evaluation $evaluation, $dateStart, $dateEnd, $isValidity = false)
    {

        if (is_null($evaluation->getId())) {
            return array('status' => 400, 'message' => "ERROR", 'result' => 'ID não informado!');
        }

        if (is_null($dateStart) && is_null($dateEnd)) {
            return array('status' => 400, 'message' => "ERROR", 'result' => 'Informe ao menos uma data!');
        }

        $start = $dateStart ? \DateTime::createFromFormat('Y-m-d', $dateStart) : null;
        $end = $dateEnd ? \DateTime::createFromFormat('Y-m-d', $dateEnd) : null;

        $evaluationDAO = new EvaluationDAO();
        return $evaluationDAO->dateStart($evaluation, $start, $end, $isValidity);
    }

    public function companyFields(Evaluation $evaluation)
    {

        if (is_null($evaluation->getId())) {
            return array('status' => 400, 'message' => "ERROR", 'result' => 'ID não informado!');
        }

        if (is_null($evaluation->getCompanyPort())) {
            return array('status' => 400, 'message' => "ERROR", 'result' => 'Porte não informado!');
        }

        if (is_null($evaluation->getCompanyIdss())) {
            return array('status' => 400, 'message' => "ERROR", 'result' => 'IDSS não informado!');
        }

        if (is_null($evaluation->getCompanyNumberOfEmployees())) {
            return array('status' => 400, 'message' => "ERROR", 'result' => 'Numero de funcionárops não informado!');
        }

        if (is_null($evaluation->getCompanyNumberOfBeneficiaries())) {
            return array('status' => 400, 'message' => "ERROR", 'result' => 'Número de beneficiários não informado!');
        }

        $evaluationDAO = new EvaluationDAO();
        return $evaluationDAO->companyFields($evaluation);
    }

    public function updatePeopleInterviewed($evaluationId, $people)
    {

        if (is_null($evaluationId)) {
            return array('status' => 400, 'message' => "ERROR", 'result' => 'ID da acreditação não informado!');
        }

        if (!is_array($people)) {
            return array('status' => 400, 'message' => "ERROR", 'result' => 'Pessoas entrevistadas não é um array');
        }

        $evaluationDAO = new EvaluationDAO();
        return $evaluationDAO->updatePeopleInterviewed($evaluationId, $people);
    }

    public function createAuditedAreas(AuditedAreas $auditedAreas)
    {

        if (is_null($auditedAreas->getEvaluation()))
            return array('status' => 400, 'message' => "ERROR", 'result' => 'ID da acreditação não informado!');

        if (is_null($auditedAreas->getDimension()))
            return array('status' => 400, 'message' => "ERROR", 'result' => 'Dimensão não informado!');

        if (is_null($auditedAreas->getName()))
            return array('status' => 400, 'message' => "ERROR", 'result' => 'Nome não informado!');

        $evaluationDAO = new EvaluationDAO();
        return $evaluationDAO->createAuditedAreas($auditedAreas);
    }

    public function removeAuditedAreas(AuditedAreas $auditedAreas)
    {

        if (is_null($auditedAreas->getId()))
            return array('status' => 400, 'message' => "ERROR", 'result' => 'ID não informado!');

        $evaluationDAO = new EvaluationDAO();
        return $evaluationDAO->removeAuditedAreas($auditedAreas);

    }

    public function updateDiary(Diary $diary, $type, $replicate, $evaluators)
    {

        if (is_null($type)) {
            return array('status' => 400, 'message' => "ERROR", 'result' => 'Tipo não informada');
        }

        if (is_null($diary->getPublicId())) {
            return array('status' => 400, 'message' => "ERROR", 'result' => 'PublicId não informada');
        }

        if ($type !== 'delete') {
            if (is_null($diary->getEvaluation())) {
                return array('status' => 400, 'message' => "ERROR", 'result' => 'ID da acreditação não informado!');
            }

            if (is_null($diary->getStartDate())) {
                return array('status' => 400, 'message' => "ERROR", 'result' => 'Data inicial não informada');
            }

            if (is_null($diary->getEndDate())) {
                return array('status' => 400, 'message' => "ERROR", 'result' => 'Data final não informada');
            }

            if (is_null($diary->getTitle())) {
                return array('status' => 400, 'message' => "ERROR", 'result' => 'Título não informada');
            }

            if (is_null($diary->getEvaluator())) {
                return array('status' => 400, 'message' => "ERROR", 'result' => 'Avaliador não informada');
            }
        }

        $evaluationDAO = new EvaluationDAO();
        return $evaluationDAO->updateDiary($diary, $type, $replicate, $evaluators);
    }

    public function updateEvaluators(Evaluation $evaluation)
    {

        if (is_null($evaluation->getId())) {
            return array('status' => 400, 'message' => "ERROR", 'result' => 'ID da acreditação não informado');
        }

        if (is_null($evaluation->getLeaderApproval())) {
            return array('status' => 400, 'message' => "ERROR", 'result' => 'Líder não informado');
        }

        if (is_null($evaluation->getEvaluator())) {
            return array('status' => 400, 'message' => "ERROR", 'result' => 'Avaliadores não informados');
        }

        if (!is_array($evaluation->getEvaluator())) {
            return array('status' => 400, 'message' => "ERROR", 'result' => 'Avaliadores não é um array');
        }

        if (count($evaluation->getEvaluator()) === 0) {
            return array('status' => 400, 'message' => "ERROR", 'result' => 'Informe ao menos 1 avaliador');
        }

        $evaluationDAO = new EvaluationDAO();
        return $evaluationDAO->updateEvaluators($evaluation);
    }

    public function updateControlVisualization(ControlVisualizationDimensions $controlVisualizationDimensions, $type)
    {

        if (is_null($controlVisualizationDimensions->getEvaluation())) {
            return array('status' => 400, 'message' => "ERROR", 'result' => 'ID da avaliação não informado!');
        }

        if (is_null($controlVisualizationDimensions->getEvaluator())) {
            return array('status' => 400, 'message' => "ERROR", 'result' => 'Avaliador não informada');
        }

        if (is_null($controlVisualizationDimensions->getDimension()) &&
            is_null($controlVisualizationDimensions->getRequirement())) {
            return array('status' => 400, 'message' => "ERROR", 'result' => 'Informe a dimensão ou o requisito');
        }

        if (is_null($type)) {
            return array('status' => 400, 'message' => "ERROR", 'result' => 'Tipo não informada');
        }

        $evaluationDAO = new EvaluationDAO();
        return $evaluationDAO->updateControlVisualization($controlVisualizationDimensions, $type);
    }

    public function populate(Evaluation $evaluation)
    {
        if (is_null($evaluation->getId())) {
            return [
                'status' => 400,
                'message' => "ERROR",
                'result' => 'ID não informado!',
            ];
        }

        $evaluationDAO = new EvaluationDAO();
        return $evaluationDAO->populate($evaluation);
    }

    public function deleteEvaluation(Evaluation $evaluation, Account $account)
    {
        if (is_null($evaluation->getId())) {
            return [
                'status' => 400,
                'message' => "ERROR",
                'result' => 'ID não informado!',
            ];
        }

        if (is_null($account->getPass())) {
            return [
                'status' => 400,
                'message' => "ERROR",
                'result' => 'Senha não informada!',
            ];
        }

        $evaluationDAO = new EvaluationDAO();
        return $evaluationDAO->deleteEvaluation($evaluation, $account);
    }

    public function submitAnalysis(Evaluation $evaluation, Account $account, $userId)
    {
        if (is_null($evaluation->getId())) {
            return [
                'status' => 400,
                'message' => "ERROR",
                'result' => 'ID não informado!',
            ];
        }

        $evaluationDAO = new EvaluationDAO();
        return $evaluationDAO->submitAnalysis($evaluation, $account, $userId);
    }

    public function submitFinishedSupervision(Evaluation $evaluation, Account $account, $userId)
    {
        if (is_null($evaluation->getId())) {
            return [
                'status' => 400,
                'message' => "ERROR",
                'result' => 'ID não informado!',
            ];
        }

        $evaluationDAO = new EvaluationDAO();
        return $evaluationDAO->submitFinishedSupervision($evaluation, $account, $userId);
    }

    public function submitFinished(Evaluation $evaluation, Account $account, $userId)
    {
        if (is_null($evaluation->getId())) {
            return [
                'status' => 400,
                'message' => "ERROR",
                'result' => 'ID não informado!',
            ];
        }

        $evaluationDAO = new EvaluationDAO();
        return $evaluationDAO->submitFinished($evaluation, $account, $userId);
    }

    public function submitFeedback(Evaluation $evaluation)
    {
        if (is_null($evaluation->getId())) {
            return [
                'status' => 400,
                'message' => "ERROR",
                'result' => 'ID não informado!',
            ];
        }

        $evaluationDAO = new EvaluationDAO();
        return $evaluationDAO->submitFeedback($evaluation);
    }


}