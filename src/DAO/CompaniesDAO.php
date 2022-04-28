<?php

namespace App\DAO;
use App\Basics\Company;
use App\Utils\WorkOut;
use App\Config\Doctrine;

class CompaniesDAO
{
    public function findAll()
    {
        try {
            $doctrine = new Doctrine();
            $entityManager = $doctrine->getEntityManager();

            $obj = $entityManager
                ->getRepository(Company::class)
                ->findBy([], ['name' => 'ASC']);

            if (empty($obj)) {
                return [
                    'status' => 200,
                    'message' => "SUCCESS",
                    'qtd' => 0,
                    'result' => [],
                ];
            } else {
                $workOut = new WorkOut();
                $companies = [
                    'active' => [],
                    'inactive' => [],
                ];
                foreach ($obj as $companiesItem) {
                    $companyTemp = $companiesItem->convertArray();
                    $companyTemp['cnpj'] = $workOut->mask($companyTemp['cnpj'], '##.###.###/####-##');
                    if ($companiesItem->getActive()) {
                        array_push($companies['active'], $companyTemp);
                    } else {
                        array_push($companies['inactive'], $companyTemp);
                    }
                }

                return [
                    'status' => 200,
                    'message' => "SUCCESS",
                    'qtd' => count($obj),
                    'result' => $companies,
                ];
            }
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

    public function validateBeforePersist(Company $company, $isEdit = false, $oldCnpj = false)
    {
        try {

            $workOut = new WorkOut();
            $checkCnpj = $workOut->checkCNPJ($company->getCnpj(), $isEdit, $oldCnpj);
            if ($checkCnpj['status'] !== 200) return $checkCnpj;

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

    public function insert(Company $company)
    {
        try {
            $doctrine = new Doctrine();
            $entityManager = $doctrine->getEntityManager();

            $validate = $this->validateBeforePersist($company);
            if ($validate['status'] !== 200) return $validate;

            $entityManager->persist($company);
            $entityManager->flush();

            return [
                'status' => 200,
                'message' => "SUCCESS",
                'result' => 'Empresa cadastrada!',
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

    public function update(Company $company)
    {
        try {
            $doctrine = new Doctrine();
            $entityManager = $doctrine->getEntityManager();

            $obj = $entityManager->find(Company::class, $company->getId());

            $validate = $this->validateBeforePersist($company, true, $obj->getCnpj());
            if ($validate['status'] !== 200) return $validate;

            $obj->setName($company->getName());
            $obj->setCnpj($company->getCnpj());
            $obj->setAnsRecord($company->getAnsRecord());
            $obj->setSegmentation($company->getSegmentation());
            $obj->setContactPerson($company->getContactPerson());
            $obj->setAddress($company->getAddress());
            $obj->setEmail($company->getEmail());
            $obj->setPhone($company->getPhone());

            $obj->setPort($company->getPort());
            $obj->setNumberOfEmployees($company->getNumberOfEmployees());
            $obj->setNumberOfBeneficiaries($company->getNumberOfBeneficiaries());
            $obj->setIdss($company->getIdss());

            if ($company->getImage()) {
                $obj->setImage($company->getImage());
            }

            $entityManager->flush();

            return [
                'status' => 200,
                'message' => "SUCCESS",
                'result' => 'Empresa atualizada!',
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

    public function enabled(Company $company)
    {
        try {
            $doctrine = new Doctrine();
            $entityManager = $doctrine->getEntityManager();

            $text = $company->getActive() ? 'ativada' : 'desativada';

            $obj = $entityManager->find(Company::class, $company->getId());

            $obj->setActive($company->getActive());

            $entityManager->flush();

            return [
                'status' => 200,
                'message' => "SUCCESS",
                'result' => 'Empresa ' . $text . '!',
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