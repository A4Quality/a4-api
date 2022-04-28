<?php


namespace App\Basics;

/**
 * @Entity
 * @Table(name="resume")
 */
class Resume
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Column(type="text", nullable=true)
     */
    private $startDay;

    /**
     * @Column(type="text", nullable=true)
     */
    private $endDay;

    /**
     * @Column(type="text", nullable=true)
     */
    private $month;

    /**
     * @Column(type="text", nullable=true)
     */
    private $year;

    /**
     * @Column(type="boolean", nullable=true)
     */
    private $isFit;

    /**
     * @Column(type="boolean", nullable=true)
     */
    private $isRemote;

    /**
     * @Column(type="text", nullable=true)
     */
    private $level;

    /**
     * @Column(type="text", nullable=true)
     */
    private $customText;


    public function convertArray()
    {
        return [
            "id" => $this->id,
            "startDay" => $this->startDay,
            "endDay" => $this->endDay,
            "month" => $this->month,
            "year" => $this->year,
            "isFit" => $this->isFit,
            "isRemote" => $this->isRemote,
            "level" => $this->level,
            "customText" => $this->customText,
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
     * @return Resume
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getStartDay()
    {
        return $this->startDay;
    }

    /**
     * @param mixed $startDay
     * @return Resume
     */
    public function setStartDay($startDay)
    {
        $this->startDay = $startDay;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEndDay()
    {
        return $this->endDay;
    }

    /**
     * @param mixed $endDay
     * @return Resume
     */
    public function setEndDay($endDay)
    {
        $this->endDay = $endDay;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMonth()
    {
        return $this->month;
    }

    /**
     * @param mixed $month
     * @return Resume
     */
    public function setMonth($month)
    {
        $this->month = $month;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * @param mixed $year
     * @return Resume
     */
    public function setYear($year)
    {
        $this->year = $year;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIsFit()
    {
        return $this->isFit;
    }

    /**
     * @param mixed $isFit
     * @return Resume
     */
    public function setIsFit($isFit)
    {
        $this->isFit = $isFit;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIsRemote()
    {
        return $this->isRemote;
    }

    /**
     * @param mixed $isRemote
     * @return Resume
     */
    public function setIsRemote($isRemote)
    {
        $this->isRemote = $isRemote;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * @param mixed $level
     * @return Resume
     */
    public function setLevel($level)
    {
        $this->level = $level;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCustomText()
    {
        return $this->customText;
    }

    /**
     * @param mixed $customText
     * @return Resume
     */
    public function setCustomText($customText)
    {
        $this->customText = $customText;
        return $this;
    }
}