<?php

namespace App\DAO;
use App\Basics\Account;
use App\Basics\Admin;
use App\Basics\Company;
use App\Basics\Evaluation;
use App\Basics\Resume;
use App\Basics\RN440\Lists\ListRN440Requirements;
use App\Basics\RN440\Lists\ListRN440RequirementsItems;
use App\Basics\RN440\RN440;
use App\Basics\Meeting;
use App\Basics\CompanyUser;
use App\Basics\ControlVisualizationDimensions;
use App\Basics\Diary;
use App\Basics\Evaluator;
use App\Basics\Logs;
use App\Basics\RN440\RN440RequirementsItems;
use App\Basics\RN440\RN440RequirementsItemsFiles;
use App\Config\Doctrine;
use App\Connection\Database;
use Doctrine\ORM\EntityManager;
use PDO;
use PDOException;

class RN440DAO
{

    private EntityManager $entityManager;

    /**
     * RN440DAO constructor.
     */
    public function __construct()
    {
        $doctrine = new Doctrine();
        $this->entityManager = $doctrine->getEntityManager();
    }

    /**
     * RN440DAO destruct.
     */
    public function __destruct()
    {
        $this->entityManager->getConnection()->close();
    }

    /// Listagem das avaliações
    public function listForAdmin($type, $classification = RN440::CLASSIFICATION_APS): array
    {
        try {

            $obj = $this->entityManager
                ->getRepository(RN440::class)
                ->findBy([
                    'type' => $type,
                    'classification' => $classification
                ], ['id' => 'ASC']);

            return $this->prepareList($obj);

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

    public function listForEvaluator($userId, $type, $classification = RN440::CLASSIFICATION_APS): array
    {
        try {

            $obj = $this->entityManager
                ->getRepository(RN440::class)
                ->findBy([
                    'type' => $type,
                    'classification' => $classification
                ], ['id' => 'ASC']);

            return $this->prepareList($obj, Account::GROUP_EVALUATOR, $userId);

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

    public function listForCompanyUser($userId, $type, $classification = RN440::CLASSIFICATION_APS): array
    {
        try {

            $companyUserObj = $this->entityManager->find(
                CompanyUser::class,
                $userId
            );

            $companyId = $companyUserObj->getCompany()->getId();

            $obj = $this->entityManager
                ->getRepository(RN440::class)
                ->findBy([
                    'type' => $type,
                    'classification' => $classification
                ], ['id' => 'ASC']);

            return $this->prepareList($obj, Account::GROUP_COMPANY_USER, $companyId);

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

    public function prepareList($obj, $group = Account::GROUP_ADMIN, $id = null): array
    {
        try {

            if (empty($obj)) {
                return [
                    'status' => 200,
                    'message' => "SUCCESS",
                    'qtd' => 0,
                    'result' => $group === Account::GROUP_COMPANY_USER ? [] : [
                        'open' => [],
                        'started' => [],
                        'analysisAndDecision' => [],
                        'feedback' => [],
                        'finished' => [],
                    ]
                ];
            } else {

                $temp = [];
                foreach ($obj as $item) {

                    $totalRequirementsTimeAndScope = sizeof($item->getRequirementsItems());
                    $totalRequirementsTimeAnswered = 0;
                    $totalRequirementsScopeAnswered = 0;

                    foreach ($item->getRequirementsItems() as $requirementsItem) {
                        if (!is_null($requirementsItem->getDeploymentTime())) {
                            $totalRequirementsTimeAnswered++;
                        }
                        if (!is_null($requirementsItem->getScope())) {
                            $totalRequirementsScopeAnswered++;
                        }
                    }

                    $totalQuestions = $totalRequirementsTimeAndScope * 2;
                    $totalAnswered = $totalRequirementsTimeAnswered + $totalRequirementsScopeAnswered;

                    $per = $totalAnswered !== 0 ? abs(((1 - ($totalAnswered / $totalQuestions)) * 100) - 100) : 0;

                    $array =  $item->convertArray();
                    $array['progress'] = number_format($per, 2);
                    $array['evaluatorsDetails'] = [];
                    $array['evaluatorsAdminDetails'] = [];

                    $evaluatorObj = $item->getEvaluation()->getEvaluator();
                    foreach ($evaluatorObj as $itemEvaluator) {
                        array_push($array['evaluatorsDetails'], [
                            'id' => $itemEvaluator->getId(),
                            'name' => $itemEvaluator->getName()
                        ]);
                    }

                    $evaluatorAdminObj = $item->getEvaluation()->getEvaluatorAdmins();
                    foreach ($evaluatorAdminObj as $itemEvaluatorAdmin) {
                        array_push($array['evaluatorsAdminDetails'], [
                            'id' => $itemEvaluatorAdmin->getId(),
                            'name' => $itemEvaluatorAdmin->getName()
                        ]);
                    }

                    $array['leaderApproval'] = $this->separatedLeaderApproval($item->getEvaluation()->getLeaderApproval());
                    $array['evaluation'] = $item->getEvaluation()->convertArray(Evaluation::TYPE_RN_440);

                    switch ($group) {
                        case Account::GROUP_ADMIN:
                            array_push($temp, $array);
                            break;
                        case Account::GROUP_COMPANY_USER:
                            if ($item->getEvaluation()->getCompany()->getId() === $id) {
                                $hasAnalysisUser = $item->getEvaluation()->getAnalysisUser();
                                if ($hasAnalysisUser) {
                                    $adminObj = $this->entityManager->find(
                                        Admin::class,
                                        $hasAnalysisUser
                                    );
                                    $array['analysisUser'] = [
                                        'id' => $adminObj->getId(),
                                        'name' => $adminObj->getName()
                                    ];
                                }

                                array_push($temp, $array);
                            }
                            break;
                        case Account::GROUP_EVALUATOR:
                            $evaluatorObj = $item->getEvaluation()->getEvaluator();
                            $hasAdd = false;
                            foreach ($evaluatorObj as $itemEvaluator) {
                                if ($itemEvaluator->getId() === $id && !$hasAdd) {
                                    array_push($temp, $array);
                                    $hasAdd = true;
                                }
                            }

                            // Verificar se é um obs
                            if (!$hasAdd) {
                                $evaluatorObsObj = $item->getEvaluation()->getEvaluatorObserver();
                                foreach ($evaluatorObsObj as $itemEvaluatorObs) {
                                    if ($itemEvaluatorObs->getId() === $id) {
                                        array_push($temp, $array);
                                    }
                                }
                            }
                            
                            break;
                    }
                }

                if ($group === Account::GROUP_COMPANY_USER) {
                    return [
                        'status' => 200,
                        'message' => "SUCCESS",
                        'qtd' => count($temp),
                        'result' => $temp,
                    ];
                } else {
                    return $this->separateForStatus($temp);
                }
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

    public function separateForStatus($obj)
    {
        try {

            $temp = [
                'open' => [],
                'started' => [],
                'analysisAndDecision' => [],
                'feedback' => [],
                'finished' => [],
            ];

            foreach ($obj as $array) {
                switch ($array['evaluation']['status']){
                    case Evaluation::STATUS_OPEN:
                        array_push($temp['open'], $array);
                        break;
                    case Evaluation::STATUS_STARTED:
                        array_push($temp['started'], $array);
                        break;
                    case Evaluation::STATUS_ANALYSIS_AND_DECISION:
                        array_push($temp['analysisAndDecision'], $array);
                        break;
                    case Evaluation::STATUS_FEEDBACK:
                        array_push($temp['feedback'], $array);
                        break;
                    case Evaluation::STATUS_FINISHED:
                        array_push($temp['finished'], $array);
                        break;
                }
            }

            return [
                'status' => 200,
                'message' => "SUCCESS",
                'qtd' => count($obj),
                'result' => $temp,
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

    /// Relatório
    public function report(Account $account, $userId, RN440 $rn440, $isPreview = false): array
    {
        try {

            $report = $this->getReportNameFunctions($rn440->getType());

            switch ($account->getGroupId()){
                case Account::GROUP_ADMIN:
                    return $this->$report($rn440);

                case Account::GROUP_EVALUATOR:
                    $evaluatorObj = $rn440->getEvaluation()->getEvaluator();
                    foreach ($evaluatorObj as $itemEvaluator) {
                        if ($itemEvaluator->getId() === $userId) {
                            return $this->$report($rn440);
                        }
                    }

                    if (!is_null($rn440->getEvaluation()->getLeaderApproval())) {
                        $explode = explode('_', $rn440->getEvaluation()->getLeaderApproval());
                        if ($explode[0] === 'e' && intval($explode[1]) === intval($userId)) return $this->$report($rn440);
                    }

                    return [
                        'status' => 400,
                        'message' => "WARNING",
                        'result' => 'Você não tem permissão para visualizar esse conteúdo!'
                    ];

                case Account::GROUP_COMPANY_USER:

                    $companyUserObj = $this->entityManager->find(CompanyUser::class, $userId);

                    if ($companyUserObj->getCompany()->getId() === $rn440->getEvaluation()->getCompany()->getId())
                        return $this->$report($rn440);
                    return [
                        'status' => 400,
                        'message' => "WARNING",
                        'result' => 'Você não tem permissão para visualizar esse conteúdo!'
                    ];
                default:
                    return array(
                        'status' => 400,
                        'message' => 'Grupo não informado!'
                    );
            }

        } catch (\Exception $ex) {
            return [
                'status' => 500,
                'message' => "ERROR",
                'result' => 'Erro na execução da instrução!',
                'CODE' => $ex->getCode(),
                'FILE' => $ex->getFile(),
                'LINE' => $ex->getLine(),
                'Exception' => $ex->getMessage(),
            ];
        }
    }

    public function getReportNameFunctions($type): ?string
    {
        switch ($type){
            case RN440::TYPE_PRE:
                return 'reportPre';
            case RN440::TYPE_SUPERVISION:
                return 'reportSupervision';
            case RN440::TYPE_CERTIFICATION:
                return 'reportCertification';
            case RN440::TYPE_SELF_EVALUATION:
                return 'reportSelfEvaluation';
            default:
                return null;
        }
    }

    public function reportPre(RN440 $rn440) {
        return $this->listRequirements($rn440, true);
    }

    public function reportSupervision(RN440 $rn440, $returnOnlyId = false) {

        $evaluation =  $rn440->getEvaluation();
        $lastDay = $evaluation->getCreatedDate()->format("Y-m-d H:i:s");
        $firstDay = date("Y-m-d H:i:s", strtotime($lastDay . ' -2 year'));

        $conn = Database::conexao();
        $sql = "SELECT rn.id as id from evaluations ev
                            INNER JOIN rn_440 rn
                            ON ev.id = rn.id_evaluation
                            where ev.createdDate BETWEEN 
                            ('".$firstDay."') and
                            ('".$lastDay."') and 
                            rn.type = ".RN440::TYPE_CERTIFICATION." and
                            ev.id_company = ".$evaluation->getCompany()->getId().";";

        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $res = $stmt->fetchAll(PDO::FETCH_OBJ);

        $id = $res[count($res) - 1];

        $acc_rn440 = new RN440();
        $acc_rn440->setId($id->id);

        if ($returnOnlyId) return $id->id;

        $acc = $this->listRequirements($acc_rn440, true);
        $super = $this->listRequirements($rn440, true);

        if ($acc['status'] === 200) {
            $super['result']['resumeLastAccreditation'] = $acc['result']['resume'];
            $super['result']['totalPointsLastAccreditation'] = $acc['result']['totalPoints'];

            foreach ($super['result']['dimensions'] as $keyDim => $rowDim) {
                $super['result']['dimensions'][$keyDim]['dimensionScoreLastAccreditation'] = $acc['result']['dimensions'][$keyDim]['dimensionScore'];
            }
        }

        return $super;
    }

    public function reportIdLastEvaluation(RN440 $rn440, $type) {

        $evaluation =  $rn440->getEvaluation();
        $lastDay = $evaluation->getCreatedDate()->format("Y-m-d H:i:s");
        $firstDay = date("Y-m-d H:i:s", strtotime($lastDay . ' -2 year'));

        $conn = Database::conexao();
        $sql = "SELECT rn.id as id from evaluations ev
                            INNER JOIN rn_440 rn
                            ON ev.id = rn.id_evaluation
                            where ev.createdDate BETWEEN 
                            ('".$firstDay."') and
                            ('".$lastDay."') and 
                            rn.type = ".$type." and
                            rn.id != ".$rn440->getId()." and
                            ev.id_company = ".$evaluation->getCompany()->getId().";";

        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $res = $stmt->fetchAll(PDO::FETCH_OBJ);

        $id = $res[count($res) - 1];

        $acc_rn440 = new RN440();
        $acc_rn440->setId($id->id);

        return $id->id;
    }

    public function reportSelfEvaluation(RN440 $rn440) {
        return $this->listRequirements($rn440, true);
    }

    public function reportCertification(RN440 $rn440) {
        return $this->listRequirements($rn440, true);
    }

    public function listRequirements(RN440 $rn440, $isComplete = false, $onlyPoint = false)
    {
        try {

            $objRN440 = $this->entityManager->find(RN440::class, $rn440->getId());
            $objReq = $this->entityManager
                ->getRepository(ListRN440Requirements::class)
                ->findBy(['active' => true], ['id' => 'ASC']);

            $dimensionsChosen = [];

            $objEvaluation = $objRN440->getEvaluation();
            
            $requirementsItems = $objRN440->getRequirementsItems();

            foreach ($requirementsItems as $item) {
                $dimension = $item->getListOfItems()->getRequirement()->getRequirementNumber();
                if (!in_array($dimension, $dimensionsChosen)) {
                    array_push($dimensionsChosen, $dimension);
                }
            }

            $dimensions = [];
            $array = [ 'requirements' => [], '$requirementsScore' => null];
            foreach ($dimensionsChosen as $chosen) {
                $dimensions[$chosen] = $array;
            }

            foreach ($objReq as $requirement) {
                $dimension = $requirement->getRequirementNumber();
                $id = $requirement->getId();
                if ($dimensions[$dimension] && !$onlyPoint) {
                    $dimensions[$dimension]['requirements'][$id] = [
                        "id" => $id,
                        "table" => [],
                        "totalItemsOfExcellence" => 0,
                        "sumPoints" => [
                            'sumPartial' => null,
                            'sumTotal' => null,
                        ],
                        "numericMarkers" => $requirement->getNumericMarkers(),
                        "text" => $requirement->getText(),
                        "items" => []
                    ];
                }

                if ($dimensions[$dimension] && $onlyPoint) {
                    $dimensions[$dimension]['requirements'][$id] = [
                        "table" => [],
                        "sumPoints" => [
                            'sumPartial' => null,
                            'sumTotal' => null,
                        ]
                    ];
                }
            }

            $totalItemsOfExcellence = 0;
            $totalPoints = 0;

            foreach ($requirementsItems as $requirement) {
                $id = $requirement->getListOfItems()->getRequirement()->getId();
                $dimension = $requirement->getListOfItems()->getRequirement()->getRequirementNumber();
                if ($dimensions[$dimension]) {

                    if (!$onlyPoint) array_push($dimensions[$dimension]['requirements'][$id]['items'], $requirement->convertArray());

                    // Realizar a pontuação de cada requisito e retornar em forma de tabela
                    if ($isComplete) {
                        $tablePointing = [
                            "marker" => $requirement->getListOfItems()->getNumericMarkers(),
                            "pointing" => $requirement->getPointing(),
                            "type" => $requirement->getListOfItems()->getType()
                        ];

                        if ($requirement->getListOfItems()->getType() === ListRN440RequirementsItems::TYPE_EXCELLENCE
                            && $requirement->getPointing() === 1) {
                            $totalItemsOfExcellence++;
                            $dimensions[$dimension]['requirements'][$id]['totalItemsOfExcellence']++;
                        }

                        array_push($dimensions[$dimension]['requirements'][$id]['table'], $tablePointing);

                    }
                }
            }

            // Realizar a soma da pontuação de cada requisito e de toda a dimensão
            if ($isComplete) {

                foreach ($dimensions as $keyDim => $rowDim) {
                    $sumTotalDim = 0;

                    $sizeReq = sizeof($dimensions[$keyDim]['requirements']);
                    foreach ($dimensions[$keyDim]['requirements'] as $keyReq => $rowReq) {
                        $sumTem = 0;
                        $zeroedAnEssential = false;

                        $tableListPoint = $dimensions[$keyDim]['requirements'][$keyReq]['table'];

                        foreach ($tableListPoint as $tableElm) {
                            $sumTem += $tableElm['pointing'];
                            if ($tableElm['pointing'] === 0 && $tableElm['type'] === ListRN440RequirementsItems::TYPE_ESSENTIAL) {
                                $zeroedAnEssential = true;
                            }
                        }

                        $sumPartial = $sumTem === 0 ? 0 : number_format(($sumTem / sizeof($tableListPoint)) * 100, 2);
                        $sumTotal = !$zeroedAnEssential ? $sumPartial : 0;

                        $dimensions[$keyDim]['requirements'][$keyReq]['sumPoints'] = [
                            'sumPartial' => $sumPartial,
                            'sumTotal' => $sumTotal,
                        ];

                        $sumTotalDim += $sumTotal;
                    }

                    $sumTotalDim = $sumTotalDim / $sizeReq;
                    $dimensions[$keyDim]['dimensionScore'] = number_format($sumTotalDim, 2);
                    $totalPoints += $sumTotalDim;
                }
            }

            $tempRN440 = $objRN440->convertArray();

            $RN440Array = $objEvaluation->convertArray(Evaluation::TYPE_RN_440);
            $RN440Array['id_rn440'] = $tempRN440['id'];
            $RN440Array['classification'] = $tempRN440['classification'];
            $RN440Array['type_rn440'] = $tempRN440['type'];

            $RN440Array['dimensions'] = $dimensions;
            $RN440Array['dimensionsList'] = [];

            foreach ($dimensionsChosen as $chosen) {
                array_push($RN440Array['dimensionsList'], $chosen);
            }

            if (!$onlyPoint) $RN440Array['leaderApproval'] = $this->separatedLeaderApproval($RN440Array['leaderApproval'], $isComplete);

            // Pegar os dados dos avaliadores e outras informações adicionais
            $RN440Array['evaluators'] = [];
            $RN440Array['evaluatorsAdmin'] = [];
            $RN440Array['evaluatorsCompany'] = [];
            $RN440Array['evaluatorObserver'] = [];

            $evaluatorObj = $objEvaluation->getEvaluator();
            foreach ($evaluatorObj as $itemEvaluator) {
                array_push($RN440Array['evaluators'], $itemEvaluator->convertArray());
            }

            $evaluatorAdminObj = $objEvaluation->getEvaluatorAdmins();
            foreach ($evaluatorAdminObj as $itemEvaluatorAdmin) {
                array_push($RN440Array['evaluatorsAdmin'], $itemEvaluatorAdmin->convertArray());
            }

            $evaluatorCompanyUsers = $objEvaluation->getEvaluatorCompanyUsers();
            foreach ($evaluatorCompanyUsers as $itemEvaluatorCompanyUser) {
                array_push($RN440Array['evaluatorsCompany'], $itemEvaluatorCompanyUser->convertArray());
            }

            $evaluatorObserver = $objEvaluation->getEvaluatorObserver();
            foreach ($evaluatorObserver as $itemEvaluator) {
                array_push($RN440Array['evaluatorObserver'], $itemEvaluator->convertArray());
            }

            usort($RN440Array['evaluators'], function($a, $b) {
                return $a['name'] <=> $b['name'];
            });

            usort($RN440Array['evaluatorObserver'], function($a, $b) {
                return $a['name'] <=> $b['name'];
            });

            usort($RN440Array['evaluatorsCompany'], function($a, $b) {
                return $a['name'] <=> $b['name'];
            });
            if ($isComplete) {
                $RN440Array['totalPoints'] = number_format($totalPoints / count($dimensionsChosen), 2);
                $RN440Array['totalItemsOfExcellence'] = $totalItemsOfExcellence;
            }

            $diary = [];
            // Pegar os dados da agenda
            $objDiary = $this->entityManager
                ->getRepository(Diary::class)
                ->findBy(['evaluation' => $objEvaluation->getId()], ['startDate' => 'ASC']);

            foreach ($objDiary as $diaries) {
                array_push($diary, $diaries->convertArray());
            }
            $RN440Array["diary"] = $diary;

            $controlVisualization = [];
            if (!$isComplete) {
                $objControlVisualization = $this->entityManager
                    ->getRepository(ControlVisualizationDimensions::class)
                    ->findBy(['evaluation' => $objEvaluation->getId()], ['createdDate' => 'ASC']);
                foreach ($objControlVisualization as $control) {
                    array_push($controlVisualization, $control->convertArray());
                }
                $RN440Array["controlVisualization"] = $controlVisualization;
            }

            if ($onlyPoint) {
                $RN440Array = [
                    "dimensionsList" => $RN440Array["dimensions"],
                    "dimensions" => $RN440Array["dimensions"],
                    "totalPoints" => $RN440Array["totalPoints"],
                    "totalItemsOfExcellence" => $RN440Array["totalItemsOfExcellence"],
                ];
            }

            return [
                'status' => 200,
                'message' => "SUCCESS",
                'result' => $RN440Array,
                'typeEvaluation' => $objEvaluation->getType(),
                'type' => $objRN440->getType(),
                'classification' => $objRN440->getClassification(),
                'createdDate' => $objEvaluation->getCreatedDate(),
                'company' => $objEvaluation->getCompany()
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

    public function separatedLeaderApproval($leaderApproval, $isComplete = false) {

        if (is_null($leaderApproval)) return null;

        $explode = explode('_', $leaderApproval);

        if ($explode[0] === 'e') {
            $evaluatorObj = $this->entityManager->find(
                Evaluator::class,
                $explode[1]
            );
            return $isComplete ? $evaluatorObj->convertArray() : [
                'id' => $evaluatorObj->getId(),
                'name' => $evaluatorObj->getName(),
                'type' => 'evaluator'
            ];
        }

        if ($explode[0] === 'd') {
            $directorObj = $this->entityManager->find(
                Admin::class,
                $explode[1]
            );
            return $isComplete ? $directorObj->convertArray() : [
                'id' => $directorObj->getId(),
                'name' => $directorObj->getName(),
                'type' => 'director'
            ];
        }

        if ($explode[0] === 'c') {
            $companyUserObj = $this->entityManager->find(
                CompanyUser::class,
                $explode[1]
            );
            return $isComplete ? $companyUserObj->convertArray() : [
                'id' => $companyUserObj->getId(),
                'name' => $companyUserObj->getName(),
                'type' => 'companyUser'
            ];
        }

        return null;
    }


    // Create
    public function insert(Evaluation $evaluation, RN440 $RN440, $requirements)
    {
        try {

            switch ($RN440->getClassification()) {
                case RN440::CLASSIFICATION_APS:
                    $reqBelongsClassification = $this->entityManager
                        ->getRepository(ListRN440RequirementsItems::class)
                        ->findBy(['active' => true], ['id' => 'ASC']);
                    break;
                default:
                    return [
                        'status' => 200,
                        'message' => "SUCCESS",
                        'result' => 'Classificação não informada!',
                    ];
            }

            if ($RN440->getType() !== RN440::TYPE_SELF_EVALUATION) {

                $EvaluationDAO = new EvaluationDAO();

                $arrayEvaluators = $EvaluationDAO->separatedEvaluatorsUsers($evaluation);

                $list = $evaluation->getEvaluatorObserver();
                $listObserver = is_array($list) ? $list : [];

                $evaluation->setEvaluator(new \Doctrine\Common\Collections\ArrayCollection());
                $evaluation->setEvaluatorAdmins(new \Doctrine\Common\Collections\ArrayCollection());
                $evaluation->setEvaluatorObserver(new \Doctrine\Common\Collections\ArrayCollection());

                foreach ($arrayEvaluators['evaluators'] as $evaluator){
                    $obj = $this->entityManager->find(Evaluator::class, $evaluator);
                    $evaluation->addEvaluator($obj);
                }

                foreach ($arrayEvaluators['directors'] as $admin){
                    $obj = $this->entityManager->find(Admin::class, $admin);
                    $evaluation->addEvaluatorAdmin($obj);
                }

                foreach ($listObserver as $evaluator){
                    $obj = $this->entityManager->find(Evaluator::class, $evaluator);
                    $evaluation->addEvaluatorObserver($obj);
                }
            }

            $companyObj = $this->entityManager->find(Company::class, $evaluation->getCompany());
            $evaluation->setCompany($companyObj);

            $evaluation->setCompanyPort($companyObj->getPort());
            $evaluation->setCompanyNumberOfEmployees($companyObj->getNumberOfEmployees());
            $evaluation->setCompanyNumberOfBeneficiaries($companyObj->getNumberOfBeneficiaries());
            $evaluation->setCompanyIdss($companyObj->getIdss());

            $this->entityManager->persist($evaluation);
            $this->entityManager->flush();

            $RN440->setEvaluation($evaluation);
            $this->entityManager->persist($RN440);
            $this->entityManager->flush();

            foreach ($reqBelongsClassification as $requirement) {
                if ($RN440->getType() === RN440::TYPE_SUPERVISION) {
                    if (in_array($requirement->getRequirement()->getRequirementNumber(), $requirements)) {
                        $RN440RequirementsItems = new RN440RequirementsItems();
                        $RN440RequirementsItems->setRn440($RN440);
                        $RN440RequirementsItems->setListOfItems($requirement);
                        $this->entityManager->persist($RN440RequirementsItems);
                        $this->entityManager->flush();
                    }
                } else {
                    $RN440RequirementsItems = new RN440RequirementsItems();
                    $RN440RequirementsItems->setRn440($RN440);
                    $RN440RequirementsItems->setListOfItems($requirement);
                    $this->entityManager->persist($RN440RequirementsItems);
                    $this->entityManager->flush();
                }
            }

            if ($RN440->getType() !== RN440::TYPE_SELF_EVALUATION) {

                // Criar Resumo
                $resume = new Resume();
                $this->entityManager->persist($resume);
                $this->entityManager->flush();

                $evaluation->setResume($resume);
                $this->entityManager->flush();


                // Criar Reuniões
                $initialMeeting = new Meeting(Meeting::TYPE_INITIAL);
                $initialMeeting->setEvaluation($evaluation);
                $finalMeeting = new Meeting(Meeting::TYPE_FINAL);
                $finalMeeting->setEvaluation($evaluation);

                $this->entityManager->persist($initialMeeting);
                $this->entityManager->persist($finalMeeting);
                $this->entityManager->flush();

            }

            return [
                'status' => 200,
                'message' => "SUCCESS",
                'result' => 'Avaliação criada!',
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

    // Update
    public function updateRequirementsItems(RN440RequirementsItems $RN440RequirementsItems, $groupId, $userId, $type)
    {
        try {

            $objReq = $this->entityManager->find(RN440RequirementsItems::class, $RN440RequirementsItems->getId());
            $evaluationObj = $objReq->getRn440()->getEvaluation();

            $logs = new Logs($RN440RequirementsItems->getId(), $userId, $groupId);
            $logs->setEvaluation($evaluationObj->getId());


            if ($evaluationObj->getStatus() === Evaluation::STATUS_OPEN) {
                $evaluationObj->setStatus(Evaluation::STATUS_STARTED);
                $evaluationObj->setStartedDate(new \DateTime());
            }

            if ($evaluationObj->getStatus() === Evaluation::STATUS_FINISHED && $groupId !== Account::GROUP_ADMIN)
                return [
                    'status' => 400,
                    'message' => "ERROR",
                    'result' => 'Esta avaliação já foi finalizada e não pode ser atualizada!',
                ];

            if ($type === 'SCOPE') {
                $logs->setBeforeChange($objReq->getScope());
                $logs->setAfterChange($RN440RequirementsItems->getScope());
                $logs->setType(Logs::TYPE_REQUIREMENTS_ITEMS_SCOPE);
                $objReq->setScope($RN440RequirementsItems->getScope());
            }

            if ($type === 'DEPLOYMENT_TIME') {
                $logs->setBeforeChange($objReq->getDeploymentTime());
                $logs->setAfterChange($RN440RequirementsItems->getDeploymentTime());
                $logs->setType(Logs::TYPE_REQUIREMENTS_ITEMS_DEPLOYMENT_TIME);
                $objReq->setDeploymentTime($RN440RequirementsItems->getDeploymentTime());
            }

            $this->entityManager->persist($logs);
            $this->entityManager->flush();

            if (!is_null($objReq->getDeploymentTime()) && !is_null($objReq->getScope())) {
                $timeValue = $objReq->getDeploymentTime();
                $scopeValue = $objReq->getScope();

                $objReq->setDegreeOfCompliance($RN440RequirementsItems->calculateDegree($timeValue, $scopeValue));
                $objReq->setPointing($RN440RequirementsItems->calculatePoints($timeValue, $scopeValue));
            }

            if (is_null($objReq->getDeploymentTime()) || is_null($objReq->getScope())) {
                $objReq->setDegreeOfCompliance(null);
                $objReq->setPointing(null);
            }

            $this->entityManager->flush();

            return [
                'status' => 200,
                'message' => "SUCCESS",
                'result' => 'Requerimento atualizado!',
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

    public function updateRequirementsComments(RN440RequirementsItems $RN440RequirementsItems, $filesNames, $groupId, $userId)
    {
        try {

            $objReq = $this->entityManager->find(RN440RequirementsItems::class, $RN440RequirementsItems->getId());

            $evaluationObj = $objReq->getRn440()->getEvaluation();

            if ($evaluationObj->getStatus() === Evaluation::STATUS_FINISHED && $groupId !== Account::GROUP_ADMIN)
                return [
                    'status' => 400,
                    'message' => "ERROR",
                    'result' => 'Esta avaliação já foi finalizada e não pode ser atualizada!',
                ];

            if (!is_null($RN440RequirementsItems->getComment())){
                if ($objReq->getComment() !== $RN440RequirementsItems->getComment()) {
                    $logsComment = new Logs($RN440RequirementsItems->getId(), $userId, $groupId);
                    $logsComment->setEvaluation($evaluationObj->getId());
                    $logsComment->setType(Logs::TYPE_REQUIREMENTS_ITEMS_COMMENT);
                    $logsComment->setBeforeChange($objReq->getComment());
                    $logsComment->setAfterChange($RN440RequirementsItems->getComment());
                    $this->entityManager->persist($logsComment);
                }
                $objReq->setComment($RN440RequirementsItems->getComment());
            }

            if (!is_null($RN440RequirementsItems->getEvidence())){
                if ($objReq->getEvidence() !== $RN440RequirementsItems->getEvidence()) {
                    $logsEvidence = new Logs($RN440RequirementsItems->getId(), $userId, $groupId);
                    $logsEvidence->setEvaluation($evaluationObj->getId());
                    $logsEvidence->setType(Logs::TYPE_REQUIREMENTS_ITEMS_EVIDENCE);
                    $logsEvidence->setBeforeChange($objReq->getEvidence());
                    $logsEvidence->setAfterChange($RN440RequirementsItems->getEvidence());
                    $this->entityManager->persist($logsEvidence);
                }
                $objReq->setEvidence($RN440RequirementsItems->getEvidence());
            }

            if (!is_null($RN440RequirementsItems->getFeedback())) {
                if ($objReq->getFeedback() !== $RN440RequirementsItems->getFeedback()) {
                    $logsEvidence = new Logs($RN440RequirementsItems->getId(), $userId, $groupId);
                    $logsEvidence->setEvaluation($evaluationObj->getId());
                    $logsEvidence->setType(Logs::TYPE_REQUIREMENTS_ITEMS_FEEDBACK);
                    $logsEvidence->setBeforeChange($objReq->getFeedback());
                    $logsEvidence->setAfterChange($RN440RequirementsItems->getFeedback());
                    $this->entityManager->persist($logsEvidence);
                }
                $objReq->setFeedback($RN440RequirementsItems->getFeedback());
            }

            if (!is_null($RN440RequirementsItems->getChangedPoint())){
                if ($objReq->getChangedPoint() !== $RN440RequirementsItems->getChangedPoint()) {
                    $logsChangedPoint = new Logs($RN440RequirementsItems->getId(), $userId, $groupId);
                    $logsChangedPoint->setEvaluation($evaluationObj->getId());
                    $logsChangedPoint->setType(Logs::TYPE_REQUIREMENTS_ITEMS_EVIDENCE);
                    $logsChangedPoint->setBeforeChange($objReq->getChangedPoint());
                    $logsChangedPoint->setAfterChange($RN440RequirementsItems->getChangedPoint());
                    $this->entityManager->persist($logsChangedPoint);
                }
                $objReq->setChangedPoint($RN440RequirementsItems->getChangedPoint());
            }

            if (!is_null($RN440RequirementsItems->getImprovementOpportunity())) {
                if ($objReq->getImprovementOpportunity() !== $RN440RequirementsItems->getImprovementOpportunity()) {
                    $logsImprovement = new Logs($RN440RequirementsItems->getId(), $userId, $groupId);
                    $logsImprovement->setEvaluation($evaluationObj->getId());
                    $logsImprovement->setType(Logs::TYPE_REQUIREMENTS_ITEMS_IMPROVEMENT_OPPORTUNITY);
                    $logsImprovement->setBeforeChange($objReq->getImprovementOpportunity());
                    $logsImprovement->setAfterChange($RN440RequirementsItems->getImprovementOpportunity());
                    $this->entityManager->persist($logsImprovement);
                }
                $objReq->setImprovementOpportunity($RN440RequirementsItems->getImprovementOpportunity());
            }

            if (!is_null($RN440RequirementsItems->getStrongPoint())) {
                if ($objReq->getStrongPoint() !== $RN440RequirementsItems->getStrongPoint()) {
                    $logsStrongPoint = new Logs($RN440RequirementsItems->getId(), $userId, $groupId);
                    $logsStrongPoint->setEvaluation($evaluationObj->getId());
                    $logsStrongPoint->setType(Logs::TYPE_REQUIREMENTS_ITEMS_STRONG_POINT);
                    $logsStrongPoint->setBeforeChange($objReq->getStrongPoint());
                    $logsStrongPoint->setAfterChange($RN440RequirementsItems->getStrongPoint());
                    $this->entityManager->persist($logsStrongPoint);
                }
                $objReq->setStrongPoint($RN440RequirementsItems->getStrongPoint());
            }

            if (!is_null($RN440RequirementsItems->getNonAttendance())) {
                if ($objReq->getNonAttendance() !== $RN440RequirementsItems->getNonAttendance()) {
                    $logsNonAttendance = new Logs($RN440RequirementsItems->getId(), $userId, $groupId);
                    $logsNonAttendance->setEvaluation($evaluationObj->getId());
                    $logsNonAttendance->setType(Logs::TYPE_REQUIREMENTS_ITEMS_NON_ATTENDANCE);
                    $logsNonAttendance->setBeforeChange($objReq->getNonAttendance());
                    $logsNonAttendance->setAfterChange($RN440RequirementsItems->getNonAttendance());
                    $this->entityManager->persist($logsNonAttendance);
                }
                $objReq->setNonAttendance($RN440RequirementsItems->getNonAttendance());
            }

            if ($objReq->getRn440()->getEvaluation()->getStatus() === Evaluation::STATUS_OPEN) {
                $objReq->getRn440()->getEvaluation()->setStatus(Evaluation::STATUS_STARTED);
                $objReq->getRn440()->getEvaluation()->setStartedDate(new \DateTime());
            }

            $this->entityManager->flush();

            $filesObj = [];
            foreach ($filesNames as $fileItem){
                $file = new RN440RequirementsItemsFiles();
                $file->setName($fileItem);
                $file->setRequirementItem($objReq);
                $this->entityManager->persist($file);
                $this->entityManager->flush();
                array_push($filesObj, [
                    'id' => $file->getId(),
                    'name' => $file->getName()
                ]);
            }

            return [
                'status' => 200,
                'message' => "SUCCESS",
                'result' => count($filesObj) > 0 ? $filesObj : 'Requerimento atualizado!',
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

    public function deleteFile($id)
    {
        try {

            $objFile = $this->entityManager->find(RN440RequirementsItemsFiles::class, $id);
            $evaluationObj = $objFile->getRequirementItem()->getRn440()->getEvaluation();

            if ($evaluationObj->getStatus() === Evaluation::STATUS_FINISHED)
                return [
                    'status' => 400,
                    'message' => "ERROR",
                    'result' => 'Esta avaliação já foi finalizada e não pode ser atualizada!',
                ];

            $this->entityManager->remove($objFile);
            $this->entityManager->flush();

            return [
                'status' => 200,
                'message' => "SUCCESS",
                'result' => 'Arquivo removido atualizado!',
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