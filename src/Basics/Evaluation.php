<?php

namespace App\Basics;

/**
 * @Entity
 * @Table(name="evaluations")
 */
class Evaluation
{

    const TYPE_RN_452 = 1;
    const TYPE_RN_440 = 2;

    const STATUS_OPEN = 1;
    const STATUS_STARTED = 2;
    const STATUS_ANALYSIS_AND_DECISION = 3;
    const STATUS_FEEDBACK = 4;
    const STATUS_FINISHED = 5;

    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Column(type="integer")
     */
    private $status;

    /**
     * @Column(type="integer")
     */
    private $type;

    /**
     * @Column(type="text", nullable=true)
     */
    private $leaderApproval;

    /**
     * @Column(type="text", nullable=true)
     */
    private $analysisUser;

    /**
     * @Column(type="datetime", nullable=true)
     */
    private $createdDate;

    /**
     * @Column(type="date", nullable=true)
     */
    private $reportStartDate;

    /**
     * @Column(type="datetime", nullable=true)
     */
    private $startedDate;

    /**
     * @Column(type="date", nullable=true)
     */
    private $reportEndDate;

    /**
     * @Column(type="datetime", nullable=true)
     */
    private $reportValidityStartDate;

    /**
     * @Column(type="date", nullable=true)
     */
    private $reportValidityEndDate;

    /**
     * @Column(type="datetime", nullable=true)
     */
    private $analysisAndDecisionDate;

    /**
     * @Column(type="datetime", nullable=true)
     */
    private $feedbackDate;

    /**
     * @Column(type="datetime", nullable=true)
     */
    private $finishedDate;

    /**
     * @Column(type="text", nullable=true)
     */
    private $companyPort;

    /**
     * @Column(type="text", nullable=true)
     */
    private $companyNumberOfEmployees;

    /**
     * @Column(type="text", nullable=true)
     */
    private $companyNumberOfBeneficiaries;

    /**
     * @Column(type="text", nullable=true)
     */
    private $companyIdss;

    /**
     * Muitos avaliações pertencem a uma empresa
     * @ManyToOne(targetEntity="Company", inversedBy="evaluation")
     * @JoinColumn(name="id_company", referencedColumnName="id")
     */
    private $company;


    /**
     * Muitas avaliações tem muitos avaliadores.
     * @ManyToMany(targetEntity="Evaluator")
     * @JoinTable(
     *      name="evaluation_evaluators",
     *      joinColumns={@JoinColumn(name="id_evaluation", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="id_evaluator", referencedColumnName="id", unique=false)}
     * )
     */
    private $evaluator;

    /**
     * Muitas Avaliações tem muitos avaliadores admins.
     * @ManyToMany(targetEntity="Admin")
     * @JoinTable(
     *      name="evaluation_admins",
     *      joinColumns={@JoinColumn(name="id_evaluation", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="id_admin", referencedColumnName="id", unique=false)}
     * )
     */
    private $evaluatorAdmins;

    /**
     * Muitas Avaliações tem muitos avaliadores da OPS.
     * @ManyToMany(targetEntity="CompanyUser")
     * @JoinTable(
     *      name="evaluation_companies",
     *      joinColumns={@JoinColumn(name="id_evaluation", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="id_company_user", referencedColumnName="id", unique=false)}
     * )
     */
    private $evaluatorCompanyUsers;

    /**
     * Uma avaliação tem mudanças de status
     * @OneToMany(targetEntity="EvaluationChangeStatus", mappedBy="evaluation")
     */
    private $evaluationChangeStatus;

    /**
     * @Column(type="text", nullable=true)
     */
    private $diary;

    /**
     * Uma avaliação possui um resumo.
     * @OneToOne(targetEntity="Resume")
     * @JoinColumn(name="id_resume", referencedColumnName="id")
     */
    private $resume;

    /**
     * Uma Avaliações tem muitas reuniões.
     * @OneToMany(targetEntity="Meeting", mappedBy="evaluation")
     */
    private $meetings;

    /**
     * Uma avaliação tem muitas pessoas entrevistadas.
     * @OneToMany(targetEntity="PeopleInterviewed", mappedBy="evaluation")
     */
    private $peopleInterviewed;

    /**
     * Uma Avaliação tem muitas áreas auditadas.
     * @OneToMany(targetEntity="AuditedAreas", mappedBy="evaluation")
     */
    private $auditedAreas;

    /**
     * Uma avaliação pode possuir um tipo RN452.
     * @OneToOne(targetEntity="App\Basics\RN452\RN452", mappedBy="evaluation")
     */
    private $rn452;

    /**
     * Uma avaliação pode possuir um tipo RN440.
     * @OneToOne(targetEntity="App\Basics\RN440\RN440", mappedBy="evaluation")
     */
    private $rn440;

