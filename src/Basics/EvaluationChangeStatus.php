<?php


namespace App\Basics;

/**
 * @Entity
 * @Table(name="evaluations_change_status")
 */
class EvaluationChangeStatus
{

    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Column(type="datetime")
     */
    private $date;

    /**
     * @Column(type="integer")
     */
    private $beforeStatus;

    /**
     * @Column(type="integer")
     */
    private $newStatus;

    /**
     * @Column(type="text")
     */
    private $userId;

    /**
     * Muitas avaliações tem mudanças no status
     * @ManyToOne(targetEntity="Evaluation", inversedBy="evaluationChangeStatus")
     * @JoinColumn(name="id_evaluation", referencedColumnName="id")
     */
    private $evaluation;

    /**
     * Account constructor.
     */
    public function __construct()
    {
        $this->date = new \DateTime();
    }

    public function convertArray()
    {
        return [
            "id" => $this->id,
            "date" => $this->date->format('d/m/Y - H:i:s'),
            "beforeStatus" => $this->beforeStatus,
            "newStatus" => $this->newStatus,
            "userId" => $this->userId,
        ];
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
     * @return EvaluationChangeStatus
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDate(): \DateTime
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     * @return EvaluationChangeStatus
     */
    public function setDate(\DateTime $date): EvaluationChangeStatus
    {
        $this->date = $date;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBeforeStatus()
    {
        return $this->beforeStatus;
    }

    /**
     * @param mixed $beforeStatus
     * @return EvaluationChangeStatus
     */
    public function setBeforeStatus($beforeStatus)
    {
        $this->beforeStatus = $beforeStatus;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNewStatus()
    {
        return $this->newStatus;
    }

    /**
     * @param mixed $newStatus
     * @return EvaluationChangeStatus
     */
    public function setNewStatus($newStatus)
    {
        $this->newStatus = $newStatus;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param mixed $userId
     * @return EvaluationChangeStatus
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEvaluation()
    {
        return $this->evaluation;
    }

    /**
     * @param mixed $evaluation
     * @return EvaluationChangeStatus
     */
    public function setEvaluation($evaluation)
    {
        $this->evaluation = $evaluation;
        return $this;
    }
}