<?php


namespace App\Basics\RN452;

/**
 * @Entity
 * @Table(name="rn_452_prerequisites")
 */
class RN452Prerequisites
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
    private $itHas;


    /**
     * Muitos Pré-requisitos pertencem a uma lista de Pré-requisitos
     * @ManyToOne(targetEntity="App\Basics\RN452\Lists\ListRN452Prerequisites", inversedBy="prerequisites")
     * @JoinColumn(name="id_prerequisite", referencedColumnName="id")
     */
    private $listOfPrerequisites;

    /**
     * Muitos pré-requisitos pertencem a uma Rn452
     * @ManyToOne(targetEntity="RN452", inversedBy="prerequisites")
     * @JoinColumn(name="id_rn_452", referencedColumnName="id")
     */
    private $rn452;


    public function convertArray()
    {
        return [
            "id" => $this->id,
            "itHas" => $this->itHas,
            "details" => $this->listOfPrerequisites->convertArray(),
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
     * @return RN452Prerequisites
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getItHas()
    {
        return $this->itHas;
    }

    /**
     * @param mixed $itHas
     * @return RN452Prerequisites
     */
    public function setItHas($itHas)
    {
        $this->itHas = $itHas;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getListOfPrerequisites()
    {
        return $this->listOfPrerequisites;
    }

    /**
     * @param mixed $listOfPrerequisites
     * @return RN452Prerequisites
     */
    public function setListOfPrerequisites($listOfPrerequisites)
    {
        $this->listOfPrerequisites = $listOfPrerequisites;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRn452()
    {
        return $this->rn452;
    }

    /**
     * @param mixed $rn452
     * @return RN452Prerequisites
     */
    public function setRn452($rn452)
    {
        $this->rn452 = $rn452;
        return $this;
    }
}