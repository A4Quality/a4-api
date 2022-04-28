<?php


namespace App\Basics\RN452\Lists;

/**
 * @Entity
 * @Table(name="list_rn_452_prerequisites")
 */
class ListRN452Prerequisites

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
    private $text;

    /**
     * @Column(type="boolean")
     */
    private $active;

    /**
     * Uma lista de Prerequisites tem muitas respostas
     * @OneToMany(targetEntity="App\Basics\RN452\RN452Prerequisites", mappedBy="listOfPrerequisites")
     */
    private $prerequisites;

    public function convertArray()
    {
        return [
            "id" => $this->id,
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
     * @return ListRN452Prerequisites
     */
    public function setId($id)
    {
        $this->id = $id;
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
     * @return ListRN452Prerequisites
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
     * @return ListRN452Prerequisites
     */
    public function setActive($active)
    {
        $this->active = $active;
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
     * @return ListRN452Prerequisites
     */
    public function setPrerequisites($prerequisites)
    {
        $this->prerequisites = $prerequisites;
        return $this;
    }
}