    /**
     * Evaluation constructor.
     * @param $type
     */
    public function __construct($type)
    {
        $this->createdDate = new \DateTime();
        $this->reportStartDate = new \DateTime();
        $this->status = $this::STATUS_OPEN;
        $this->type = $type;
    }

    public function convertArray($type = Evaluation::TYPE_RN_452)
    {
        return [
            "id" => $this->id,
            "status" => $this->status,
            "statusDetails" => $this->getStatusDetails(),
            "leaderApproval" => $this->leaderApproval,
            "analysisUser" => $this->analysisUser,
            "type" => $this->type,
            "createdDate" => $this->createdDate->format('d/m/Y - H:i:s'),
            "createdDateTimestamp" => $this->createdDate->format('U') . "000",
            "reportStartDate" => $this->reportStartDate ? $this->reportStartDate->format('d/m/Y') : null,
            "reportEndDate" => $this->reportEndDate ? $this->reportEndDate->format('d/m/Y') : null,
            "reportStartDateInput" => $this->reportStartDate ? $this->reportStartDate->format('Y-m-d') : null,
            "reportEndDateInput" => $this->reportEndDate ? $this->reportEndDate->format('Y-m-d') : null,
            "reportValidityStartDate" => $this->reportValidityStartDate ? $this->reportValidityStartDate->format('d/m/Y') : null,
            "reportValidityEndDate" => $this->reportValidityEndDate ? $this->reportValidityEndDate->format('d/m/Y') : null,
            "reportValidityStartDateInput" => $this->reportValidityStartDate ? $this->reportValidityStartDate->format('Y-m-d') : null,
            "reportValidityEndDateInput" => $this->reportValidityEndDate ? $this->reportValidityEndDate->format('Y-m-d') : null,
            "startedDate" => $this->startedDate ? $this->startedDate->format('d/m/Y - H:i:s') : null,
            "analysisAndDecisionDate" => $this->analysisAndDecisionDate ? $this->analysisAndDecisionDate->format('d/m/Y - H:i:s') : null,
            "feedbackDate" => $this->feedbackDate ? $this->feedbackDate->format('d/m/Y - H:i:s') : null,
            "finishedDate" => $this->finishedDate ? $this->finishedDate->format('d/m/Y - H:i:s') : null,
            "company" => $this->company->convertArray(),
            "companyPort" => $this->companyPort,
            "companyNumberOfEmployees" => $this->companyNumberOfEmployees,
            "companyNumberOfBeneficiaries" => $this->companyNumberOfBeneficiaries,
            "companyIdss" => $this->companyIdss,
            // Evaluations Details
            "resume" => $this->resume ?  $this->resume->convertArray() : null,
            "diary" => $this->diary,
            "meetings" => $this->listMeetings(),
            "peopleInterviewed" => $this->listPeopleInterviewed(),
            "auditedAreas" => $this->listAuditedAreas($type),

        ];
    }

    /**
     * @return array
     */
    public function listMeetings()
    {
        if (!$this->meetings && count($this->meetings) === 0) return [];
        $list = [];

        foreach ($this->meetings as $meeting) {
            array_push($list, $meeting->convertArray());
        }
        return $list;
    }


    /**
     * @return array
     */
    public function listPeopleInterviewed()
    {
        if (!$this->peopleInterviewed && count($this->peopleInterviewed) === 0) return [];
        $list = [];

        foreach ($this->peopleInterviewed as $interviewed) {
            array_push($list, $interviewed->convertArray());
        }
        return $list;
    }

    /**
     * @return array
     */
    public function listAuditedAreas($type)
    {
        switch ($type) {
            case Evaluation::TYPE_RN_452:
                $list = ["1" => [], "2" => [], "3" => [], "4" => []];
                break;
            case Evaluation::TYPE_RN_440:
                $list = ["1" => [], "2" => [], "3" => [], "4" => [], "5" => [], "6" => [], "7" => []];
                break;
            default:
                $list = ["1" => [], "2" => [], "3" => [], "4" => []];
        }

        if (!$this->auditedAreas && count($this->auditedAreas) === 0)
            return $list;

        foreach ($this->auditedAreas as $audited) {
            array_push($list[$audited->getDimension()], $audited->convertArray());
        }
        return $list;
    }

