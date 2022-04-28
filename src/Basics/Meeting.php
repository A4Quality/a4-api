<?php


namespace App\Basics;

/**
 * @Entity
 * @Table(name="meetings")
 */
class Meeting
{

    const TYPE_INITIAL = 1;

    const TYPE_FINAL = 2;

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
     * @Column(type="text", nullable=true)
     */
    private $date;

    /**
     * @Column(type="text", nullable=true)
     */
    private $schedule;

    /**
     * @Column(type="text", nullable=true)
     */
    private $place;

    /**
     * Muitas reuniões pertencem a uma avaliação
     * @ManyToOne(targetEntity="Evaluation", inversedBy="meetings")
     * @JoinColumn(name="id_evaluation", referencedColumnName="id")
     */
    private $evaluation;

    /**
     * Uma Reunião Inicial tem muitos participantes
     * @OneToMany(targetEntity="MeetingParticipants", mappedBy="meeting")
     */
    private $participants;

    /**
     * Meeting constructor.
     * @param $type
     */
    public function __construct($type)
    {
        $this->type = $type;
    }

    public function convertArray()
    {
        return [
            "id" => $this->id,
            "type" => $this->type,
            "date" => $this->date,
            "schedule" => $this->schedule,
            "place" => $this->place,
            "participants" => $this->listParticipants(),
        ];
    }

    public function listParticipants()
    {
        $list = [];
        foreach ($this->participants as $participant) {
            array_push($list, $participant->convertArray());
        }
        return $list;
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
     * @return Meeting
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
     * @return Meeting
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param mixed $date
     * @return Meeting
     */
    public function setDate($date)
    {
        $this->date = $date;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSchedule()
    {
        return $this->schedule;
    }

    /**
     * @param mixed $schedule
     * @return Meeting
     */
    public function setSchedule($schedule)
    {
        $this->schedule = $schedule;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPlace()
    {
        return $this->place;
    }

    /**
     * @param mixed $place
     * @return Meeting
     */
    public function setPlace($place)
    {
        $this->place = $place;
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
     * @return Meeting
     */
    public function setEvaluation($evaluation)
    {
        $this->evaluation = $evaluation;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getParticipants()
    {
        return $this->participants;
    }

    /**
     * @param mixed $participants
     * @return Meeting
     */
    public function setParticipants($participants)
    {
        $this->participants = $participants;
        return $this;
    }
}