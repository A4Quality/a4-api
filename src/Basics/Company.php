<?php

namespace App\Basics;

/**
 * @Entity
 * @Table(name="companies")
 */
class Company
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
    private $cnpj;

    /**
     * @Column(type="text")
     */
    private $ansRecord;

    /**
     * @Column(type="text")
     */
    private $segmentation;

    /**
     * @Column(type="text")
     */
    private $contactPerson;

    /**
     * @Column(type="text")
     */
    private $port;

    /**
     * @Column(type="text")
     */
    private $numberOfEmployees;

    /**
     * @Column(type="text")
     */
    private $numberOfBeneficiaries;

    /**
     * @Column(type="text")
     */
    private $idss;

    /**
     * @Column(type="text")
     */
    private $image;

    /**
     * @Column(type="text")
     */
    private $address;

    /**
     * @Column(type="string", length=14)
     */
    private $phone;

    /**
     * @Column(type="string", length=120)
     */
    private $email;

    /**
     * @Column(type="datetime", nullable=true)
     */
    private $createdDate;

    /**
     * @Column(type="boolean")
     */
    private $active;

    /**
     * Uma empresa tem muitos funcionarios
     * @OneToMany(targetEntity="CompanyUser", mappedBy="company")
     */
    private $companyUsers;

    /**
     * Uma empresa tem muitas avaliações
     * @OneToMany(targetEntity="Evaluation", mappedBy="company")
     */
    private $evaluation;

    /**
     * Account constructor.
     */
    public function __construct()
    {
        $this->createdDate = new \DateTime();
    }

    public function convertArray()
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "cnpj" => $this->cnpj,
            "ansRecord" => $this->ansRecord,
            "segmentation" => $this->segmentation,
            "contactPerson" => $this->contactPerson,
            "address" => $this->address,
            "port" => $this->port,
            "numberOfEmployees" => $this->numberOfEmployees,
            "numberOfBeneficiaries" => $this->numberOfBeneficiaries,
            "idss" => $this->idss,
            "image" => $this->image,
            "phone" => $this->phone,
            "email" => $this->email,
            "active" => $this->active
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
     * @return Company
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
     * @return Company
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCnpj()
    {
        return $this->cnpj;
    }

    /**
     * @param mixed $cnpj
     * @return Company
     */
    public function setCnpj($cnpj)
    {
        $this->cnpj = $cnpj;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAnsRecord()
    {
        return $this->ansRecord;
    }

    /**
     * @param mixed $ansRecord
     * @return Company
     */
    public function setAnsRecord($ansRecord)
    {
        $this->ansRecord = $ansRecord;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSegmentation()
    {
        return $this->segmentation;
    }

    /**
     * @param mixed $segmentation
     * @return Company
     */
    public function setSegmentation($segmentation)
    {
        $this->segmentation = $segmentation;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getContactPerson()
    {
        return $this->contactPerson;
    }

    /**
     * @param mixed $contactPerson
     * @return Company
     */
    public function setContactPerson($contactPerson)
    {
        $this->contactPerson = $contactPerson;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param mixed $address
     * @return Company
     */
    public function setAddress($address)
    {
        $this->address = $address;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param mixed $image
     * @return Company
     */
    public function setImage($image)
    {
        $this->image = $image;
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
     * @return Company
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
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
     * @return Company
     */
    public function setEmail($email)
    {
        $this->email = $email;
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
     * @return Company
     */
    public function setActive($active)
    {
        $this->active = $active;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @param mixed $port
     * @return Company
     */
    public function setPort($port)
    {
        $this->port = $port;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNumberOfEmployees()
    {
        return $this->numberOfEmployees;
    }

    /**
     * @param mixed $numberOfEmployees
     * @return Company
     */
    public function setNumberOfEmployees($numberOfEmployees)
    {
        $this->numberOfEmployees = $numberOfEmployees;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNumberOfBeneficiaries()
    {
        return $this->numberOfBeneficiaries;
    }

    /**
     * @param mixed $numberOfBeneficiaries
     * @return Company
     */
    public function setNumberOfBeneficiaries($numberOfBeneficiaries)
    {
        $this->numberOfBeneficiaries = $numberOfBeneficiaries;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIdss()
    {
        return $this->idss;
    }

    /**
     * @param mixed $idss
     * @return Company
     */
    public function setIdss($idss)
    {
        $this->idss = $idss;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCreatedDate()
    {
        return $this->createdDate;
    }

    /**
     * @param mixed $createdDate
     * @return Company
     */
    public function setCreatedDate($createdDate)
    {
        $this->createdDate = $createdDate;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCompanyUsers()
    {
        return $this->companyUsers;
    }

    /**
     * @param mixed $companyUsers
     * @return Company
     */
    public function setCompanyUsers($companyUsers)
    {
        $this->companyUsers = $companyUsers;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEvaluation()
    {
        return $this->evaluation;
    }

    /**
     * @param mixed $evaluation
     * @return Company
     */
    public function setEvaluation($evaluation)
    {
        $this->evaluation = $evaluation;
        return $this;
    }
}
