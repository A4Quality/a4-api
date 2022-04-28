<?php

namespace App\Controller;

use App\Basics\Company;
use App\DAO\CompaniesDAO;
use App\Utils\WorkOut;

class CompaniesController
{
    public function findAll(){
        $companiesDAO = new CompaniesDAO();
        return $companiesDAO->findAll();
    }

    public function validateCompany(Company $company, $isInsert = true){
        if (!$isInsert && is_null($company->getId())) {
            return [
                'status' => 400,
                'message' => "ERROR",
                'result' => 'ID não informado!',
            ];
        }
        else if (is_null($company->getName())) {
            return [
                'status' => 400,
                'message' => "ERROR",
                'result' => 'Nome não informado!',
            ];
        }
        else if (is_null($company->getCnpj())) {
            return [
                'status' => 400,
                'message' => "ERROR",
                'result' => 'CNPJ não informado!',
            ];
        }
        else if (is_null($company->getAnsRecord())) {
            return [
                'status' => 400,
                'message' => "ERROR",
                'result' => 'Registro ANS não informado!',
            ];
        }
        else if (is_null($company->getSegmentation())) {
            return [
                'status' => 400,
                'message' => "ERROR",
                'result' => 'Segmentação não informada!',
            ];
        }
        else if (is_null($company->getContactPerson())) {
            return [
                'status' => 400,
                'message' => "ERROR",
                'result' => 'Pessoa de contato não informada!',
            ];
        }
        else if (is_null($company->getAddress())) {
            return [
                'status' => 400,
                'message' => "ERROR",
                'result' => 'Endereço não informado!',
            ];
        }
        else if (is_null($company->getEmail())) {
            return [
                'status' => 400,
                'message' => "ERROR",
                'result' => 'E-mail não informado!',
            ];
        }
        else if (is_null($company->getPhone())) {
            return [
                'status' => 400,
                'message' => "ERROR",
                'result' => 'Telefone não informado!',
            ];
        }
        else if (is_null($company->getPort())) {
            return [
                'status' => 400,
                'message' => "ERROR",
                'result' => 'Porte não informado!',
            ];
        }
        else if (is_null($company->getNumberOfEmployees())) {
            return [
                'status' => 400,
                'message' => "ERROR",
                'result' => 'Número de colaboradores não informado!',
            ];
        }
        else if (is_null($company->getNumberOfBeneficiaries())) {
            return [
                'status' => 400,
                'message' => "ERROR",
                'result' => 'Número de beneficiários não informado!',
            ];
        }
        else if (is_null($company->getIdss())) {
            return [
                'status' => 400,
                'message' => "ERROR",
                'result' => 'IDSS não informado!',
            ];
        }
    else{
            return array('status' => 200);
        }
    }

    public function insert(Company $company)
    {
        if (is_null($company->getImage())) {
            return [
                'status' => 400,
                'message' => "ERROR",
                'result' => 'Foto não informada!',
            ];
        }
        
        $result = $this->validateCompany($company);
        if ($result['status'] !== 200) return $result;

        $workOut = new WorkOut();
        $company->setCnpj($workOut->removeMask($company->getCnpj(), 'cnpj'));

        $companiesDAO = new CompaniesDAO();
        return $companiesDAO->insert($company);
    }

    public function update(Company $company)
    {
        $result = $this->validateCompany($company, false);
        if ($result['status'] !== 200) return $result;

        $workOut = new WorkOut();
        $company->setCnpj($workOut->removeMask($company->getCnpj(), 'cnpj'));

        $companiesDAO = new CompaniesDAO();
        return $companiesDAO->update($company);
    }

    public function enabled(Company $company)
    {
        if (is_null($company->getId())) {
            return [
                'status' => 400,
                'message' => "ERROR",
                'result' => 'Id não informado!',
            ];
        }

        if (is_null($company->getActive())) {
            return [
                'status' => 400,
                'message' => "ERROR",
                'result' => 'Status da conta não informado!',
            ];
        }

        if (is_string($company->getActive())) {
            $company->setActive($company->getActive() === 'true');
        }

        $companiesDAO = new CompaniesDAO();
        return $companiesDAO->enabled($company);
    }
}