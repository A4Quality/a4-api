<?php

namespace App\Basics\RN440;

/**
 * @Entity
 * @Table(name="rn_440")
 */
class RN440
{

    const CLASSIFICATION_APS = 1;

    const TYPE_PRE = 1;
    const TYPE_SUPERVISION = 2;
    const TYPE_CERTIFICATION = 3;
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
     * Uma RN440 tem muitos requerimentos
     * @OneToMany(targetEntity="RN440RequirementsItems", mappedBy="rn440")
     */
    private $requirementsItems;

    /**
     * Uma RN440 pertence a uma avaliação.
     * @OneToOne(targetEntity="App\Basics\Evaluation", inversedBy="rn440")
     * @JoinColumn(name="id_evaluation", referencedColumnName="id")
     */
    private $evaluation;

    /**
     * RN440 constructor.
     */
    public function __construct()
    {
        $this->classification = $this::CLASSIFICATION_APS;
    }


    public function convertArray()
    {
        return [
            "id" => $this->id,
            "classification" => $this->classification,
            "type" => $this->type,
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
     * @return RN440
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
     * @return RN440
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
     * @return RN440
     */
    public function setType($type)
    {
        $this->type = $type;
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
     * @return RN440
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
     * @return RN440
     */
    public function setEvaluation($evaluation)
    {
        $this->evaluation = $evaluation;
        return $this;
    }
}