<?php

namespace App\Basics;

/**
 * @Entity
 * @Table(name="admins")
 */
class Admin
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Column(type="string", length=120)
     */
    private $name;

    /**
     * @Column(type="string", length=14, unique=true)
     */
    private $cpf;

    /**
     * @Column(type="string", length=14)
     */
    private $phone;

    /**
     * @Column(type="string", nullable=true)
     */
    private $universityGraduate;

    /**
     * @Column(type="string", nullable=true)
     */
    private $postGraduate;

    /**
     * @Column(type="boolean")
     */
    private $minimumExperienceInBusinessAudit;

    /**
     * @Column(type="boolean")
     */
    private $minimumExperienceInControllership;

    /**
     * @Column(type="boolean")
     */
    private $minimumExperienceInHealthAccreditation;

    /**
     * @Column(type="boolean")
     */
    private $minimumExperienceInHealthAaudit;

    /**
     * @Column(type="text")
     */
    private $subscription;

    /**
     * Um Usuário possui uma conta.
     * @OneToOne(targetEntity="Account")
     * @JoinColumn(name="id_account", referencedColumnName="id")
     */
    private $account;

    public function convertArray()
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "cpf" => $this->cpf,
            "phone" => $this->phone,
            "universityGraduate" => $this->universityGraduate,
            "postGraduate" => $this->postGraduate,
            "minimumExperienceInBusinessAudit" => $this->minimumExperienceInBusinessAudit,
            "minimumExperienceInControllership" => $this->minimumExperienceInControllership,
            "minimumExperienceInHealthAccreditation" => $this->minimumExperienceInHealthAccreditation,
            "minimumExperienceInHealthAaudit" => $this->minimumExperienceInHealthAaudit,
            "subscription" => $this->subscription,
            "leaderType" => "director",
            "account" => $this->account->convertArray(),
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
     * @return Admin
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
     * @return Admin
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCpf()
    {
        return $this->cpf;
    }

    /**
     * @param mixed $cpf
     * @return Admin
     */
    public function setCpf($cpf)
    {
        $this->cpf = $cpf;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param mixed $phone
     * @return Admin
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUniversityGraduate()
    {
        return $this->universityGraduate;
    }

    /**
     * @param mixed $universityGraduate
     * @return Admin
     */
    public function setUniversityGraduate($universityGraduate)
    {
        $this->universityGraduate = $universityGraduate;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPostGraduate()
    {
        return $this->postGraduate;
    }

    /**
     * @param mixed $postGraduate
     * @return Admin
     */
    public function setPostGraduate($postGraduate)
    {
        $this->postGraduate = $postGraduate;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMinimumExperienceInBusinessAudit()
    {
        return $this->minimumExperienceInBusinessAudit;
    }

    /**
     * @param mixed $minimumExperienceInBusinessAudit
     * @return Admin
     */
    public function setMinimumExperienceInBusinessAudit($minimumExperienceInBusinessAudit)
    {
        $this->minimumExperienceInBusinessAudit = $minimumExperienceInBusinessAudit;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMinimumExperienceInControllership()
    {
        return $this->minimumExperienceInControllership;
    }

    /**
     * @param mixed $minimumExperienceInControllership
     * @return Admin
     */
    public function setMinimumExperienceInControllership($minimumExperienceInControllership)
    {
        $this->minimumExperienceInControllership = $minimumExperienceInControllership;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMinimumExperienceInHealthAccreditation()
    {
        return $this->minimumExperienceInHealthAccreditation;
    }

    /**
     * @param mixed $minimumExperienceInHealthAccreditation
     * @return Admin
     */
    public function setMinimumExperienceInHealthAccreditation($minimumExperienceInHealthAccreditation)
    {
        $this->minimumExperienceInHealthAccreditation = $minimumExperienceInHealthAccreditation;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMinimumExperienceInHealthAaudit()
    {
        return $this->minimumExperienceInHealthAaudit;
    }

    /**
     * @param mixed $minimumExperienceInHealthAaudit
     * @return Admin
     */
    public function setMinimumExperienceInHealthAaudit($minimumExperienceInHealthAaudit)
    {
        $this->minimumExperienceInHealthAaudit = $minimumExperienceInHealthAaudit;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSubscription()
    {
        return $this->subscription;
    }

    /**
     * @param mixed $subscription
     * @return Admin
     */
    public function setSubscription($subscription)
    {
        $this->subscription = $subscription;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * @param mixed $account
     * @return Admin
     */
    public function setAccount($account)
    {
        $this->account = $account;
        return $this;
    }
}
