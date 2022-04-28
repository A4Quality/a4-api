<?php


namespace App\Basics;

/**
 * @Entity
 * @Table(name="meeting_participants")
 */
class MeetingParticipants
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
    private $name;

    /**
     * @Column(type="text", nullable=true)
     */
    private $occupation;

    /**
     * Muitos participantes pertencem a uma reunião
     * @ManyToOne(targetEntity="Meeting", inversedBy="participants")
     * @JoinColumn(name="id_meeting", referencedColumnName="id")
     */
    private $meeting;

    public function convertArray()
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "occupation" => $this->occupation,
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
     * @return MeetingParticipants
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     * @return MeetingParticipants
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getOccupation()
    {
        return $this->occupation;
    }

    /**
     * @param mixed $occupation
     * @return MeetingParticipants
     */
    public function setOccupation($occupation)
    {
        $this->occupation = $occupation;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMeeting()
    {
        return $this->meeting;
    }

    /**
     * @param mixed $meeting
     * @return MeetingParticipants
     */
    public function setMeeting($meeting)
    {
        $this->meeting = $meeting;
        return $this;
    }
}