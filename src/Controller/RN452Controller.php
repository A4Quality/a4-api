<?php

namespace App\Controller;

use App\Basics\Evaluation;
use App\Basics\RN452\RN452;
use App\Basics\RN452\RN452MonitoredIndicators;
use App\Basics\RN452\RN452Prerequisites;
use App\Basics\RN452\RN452RequirementsItems;
use App\DAO\RN452DAO;
use App\Utils\WorkOut;

class RN452Controller
{
    public function insert(Evaluation $evaluation, RN452 $RN452, $dimensions)
    {
        $result = $this->validateRN452($evaluation, $RN452);
        if ($result['status'] !== 200) return $result;

        if ($RN452->getType() === RN452::TYPE_SUPERVISION) {
            if (is_null($dimensions)) {
                return array('status' => 400, 'message' => "ERROR", 'result' => 'Dimensões não informadas!');
            }
        }

        $RN452DAO = new RN452DAO();
        return $RN452DAO->insert($evaluation, $RN452, $dimensions);
    }

    public function validateRN452(Evaluation $evaluation, RN452 $RN452, $isInsert = true){
        if (!$isInsert && is_null($evaluation->getId())) {
            return [
                'status' => 400,
                'message' => "ERROR",
                'result' => 'ID não informado!',
            ];
        }
        else if (is_null($evaluation->getCompany())) {
            return [
                'status' => 400,
                'message' => "ERROR",
                'result' => 'Cliente não informado!',
            ];
        }else if (is_null($evaluation->getLeaderApproval())) {
            return [
                'status' => 400,
                'message' => "ERROR",
                'result' => 'Líder não informado!',
            ];
        }


        else if ($RN452->getType() !== RN452::TYPE_SELF_EVALUATION && is_null($evaluation->getEvaluator())) {
            return [
                'status' => 400,
                'message' => "ERROR",
                'result' => 'Avaliadores não informados!',
            ];
        }
        else if ($RN452->getType() === RN452::TYPE_ACCREDITATION && is_null($evaluation->getAnalysisUser())) {
            return [
                'status' => 400,
                'message' => "ERROR",
                'result' => 'Usuário de análise e decisão não informados!',
            ];
        }
        else if ($RN452->getType() !== RN452::TYPE_SELF_EVALUATION && in_array('d_'.$evaluation->getAnalysisUser(), $evaluation->getEvaluator())) {
            return [
                'status' => 400,
                'message' => "ERROR",
                'result' => 'A diretoria que realizará a análise e decisão não deverá participar da avaliação inicial.',
            ];
        }
        else{
            return array('status' => 200);
        }
    }

    public function listRequirements(RN452 $RN452, $group)
    {

        if (is_null($RN452->getId())) {
            return array('status' => 400, 'message' => "ERROR", 'result' => 'ID da acreditao não informado!');
        }
        else if (is_null($group)) {
            return array('status' => 400, 'message' => "ERROR", 'result' => 'Grupo não informado!');
        }

        $RN452DAO = new RN452DAO();
        $result = $RN452DAO->listRequirements($RN452);

        switch ($result['type']) {
            case RN452::TYPE_SUPERVISION:
                $typeChose = RN452::TYPE_ACCREDITATION;
                break;
            case RN452::TYPE_ACCREDITATION:
                $typeChose = RN452::TYPE_PRE;
                break;
            default:
                $typeChose = $result['type'];
                break;
        }

        $evaluation = new Evaluation(Evaluation::TYPE_RN_452);
        $evaluation->setCompany($result['company']);
        $evaluation->setCreatedDate($result['createdDate']);
        $RN452->setEvaluation($evaluation);

        $lastRN452 = new RN452();
        $lastRN452->setId($RN452DAO->reportIdLastEvaluation($RN452, $typeChose));

        if ($lastRN452->getId() === null) {
            $result['result']['lastEvaluation'] = null;
            return $result;
        }

        $listRequirements = $RN452DAO->listRequirements($lastRN452);
        $result['result']['lastEvaluation'] = $listRequirements['result'];
        return $result;

    }

    public function updateMonitoredIndicators(RN452MonitoredIndicators $monitoredIndicators, $groupId, $userId)
    {
        if (is_null($monitoredIndicators->getId())) {
            return [
                'status' => 400,
                'message' => "ERROR",
                'result' => 'ID não informado!',
            ];
        }

        $RN452DAO = new RN452DAO();
        return $RN452DAO->updateMonitoredIndicators($monitoredIndicators, $groupId, $userId);
    }

    public function updateRequirementsItems(RN452RequirementsItems $RN452RequirementsItems, $groupId, $userId, $type)
    {
        if (is_null($RN452RequirementsItems->getId())) {
            return [
                'status' => 400,
                'message' => "ERROR",
                'result' => 'ID não informado!',
            ];
        }
        else if (is_null($type)) {
            return [
                'status' => 400,
                'message' => "ERROR",
                'result' => 'Tipo não informado',
            ];
        }

        $RN452DAO = new RN452DAO();
        return $RN452DAO->updateRequirementsItems($RN452RequirementsItems, $groupId, $userId, $type);
    }

    public function updateRequirementsComments(RN452RequirementsItems $RN452RequirementsItems, $files, $groupId, $userId)
    {
        if (is_null($RN452RequirementsItems->getId())) {
            return [
                'status' => 400,
                'message' => "ERROR",
                'result' => 'ID não informado!',
            ];
        }
        else if (
            is_null($RN452RequirementsItems->getComment()) &&
            is_null($RN452RequirementsItems->getEvidence()) &&
            is_null($RN452RequirementsItems->getFeedback()) &&
            is_null($RN452RequirementsItems->getChangedPoint()) &&
            is_null($RN452RequirementsItems->getImprovementOpportunity()) &&
            is_null($RN452RequirementsItems->getStrongPoint()) &&
            is_null($RN452RequirementsItems->getNonAttendance())) {
            return [
                'status' => 400,
                'message' => "ERROR",
                'result' => 'Informe ao menos algum comentário ou evidência.',
            ];
        }

        $workOut = new WorkOut();
        $filesNames = [];
        foreach ($files as $file){
            $fileName = $workOut->base64_to_file($file['base64'], $file['name'], $file['ext'], 'comments');
            array_push($filesNames, $fileName);
        }

        $RN452DAO = new RN452DAO();
        return $RN452DAO->updateRequirementsComments($RN452RequirementsItems, $filesNames, $groupId, $userId);
    }

    public function deleteFile($id)
    {
        if (is_null($id)) {
            return [
                'status' => 400,
                'message' => "ERROR",
                'result' => 'Nenhum ID não informado!',
            ];
        }

        $RN452DAO = new RN452DAO();
        return $RN452DAO->deleteFile($id);
    }

    public function listPrerequisites(RN452 $RN452)
    {

        if (is_null($RN452->getId())) {
            return array('status' => 400, 'message' => "ERROR", 'result' => 'ID da acreditação não informado!');
        }

        $RN452DAO = new RN452DAO();
        return $RN452DAO->listPrerequisites($RN452);
    }

    public function updatePrerequisites(RN452Prerequisites $evaluationPrerequisites)
    {

        if (is_null($evaluationPrerequisites->getId())) {
            return array('status' => 400, 'message' => "ERROR", 'result' => 'ID não informado!');
        }
        else if (is_null($evaluationPrerequisites->getItHas())) {
            return array('status' => 400, 'message' => "ERROR", 'result' => 'ItHas não informado!');
        }

        $RN452DAO = new RN452DAO();
        return $RN452DAO->updatePrerequisites($evaluationPrerequisites);
    }

}