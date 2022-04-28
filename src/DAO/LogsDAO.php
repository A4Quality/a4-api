<?php

namespace App\DAO;
use App\Basics\Account;
use App\Basics\Admin;
use App\Basics\CompanyUser;
use App\Basics\Evaluator;
use App\Basics\Logs;
use App\Config\Doctrine;
use Doctrine\ORM\EntityManager;
use PDO;
use PDOException;

class LogsDAO
{

    private EntityManager $entityManager;

    /**
     * AccreditationDAO constructor.
     */
    public function __construct()
    {
        $doctrine = new Doctrine();
        $this->entityManager = $doctrine->getEntityManager();
    }

    /**
     * AccreditationDAO destruct.
     */
    public function __destruct()
    {
        $this->entityManager->getConnection()->close();
    }

    public function getLog(Logs $logs)
    {
        try {

            $obj = $this->entityManager
                ->getRepository(Logs::class)
                ->findBy([
                    'type' => $logs->getType(),
                    'idType' => $logs->getIdType()
                ], ['createdDate' => 'DESC']);


            $list = [];

            foreach ($obj as $log){
                $user = $this->getUser($log->getUserId(), $log->getGroupId());
                $logArray = $log->convertArray();
                $logArray['userDetails'] = $user;
                array_push($list, $logArray);
            }

            return [
                'status' => 200,
                'message' => "SUCCESS",
                'result' => $list
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

    public function getUser($userId, $groupId)
    {
        try {

            switch (intval($groupId)){
                case Account::GROUP_ADMIN:
                    $obj = $this->entityManager->getRepository(Admin::class)->findBy(array(
                        'id'  => $userId,
                    ), array(
                        'id' => 'ASC'
                    ), 1);

                    break;
                case Account::GROUP_EVALUATOR:
                    $obj = $this->entityManager->getRepository(Evaluator::class)->findBy(array(
                        'id'  => $userId,
                    ), array(
                        'id' => 'ASC'
                    ), 1);

                    break;
                case Account::GROUP_COMPANY_USER:
                    $obj = $this->entityManager->getRepository(CompanyUser::class)->findBy(array(
                        'id'  => $userId,
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

            return $obj[0]->convertArray();

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