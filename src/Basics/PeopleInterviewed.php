<?php


namespace App\Basics;

/**
 * @Entity
 * @Table(name="people_interviewed")
 */
class PeopleInterviewed
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
     * Muitas pessoas entrevistadas pertencem a uma avaliação
     * @ManyToOne(targetEntity="Evaluation", inversedBy="peopleInterviewed")
     * @JoinColumn(name="id_evaluation", referencedColumnName="id")
     */
    private $evaluation;

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
     * @return PeopleInterviewed
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
     * @return PeopleInterviewed
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
     * @return PeopleInterviewed
     */
    public function setOccupation($occupation)
    {
        $this->occupation = $occupation;
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
     * @return PeopleInterviewed
     */
    public function setEvaluation($evaluation)
    {
        $this->evaluation = $evaluation;
        return $this;
    }
}