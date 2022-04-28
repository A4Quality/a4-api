<?php

namespace App\DAO;
use App\Basics\Account;
use App\Basics\AuditedAreas;
use App\Basics\CompanyUser;
use App\Basics\Evaluation;
use App\Basics\Logs;
use App\Basics\MeetingParticipants;
use App\Basics\PeopleInterviewed;
use App\Basics\Meeting;
use App\Basics\Resume;
use App\Basics\Admin;
use App\Basics\ControlVisualizationDimensions;
use App\Basics\Diary;
use App\Basics\Evaluator;
use App\Basics\RN440\RN440;
use App\Basics\RN440\RN440RequirementsItems;
use App\Basics\RN440\RN440RequirementsItemsFiles;
use App\Basics\RN452\RN452;
use App\Basics\RN452\RN452MonitoredIndicators;
use App\Basics\RN452\RN452Prerequisites;
use App\Basics\RN452\RN452RequirementsItems;
use App\Basics\RN452\RN452RequirementsItemsFiles;
use App\Config\Doctrine;
use App\Connection\Database;
use Doctrine\ORM\EntityManager;
use PDO;
use PDOException;
use DateTime;

class EvaluationDAO
{

    private EntityManager $entityManager;

    /**
     * EvaluationDAO constructor.
     */
    public function __construct()
    {
        $doctrine = new Doctrine();
        $this->entityManager = $doctrine->getEntityManager();
    }

    /**
     * EvaluationDAO destruct.
     */
    public function __destruct()
    {
        $this->entityManager->getConnection()->close();
    }