    /**
     * @return array
     */
    public function getStatusDetails()
    {
        switch ($this->status){
            case $this::STATUS_OPEN:
                return [
                    'name' => 'Aberta',
                    'time' => $this->createdDate->format('H:i - d/m/Y')
                ];
            case $this::STATUS_STARTED:
                return [
                    'name' => 'Iniciada',
                    'time' => $this->startedDate->format('H:i - d/m/Y')
                ];
            case $this::STATUS_ANALYSIS_AND_DECISION:
                return [
                    'name' => 'Em análise e decisão',
                    'time' => $this->analysisAndDecisionDate->format('H:i - d/m/Y')
                ];
            case $this::STATUS_FEEDBACK:
                return [
                    'name' => 'Em feedback',
                    'time' => $this->feedbackDate->format('H:i - d/m/Y')
                ];
            case $this::STATUS_FINISHED:
                return [
                    'name' => 'Finalizada',
                    'time' => $this->finishedDate ? $this->finishedDate->format('H:i - d/m/Y') : null
                ];
            default:
                return [];
        }
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return Evaluation
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @param int $status
     * @return Evaluation
     */
    public function setStatus(int $status): Evaluation
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     * @return Evaluation
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLeaderApproval()
    {
        return $this->leaderApproval;
    }

    /**
     * @param mixed $leaderApproval
     * @return Evaluation
     */
    public function setLeaderApproval($leaderApproval)
    {
        $this->leaderApproval = $leaderApproval;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAnalysisUser()
    {
        return $this->analysisUser;
    }

    /**
     * @param mixed $analysisUser
     * @return Evaluation
     */
    public function setAnalysisUser($analysisUser)
    {
        $this->analysisUser = $analysisUser;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedDate(): \DateTime
    {
        return $this->createdDate;
    }

    /**
     * @param \DateTime $createdDate
     * @return Evaluation
     */
    public function setCreatedDate(\DateTime $createdDate): Evaluation
    {
        $this->createdDate = $createdDate;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getReportStartDate(): \DateTime
    {
        return $this->reportStartDate;
    }

    /**
     * @param \DateTime $reportStartDate
     * @return Evaluation
     */
    public function setReportStartDate(\DateTime $reportStartDate): Evaluation
    {
        $this->reportStartDate = $reportStartDate;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getReportEndDate()
    {
        return $this->reportEndDate;
    }

    /**
     * @param mixed $reportEndDate
     * @return Evaluation
     */
    public function setReportEndDate($reportEndDate)
    {
        $this->reportEndDate = $reportEndDate;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getReportValidityStartDate()
    {
        return $this->reportValidityStartDate;
    }

    /**
     * @param mixed $reportValidityStartDate
     * @return Evaluation
     */
    public function setReportValidityStartDate($reportValidityStartDate)
    {
        $this->reportValidityStartDate = $reportValidityStartDate;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getReportValidityEndDate()
    {
        return $this->reportValidityEndDate;
    }

    /**
     * @param mixed $reportValidityEndDate
     * @return Evaluation
     */
    public function setReportValidityEndDate($reportValidityEndDate)
    {
        $this->reportValidityEndDate = $reportValidityEndDate;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getStartedDate()
    {
        return $this->startedDate;
    }

    /**
     * @param mixed $startedDate
     * @return Evaluation
     */
    public function setStartedDate($startedDate)
    {
        $this->startedDate = $startedDate;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAnalysisAndDecisionDate()
    {
        return $this->analysisAndDecisionDate;
    }

    /**
     * @param mixed $analysisAndDecisionDate
     * @return Evaluation
     */
    public function setAnalysisAndDecisionDate($analysisAndDecisionDate)
    {
        $this->analysisAndDecisionDate = $analysisAndDecisionDate;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFeedbackDate()
    {
        return $this->feedbackDate;
    }

    /**
     * @param mixed $feedbackDate
     * @return Evaluation
     */
    public function setFeedbackDate($feedbackDate)
    {
        $this->feedbackDate = $feedbackDate;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFinishedDate()
    {
        return $this->finishedDate;
    }

    /**
     * @param mixed $finishedDate
     * @return Evaluation
     */
    public function setFinishedDate($finishedDate)
    {
        $this->finishedDate = $finishedDate;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCompanyPort()
    {
        return $this->companyPort;
    }

    /**
     * @param mixed $companyPort
     * @return Evaluation
     */
    public function setCompanyPort($companyPort)
    {
        $this->companyPort = $companyPort;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCompanyNumberOfEmployees()
    {
        return $this->companyNumberOfEmployees;
    }

    /**
     * @param mixed $companyNumberOfEmployees
     * @return Evaluation
     */
    public function setCompanyNumberOfEmployees($companyNumberOfEmployees)
    {
        $this->companyNumberOfEmployees = $companyNumberOfEmployees;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCompanyNumberOfBeneficiaries()
    {
        return $this->companyNumberOfBeneficiaries;
    }

    /**
     * @param mixed $companyNumberOfBeneficiaries
     * @return Evaluation
     */
    public function setCompanyNumberOfBeneficiaries($companyNumberOfBeneficiaries)
    {
        $this->companyNumberOfBeneficiaries = $companyNumberOfBeneficiaries;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCompanyIdss()
    {
        return $this->companyIdss;
    }

    /**
     * @param mixed $companyIdss
     * @return Evaluation
     */
    public function setCompanyIdss($companyIdss)
    {
        $this->companyIdss = $companyIdss;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @param mixed $company
     * @return Evaluation
     */
    public function setCompany($company)
    {
        $this->company = $company;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEvaluator()
    {
        return $this->evaluator;
    }

    /**
     * @param mixed $evaluator
     * @return Evaluation
     */
    public function setEvaluator($evaluator)
    {
        $this->evaluator = $evaluator;
        return $this;
    }

    /**
     * @param mixed $evaluator
     * @return Evaluation
     */
    public function addEvaluator($evaluator)
    {
        $this->evaluator->add($evaluator);
        return $this;
    }

    /**
     * @param mixed $evaluator
     * @return Evaluation
     */
    public function removeEvaluator($evaluator)
    {
        $this->evaluator->removeElement($evaluator);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEvaluatorAdmins()
    {
        return $this->evaluatorAdmins;
    }

    /**
     * @param mixed $evaluatorAdmins
     * @return Evaluation
     */
    public function setEvaluatorAdmins($evaluatorAdmins)
    {
        $this->evaluatorAdmins = $evaluatorAdmins;
        return $this;
    }

    /**
     * @param mixed $evaluatorAdmin
     * @return Evaluation
     */
    public function addEvaluatorAdmin($evaluatorAdmin)
    {
        $this->evaluatorAdmins->add($evaluatorAdmin);
        return $this;
    }

    /**
     * @param mixed $evaluatorAdmin
     * @return Evaluation
     */
    public function removeEvaluatorAdmin($evaluatorAdmin)
    {
        $this->evaluatorAdmins->removeElement($evaluatorAdmin);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEvaluatorCompanyUsers()
    {
        return $this->evaluatorCompanyUsers;
    }

    /**
     * @param mixed $evaluatorCompanyUsers
     * @return Evaluation
     */
    public function setEvaluatorCompanyUsers($evaluatorCompanyUsers)
    {
        $this->evaluatorCompanyUsers = $evaluatorCompanyUsers;
        return $this;
    }

    /**
     * @param mixed $evaluatorCompanyUsers
     * @return Evaluation
     */
    public function addEvaluatorCompanyUsers($evaluatorCompanyUsers)
    {
        $this->evaluatorCompanyUsers->add($evaluatorCompanyUsers);
        return $this;
    }

    /**
     * @param mixed $evaluatorCompanyUsers
     * @return Evaluation
     */
    public function removeEvaluatorCompanyUsers($evaluatorCompanyUsers)
    {
        $this->evaluatorCompanyUsers->removeElement($evaluatorCompanyUsers);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEvaluationChangeStatus()
    {
        return $this->evaluationChangeStatus;
    }

    /**
     * @param mixed $evaluationChangeStatus
     * @return Evaluation
     */
    public function setEvaluationChangeStatus($evaluationChangeStatus)
    {
        $this->evaluationChangeStatus = $evaluationChangeStatus;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDiary()
    {
        return $this->diary;
    }

    /**
     * @param mixed $diary
     * @return Evaluation
     */
    public function setDiary($diary)
    {
        $this->diary = $diary;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getResume()
    {
        return $this->resume;
    }

    /**
     * @param mixed $resume
     * @return Evaluation
     */
    public function setResume($resume)
    {
        $this->resume = $resume;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMeetings()
    {
        return $this->meetings;
    }

    /**
     * @param mixed $meetings
     * @return Evaluation
     */
    public function setMeetings($meetings)
    {
        $this->meetings = $meetings;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPeopleInterviewed()
    {
        return $this->peopleInterviewed;
    }

    /**
     * @param mixed $peopleInterviewed
     * @return Evaluation
     */
    public function setPeopleInterviewed($peopleInterviewed)
    {
        $this->peopleInterviewed = $peopleInterviewed;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAuditedAreas()
    {
        return $this->auditedAreas;
    }

    /**
     * @param mixed $auditedAreas
     * @return Evaluation
     */
    public function setAuditedAreas($auditedAreas)
    {
        $this->auditedAreas = $auditedAreas;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRn452()
    {
        return $this->rn452;
    }

    /**
     * @param mixed $rn452
     * @return Evaluation
     */
    public function setRn452($rn452)
    {
        $this->rn452 = $rn452;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRn440()
    {
        return $this->rn440;
    }

    /**
     * @param mixed $rn440
     * @return Evaluation
     */
    public function setRn440($rn440)
    {
        $this->rn440 = $rn440;
        return $this;
    }
}