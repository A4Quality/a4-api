<?php

namespace App\Basics\RN452;

/**
 * @Entity
 * @Table(name="rn_452")
 */
class RN452
{

    const CLASSIFICATION_MEDICAL_HOSPITAL = 1;
    const CLASSIFICATION_DENTAL = 2;
    const CLASSIFICATION_SELF_MANAGEMENT = 3;

    const TYPE_PRE = 1;
    const TYPE_SUPERVISION = 2;
    const TYPE_ACCREDITATION = 3;
    const TYPE_SELF_EVALUATION = 4;

    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Column(type="integer")
     */
    private $classification;

    /**
     * @Column(type="integer")
     */
    private $type;

    /**
     * Uma RN452 tem muitos pré-requisitos
     * @OneToMany(targetEntity="RN452Prerequisites", mappedBy="rn452")
     */
    private $prerequisites;

    /**
     * Uma RN452 pode ter muitos indicadores
     * @OneToMany(targetEntity="RN452MonitoredIndicators", mappedBy="rn452")
     */
    private $monitoredIndicators;

    /**
     * Uma RN452 tem muitos requerimentos
     * @OneToMany(targetEntity="RN452RequirementsItems", mappedBy="rn452")
     */
    private $requirementsItems;

    /**
     * Oma RN452 pertence a uma avaliação.
     * @OneToOne(targetEntity="App\Basics\Evaluation", inversedBy="rn452")
     * @JoinColumn(name="id_evaluation", referencedColumnName="id")
     */
    private $evaluation;

    public function convertArray()
    {
        return [
            "id" => $this->id,
            "classification" => $this->classification,
            "type" => $this->type,
            "prerequisites" => $this->listPrerequisites()
        ];
    }

    /**
     * @return array
     */
    public function listPrerequisites()
    {
        if (!$this->prerequisites && count($this->prerequisites) === 0) return [];
        $list = [];

        foreach ($this->prerequisites as $prerequisite) {
            array_push($list, $prerequisite->convertArray());
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
     * @return RN452
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getClassification()
    {
        return $this->classification;
    }

    /**
     * @param mixed $classification
     * @return RN452
     */
    public function setClassification($classification)
    {
        $this->classification = $classification;
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
     * @return RN452
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }


    /**
     * @return mixed
     */
    public function getPrerequisites()
    {
        return $this->prerequisites;
    }

    /**
     * @param mixed $prerequisites
     * @return RN452
     */
    public function setPrerequisites($prerequisites)
    {
        $this->prerequisites = $prerequisites;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMonitoredIndicators()
    {
        return $this->monitoredIndicators;
    }

    /**
     * @param mixed $monitoredIndicators
     * @return RN452
     */
    public function setMonitoredIndicators($monitoredIndicators)
    {
        $this->monitoredIndicators = $monitoredIndicators;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRequirementsItems()
    {
        return $this->requirementsItems;
    }

    /**
     * @param mixed $requirementsItems
     * @return RN452
     */
    public function setRequirementsItems($requirementsItems)
    {
        $this->requirementsItems = $requirementsItems;
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
     * @return RN452
     */
    public function setEvaluation($evaluation)
    {
        $this->evaluation = $evaluation;
        return $this;
    }
}