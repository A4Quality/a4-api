<?php


namespace App\Basics;

/**
 * @Entity
 * @Table(name="diary")
 */
class Diary
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
    private $createdDate;

    /**
     * @Column(type="datetime")
     */
    private $startDate;

    /**
     * @Column(type="datetime")
     */
    private $endDate;

    /**
     * @Column(type="text")
     */
    private $title;

    /**
     * @Column(type="text")
     */
    private $evaluator;

    /**
     * @Column(type="integer")
     */
    private $evaluation;

    /**
     * @Column(type="text")
     */
    private $publicId;

    /**
     * Diary constructor.
     */
    public function __construct()
    {
        $this->createdDate = new \DateTime();
    }

    public function convertArray()
    {
        return [
            "id" => $this->id,
            "createdDate" => $this->createdDate->format('H:i:s - d/m/Y'),
            "startDate" => $this->startDate->format('Y-m-d\TH:i:s'),
            "endDate" => $this->endDate->format('Y-m-d\TH:i:s'),
            "startDateOnly" => $this->startDate->format('d/m/Y'),
            "startDateTime" => $this->startDate->format('H:i'),
            "endDateOnly" => $this->endDate->format('d/m/Y'),
            "endDateTime" => $this->endDate->format('H:i'),
            "startDateJson" => $this->startDate->format('d_m_Y'),
            "endDateJson" => $this->endDate->format('d_m_Y'),
            "title" => $this->title,
            "evaluator" => $this->evaluator,
            "evaluation" => $this->evaluation,
            "publicId" => $this->publicId,
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
     * @return Diary
     */
    public function setId($id)
    {
        $this->id = $id;
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
     * @return Diary
     */
    public function setCreatedDate(\DateTime $createdDate): Diary
    {
        $this->createdDate = $createdDate;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @param mixed $startDate
     * @return Diary
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @param mixed $endDate
     * @return Diary
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     * @return Diary
     */
    public function setTitle($title)
    {
        $this->title = $title;
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
     * @return Diary
     */
    public function setEvaluator($evaluator)
    {
        $this->evaluator = $evaluator;
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
     * @return Diary
     */
    public function setEvaluation($evaluation)
    {
        $this->evaluation = $evaluation;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPublicId()
    {
        return $this->publicId;
    }

    /**
     * @param mixed $publicId
     * @return Diary
     */
    public function setPublicId($publicId)
    {
        $this->publicId = $publicId;
        return $this;
    }
}