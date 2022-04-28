<?php


namespace App\Basics\RN452\Lists;

/**
 * @Entity
 * @Table(name="list_rn_452_requirements")
 */
class ListRN452Requirements

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
     * @OneToMany(targetEntity="ListRN452RequirementsItems", mappedBy="requirement")
     */
    private $items;

    public function convertArray()
    {
        return [
            "id" => $this->id,
            "dimension" => $this->dimension,
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
     * @return ListRN452Requirements
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
     * @return ListRN452Requirements
     */
    public function setDimension($dimension)
    {
        $this->dimension = $dimension;
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
     * @return ListRN452Requirements
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
     * @return ListRN452Requirements
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
     * @return ListRN452Requirements
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
     * @return ListRN452Requirements
     */
    public function setItems($items)
    {
        $this->items = $items;
        return $this;
    }
}