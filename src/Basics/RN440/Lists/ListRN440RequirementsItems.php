<?php

namespace App\Basics\RN440\Lists;

/**
 * @Entity
 * @Table(name="list_rn_440_requirements_items")
 */
class ListRN440RequirementsItems

{

    const TYPE_EXCELLENCE = 1;

    const TYPE_ESSENTIAL = 2;

    const TYPE_COMPLEMENTARY = 3;

    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

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
     * @Column(type="integer")
     */
    private $type;

    /**
     * @Column(type="text")
     */
    private $evidenceTip;

    /**
     * @Column(type="text")
     */
    private $interpretationTip;

    /**
     * Muitos itens pertencem a um requisito
     * @ManyToOne(targetEntity="ListRN440Requirements", inversedBy="items")
     * @JoinColumn(name="id_requirement", referencedColumnName="id")
     */
    private $requirement;

    /**
     * Uma lista de itens tem muitos itens vinculados a uma avaliação
     * @OneToMany(targetEntity="App\Basics\RN440\RN440RequirementsItems", mappedBy="listOfItems")
     */
    private $items;


    public function convertArray()
    {
        return [
            "id" => $this->id,
            "numericMarkers" => $this->numericMarkers,
            "text" => $this->text,
            "evidenceTip" => $this->evidenceTip,
            "interpretationTip" => $this->interpretationTip,
            "type" => $this->type,
            "active" => $this->active,
        ];
    }

    public static function getTypeName($type)
    {
        switch ($type) {
            case self::TYPE_EXCELLENCE:
                return 'Excelência';
            case self::TYPE_ESSENTIAL:
                return 'Essencial';
            case self::TYPE_COMPLEMENTARY:
                return 'Complementar';
            default:
                return '';
        }
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
     * @return ListRN440RequirementsItems
     */
    public function setId($id)
    {
        $this->id = $id;
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
     * @return ListRN440RequirementsItems
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
     * @return ListRN440RequirementsItems
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
     * @return ListRN440RequirementsItems
     */
    public function setActive($active)
    {
        $this->active = $active;
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
     * @return ListRN440RequirementsItems
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEvidenceTip()
    {
        return $this->evidenceTip;
    }

    /**
     * @param mixed $evidenceTip
     * @return ListRN440RequirementsItems
     */
    public function setEvidenceTip($evidenceTip)
    {
        $this->evidenceTip = $evidenceTip;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getInterpretationTip()
    {
        return $this->interpretationTip;
    }

    /**
     * @param mixed $interpretationTip
     * @return ListRN440RequirementsItems
     */
    public function setInterpretationTip($interpretationTip)
    {
        $this->interpretationTip = $interpretationTip;
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
     * @return ListRN440RequirementsItems
     */
    public function setRequirement($requirement)
    {
        $this->requirement = $requirement;
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
     * @return ListRN440RequirementsItems
     */
    public function setItems($items)
    {
        $this->items = $items;
        return $this;
    }
}