    public function prepareReport(Account $account, $userId, Evaluation $evaluation, $isPreview = false)
    {
        try {

            $objAcc = $this->entityManager->find(Evaluation::class, $evaluation->getId());

            if (is_null($objAcc) || empty($objAcc))
                return [
                    'status' => 400,
                    'message' => "WARNING",
                    'result' => 'Avaliação não localizada!',
                ];

            if (!$isPreview && $objAcc->getStatus() !== Evaluation::STATUS_FINISHED)
                return [
                    'status' => 400,
                    'message' => "NOTE_FINISHED",
                    'result' => 'Esta avaliação ainda não está finalizada!',
                ];

            switch ($objAcc->getType()){
                case Evaluation::TYPE_RN_452:
                    $ReportRN452 = new RN452DAO();
                    $rn452 = $objAcc->getRn452();
                    return $ReportRN452->report($account, $userId, $rn452, $isPreview);
                case Evaluation::TYPE_RN_440:
                    $ReportRN440 = new RN440DAO();
                    $rn440 = $objAcc->getRn440();
                    return $ReportRN440->report($account, $userId, $rn440, $isPreview);
                default:
                    return array(
                        'status' => 400,
                        'message' => 'Tipo não informado!'
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

    public function listResume(Evaluation $evaluation)
    {
        try {

            $objAcc = $this->entityManager->find(Evaluation::class, $evaluation->getId());

            $resume = $objAcc->getResume()->convertArray();

            return [
                'status' => 200,
                'message' => "SUCCESS",
                'result' => $resume,
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

    public function updateResume(Resume $resume)
    {
        try {

            $objAcc = $this->entityManager->find(Resume::class, $resume->getId());

            $objAcc->setStartDay($resume->getStartDay());
            $objAcc->setEndDay($resume->getEndDay());
            $objAcc->setMonth($resume->getMonth());
            $objAcc->setYear($resume->getYear());
            $objAcc->setCustomText($resume->getCustomText());
            $objAcc->setIsFit($resume->getIsFit());
            $objAcc->setIsRemote($resume->getIsRemote());
            $objAcc->setLevel($resume->getLevel());
            $this->entityManager->flush();

            return [
                'status' => 200,
                'message' => "SUCCESS",
                'result' => 'Resumo atualizado',
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

    public function updateMeeting(Meeting $meeting, $participants)
    {
        try {

            $objAcc = $this->entityManager->find(Meeting::class, $meeting->getId());

            $objAcc->setDate($meeting->getDate());
            $objAcc->setPlace($meeting->getPlace());
            $objAcc->setSchedule($meeting->getSchedule());
            $this->entityManager->flush();

            foreach ($objAcc->getParticipants() as $participant){
                $this->entityManager->remove($participant);
                $this->entityManager->flush();
            }

            foreach ($participants as $participant){
                $meetingParticipants = new MeetingParticipants();
                $meetingParticipants->setName($participant['name']);
                $meetingParticipants->setOccupation($participant['occupation']);
                $meetingParticipants->setMeeting($objAcc);
                $this->entityManager->persist($meetingParticipants);
                $this->entityManager->flush();
            }

            return [
                'status' => 200,
                'message' => "SUCCESS",
                'result' => 'Resumo atualizado',
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

    public function dateStart(Evaluation $evaluation, $dateStart, $dateEnd, $isValidity)
    {
        try {

            $objAcc = $this->entityManager->find(Evaluation::class, $evaluation->getId());

            if (!$isValidity) {
                $objAcc->setReportStartDate($dateStart);
                $objAcc->setReportEndDate($dateEnd);
            } else {
                $objAcc->setReportValidityStartDate($dateStart);
                $objAcc->setReportValidityEndDate($dateEnd);
            }

            $this->entityManager->flush();

            return [
                'status' => 200,
                'message' => "SUCCESS",
                'result' => 'Data atualizada!',
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

    public function companyFields(Evaluation $evaluation)
    {
        try {

            $objAcc = $this->entityManager->find(Evaluation::class, $evaluation->getId());

            $objAcc->setCompanyPort($evaluation->getCompanyPort());
            $objAcc->setCompanyIdss($evaluation->getCompanyIdss());
            $objAcc->setCompanyNumberOfBeneficiaries($evaluation->getCompanyNumberOfBeneficiaries());
            $objAcc->setCompanyNumberOfEmployees($evaluation->getCompanyNumberOfEmployees());

            $this->entityManager->flush();

            return [
                'status' => 200,
                'message' => "SUCCESS",
                'result' => 'Campos atualizados!',
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

    public function updatePeopleInterviewed($evaluationId, $people)
    {
        try {

            $objAcc = $this->entityManager->find(Evaluation::class, $evaluationId);

            foreach ($objAcc->getPeopleInterviewed() as $person){
                $this->entityManager->remove($person);
                $this->entityManager->flush();
            }

            foreach ($people as $person){
                $peopleInterviewed = new PeopleInterviewed();
                $peopleInterviewed->setName($person['name']);
                $peopleInterviewed->setOccupation($person['occupation']);
                $peopleInterviewed->setEvaluation($objAcc);
                $this->entityManager->persist($peopleInterviewed);
                $this->entityManager->flush();
            }

            return [
                'status' => 200,
                'message' => "SUCCESS",
                'result' => 'Lista de Pessoas entrevistadas atualizada',
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

    public function createAuditedAreas(AuditedAreas $auditedAreas)
    {
        try {

            $objAcc = $this->entityManager->find(Evaluation::class, $auditedAreas->getEvaluation());

            $auditedAreas->setEvaluation($objAcc);
            $this->entityManager->persist($auditedAreas);
            $this->entityManager->flush();

            return [
                'status' => 200,
                'message' => "SUCCESS",
                'result' => [
                    'message' => 'Lista de Pessoas entrevistadas atualizada',
                    'id' => $auditedAreas->getId()
                ]
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

    public function removeAuditedAreas(AuditedAreas $auditedAreas)
    {
        try {
            $objAcc = $this->entityManager->find(AuditedAreas::class, $auditedAreas->getId());
            $this->entityManager->remove($objAcc);
            $this->entityManager->flush();

            return [
                'status' => 200,
                'message' => "SUCCESS",
                'result' => [
                    'message' => 'Lista de Pessoas entrevistadas atualizada',
                    'id' => $auditedAreas->getId()
                ]
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

    public function updateDiary(Diary $diary, $type, $replicate, $evaluators)
    {
        try {

            if ($type === 'delete') {
                $objDiary = $this->entityManager
                    ->getRepository(Diary::class)
                    ->findBy(['publicId' => $diary->getPublicId()], ['id' => 'ASC']);

                foreach ($objDiary as $obj) {
                    $this->entityManager->remove($obj);
                    $this->entityManager->flush();
                }
            }

            if ($type === 'create') {

                $diary->setStartDate(DateTime::createFromFormat('Y-m-d\TH:i:s', $diary->getStartDate()));
                $diary->setEndDate(DateTime::createFromFormat('Y-m-d\TH:i:s', $diary->getEndDate()));

                if ($replicate) {
                    foreach ($evaluators as $participant) {
                        $diaryReplicate = new Diary();
                        $diaryReplicate->setEvaluation($diary->getEvaluation());
                        $diaryReplicate->setEvaluator($participant);
                        $diaryReplicate->setStartDate($diary->getStartDate());
                        $diaryReplicate->setEndDate($diary->getEndDate());
                        $diaryReplicate->setTitle($diary->getTitle());
                        $diaryReplicate->setPublicId($diary->getPublicId());
                        $this->entityManager->persist($diaryReplicate);
                        $this->entityManager->flush();
                    }
                } else {
                    $this->entityManager->persist($diary);
                    $this->entityManager->flush();
                }
            }

            return [
                'status' => 200,
                'message' => "SUCCESS",
                'result' => [
                    'message' =>  'Diário atualizado',
                ]
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

    public function updateEvaluators(Evaluation $evaluation)
    {
        try {

            $objAcc = $this->entityManager->find(Evaluation::class, $evaluation->getId());

            foreach ($objAcc->getEvaluator() as $evaluator){
                $objAcc->removeEvaluator($evaluator);
                $this->entityManager->flush();
            }

            foreach ($objAcc->getEvaluatorAdmins() as $evaluatorAdmin){
                $objAcc->removeEvaluatorAdmin($evaluatorAdmin);
                $this->entityManager->flush();
            }

            $arrayEvaluators = $this->separatedEvaluatorsUsers($evaluation);

            foreach ($arrayEvaluators['evaluators'] as $evaluator){
                $obj = $this->entityManager->find(Evaluator::class, $evaluator);
                $objAcc->addEvaluator($obj);
            }

            foreach ($arrayEvaluators['directors'] as $admin){
                $obj = $this->entityManager->find(Admin::class, $admin);
                $objAcc->addEvaluatorAdmin($obj);
            }

            $objAcc->setLeaderApproval($evaluation->getLeaderApproval());
            $this->entityManager->flush();

            return [
                'status' => 200,
                'message' => "SUCCESS",
                'result' => [
                    'message' =>  'Avaliadores atualizados',
                ]
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

    public function separatedEvaluatorsUsers(Evaluation $evaluation)
    {
        $array = [
            'directors' => [],
            'evaluators' => []
        ];

        foreach ($evaluation->getEvaluator() as $item){
            $explode = explode('_', $item);
            if ($explode[0] === 'd') array_push($array['directors'], $explode[1]);
            if ($explode[0] === 'e') array_push($array['evaluators'], $explode[1]);
        }
        return $array;
    }

    public function updateControlVisualization(ControlVisualizationDimensions $controlVisualizationDimensions, $type)
    {
        try {

            if ($type === 'delete') {
                $objControl = $this->entityManager
                    ->getRepository(ControlVisualizationDimensions::class)
                    ->findBy([
                        'evaluation' => $controlVisualizationDimensions->getEvaluation(),
                        'dimension' => $controlVisualizationDimensions->getDimension(),
                        'requirement' => $controlVisualizationDimensions->getRequirement(),
                        'evaluator' => $controlVisualizationDimensions->getEvaluator(),
                    ], ['id' => 'ASC']);

                $this->entityManager->remove($objControl[0]);
                $this->entityManager->flush();
            }

            if ($type === 'create') {

                $this->entityManager->persist($controlVisualizationDimensions);
                $this->entityManager->flush();
            }

            return [
                'status' => 200,
                'message' => "SUCCESS",
                'result' => [
                    'message' =>  'Controle de visualização atualizado',
                ]
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

    // Aprovações
    public function submitFeedback(Evaluation $evaluation)
    {
        try {

            $objEva = $this->entityManager->find(Evaluation::class, $evaluation->getId());

            switch ($objEva->getType()){
                case Evaluation::TYPE_RN_452:
                    $rnObj = $objEva->getRn452();
                    $SELF_EVALUATION = RN452::TYPE_SELF_EVALUATION;
                    break;

                case Evaluation::TYPE_RN_440:
                    $rnObj = $objEva->getRn440();
                    $SELF_EVALUATION = RN440::TYPE_SELF_EVALUATION;
                    break;
                default:
                    return array(
                        'status' => 400,
                        'message' => 'Tipo não informado!'
                    );
            }

            if ($objEva->getStatus() === Evaluation::STATUS_FINISHED)
                return [
                    'status' => 400,
                    'message' => "ERROR",
                    'result' => 'Esta avaliação já foi finalizada e não pode ser atualizada!',
                ];

            if ($rnObj->getType() === $SELF_EVALUATION) {
                $objEva->setStatus(Evaluation::STATUS_FEEDBACK);
                $objEva->setFeedbackDate(new \DateTime());
                $this->entityManager->flush();

                return [
                    'status' => 200,
                    'message' => "SUCCESS",
                    'result' => 'Status atualizado!',
                ];
            } else {
                return [
                    'status' => 400,
                    'message' => "WARNING",
                    'result' => 'Somente autoavaliação possui esse status!',
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

    public function submitAnalysis(Evaluation $evaluation, Account $account, $userId)
    {
        try {

            $objEva = $this->entityManager->find(Evaluation::class, $evaluation->getId());

            if ($evaluation->getStatus() === Evaluation::STATUS_FINISHED)
                return [
                    'status' => 400,
                    'message' => "ERROR",
                    'result' => 'Esta avaliação já foi finalizada e não pode ser atualizada!',
                ];

            $explode = explode('_', $objEva->getLeaderApproval());
            if ($explode[0] === 'e' &&
                $account->getGroupId() === Account::GROUP_EVALUATOR &&
                intval($explode[1]) === intval($userId) ) {
                $objEva->setStatus(Evaluation::STATUS_ANALYSIS_AND_DECISION);
                $objEva->setAnalysisAndDecisionDate(new \DateTime());
            } else if ($explode[0] === 'd' &&
                $account->getGroupId() === Account::GROUP_ADMIN &&
                intval($explode[1]) === intval($userId) ) {
                $objEva->setStatus(Evaluation::STATUS_ANALYSIS_AND_DECISION);
                $objEva->setAnalysisAndDecisionDate(new \DateTime());
            } else {
                return [
                    'status' => 400,
                    'message' => "WARNING",
                    'result' => 'Você não está vinculado como líder aprovador!',
                ];
            }

            $this->entityManager->flush();
            return [
                'status' => 200,
                'message' => "SUCCESS",
                'result' => 'Status atualizado!',
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

    public function submitFinishedSupervision(Evaluation $evaluation, Account $account, $userId)
    {
        try {

            $objEva = $this->entityManager->find(Evaluation::class, $evaluation->getId());

            switch ($objEva->getType()){
                case Evaluation::TYPE_RN_452:
                    $rnObj = $objEva->getRn452();
                    $TYPE_SUPERVISION = RN452::TYPE_SUPERVISION;
                    break;

                case Evaluation::TYPE_RN_440:
                    $rnObj = $objEva->getRn440();
                    $TYPE_SUPERVISION = RN440::TYPE_SUPERVISION;
                    break;
                default:
                    return array(
                        'status' => 400,
                        'message' => 'Tipo não informado!'
                    );
            }

            if ($objEva->getStatus() === Evaluation::STATUS_FINISHED)
                return [
                    'status' => 400,
                    'message' => "ERROR",
                    'result' => 'Esta avaliação já foi finalizada e não pode ser atualizada!',
                ];

            if ($rnObj->getType() !== $TYPE_SUPERVISION)
                return [
                    'status' => 400,
                    'message' => "ERROR",
                    'result' => 'Esta avaliação precisa ser do tipo supervisão para ser encerrada!',
                ];

            $explode = explode('_', $objEva->getLeaderApproval());
            if ($explode[0] === 'e' &&
                $account->getGroupId() === Account::GROUP_EVALUATOR &&
                intval($explode[1]) === intval($userId) ) {
                $objEva->setStatus(Evaluation::STATUS_FINISHED);
                $objEva->setFinishedDate(new \DateTime());
            } else if ($explode[0] === 'd' &&
                $account->getGroupId() === Account::GROUP_ADMIN &&
                intval($explode[1]) === intval($userId) ) {
                $objEva->setStatus(Evaluation::STATUS_FINISHED);
                $objEva->setFinishedDate(new \DateTime());
            } else {
                return [
                    'status' => 400,
                    'message' => "WARNING",
                    'result' => 'Você não está vinculado como líder aprovador!',
                ];
            }

            $this->entityManager->flush();
            return [
                'status' => 200,
                'message' => "SUCCESS",
                'result' => 'Status atualizado!',
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

    public function submitFinished(Evaluation $evaluation, Account $account, $userId)
    {
        try {

            $objEva = $this->entityManager->find(Evaluation::class, $evaluation->getId());

            switch ($objEva->getType()){
                case Evaluation::TYPE_RN_452:
                    $rnObj = $objEva->getRn452();
                    $TYPE_SELF_EVALUATION = RN452::TYPE_SELF_EVALUATION;
                    break;

                case Evaluation::TYPE_RN_440:
                    $rnObj = $objEva->getRn440();
                    $TYPE_SELF_EVALUATION = RN440::TYPE_SELF_EVALUATION;
                    break;
                default:
                    return array(
                        'status' => 400,
                        'message' => 'Tipo não informado!'
                    );
            }

            if ($rnObj->getType() === $TYPE_SELF_EVALUATION) {
                $users = $objEva->getCompany()->getCompanyUsers();
                foreach ($users as $item){
                    if ($item->getAccount()->getActive()) {
                        $objEva->addEvaluatorCompanyUsers($item);
                    }
                }
            }

            if ($objEva->getStatus() === Evaluation::STATUS_FINISHED)
                return [
                    'status' => 400,
                    'message' => "ERROR",
                    'result' => 'Esta avaliação já foi finalizada e não pode ser atualizada!',
                ];

            $explode = explode('_', $objEva->getLeaderApproval());

            if ($account->getGroupId() === Account::GROUP_COMPANY_USER) {

                $companyUserObj = $this->entityManager->find(
                    CompanyUser::class,
                    $userId
                );

                if ($objEva->getCompany()->getId() === $companyUserObj->getCompany()->getId() &&
                    $rnObj->getType() === $TYPE_SELF_EVALUATION &&
                    $explode[0] === 'c' && intval($explode[1]) === intval($userId)
                ) {
                    $objEva->setStatus(Evaluation::STATUS_FINISHED);
                    $objEva->setFinishedDate(new \DateTime());
                    $this->entityManager->flush();
                } else {
                    return [
                        'status' => 400,
                        'message' => "ERROR",
                        'result' => 'Você não tem permissão para finalizar essa avaliação!',
                    ];
                }

            } else {
                $objEva->setStatus(Evaluation::STATUS_FINISHED);
                $objEva->setFinishedDate(new \DateTime());
                $this->entityManager->flush();
            }

            return [
                'status' => 200,
                'message' => "SUCCESS",
                'result' => 'Status atualizado!',
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

    public function populate(Evaluation $evaluation)
    {
        try {

            $objEva = $this->entityManager->find(Evaluation::class, $evaluation->getId());

            if ($objEva->getStatus() === Evaluation::STATUS_OPEN) {
                $objEva->setStatus(Evaluation::STATUS_STARTED);
                $objEva->setStartedDate(new \DateTime());
            }

            switch ($objEva->getType()){
                case Evaluation::TYPE_RN_452:
                    $rnObj = $objEva->getRn452();
                    $objReq = $this->entityManager
                        ->getRepository(RN452RequirementsItems::class)
                        ->findBy(['rn452' => $rnObj->getId()]);

                    $objMon = $this->entityManager
                        ->getRepository(RN452MonitoredIndicators::class)
                        ->findBy(['rn452' => $rnObj->getId()]);

                    foreach ($objMon as $key => $item) {
                        if ($key % 2 == 0) {
                            $item->setItHas(0);
                        } else {
                            $item->setItHas(1);
                        }
                    }
                    break;

                case Evaluation::TYPE_RN_440:
                    $rnObj = $objEva->getRn440();
                    $objReq = $this->entityManager
                        ->getRepository(RN440RequirementsItems::class)
                        ->findBy(['rn452' => $rnObj->getId()]);
                    break;
                default:
                    return array(
                        'status' => 400,
                        'message' => 'Tipo não informado!'
                    );
            }

            foreach ($objReq as $key => $item) {
                if ($key % 2 == 0) {
                    $item->setScope(1);
                    $item->setDeploymentTime(13);
                    $item->setDegreeOfCompliance(1);
                    $item->setPointing(1);
                } else {
                    $item->setScope(1);
                    $item->setDeploymentTime(6);
                    $item->setDegreeOfCompliance(0);
                    $item->setPointing(0);
                }
            }

            $this->entityManager->flush();

            return [
                'status' => 200,
                'message' => "SUCCESS",
                'result' => 'Avaliação populada!',
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

    public function deleteEvaluation(Evaluation $evaluation, Account $account)
    {
        try {

            $objAccount = $this->entityManager->find(Account::class, $account->getId());

            if ($objAccount->getPass() !== strtoupper(sha1($account->getPass()))) {
                return array(
                    'status' => 400,
                    'message' => 'Senha não confere!'
                );
            }

            $objEva = $this->entityManager->find(Evaluation::class, $evaluation->getId());

            if (empty($objEva)) {
                return array(
                    'status' => 400,
                    'message' => 'Avaliação não encontrada!'
                );
            }

            // ControlVisualizationDimensions
            $objControlVis = $this->entityManager
                ->getRepository(ControlVisualizationDimensions::class)
                ->findBy(['evaluation' => $objEva->getId()]);
            if (!empty($objControlVis)) {
                foreach ($objControlVis as $item) {
                    $this->entityManager->remove($item);
                }
            }

            // Resume
            if ($objEva->getResume()) {
                $objResume = $this->entityManager->find(Resume::class, $objEva->getResume());
                if (!empty($objResume)) {
                    $this->entityManager->remove($objResume);
                }
            }

            // Diary
            $objDiary = $this->entityManager
                ->getRepository(Diary::class)
                ->findBy(['evaluation' => $objEva->getId()]);
            if (!empty($objDiary)) {
                foreach ($objDiary as $item) {
                    $this->entityManager->remove($item);
                }
            }

            // AuditedAreas
            $objAudited = $this->entityManager
                ->getRepository(AuditedAreas::class)
                ->findBy(['evaluation' => $objEva->getId()]);
            if (!empty($objAudited)) {
                foreach ($objAudited as $item) {
                    $this->entityManager->remove($item);
                }
            }

            // Logs
            $objLogs = $this->entityManager
                ->getRepository(Logs::class)
                ->findBy(['evaluation' => $objEva->getId()]);
            if (!empty($objLogs)) {
                foreach ($objLogs as $item) {
                    $this->entityManager->remove($item);
                }
            }

            // PeopleInterviewed
            $objPeopleInterviewed = $this->entityManager
                ->getRepository(PeopleInterviewed::class)
                ->findBy(['evaluation' => $objEva->getId()]);
            if (!empty($objPeopleInterviewed)) {
                foreach ($objPeopleInterviewed as $item) {
                    $this->entityManager->remove($item);
                }
            }

            // Meeting
            $objMeeting = $this->entityManager
                ->getRepository(Meeting::class)
                ->findBy(['evaluation' => $objEva->getId()]);
            if (!empty($objMeeting)) {
                foreach ($objMeeting as $item) {

                    $objParticipants = $this->entityManager
                        ->getRepository(MeetingParticipants::class)
                        ->findBy(['meeting' => $item->getId()]);

                    if (!empty($objParticipants)) {
                        foreach ($objParticipants as $participant) {
                            $this->entityManager->remove($participant);
                        }
                    }
                    $this->entityManager->remove($item);
                }
            }


            // EvaluationsAdmins - Companies - Evaluators
            $conn = Database::conexao();
            $sql1 = "DELETE FROM `evaluation_admins` WHERE id_evaluation = ".$objEva->getId();
            $stmt1 = $conn->prepare($sql1);
            $stmt1->execute();

            $sql2 = "DELETE FROM `evaluation_companies` WHERE id_evaluation = ".$objEva->getId();
            $stmt2 = $conn->prepare($sql2);
            $stmt2->execute();

            $sql3 = "DELETE FROM `evaluation_evaluators` WHERE id_evaluation = ".$objEva->getId();
            $stmt3 = $conn->prepare($sql3);
            $stmt3->execute();

            switch ($objEva->getType()) {
                case Evaluation::TYPE_RN_452:
                    return $this->deleteRN452($evaluation);
                case Evaluation::TYPE_RN_440:
                    return $this->deleteRN440($evaluation);
                default:
                    return array(
                        'status' => 400,
                        'message' => 'Tipo não encontrado!'
                    );
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

    public function deleteRN452(Evaluation $evaluation)
    {
        try {

            $objEva = $this->entityManager->find(Evaluation::class, $evaluation->getId());

            $rnObj = $objEva->getRn452();

            $objMon = $this->entityManager
                ->getRepository(RN452MonitoredIndicators::class)
                ->findBy(['rn452' => $rnObj->getId()]);
            if (!empty($objMon))  {
                foreach ($objMon as $item) {
                    $this->entityManager->remove($item);
                }
            }

            $objPre = $this->entityManager
                ->getRepository(RN452Prerequisites::class)
                ->findBy(['rn452' => $rnObj->getId()]);
            if (!empty($objPre)) {
                foreach ($objPre as $item) {
                    $this->entityManager->remove($item);
                }
            }

            $objReq = $this->entityManager
                ->getRepository(RN452RequirementsItems::class)
                ->findBy(['rn452' => $rnObj->getId()]);
            if (!empty($objReq)) {
                foreach ($objReq as $item) {

                    $objFiles = $this->entityManager
                        ->getRepository(RN452RequirementsItemsFiles::class)
                        ->findBy(['requirementItem' => $item->getId()]);

                    if (!empty($objFiles)) {
                        foreach ($objFiles as $file) {
                            $this->entityManager->remove($file);
                        }
                    }

                    $this->entityManager->remove($item);
                }
            }

            $this->entityManager->remove($rnObj);
            $this->entityManager->remove($objEva);

            $this->entityManager->flush();

            return [
                'status' => 200,
                'message' => "SUCCESS",
                'result' => 'Avaliação revomida!',
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

    public function deleteRN440(Evaluation $evaluation)
    {
        try {

            $objEva = $this->entityManager->find(Evaluation::class, $evaluation->getId());

            $rnObj = $objEva->getRn440();

            $objReq = $this->entityManager
                ->getRepository(RN440RequirementsItems::class)
                ->findBy(['rn440' => $rnObj->getId()]);
            if (!empty($objReq)) {
                foreach ($objReq as $item) {

                    $objFiles = $this->entityManager
                        ->getRepository(RN440RequirementsItemsFiles::class)
                        ->findBy(['requirementItem' => $item->getId()]);

                    if (!empty($objFiles)) {
                        foreach ($objFiles as $file) {
                            $this->entityManager->remove($file);
                        }
                    }

                    $this->entityManager->remove($item);
                }
            }

            $this->entityManager->remove($rnObj);
            $this->entityManager->remove($objEva);

            $this->entityManager->flush();

            return [
                'status' => 200,
                'message' => "SUCCESS",
                'result' => 'Avaliação revomida!',
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