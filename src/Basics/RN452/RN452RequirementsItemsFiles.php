<?php


namespace App\Basics\RN452;

/**
 * @Entity
 * @Table(name="rn_452_requeriments_items_files")
 */
class RN452RequirementsItemsFiles
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Column(type="text")
     */
    private $name;

    /**
     * Muitos itens pertencem a uma lista de itens
     * @ManyToOne(targetEntity="RN452RequirementsItems", inversedBy="files")
     * @JoinColumn(name="id_requiriment", referencedColumnName="id")
     */
    private $requirementItem;

    public function convertArray()
    {
        return [
            "id" => $this->id,
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
     * @return RN452RequirementsItemsFiles
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
     * @return RN452RequirementsItemsFiles
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRequirementItem()
    {
        return $this->requirementItem;
    }

    /**
     * @param mixed $requirementItem
     * @return RN452RequirementsItemsFiles
     */
    public function setRequirementItem($requirementItem)
    {
        $this->requirementItem = $requirementItem;
        return $this;
    }
}