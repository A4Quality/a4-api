<?php


namespace App\Basics;

/**
 * @Entity
 * @Table(name="logs")
 */
class Logs
{

    const TYPE_MONITORED_INDICATORS = 1;

    const TYPE_REQUIREMENTS_ITEMS_SCOPE = 2;

    const TYPE_REQUIREMENTS_ITEMS_DEPLOYMENT_TIME = 3;

    const TYPE_REQUIREMENTS_ITEMS_COMMENT = 4;

    const TYPE_REQUIREMENTS_ITEMS_EVIDENCE = 5;

    const TYPE_REQUIREMENTS_ITEMS_FEEDBACK = 6;

    const TYPE_REQUIREMENTS_ITEMS_CHANGED_POINT = 7;

    const TYPE_REQUIREMENTS_ITEMS_IMPROVEMENT_OPPORTUNITY = 8;

    const TYPE_REQUIREMENTS_ITEMS_STRONG_POINT = 9;

    const TYPE_REQUIREMENTS_ITEMS_NON_ATTENDANCE = 10;

    const TYPE_PRE_REQUISITES = 11;

    const TYPE_RESUME = 12;

    const TYPE_MEETING = 13;

    const TYPE_PEOPLE_INTERVIEWED = 14;

    const TYPE_AUDITED_AREAS = 15;

    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Column(type="integer")
     */
    private $type;

    /**
     * @Column(type="datetime")
     */
    private $createdDate;

    /**
     * @Column(type="text", nullable=true)
     */
    private $beforeChange;

    /**
     * @Column(type="text", nullable=true)
     */
    private $afterChange;

    /**
     * @Column(type="text")
     */
    private $idType;

    /**
     * @Column(type="text", nullable=true)
     */
    private $msg;

    /**
     * @Column(type="text")
     */
    private $userId;

    /**
     * @Column(type="text")
     */
    private $groupId;

    /**
     * @Column(type="integer")
     */
    private $evaluation;

    /**
     * Logs constructor.
     * @param $idType
     * @param $userId
     * @param $groupId
     */
    public function __construct($idType, $userId, $groupId)
    {
        $this->createdDate = new \DateTime();
        $this->idType = $idType;
        $this->userId = $userId;
        $this->groupId = $groupId;
    }

    public function convertArray()
    {
        return [
            "id" => $this->id,
            "type" => $this->type,
            "createdDate" => $this->createdDate->format('H:i:s - d/m/Y'),
            "beforeChange" => $this->beforeChange,
            "afterChange" => $this->afterChange,
            "idType" => $this->idType,
            "msg" => $this->msg,
            "userId" => $this->userId,
            "groupId" => $this->groupId,
            "evaluation" => $this->evaluation
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
     * @return Logs
     */
    public function setId($id)
    {
        $this->id = $id;
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
     * @return Logs
     */
    public function setType($type)
    {
        $this->type = $type;
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
     * @return Logs
     */
    public function setCreatedDate(\DateTime $createdDate): Logs
    {
        $this->createdDate = $createdDate;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBeforeChange()
    {
        return $this->beforeChange;
    }

    /**
     * @param mixed $beforeChange
     * @return Logs
     */
    public function setBeforeChange($beforeChange)
    {
        $this->beforeChange = $beforeChange;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAfterChange()
    {
        return $this->afterChange;
    }

    /**
     * @param mixed $afterChange
     * @return Logs
     */
    public function setAfterChange($afterChange)
    {
        $this->afterChange = $afterChange;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIdType()
    {
        return $this->idType;
    }

    /**
     * @param mixed $idType
     * @return Logs
     */
    public function setIdType($idType)
    {
        $this->idType = $idType;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMsg()
    {
        return $this->msg;
    }

    /**
     * @param mixed $msg
     * @return Logs
     */
    public function setMsg($msg)
    {
        $this->msg = $msg;
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
     * @return Logs
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getGroupId()
    {
        return $this->groupId;
    }

    /**
     * @param mixed $groupId
     * @return Logs
     */
    public function setGroupId($groupId)
    {
        $this->groupId = $groupId;
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
     * @return Logs
     */
    public function setEvaluation($evaluation)
    {
        $this->evaluation = $evaluation;
        return $this;
    }
}