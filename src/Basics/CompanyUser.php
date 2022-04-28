<?php

namespace App\Basics;

/**
 * @Entity
 * @Table(name="company_users")
 */
class CompanyUser
{

    const TYPE_CONTACT = 1;

    const TYPE_COMMON = 2;

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
     * @Column(type="string", length=14, unique=true, nullable=true)
     */
    private $cpf;

    /**
     * @Column(type="string", length=14, nullable=true)
     */
    private $phone;

    /**
     * @Column(type="integer")
     */
    private $type;

    /**
     * Um cliente possui uma conta.
     * @OneToOne(targetEntity="Account")
     * @JoinColumn(name="id_account", referencedColumnName="id")
     */
    private $account;

    /**
     * Muitos usuarios pertencem a uma empresa
     * @ManyToOne(targetEntity="Company", inversedBy="companyUsers")
     * @JoinColumn(name="id_company", referencedColumnName="id")
     */
    private $company;

    public function convertArray()
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "cpf" => $this->cpf,
            "phone" => $this->phone,
            "type" => $this->type,
            "leaderType" => "companyUser",
            "account" => $this->account->convertArray(),
            "company" => $this->company->convertArray(),
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
     * @return CompanyUser
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
     * @return CompanyUser
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
     * @return CompanyUser
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
     * @return CompanyUser
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
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
     * @return CompanyUser
     */
    public function setType($type)
    {
        $this->type = $type;
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
     * @return CompanyUser
     */
    public function setAccount($account)
    {
        $this->account = $account;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @param mixed $company
     * @return CompanyUser
     */
    public function setCompany($company)
    {
        $this->company = $company;
        return $this;
    }
}
