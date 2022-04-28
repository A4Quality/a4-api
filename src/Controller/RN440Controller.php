<?php

namespace App\Controller;

use App\Basics\Evaluation;
use App\Basics\RN440\RN440;
use App\Basics\RN440\RN440RequirementsItems;
use App\DAO\RN440DAO;
use App\Utils\WorkOut;

class RN440Controller
{
    public function insert(Evaluation $evaluation, RN440 $RN440, $requirements)
    {
        $result = $this->validateRN440($evaluation, $RN440);
        if ($result['status'] !== 200) return $result;

        if ($RN440->getType() === RN440::TYPE_SUPERVISION) {
            if (is_null($requirements)) {
                return array('status' => 400, 'message' => "ERROR", 'result' => 'Requisitos não informadas!');
            }
        }

        $RN402DAO = new RN440DAO();
        return $RN402DAO->insert($evaluation, $RN440, $requirements);
    }

    public function validateRN440(Evaluation $evaluation, RN440 $RN440, $isInsert = true){
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


        else if ($RN440->getType() !== RN440::TYPE_SELF_EVALUATION && is_null($evaluation->getEvaluator())) {
            return [
                'status' => 400,
                'message' => "ERROR",
                'result' => 'Avaliadores não informados!',
            ];
        }
        else if ($RN440->getType() === RN440::TYPE_CERTIFICATION && is_null($evaluation->getAnalysisUser())) {
            return [
                'status' => 400,
                'message' => "ERROR",
                'result' => 'Usuário de análise e decisão não informados!',
            ];
        }
        else if ($RN440->getType() !== RN440::TYPE_SELF_EVALUATION && in_array('d_'.$evaluation->getAnalysisUser(), $evaluation->getEvaluator())) {
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

    public function listRequirements(RN440 $RN440, $group)
    {

        if (is_null($RN440->getId())) {
            return array('status' => 400, 'message' => "ERROR", 'result' => 'ID da acreditao não informado!');
        }
        else if (is_null($group)) {
            return array('status' => 400, 'message' => "ERROR", 'result' => 'Grupo não informado!');
        }

        $RN440DAO = new RN440DAO();
        $result = $RN440DAO->listRequirements($RN440);
        if ($result['status'] === 200 && $result['type'] === RN440::TYPE_SUPERVISION) {

            $evaluation = new Evaluation(Evaluation::TYPE_RN_440);
            $evaluation->setCompany($result['company']);
            $evaluation->setCreatedDate($result['createdDate']);
            $RN440->setEvaluation($evaluation);

            $lastRN440 = new RN440();
            $lastRN440->setId($RN440DAO->reportSupervision($RN440, true));
            $listRequirements = $RN440DAO->listRequirements($lastRN440);
            $result['result']['lastEvaluation'] = $listRequirements['result'];
            return $result;
        } else {
            return $result;
        }
    }

    public function updateRequirementsItems(RN440RequirementsItems $RN440RequirementsItems, $groupId, $userId, $type)
    {
        if (is_null($RN440RequirementsItems->getId())) {
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

        $RN402DAO = new RN440DAO();
        return $RN402DAO->updateRequirementsItems($RN440RequirementsItems, $groupId, $userId, $type);
    }

    public function updateRequirementsComments(RN440RequirementsItems $RN440RequirementsItems, $files, $groupId, $userId)
    {
        if (is_null($RN440RequirementsItems->getId())) {
            return [
                'status' => 400,
                'message' => "ERROR",
                'result' => 'ID não informado!',
            ];
        }
        else if (
            is_null($RN440RequirementsItems->getComment()) &&
            is_null($RN440RequirementsItems->getEvidence()) &&
            is_null($RN440RequirementsItems->getFeedback()) &&
            is_null($RN440RequirementsItems->getChangedPoint()) &&
            is_null($RN440RequirementsItems->getImprovementOpportunity()) &&
            is_null($RN440RequirementsItems->getStrongPoint()) &&
            is_null($RN440RequirementsItems->getNonAttendance())) {
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

        $RN402DAO = new RN440DAO();
        return $RN402DAO->updateRequirementsComments($RN440RequirementsItems, $filesNames, $groupId, $userId);
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

        $RN402DAO = new RN440DAO();
        return $RN402DAO->deleteFile($id);
    }

}