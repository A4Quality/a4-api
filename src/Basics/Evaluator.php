<?php

namespace App\Basics;

/**
 * @Entity
 * @Table(name="evaluators")
 */
class Evaluator
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
     * @Column(type="datetime", nullable=true)
     */
    private $statementOfResponsibility;

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
            "statementOfResponsibility" => !$this->statementOfResponsibility ? null : $this->statementOfResponsibility->format('Y-m-d H:i:s'),
            "minimumExperienceInBusinessAudit" => $this->minimumExperienceInBusinessAudit,
            "minimumExperienceInControllership" => $this->minimumExperienceInControllership,
            "minimumExperienceInHealthAccreditation" => $this->minimumExperienceInHealthAccreditation,
            "minimumExperienceInHealthAaudit" => $this->minimumExperienceInHealthAaudit,
            "subscription" => $this->subscription,
            "leaderType" => "evaluator",
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
     * @return Evaluator
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
     * @return Evaluator
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
     * @return Evaluator
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
     * @return Evaluator
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
     * @return Evaluator
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
     * @return Evaluator
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
     * @return Evaluator
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
     * @return Evaluator
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
     * @return Evaluator
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
     * @return Evaluator
     */
    public function setMinimumExperienceInHealthAaudit($minimumExperienceInHealthAaudit)
    {
        $this->minimumExperienceInHealthAaudit = $minimumExperienceInHealthAaudit;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getStatementOfResponsibility()
    {
        return $this->statementOfResponsibility;
    }

    /**
     * @param mixed $statementOfResponsibility
     * @return Evaluator
     */
    public function setStatementOfResponsibility($statementOfResponsibility)
    {
        $this->statementOfResponsibility = $statementOfResponsibility;
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
     * @return Evaluator
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
     * @return Evaluator
     */
    public function setAccount($account)
    {
        $this->account = $account;
        return $this;
    }
}
