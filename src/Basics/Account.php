<?php

namespace App\Basics;

/**
 * @Entity
 * @Table(name="accounts")
 */
class Account
{
    const GROUP_ADMIN = 1;

    const GROUP_EVALUATOR = 2;

    const GROUP_COMPANY_USER = 3;

    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Column(type="string", length=120, unique=true)
     */
    private $email;

    /**
     * @Column(type="string", length=50)
     */
    private $pass;

    /**
     * @Column(type="integer")
     */
    private $groupId;

    /**
     * @Column(type="datetime")
     */
    private $created;

    /**
     * @Column(type="boolean")
     */
    private $active;

    /**
     * @Column(type="boolean")
     */
    private $social;

    /**
     * Account constructor.
     */
    public function __construct()
    {
        $this->created = new \DateTime();
        $this->social = false;
    }

    public function convertArray()
    {
        return [
            "id" => $this->id,
            "email" => $this->email,
            "groupId" => $this->groupId,
            "created" => $this->created->format('Y-m-d H:i:s'),
            "active" => $this->active,
            "social" => $this->social,
        ];
    }

    /**
     * @return \DateTime
     */
    public function getCreate(): \DateTime
    {
        return $this->created;
    }

    /**
     * @param \DateTime $create
     * @return Account
     */
    public function setCreate(\DateTime $create): Account
    {
        $this->created = $create;
        return $this;
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
     * @return Account
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     * @return Account
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPass()
    {
        return $this->pass;
    }

    /**
     * @param mixed $pass
     * @return Account
     */
    public function setPass($pass)
    {
        $this->pass = $pass;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getGroupId()
    {
        return $this->groupId;
    }

    /**
     * @param mixed $groupId
     * @return Account
     */
    public function setGroupId($groupId)
    {
        $this->groupId = $groupId;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param mixed $created
     * @return Account
     */
    public function setCreated($created)
    {
        $this->created = $created;
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
     * @return Account
     */
    public function setActive($active)
    {
        $this->active = $active;
        return $this;
    }

    /**
     * @return false
     */
    public function getSocial()
    {
        return $this->social;
    }

    /**
     * @param false $social
     * @return Account
     */
    public function setSocial($social)
    {
        $this->social = $social;
        return $this;
    }
}
