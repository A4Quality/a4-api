<?php

namespace App\DAO;
use App\Basics\Evaluation;
use App\Basics\RN440\Lists\ListRN440RequirementsItems;
use App\Basics\RN452\Lists\ListRN452RequirementsItems;
use App\Basics\RN452\RN452;
use App\Basics\Company;
use App\Basics\CompanyUser;
use App\Basics\RN452\RN452RequirementsItems;
use App\Config\Doctrine;
use App\Connection\Database;
use App\Utils\WorkOut;
use Doctrine\ORM\EntityManager;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PDO;
use PDOException;

class GraphicsDAO
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

    public function onlyDimension($typeEvaluation, $classification, $type, CompanyUser $companyUser)
    {
        try {

            $companyUserObj = $this->entityManager->find(
                CompanyUser::class,
                $companyUser->getId()
            );

            $companyId = $companyUserObj->getCompany()->getId();

            $allAccObj = $this->entityManager
                ->getRepository(Evaluation::class)
                ->findBy([
                    'type' => $typeEvaluation,
                    'status' => Evaluation::STATUS_FINISHED
                ], ['createdDate' => 'DESC']);

            $allCompany = [];
            $allAcc = [];

            $arrayDefault = [
                "dimensionScore" => 0,
                "requirementsSum" => [],
            ];

            $totalDimensionScore = [
                "1" => $arrayDefault,
                "2" => $arrayDefault,
                "3" => $arrayDefault,
                "4" => $arrayDefault
            ];
            $myDimensionScore = [
                "1" => $arrayDefault,
                "2" => $arrayDefault,
                "3" => $arrayDefault,
                "4" => $arrayDefault
            ];

            if (empty($allAccObj)) {
                return [
                    'status' => 200,
                    'message' => "SUCCESS",
                    'result' => [
                        'media'=> $totalDimensionScore,
                        'my'=> $myDimensionScore,
                    ],
                ];
            }

            switch ($typeEvaluation) {
                case Evaluation::TYPE_RN_452:
                    foreach ($allAccObj as $evaluation) {
                        $id = $evaluation->getCompany()->getId();
                        if (!in_array($id, $allCompany)) {
                            array_push($allCompany, $id);

                            $RN452DAO = new RN452DAO();
                            $details = $RN452DAO->listRequirements($evaluation->getRn452(), true, true);
                            array_push($allAcc, $details['result']);

                            foreach ($details['result']['dimensions'] as $dimension => $elm) {
                                $totalDimensionScore[$dimension]['dimensionScore'] += $elm['dimensionScore'];
                                if ($companyId === $id) $myDimensionScore[$dimension]['dimensionScore'] = $elm['dimensionScore'];

                            }
                        }
                    }

                    foreach ($totalDimensionScore as $dimension => $elm) {
                        $totalDimensionScore[$dimension]['dimensionScore'] = $totalDimensionScore[$dimension]['dimensionScore'] / sizeof($allAcc);
                    }

                    $calc = [
                        'media' => $totalDimensionScore,
                        'my' => $myDimensionScore,
                    ];

//                    $pool = Pool::create();
//                    foreach ($allAccObj as $acc) {
//                        if (!in_array($acc->getCompany()->getId(), $allCompany)) {
//                            array_push($allCompany, $acc->getCompany()->getId());
//
//                            $pool[] = async(function () use ($acc) {
//                                $accreditation = new Accreditation();
//                                $accreditationDAO = new AccreditationDAO();
//                                $accreditation->setId($acc->getId());
//                                return $accreditationDAO->listRequirements($accreditation, true, true);
//                            })->then(function ($details) use ($allAcc) {
//                                array_push($allAcc, $details['result']);
//                            });
//                        }
//                    }
//                    await($pool);

                    return [
                        'status' => 200,
                        'message' => "SUCCESS",
                        'result' => $calc,
                    ];
                case Evaluation::TYPE_RN_440:
                    return [
                        'status' => 200,
                        'message' => "SUCCESS",
                        'result' => [],
                    ];
                default:
                    return [
                        'status' => 400,
                        'message' => "SUCCESS",
                        'result' => [],
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

    public function homeRN452()
    {
        try {

            $allAccObj = $this->entityManager
                ->getRepository(Company::class)
                ->findBy([], []);

            $companies = sizeof($allAccObj);

            $allTypes = [
                RN452::TYPE_PRE => 0,
                RN452::TYPE_SUPERVISION => 0,
                RN452::TYPE_ACCREDITATION => 0,
                RN452::TYPE_SELF_EVALUATION => 0
            ];

            $allAccObj = $this->entityManager
                ->getRepository(Evaluation::class)
                ->findBy(['type' => Evaluation::TYPE_RN_452], ['createdDate' => 'DESC']);

            foreach ($allAccObj as $acc) {
                $allTypes[$acc->getRn452()->getType()]++;
            }

            $workOut = new WorkOut();
            $lastMonths = $workOut->get12Months();

            $allCounts = [
                RN452::TYPE_PRE => [],
                RN452::TYPE_SUPERVISION => [],
                RN452::TYPE_ACCREDITATION => [],
                RN452::TYPE_SELF_EVALUATION => [],
            ];

            $allClients = [];

            $conn = Database::conexao();
            foreach ($allCounts as $key => $type) {
                foreach ($lastMonths as $month) {

                    $sql = "SELECT count(ev.id) as soma from evaluations ev
                            INNER JOIN rn_452 rn
                            ON ev.id = rn.id_evaluation
                            where ev.createdDate BETWEEN 
                            ('".$month['firstDay']."') and
                            ('".$month['lastDay']."') and 
                            rn.type = ".$key.";";

                    $stmt = $conn->prepare($sql);
                    $stmt->execute();
                    $res = $stmt->fetch(PDO::FETCH_OBJ);

                    $temp = [
                        'sum' => $res->soma,
                        'month' => $month['month'],
                        'firstDay' => $month['firstDay'],
                        'lastDay' => $month['lastDay'],
                    ];

                    array_push($allCounts[$key], $temp);
                }
            }

            foreach ($lastMonths as $month) {

                $sql = "SELECT count(id) as soma from companies 
                            where createdDate BETWEEN 
                            ('".$month['firstDay']."') and
                            ('".$month['lastDay']."');";

                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $res = $stmt->fetch(PDO::FETCH_OBJ);

                $temp = [
                    'sum' => $res->soma,
                    'month' => $month['month'],
                ];

                array_push($allClients, $temp);
            }

            return [
                'status' => 200,
                'message' => "SUCCESS",
                'result' => [
                    'companies' => $companies,
                    'allClients' => $allClients,
                    'allTypes' => $allTypes,
                    'allCounts' => $allCounts
                ],
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

    public function exportReport($choiceCompanies, RN452 $RN452)
    {
        try {

            $allRNObjList = $this->entityManager
                ->getRepository(RN452::class)
                ->findBy([
                    'type' => $RN452->getType(),
                ], ['id' => 'DESC']);

//            $allRNObjList = [];
//            foreach ($allAccObj as $obj) {
//                if ($obj->getEvaluation()->getStatus() === Evaluation::STATUS_FINISHED) {
//                    array_push($allRNObjList, $obj);
//                }
//            }

            $listCaptureCompanies = [];
            $allAcc = [];

            $RN452DAO = new RN452DAO();

            $OPS_LEGEND = [];
            $rowLegend = 1;

            $arrayDefault = [
                "dimensionScore" => 0,
                "items" => [],
            ];
            $totalDimensions = [
                "1" => $arrayDefault,
                "2" => $arrayDefault,
                "3" => $arrayDefault,
                "4" => $arrayDefault
            ];
            $cellOPS = 'A';
            $type = false;
            $marker = false;

            foreach ($allRNObjList as $rn) {
                $id = $rn->getEvaluation()->getCompany()->getId();
                $name = $rn->getEvaluation()->getCompany()->getName();
                if (!in_array($id, $listCaptureCompanies) && in_array($id, $choiceCompanies)) {

                    $RN452->setId($rn->getId());
                    $details = $RN452DAO->listRequirements($RN452, true, true);
                    array_push($allAcc, $details['result']);
                    array_push($listCaptureCompanies, $id);
                    array_push($OPS_LEGEND, [
                        'cell' => 'A'.($rowLegend),
                        'text' => $name,
                    ], [
                        'cell' => 'B'.($rowLegend),
                        'text' => 'OPS-'.($rowLegend),
                    ]);

                    $row = 2;

                    if (!$type) {
                        foreach ($details['result']['dimensions'] as $dimension => $elm) {
                            foreach ($elm['requirements'] as $requirement => $elmReq) {
                                array_push($totalDimensions[$dimension]['items'], [
                                    'cell' => $cellOPS.$row,
                                    'text' => '',
                                ]);
                                $row++;
                                foreach ($elmReq['table'] as $itemTable) {

                                    array_push($totalDimensions[$dimension]['items'], [
                                        'cell' => $cellOPS.$row,
                                        'text' => ListRN452RequirementsItems::getTypeName($itemTable['type'])
                                    ]);
                                    $row++;
                                }
                                $row++;
                                $row++;
                                $row++;
                            }
                            $row = 2;
                        }
                        $cellOPS++;
                        $row = 2;
                        $type = true;
                    }

                    if (!$marker) {
                        foreach ($details['result']['dimensions'] as $dimension => $elm) {
                            foreach ($elm['requirements'] as $requirement => $elmReq) {
                                array_push($totalDimensions[$dimension]['items'], [
                                    'cell' => $cellOPS.$row,
                                    'text' => 'ITEM',
                                ]);
                                $row++;
                                foreach ($elmReq['table'] as $itemTable) {
                                    array_push($totalDimensions[$dimension]['items'], [
                                        'cell' => $cellOPS.$row,
                                        'text' => $itemTable['marker'],
                                    ]);
                                    $row++;
                                }

                                array_push($totalDimensions[$dimension]['items'], [
                                    'cell' => $cellOPS.$row,
                                    'text' => 'Pontuação Parcial',
                                ]);
                                $row++;

                                array_push($totalDimensions[$dimension]['items'], [
                                    'cell' => $cellOPS.$row,
                                    'text' => 'Pontuação  Final',
                                ]);

                                $row++;
                                $row++;
                            }
                            $row = 2;
                        }
                        $cellOPS++;
                        $row = 2;
                        $marker = true;
                    }

                    foreach ($details['result']['dimensions'] as $dimension => $elm) {
                        foreach ($elm['requirements'] as $requirement => $elmReq) {
                            array_push($totalDimensions[$dimension]['items'], [
                                'cell' => $cellOPS.$row,
                                'text' => 'OPS-'.($rowLegend),
                            ]);
                            $row++;
                            foreach ($elmReq['table'] as $itemTable) {
                                array_push($totalDimensions[$dimension]['items'], [
                                    'cell' => $cellOPS.$row,
                                    'text' => $itemTable['pointing'],
                                ]);
                                $row++;
                            }

                            array_push($totalDimensions[$dimension]['items'], [
                                'cell' => $cellOPS.$row,
                                'text' => $elmReq['sumPoints']['sumPartial'],
                            ]);
                            $row++;

                            array_push($totalDimensions[$dimension]['items'], [
                                'cell' => $cellOPS.$row,
                                'text' => $elmReq['sumPoints']['sumTotal'],
                            ]);

                            $row++;
                            $row++;
                        }
                        $row = 2;
                    }
                    $cellOPS++;

                    $rowLegend++;
                }
            }

            $spreadsheet = new Spreadsheet();

            $spreadsheet->setActiveSheetIndex(0);
            $spreadsheet->getActiveSheet()->setTitle('Operadoras');

            foreach ($OPS_LEGEND as $ops) {
                $spreadsheet
                    ->getActiveSheet()
                    ->getCell($ops['cell'])
                    ->setValue($ops['text']);
            }

            for ($x = 1; $x <= 4; $x++) {
                $myWorkSheet = new Worksheet($spreadsheet, 'Dimensão '.$x);
                $spreadsheet->addSheet($myWorkSheet, $x);

                $spreadsheet->setActiveSheetIndex($x);

                foreach ($totalDimensions[$x]['items'] as $cell) {
                    $spreadsheet
                        ->getActiveSheet()
                        ->getCell($cell['cell'])
                        ->setValue($cell['text']);
                }
            }

            $writer = new Xlsx($spreadsheet);

            $excelFileName = __DIR__ . '/file.xlsx';
            $writer->save($excelFileName);
            $get = file_get_contents($excelFileName);
            $base64 = base64_encode($get);
            unlink( $excelFileName );

            return [
                'status' => 200,
                'message' => "SUCCESS",
                'result' => [
//                    'allAcc' => $allAcc,
//                    '$totalDimensions' => $totalDimensions,
                    'base' => $base64
                ],
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

    public function custom($choiceCompanies, $type, $start, $end)
    {
        try {

            $conn = Database::conexao();
            $sql = "SELECT id, id_company from accreditations 
                            where createdDate BETWEEN 
                            ('".$start."') and
                            ('".$end."') and type = ".$type."
                            and id_company IN (".implode(', ', $choiceCompanies).")
                            ORDER BY createdDate DESC;";

            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $ignoreCompany = [];
            $acc = [];

            foreach ($res as $accTemp) {
                if (!in_array($accTemp['id_company'], $ignoreCompany)) {
                    array_push($acc, $accTemp['id']);
                    array_push($ignoreCompany, $accTemp['id_company']);
                }
            }

            $accreditationDAO = new AccreditationDAO();
            $details = [];
            foreach ($acc as $id) {
                $accreditation = new Accreditation();
                $accreditation->setId($id);

                $temp = $accreditationDAO->listRequirements($accreditation, true, true);
                array_push($details, [
                    "createdDate" => $temp['createdDate'],
                    "company" => $temp['company']->convertArray(),
                    "dimensions" => $temp['result']['dimensions'],
                ]);
            }

            return [
                'status' => 200,
                'message' => "SUCCESS",
                'result' => $details,
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