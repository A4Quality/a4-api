<?php

namespace App\DAO;
use App\Basics\Account;
use App\Basics\Admin;
use App\Basics\Company;
use App\Basics\Evaluation;
use App\Basics\Resume;
use App\Basics\RN452\Lists\ListRN452MonitoredIndicators;
use App\Basics\RN452\Lists\ListRN452Prerequisites;
use App\Basics\RN452\Lists\ListRN452Requirements;
use App\Basics\RN452\Lists\ListRN452RequirementsItems;
use App\Basics\RN452\RN452;
use App\Basics\Meeting;
use App\Basics\CompanyUser;
use App\Basics\ControlVisualizationDimensions;
use App\Basics\Diary;
use App\Basics\Evaluator;
use App\Basics\Logs;
use App\Basics\RN452\RN452MonitoredIndicators;
use App\Basics\RN452\RN452Prerequisites;
use App\Basics\RN452\RN452RequirementsItems;
use App\Basics\RN452\RN452RequirementsItemsFiles;
use App\Config\Doctrine;
use App\Connection\Database;
use Doctrine\ORM\EntityManager;
use PDO;
use PDOException;

class RN452DAO
{

    private EntityManager $entityManager;

    /**
     * RN452DAO constructor.
     */
    public function __construct()
    {
        $doctrine = new Doctrine();
        $this->entityManager = $doctrine->getEntityManager();
    }

    /**
     * RN452DAO destruct.
     */
    public function __destruct()
    {
        $this->entityManager->getConnection()->close();
    }


