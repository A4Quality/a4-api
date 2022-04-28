<?php

namespace App\Basics;

/**
 * @Entity
 * @Table(name="control_visualization_dimensions")
 */
class ControlVisualizationDimensions
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
    private $evaluation;

    /**
     * @Column(type="text", nullable=true)
     */
    private $dimension;

    /**
     * @Column(type="text", nullable=true)
     */
    private $requirement;

    /**
     * @Column(type="text")
     */
    private $evaluator;

    /**
     * @Column(type="datetime", nullable=true)
     */
    private $createdDate;

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
            "evaluation" => $this->evaluation,
            "dimension" => $this->dimension,
            "requirement" => $this->requirement,
            "evaluator" => $this->evaluator
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
     * @return ControlVisualizationDimensions
     */
    public function setId($id)
    {
        $this->id = $id;
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
     * @return ControlVisualizationDimensions
     */
    public function setEvaluation($evaluation)
    {
        $this->evaluation = $evaluation;
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
     * @return ControlVisualizationDimensions
     */
    public function setDimension($dimension)
    {
        $this->dimension = $dimension;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRequirement()
    {
        return $this->requirement;
    }

    /**
     * @param mixed $requirement
     * @return ControlVisualizationDimensions
     */
    public function setRequirement($requirement)
    {
        $this->requirement = $requirement;
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
     * @return ControlVisualizationDimensions
     */
    public function setEvaluator($evaluator)
    {
        $this->evaluator = $evaluator;
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
     * @return ControlVisualizationDimensions
     */
    public function setCreatedDate(\DateTime $createdDate): ControlVisualizationDimensions
    {
        $this->createdDate = $createdDate;
        return $this;
    }
}