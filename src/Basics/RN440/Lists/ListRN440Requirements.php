<?php


namespace App\Basics\RN440\Lists;

/**
 * @Entity
 * @Table(name="list_rn_440_requirements")
 */
class ListRN440Requirements

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
    private $requirementNumber;

    /**
     * @Column(type="string", length=120)
     */
    private $numericMarkers;

    /**
     * @Column(type="text")
     */
    private $text;

    /**
     * @Column(type="boolean")
     */
    private $active;

    /**
     * Uma lista de requisitos tem muitos itens
     * @OneToMany(targetEntity="ListRN440RequirementsItems", mappedBy="requirement")
     */
    private $items;

    public function convertArray()
    {
        return [
            "id" => $this->id,
            "requirementNumber" => $this->requirementNumber,
            "numericMarkers" => $this->numericMarkers,
            "text" => $this->text,
            "active" => $this->active,
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
     * @return ListRN440Requirements
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRequirementNumber()
    {
        return $this->requirementNumber;
    }

    /**
     * @param mixed $requirementNumber
     * @return ListRN440Requirements
     */
    public function setRequirementNumber($requirementNumber)
    {
        $this->requirementNumber = $requirementNumber;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNumericMarkers()
    {
        return $this->numericMarkers;
    }

    /**
     * @param mixed $numericMarkers
     * @return ListRN440Requirements
     */
    public function setNumericMarkers($numericMarkers)
    {
        $this->numericMarkers = $numericMarkers;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param mixed $text
     * @return ListRN440Requirements
     */
    public function setText($text)
    {
        $this->text = $text;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * @param mixed $active
     * @return ListRN440Requirements
     */
    public function setActive($active)
    {
        $this->active = $active;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param mixed $items
     * @return ListRN440Requirements
     */
    public function setItems($items)
    {
        $this->items = $items;
        return $this;
    }
}