    /// Listagem das avaliações
    public function listForAdmin($type, $classification): array
    {
        try {

            $obj = $this->entityManager
                ->getRepository(RN452::class)
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

    public function listForEvaluator($userId, $type, $classification): array
    {
        try {

            $obj = $this->entityManager
                ->getRepository(RN452::class)
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

    public function listForCompanyUser($userId, $type, $classification): array
    {
        try {

            $companyUserObj = $this->entityManager->find(
                CompanyUser::class,
                $userId
            );

            $companyId = $companyUserObj->getCompany()->getId();

            $obj = $this->entityManager
                ->getRepository(RN452::class)
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

                    $totalIndicators = sizeof($item->getMonitoredIndicators());
                    $totalIndicatorsAnswered = 0;

                    foreach ($item->getMonitoredIndicators() as $indicatorItem) {
                        if (!is_null($indicatorItem->getItHas())) {
                            $totalIndicatorsAnswered++;
                        }
                    }

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

                    $totalQuestions = $totalIndicators + ($totalRequirementsTimeAndScope * 2);
                    $totalAnswered = $totalIndicatorsAnswered + $totalRequirementsTimeAnswered + $totalRequirementsScopeAnswered;

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
                    $array['evaluation'] = $item->getEvaluation()->convertArray();

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
                            foreach ($evaluatorObj as $itemEvaluator) {
                                if ($itemEvaluator->getId() === $id) {
                                    array_push($temp, $array);
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
    public function report(Account $account, $userId, RN452 $rn452, $isPreview = false): array
    {
        try {

            $report = $this->getReportNameFunctions($rn452->getType());

            switch ($account->getGroupId()){
                case Account::GROUP_ADMIN:
                    return $this->$report($rn452);

                case Account::GROUP_EVALUATOR:
                    $evaluatorObj = $rn452->getEvaluation()->getEvaluator();
                    foreach ($evaluatorObj as $itemEvaluator) {
                        if ($itemEvaluator->getId() === $userId) {
                            return $this->$report($rn452);
                        }
                    }

                    if (!is_null($rn452->getEvaluation()->getLeaderApproval())) {
                        $explode = explode('_', $rn452->getEvaluation()->getLeaderApproval());
                        if ($explode[0] === 'e' && intval($explode[1]) === intval($userId)) return $this->$report($rn452);
                    }

                    return [
                        'status' => 400,
                        'message' => "WARNING",
                        'result' => 'Você não tem permissão para visualizar esse conteúdo!'
                    ];

                case Account::GROUP_COMPANY_USER:

                    $companyUserObj = $this->entityManager->find(CompanyUser::class, $userId);

                    if ($companyUserObj->getCompany()->getId() === $rn452->getEvaluation()->getCompany()->getId())
                        return $this->$report($rn452);
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
            case RN452::TYPE_PRE:
                return 'reportPre';
            case RN452::TYPE_SUPERVISION:
                return 'reportSupervision';
            case RN452::TYPE_ACCREDITATION:
                return 'reportAccreditation';
            case RN452::TYPE_SELF_EVALUATION:
                return 'reportSelfEvaluation';
            default:
                return null;
        }
    }

    public function reportPre(RN452 $rn452) {
        return $this->listRequirements($rn452, true);
    }

    /* TODO - Realizar captura de ultima surpevisão
        Primeiro a ultima avaliação depois descer para a RN452

    */

    public function reportSupervision(RN452 $rn452, $returnOnlyId = false) {

        $evaluation =  $rn452->getEvaluation();
        $lastDay = $evaluation->getCreatedDate()->format("Y-m-d H:i:s");
        $firstDay = date("Y-m-d H:i:s", strtotime($lastDay . ' -1 year'));

        $conn = Database::conexao();
        $sql = "SELECT rn.id as id from evaluations ev
                            INNER JOIN rn_452 rn
                            ON ev.id = rn.id_evaluation
                            where ev.createdDate BETWEEN 
                            ('".$firstDay."') and
                            ('".$lastDay."') and 
                            rn.type = ".RN452::TYPE_ACCREDITATION." and
                            ev.id_company = ".$evaluation->getCompany()->getId().";";

        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $res = $stmt->fetchAll(PDO::FETCH_OBJ);

        $id = $res[count($res) - 1];

        $acc_rn452 = new RN452();
        $acc_rn452->setId($id);

        if ($returnOnlyId) return $id->id;

        $acc = $this->listRequirements($acc_rn452, true);
        $super = $this->listRequirements($rn452, true);
        $super['result']['resumeLastAccreditation'] = $acc['result']['resume'];
        $super['result']['totalPointsLastAccreditation'] = $acc['result']['totalPoints'];

        foreach ($super['result']['dimensions'] as $keyDim => $rowDim) {
            $super['result']['dimensions'][$keyDim]['dimensionScoreLastAccreditation'] = $acc['result']['dimensions'][$keyDim]['dimensionScore'];
        }

        return $super;
    }

    public function reportSelfEvaluation(RN452 $rn452) {
        return $this->listRequirements($rn452, true);
    }

    public function reportAccreditation(RN452 $rn452) {
        return $this->listRequirements($rn452, true);
    }

    public function listRequirements(RN452 $rn452, $isComplete = false, $onlyPoint = false)
    {
        try {

            $objRN452 = $this->entityManager->find(RN452::class, $rn452->getId());
            $objReq = $this->entityManager
                ->getRepository(ListRN452Requirements::class)
                ->findBy(['active' => true], ['id' => 'ASC']);

            $dimensionsChosen = [];

            $objEvaluation = $objRN452->getEvaluation();

            $monitoredIndicators = $objRN452->getMonitoredIndicators();
            $requirementsItems = $objRN452->getRequirementsItems();

            foreach ($monitoredIndicators as $indicator) {
                $dimension = $indicator->getListOfMonitoredIndicators()->getDimension();
                if (!in_array($dimension, $dimensionsChosen)) {
                    array_push($dimensionsChosen, $dimension);
                }
            }

            $dimensions = [];
            $array = [ 'indicators' => [], 'requirements' => [], 'dimensionScore' => null];
            foreach ($dimensionsChosen as $chosen) {
                $dimensions[$chosen] = $array;
            }

            if (!$onlyPoint) {
                foreach ($monitoredIndicators as $indicator) {
                    $dimension = $indicator->getListOfMonitoredIndicators()->getDimension();
                    array_push($dimensions[$dimension]['indicators'], [
                        "id" => $indicator->getId(),
                        "itHas" => $indicator->getItHas(),
                        "numericMarkers" => $indicator->getListOfMonitoredIndicators()->getNumericMarkers(),
                        "text" => $indicator->getListOfMonitoredIndicators()->getText(),
                    ]);
                }
            }

            foreach ($objReq as $requirement) {
                $dimension = $requirement->getDimension();
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
                $dimension = $requirement->getListOfItems()->getRequirement()->getDimension();
                if ($dimensions[$dimension]) {

                    if (!$onlyPoint) array_push($dimensions[$dimension]['requirements'][$id]['items'], $requirement->convertArray());

                    // Realizar a pontuação de cada requisito e retornar em forma de tabela
                    if ($isComplete) {
                        $tablePointing = [
                            "marker" => $requirement->getListOfItems()->getNumericMarkers(),
                            "pointing" => $requirement->getPointing(),
                            "type" => $requirement->getListOfItems()->getType()
                        ];

                        if ($requirement->getListOfItems()->getType() === ListRN452RequirementsItems::TYPE_EXCELLENCE
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
                    $subTableZero = 0;

                    foreach ($dimensions[$keyDim]['requirements'] as $keyReq => $rowReq) {
                        $sumTem = 0;
                        $zeroedAnEssential = false;

                        $tableListPoint = $dimensions[$keyDim]['requirements'][$keyReq]['table'];

                        if (count($tableListPoint) === 0) {
                            $subTableZero++;
                            unset($dimensions[$keyDim]['requirements'][$keyReq]);
                        } else {
                            foreach ($tableListPoint as $tableElm) {
                                $sumTem += $tableElm['pointing'];
                                if ($tableElm['pointing'] === 0 && $tableElm['type'] === ListRN452RequirementsItems::TYPE_ESSENTIAL) {
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
                    }

                    $realSize = $sizeReq - $subTableZero;

                    $sumTotalDim = $sumTotalDim / $realSize;
                    $dimensions[$keyDim]['dimensionScore'] = number_format($sumTotalDim, 2);
                    $totalPoints += $sumTotalDim;
                }
            }

            $tempRN452 = $objRN452->convertArray();

            $RN452Array = $objEvaluation->convertArray();
            $RN452Array['id_rn452'] = $tempRN452['id'];
            $RN452Array['classification'] = $tempRN452['classification'];
            $RN452Array['type_rn452'] = $tempRN452['type'];
            $RN452Array['prerequisites'] = $tempRN452['prerequisites'];

            $RN452Array['dimensions'] = $dimensions;
            $RN452Array['dimensionsList'] = [];

            foreach ($dimensionsChosen as $chosen) {
                array_push($RN452Array['dimensionsList'], $chosen);
            }

            if (!$onlyPoint) $RN452Array['leaderApproval'] = $this->separatedLeaderApproval($RN452Array['leaderApproval'], $isComplete);

            // Pegar os dados dos avaliadores e outras informações adicionais
            $RN452Array['evaluators'] = [];
            $RN452Array['evaluatorsAdmin'] = [];
            $RN452Array['evaluatorsCompany'] = [];

            $evaluatorObj = $objEvaluation->getEvaluator();
            foreach ($evaluatorObj as $itemEvaluator) {
                array_push($RN452Array['evaluators'], $itemEvaluator->convertArray());
            }

            $evaluatorAdminObj = $objEvaluation->getEvaluatorAdmins();
            foreach ($evaluatorAdminObj as $itemEvaluatorAdmin) {
                array_push($RN452Array['evaluatorsAdmin'], $itemEvaluatorAdmin->convertArray());
            }

            $evaluatorCompanyUsers = $objEvaluation->getEvaluatorCompanyUsers();
            foreach ($evaluatorCompanyUsers as $itemEvaluatorCompanyUser) {
                array_push($RN452Array['evaluatorsCompany'], $itemEvaluatorCompanyUser->convertArray());
            }

            usort($RN452Array['evaluators'], function($a, $b) {
                return $a['name'] <=> $b['name'];
            });

            usort($RN452Array['evaluatorsCompany'], function($a, $b) {
                return $a['name'] <=> $b['name'];
            });
            if ($isComplete) {
                if ($totalPoints === 0 || count($dimensionsChosen) === 0) {
                    $RN452Array['totalPoints'] = 0;
                } else {
                    $RN452Array['totalPoints'] = number_format($totalPoints / count($dimensionsChosen), 2);
                }
                $RN452Array['totalItemsOfExcellence'] = $totalItemsOfExcellence;
            }

            $diary = [];
            // Pegar os dados da agenda
            $objDiary = $this->entityManager
                ->getRepository(Diary::class)
                ->findBy(['evaluation' => $objEvaluation->getId()], ['startDate' => 'ASC']);

            foreach ($objDiary as $diaries) {
                array_push($diary, $diaries->convertArray());
            }
            $RN452Array["diary"] = $diary;

            $controlVisualization = [];
            if (!$isComplete) {
                $objControlVisualization = $this->entityManager
                    ->getRepository(ControlVisualizationDimensions::class)
                    ->findBy(['evaluation' => $objEvaluation->getId()], ['createdDate' => 'ASC']);
                foreach ($objControlVisualization as $control) {
                    array_push($controlVisualization, $control->convertArray());
                }
                $RN452Array["controlVisualization"] = $controlVisualization;
            }

            if ($onlyPoint) {
                $RN452Array = [
                    "dimensionsList" => $RN452Array["dimensions"],
                    "dimensions" => $RN452Array["dimensions"],
                    "totalPoints" => $RN452Array["totalPoints"],
                    "totalItemsOfExcellence" => $RN452Array["totalItemsOfExcellence"],
                ];
            }

            return [
                'status' => 200,
                'message' => "SUCCESS",
                'result' => $RN452Array,
                'typeEvaluation' => $objEvaluation->getType(),
                'type' => $objRN452->getType(),
                'classification' => $objRN452->getClassification(),
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
    public function insert(Evaluation $evaluation, RN452 $RN452, $dimensions)
    {
        try {

            // SELECT * FROM `list_rn_452_requirements_items` ORDER BY INET_ATON(SUBSTRING_INDEX(CONCAT(numericMarkers,'.0.0.0'),'.',4))
            // UPDATE `list_rn_452_requirements_items` SET `numericMarkersOrder` = CONCAT(id + 1) WHERE id > 90

            switch ($RN452->getClassification()) {
                case RN452::CLASSIFICATION_MEDICAL_HOSPITAL:
                    $reqBelongsClassification = $this->entityManager
                        ->getRepository(ListRN452RequirementsItems::class)
                        ->findBy(['active' => true, 'belongsMedicalHospital' => true], ["numericMarkersOrder" => 'ASC']);
                    $indBelongsClassification = $this->entityManager
                        ->getRepository(ListRN452MonitoredIndicators::class)
                        ->findBy(['active' => true, 'belongsMedicalHospital' => true], ['id' => 'ASC']);
                    break;
                case RN452::CLASSIFICATION_DENTAL:
                    $reqBelongsClassification = $this->entityManager
                        ->getRepository(ListRN452RequirementsItems::class)
                        ->findBy(['active' => true, 'belongsDental' => true], ["numericMarkersOrder" => 'ASC']);
                    $indBelongsClassification = $this->entityManager
                        ->getRepository(ListRN452MonitoredIndicators::class)
                        ->findBy(['active' => true, 'belongsDental' => true], ['id' => 'ASC']);
                    break;
                case RN452::CLASSIFICATION_SELF_MANAGEMENT:
                    $reqBelongsClassification = $this->entityManager
                        ->getRepository(ListRN452RequirementsItems::class)
                        ->findBy(['active' => true, 'belongsSelfManagement' => true], ["numericMarkersOrder" => 'ASC']);
                    $indBelongsClassification = $this->entityManager
                        ->getRepository(ListRN452MonitoredIndicators::class)
                        ->findBy(['active' => true, 'belongsSelfManagement' => true], ['id' => 'ASC']);
                    break;
                default:
                    return [
                        'status' => 200,
                        'message' => "SUCCESS",
                        'result' => 'Classificação não informada!',
                    ];
            }

            if ($RN452->getType() !== RN452::TYPE_SELF_EVALUATION) {

                $EvaluationDAO = new EvaluationDAO();

                $arrayEvaluators = $EvaluationDAO->separatedEvaluatorsUsers($evaluation);

                $evaluation->setEvaluator(new \Doctrine\Common\Collections\ArrayCollection());
                $evaluation->setEvaluatorAdmins(new \Doctrine\Common\Collections\ArrayCollection());

                foreach ($arrayEvaluators['evaluators'] as $evaluator){
                    $obj = $this->entityManager->find(Evaluator::class, $evaluator);
                    $evaluation->addEvaluator($obj);
                }

                foreach ($arrayEvaluators['directors'] as $admin){
                    $obj = $this->entityManager->find(Admin::class, $admin);
                    $evaluation->addEvaluatorAdmin($obj);
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

            $RN452->setEvaluation($evaluation);
            $this->entityManager->persist($RN452);
            $this->entityManager->flush();

            // Separação de indicadores que pertencem somente a classificação da solicitação
            foreach ($indBelongsClassification as $indicator){
                if ($RN452->getType() === RN452::TYPE_SUPERVISION) {
                    if (in_array($indicator->getDimension(), $dimensions)) {
                        $rn452MonitoredIndicators = new RN452MonitoredIndicators();
                        $rn452MonitoredIndicators->setRn452($RN452);
                        $rn452MonitoredIndicators->setListOfMonitoredIndicators($indicator);
                        $this->entityManager->persist($rn452MonitoredIndicators);
                        $this->entityManager->flush();
                    }
                } else {
                    $rn452MonitoredIndicators = new RN452MonitoredIndicators();
                    $rn452MonitoredIndicators->setRn452($RN452);
                    $rn452MonitoredIndicators->setListOfMonitoredIndicators($indicator);
                    $this->entityManager->persist($rn452MonitoredIndicators);
                    $this->entityManager->flush();
                }

            }

            // Separação de itens dos requisitos que pertencem somente a classificação da solicitação
            foreach ($reqBelongsClassification as $requirement){
                if ($RN452->getType() === RN452::TYPE_SUPERVISION) {
                    if (in_array($requirement->getRequirement()->getDimension(), $dimensions)) {
                        $rn452RequirementsItems = new RN452RequirementsItems();
                        $rn452RequirementsItems->setRn452($RN452);
                        $rn452RequirementsItems->setListOfItems($requirement);
                        $this->entityManager->persist($rn452RequirementsItems);
                        $this->entityManager->flush();
                    }
                } else {
                    $rn452RequirementsItems = new RN452RequirementsItems();
                    $rn452RequirementsItems->setRn452($RN452);
                    $rn452RequirementsItems->setListOfItems($requirement);
                    $this->entityManager->persist($rn452RequirementsItems);
                    $this->entityManager->flush();
                }
            }

            if ($RN452->getType() !== RN452::TYPE_SELF_EVALUATION) {

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


                // Criar pré-requisitos
                $obj = $this->entityManager
                    ->getRepository(ListRN452Prerequisites::class)
                    ->findBy(['active' => true], ['id' => 'ASC']);

                foreach ($obj as $prerequisitesItem){
                    $prerequisites = new RN452Prerequisites();
                    $prerequisites->setRn452($RN452);
                    $prerequisites->setListOfPrerequisites($prerequisitesItem);
                    $this->entityManager->persist($prerequisites);
                    $this->entityManager->flush();
                }
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
    public function updateMonitoredIndicators(RN452MonitoredIndicators $RN452MonitoredIndicators, $groupId, $userId)
    {
        try {

            $objMon = $this->entityManager->find(RN452MonitoredIndicators::class, $RN452MonitoredIndicators->getId());
            $evaluationObj = $objMon->getRn452()->getEvaluation();

            $logs = new Logs($RN452MonitoredIndicators->getId(), $userId, $groupId);
            $logs->setType(Logs::TYPE_MONITORED_INDICATORS);
            $logs->setEvaluation($evaluationObj->getId());
            $logs->setBeforeChange($objMon->getItHas() === false ? 0 : ($objMon->getItHas() === null ? null : $objMon->getItHas()));
            $logs->setAfterChange($RN452MonitoredIndicators->getItHas());

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

            $objMon->setItHas($RN452MonitoredIndicators->getItHas());
            $this->entityManager->persist($logs);
            $this->entityManager->flush();

            return [
                'status' => 200,
                'message' => "SUCCESS",
                'result' => 'Indicador atualizado!',
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

    public function updateRequirementsItems(RN452RequirementsItems $RN452RequirementsItems, $groupId, $userId, $type)
    {
        try {

            $objMon = $this->entityManager->find(RN452RequirementsItems::class, $RN452RequirementsItems->getId());
            $evaluationObj = $objMon->getRn452()->getEvaluation();

            $logs = new Logs($RN452RequirementsItems->getId(), $userId, $groupId);
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
                $logs->setBeforeChange($objMon->getScope());
                $logs->setAfterChange($RN452RequirementsItems->getScope());
                $logs->setType(Logs::TYPE_REQUIREMENTS_ITEMS_SCOPE);
                $objMon->setScope($RN452RequirementsItems->getScope());
            }

            if ($type === 'DEPLOYMENT_TIME') {
                $logs->setBeforeChange($objMon->getDeploymentTime());
                $logs->setAfterChange($RN452RequirementsItems->getDeploymentTime());
                $logs->setType(Logs::TYPE_REQUIREMENTS_ITEMS_DEPLOYMENT_TIME);
                $objMon->setDeploymentTime($RN452RequirementsItems->getDeploymentTime());
            }

            $this->entityManager->persist($logs);
            $this->entityManager->flush();

            if (!is_null($objMon->getDeploymentTime()) && !is_null($objMon->getScope())) {
                $timeValue = $objMon->getDeploymentTime();
                $scopeValue = $objMon->getScope();

                $objMon->setDegreeOfCompliance($RN452RequirementsItems->calculateDegree($timeValue, $scopeValue));
                $objMon->setPointing($RN452RequirementsItems->calculatePoints($timeValue, $scopeValue));
            }

            if (is_null($objMon->getDeploymentTime()) || is_null($objMon->getScope())) {
                $objMon->setDegreeOfCompliance(null);
                $objMon->setPointing(null);
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

    public function updateRequirementsComments(RN452RequirementsItems $RN452RequirementsItems, $filesNames, $groupId, $userId)
    {
        try {

            $objMon = $this->entityManager->find(RN452RequirementsItems::class, $RN452RequirementsItems->getId());

            $evaluationObj = $objMon->getRn452()->getEvaluation();

            if ($evaluationObj->getStatus() === Evaluation::STATUS_FINISHED && $groupId !== Account::GROUP_ADMIN)
                return [
                    'status' => 400,
                    'message' => "ERROR",
                    'result' => 'Esta avaliação já foi finalizada e não pode ser atualizada!',
                ];

            if (!is_null($RN452RequirementsItems->getComment())){
                if ($objMon->getComment() !== $RN452RequirementsItems->getComment()) {
                    $logsComment = new Logs($RN452RequirementsItems->getId(), $userId, $groupId);
                    $logsComment->setEvaluation($evaluationObj->getId());
                    $logsComment->setType(Logs::TYPE_REQUIREMENTS_ITEMS_COMMENT);
                    $logsComment->setBeforeChange($objMon->getComment());
                    $logsComment->setAfterChange($RN452RequirementsItems->getComment());
                    $this->entityManager->persist($logsComment);
                }
                $objMon->setComment($RN452RequirementsItems->getComment());
            }

            if (!is_null($RN452RequirementsItems->getEvidence())){
                if ($objMon->getEvidence() !== $RN452RequirementsItems->getEvidence()) {
                    $logsEvidence = new Logs($RN452RequirementsItems->getId(), $userId, $groupId);
                    $logsEvidence->setEvaluation($evaluationObj->getId());
                    $logsEvidence->setType(Logs::TYPE_REQUIREMENTS_ITEMS_EVIDENCE);
                    $logsEvidence->setBeforeChange($objMon->getEvidence());
                    $logsEvidence->setAfterChange($RN452RequirementsItems->getEvidence());
                    $this->entityManager->persist($logsEvidence);
                }
                $objMon->setEvidence($RN452RequirementsItems->getEvidence());
            }

            if (!is_null($RN452RequirementsItems->getFeedback())) {
                if ($objMon->getFeedback() !== $RN452RequirementsItems->getFeedback()) {
                    $logsEvidence = new Logs($RN452RequirementsItems->getId(), $userId, $groupId);
                    $logsEvidence->setEvaluation($evaluationObj->getId());
                    $logsEvidence->setType(Logs::TYPE_REQUIREMENTS_ITEMS_FEEDBACK);
                    $logsEvidence->setBeforeChange($objMon->getFeedback());
                    $logsEvidence->setAfterChange($RN452RequirementsItems->getFeedback());
                    $this->entityManager->persist($logsEvidence);
                }
                $objMon->setFeedback($RN452RequirementsItems->getFeedback());
            }

            if (!is_null($RN452RequirementsItems->getChangedPoint())){
                if ($objMon->getChangedPoint() !== $RN452RequirementsItems->getChangedPoint()) {
                    $logsChangedPoint = new Logs($RN452RequirementsItems->getId(), $userId, $groupId);
                    $logsChangedPoint->setEvaluation($evaluationObj->getId());
                    $logsChangedPoint->setType(Logs::TYPE_REQUIREMENTS_ITEMS_EVIDENCE);
                    $logsChangedPoint->setBeforeChange($objMon->getChangedPoint());
                    $logsChangedPoint->setAfterChange($RN452RequirementsItems->getChangedPoint());
                    $this->entityManager->persist($logsChangedPoint);
                }
                $objMon->setChangedPoint($RN452RequirementsItems->getChangedPoint());
            }

            if (!is_null($RN452RequirementsItems->getImprovementOpportunity())) {
                if ($objMon->getImprovementOpportunity() !== $RN452RequirementsItems->getImprovementOpportunity()) {
                    $logsImprovement = new Logs($RN452RequirementsItems->getId(), $userId, $groupId);
                    $logsImprovement->setEvaluation($evaluationObj->getId());
                    $logsImprovement->setType(Logs::TYPE_REQUIREMENTS_ITEMS_IMPROVEMENT_OPPORTUNITY);
                    $logsImprovement->setBeforeChange($objMon->getImprovementOpportunity());
                    $logsImprovement->setAfterChange($RN452RequirementsItems->getImprovementOpportunity());
                    $this->entityManager->persist($logsImprovement);
                }
                $objMon->setImprovementOpportunity($RN452RequirementsItems->getImprovementOpportunity());
            }

            if (!is_null($RN452RequirementsItems->getStrongPoint())) {
                if ($objMon->getStrongPoint() !== $RN452RequirementsItems->getStrongPoint()) {
                    $logsStrongPoint = new Logs($RN452RequirementsItems->getId(), $userId, $groupId);
                    $logsStrongPoint->setEvaluation($evaluationObj->getId());
                    $logsStrongPoint->setType(Logs::TYPE_REQUIREMENTS_ITEMS_STRONG_POINT);
                    $logsStrongPoint->setBeforeChange($objMon->getStrongPoint());
                    $logsStrongPoint->setAfterChange($RN452RequirementsItems->getStrongPoint());
                    $this->entityManager->persist($logsStrongPoint);
                }
                $objMon->setStrongPoint($RN452RequirementsItems->getStrongPoint());
            }

            if (!is_null($RN452RequirementsItems->getNonAttendance())) {
                if ($objMon->getNonAttendance() !== $RN452RequirementsItems->getNonAttendance()) {
                    $logsNonAttendance = new Logs($RN452RequirementsItems->getId(), $userId, $groupId);
                    $logsNonAttendance->setEvaluation($evaluationObj->getId());
                    $logsNonAttendance->setType(Logs::TYPE_REQUIREMENTS_ITEMS_NON_ATTENDANCE);
                    $logsNonAttendance->setBeforeChange($objMon->getNonAttendance());
                    $logsNonAttendance->setAfterChange($RN452RequirementsItems->getNonAttendance());
                    $this->entityManager->persist($logsNonAttendance);
                }
                $objMon->setNonAttendance($RN452RequirementsItems->getNonAttendance());
            }

            if ($objMon->getRn452()->getEvaluation()->getStatus() === Evaluation::STATUS_OPEN) {
                $objMon->getRn452()->getEvaluation()->setStatus(Evaluation::STATUS_STARTED);
                $objMon->getRn452()->getEvaluation()->setStartedDate(new \DateTime());
            }

            $this->entityManager->flush();

            $filesObj = [];
            foreach ($filesNames as $fileItem){
                $file = new RN452RequirementsItemsFiles();
                $file->setName($fileItem);
                $file->setRequirementItem($objMon);
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

            $objFile = $this->entityManager->find(RN452RequirementsItemsFiles::class, $id);
            $evaluationObj = $objFile->getRequirementItem()->getRn452()->getEvaluation();

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

    // Detalhes
    public function listPrerequisites(RN452 $RN452)
    {
        try {

            $objAcc = $this->entityManager->find(RN452::class, $RN452->getId());

            $listPrerequisites = $objAcc->listPrerequisites();

            return [
                'status' => 200,
                'message' => "SUCCESS",
                'result' => $listPrerequisites,
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

    public function updatePrerequisites(RN452Prerequisites $prerequisites)
    {
        try {

            $objAcc = $this->entityManager->find(RN452Prerequisites::class, $prerequisites->getId());
            $objAcc->setItHas($prerequisites->getItHas());
            $this->entityManager->flush();

            return [
                'status' => 200,
                'message' => "SUCCESS",
                'result' => 'Pré requisito atualizado',
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