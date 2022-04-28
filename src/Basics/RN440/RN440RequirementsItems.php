<?php


namespace App\Basics\RN440;

/**
 * @Entity
 * @Table(name="rn_440_requeriments_items")
 */
class RN440RequirementsItems
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Column(type="boolean", nullable=true)
     */
    private $degreeOfCompliance;

    /**
     * @Column(type="integer", nullable=true)
     */
    private $pointing;

    /**
     * @Column(type="boolean", nullable=true)
     */
    private $scope;

    /**
     * @Column(type="integer", nullable=true)
     */
    private $deploymentTime;

    /**
     * @Column(type="text", nullable=true)
     */
    private $comment;

    /**
     * @Column(type="text", nullable=true)
     */
    private $evidence;

    /**
     * @Column(type="text", nullable=true)
     */
    private $feedback;

    /**
     * @Column(type="boolean", nullable=true)
     */
    private $changedPoint;

    /**
     * @Column(type="text", nullable=true)
     */
    private $improvementOpportunity;

    /**
     * @Column(type="text", nullable=true)
     */
    private $strongPoint;

    /**
     * @Column(type="text", nullable=true)
     */
    private $nonAttendance;

    /**
     * Muitos itens pertencem a uma lista de itens
     * @ManyToOne(targetEntity="App\Basics\RN440\Lists\ListRN440RequirementsItems", inversedBy="items")
     * @JoinColumn(name="id_item", referencedColumnName="id")
     */
    private $listOfItems;

    /**
     * Muitos requerimentos pertencem a uma RN440
     * @ManyToOne(targetEntity="RN440", inversedBy="requirementsItems")
     * @JoinColumn(name="id_rn_440", referencedColumnName="id")
     */
    private $rn440;

    /**
     * Um requerimento de itens tem muitos arquivos vinculados
     * @OneToMany(targetEntity="RN440RequirementsItemsFiles", mappedBy="requirementItem")
     */
    private $files;

    public function convertArray()
    {
        return [
            "id" => $this->id,
            "degreeOfCompliance" => $this->degreeOfCompliance,
            "pointing" => $this->pointing,
            "scope" => $this->scope,
            "deploymentTime" => $this->deploymentTime,
            "comment" => $this->comment,
            "evidence" => $this->evidence,
            "feedback" => $this->feedback,
            "changedPoint" => $this->changedPoint,
            "improvementOpportunity" => $this->improvementOpportunity,
            "strongPoint" => $this->strongPoint,
            "nonAttendance" => $this->nonAttendance,
            "details" => $this->listOfItems->convertArray(),
            "files" => $this->listFiles(),
        ];
    }

    public function calculatePoints($timeValue, $scopeValue)
    {
        return $scopeValue && $timeValue >= 6 ? 1 : 0;
    }

    public function calculateDegree($timeValue, $scopeValue)
    {
        return $scopeValue && $timeValue >= 6;
    }

    public function listFiles()
    {
        $list = [];
        foreach ($this->files as $file) {
            array_push($list, $file->convertArray());
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
     * @return RN440RequirementsItems
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDegreeOfCompliance()
    {
        return $this->degreeOfCompliance;
    }

    /**
     * @param mixed $degreeOfCompliance
     * @return RN440RequirementsItems
     */
    public function setDegreeOfCompliance($degreeOfCompliance)
    {
        $this->degreeOfCompliance = $degreeOfCompliance;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPointing()
    {
        return $this->pointing;
    }

    /**
     * @param mixed $pointing
     * @return RN440RequirementsItems
     */
    public function setPointing($pointing)
    {
        $this->pointing = $pointing;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getScope()
    {
        return $this->scope;
    }

    /**
     * @param mixed $scope
     * @return RN440RequirementsItems
     */
    public function setScope($scope)
    {
        $this->scope = $scope;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDeploymentTime()
    {
        return $this->deploymentTime;
    }

    /**
     * @param mixed $deploymentTime
     * @return RN440RequirementsItems
     */
    public function setDeploymentTime($deploymentTime)
    {
        $this->deploymentTime = $deploymentTime;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param mixed $comment
     * @return RN440RequirementsItems
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEvidence()
    {
        return $this->evidence;
    }

    /**
     * @param mixed $evidence
     * @return RN440RequirementsItems
     */
    public function setEvidence($evidence)
    {
        $this->evidence = $evidence;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFeedback()
    {
        return $this->feedback;
    }

    /**
     * @param mixed $feedback
     * @return RN440RequirementsItems
     */
    public function setFeedback($feedback)
    {
        $this->feedback = $feedback;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getChangedPoint()
    {
        return $this->changedPoint;
    }

    /**
     * @param mixed $changedPoint
     * @return RN440RequirementsItems
     */
    public function setChangedPoint($changedPoint)
    {
        $this->changedPoint = $changedPoint;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getImprovementOpportunity()
    {
        return $this->improvementOpportunity;
    }

    /**
     * @param mixed $improvementOpportunity
     * @return RN440RequirementsItems
     */
    public function setImprovementOpportunity($improvementOpportunity)
    {
        $this->improvementOpportunity = $improvementOpportunity;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getStrongPoint()
    {
        return $this->strongPoint;
    }

    /**
     * @param mixed $strongPoint
     * @return RN440RequirementsItems
     */
    public function setStrongPoint($strongPoint)
    {
        $this->strongPoint = $strongPoint;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNonAttendance()
    {
        return $this->nonAttendance;
    }

    /**
     * @param mixed $nonAttendance
     * @return RN440RequirementsItems
     */
    public function setNonAttendance($nonAttendance)
    {
        $this->nonAttendance = $nonAttendance;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getListOfItems()
    {
        return $this->listOfItems;
    }

    /**
     * @param mixed $listOfItems
     * @return RN440RequirementsItems
     */
    public function setListOfItems($listOfItems)
    {
        $this->listOfItems = $listOfItems;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRn440()
    {
        return $this->rn440;
    }

    /**
     * @param mixed $rn440
     * @return RN440RequirementsItems
     */
    public function setRn440($rn440)
    {
        $this->rn440 = $rn440;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * @param mixed $files
     * @return RN440RequirementsItems
     */
    public function setFiles($files)
    {
        $this->files = $files;
        return $this;
    }

}