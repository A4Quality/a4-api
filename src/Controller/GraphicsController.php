<?php

namespace App\Controller;

use App\Basics\Accreditation;
use App\Basics\CompanyUser;
use App\Basics\Evaluation;
use App\Basics\RN452\RN452;
use App\DAO\GraphicsDAO;

class GraphicsController
{
    public function onlyDimension($typeEvaluation, $classification, $type, CompanyUser $companyUser)
    {

        if (is_null($typeEvaluation)) {
            return array('status' => 400, 'message' => "ERROR", 'result' => 'Tipo da avaliação não informado!');
        }

        if (is_null($classification)) {
            return array('status' => 400, 'message' => "ERROR", 'result' => 'Classificação não informado!');
        }

        if (is_null($type)) {
            return array('status' => 400, 'message' => "ERROR", 'result' => 'Tipo não informado!');
        }

        $accreditationDAO = new GraphicsDAO();
        return $accreditationDAO->onlyDimension($typeEvaluation, $classification, $type, $companyUser);
    }

    public function home($typeEvaluation)
    {
        $graphicsDAO = new GraphicsDAO();

        switch ($typeEvaluation){
            case Evaluation::TYPE_RN_452:
                return $graphicsDAO->homeRN452();
            case Evaluation::TYPE_RN_440:
                return array(
                    'status' => 400,
                    'message' => 'Tipo em desenvolvimento!'
                );
            default:
                return array(
                    'status' => 400,
                    'message' => 'Grupo não informado!'
                );
        }
    }

    public function exportReport($companies, RN452 $RN452)
    {

        if (!is_array($companies)) {
            return array('status' => 400, 'message' => "ERROR", 'result' => 'Você não informou a listagemd as empresas');
        }

        if (is_null($RN452->getType())) {
            return array('status' => 400, 'message' => "ERROR", 'result' => 'Tipo da acreditação não informado!');
        }

        $accreditationDAO = new GraphicsDAO();
        return $accreditationDAO->exportReport($companies, $RN452);
    }

    public function custom($companies, Accreditation $accreditation, $start, $end)
    {

        if (!is_array($companies)) {
            return array('status' => 400, 'message' => "ERROR", 'result' => 'Você não informou a listagemd as empresas');
        }

        if (is_null($accreditation->getType())) {
            return array('status' => 400, 'message' => "ERROR", 'result' => 'Tipo da acreditação não informado!');
        }

        if (is_null($start)) {
            return array('status' => 400, 'message' => "ERROR", 'result' => 'Data inicial do intervalo não informado!');
        }

        if (is_null($end)) {
            return array('status' => 400, 'message' => "ERROR", 'result' => 'Data final do intervalo não informado!');
        }

        $accreditationDAO = new GraphicsDAO();
        return $accreditationDAO->custom($companies, $accreditation->getType(), $start, $end);
    }


}