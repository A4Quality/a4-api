<?php


namespace App\Basics;

/**
 * @Entity
 * @Table(name="audited_areas")
 */
class AuditedAreas
{

    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Column(type="integer")
     */
    private $dimension;

    /**
     * @Column(type="text", nullable=true)
     */
    private $name;

    /**
     * Muitas áreas auditadas pertencem a uma Avaliação
     * @ManyToOne(targetEntity="Evaluation", inversedBy="auditedAreas")
     * @JoinColumn(name="id_evaluation", referencedColumnName="id")
     */
    private $evaluation;

    public function convertArray()
    {
        return [
            "id" => $this->id,
            "dimension" => $this->dimension,
            "name" => $this->name,
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
     * @return AuditedAreas
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDimension()
    {
        return $this->dimension;
    }

    /**
     * @param mixed $dimension
     * @return AuditedAreas
     */
    public function setDimension($dimension)
    {
        $this->dimension = $dimension;
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
     * @return AuditedAreas
     */
    public function setName($name)
    {
        $this->name = $name;
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
     * @return AuditedAreas
     */
    public function setEvaluation($evaluation)
    {
        $this->evaluation = $evaluation;
        return $this;
    }
}