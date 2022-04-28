<?php


namespace App\Basics\RN452\Lists;

/**
 * @Entity
 * @Table(name="list_rn_452_monitored_indicators")
 */
class ListRN452MonitoredIndicators

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
     * @Column(type="boolean", nullable=true)
     */
    private $belongsMedicalHospital;

    /**
     * @Column(type="boolean", nullable=true)
     */
    private $belongsDental;

    /**
     * @Column(type="boolean", nullable=true)
     */
    private $belongsSelfManagement;

    /**
     * Uma lista de documentos requeridos tem muitos documentos requeridos
     * @OneToMany(targetEntity="App\Basics\RN452\RN452MonitoredIndicators", mappedBy="listOfMonitoredIndicators")
     */
    private $monitoredIndicators;

    public function convertArray()
    {
        return [
            "id" => $this->id,
            "dimension" => $this->dimension,
            "numericMarkers" => $this->numericMarkers,
            "text" => $this->text,
            "active" => $this->active,
            "belongsMedicalHospital" => $this->belongsMedicalHospital,
            "belongsDental" => $this->belongsDental,
            "belongsSelfManagement" => $this->belongsSelfManagement,
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
     * @return ListRN452MonitoredIndicators
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
     * @return ListRN452MonitoredIndicators
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
     * @return ListRN452MonitoredIndicators
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
     * @return ListRN452MonitoredIndicators
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
     * @return ListRN452MonitoredIndicators
     */
    public function setActive($active)
    {
        $this->active = $active;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBelongsMedicalHospital()
    {
        return $this->belongsMedicalHospital;
    }

    /**
     * @param mixed $belongsMedicalHospital
     * @return ListRN452MonitoredIndicators
     */
    public function setBelongsMedicalHospital($belongsMedicalHospital)
    {
        $this->belongsMedicalHospital = $belongsMedicalHospital;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBelongsDental()
    {
        return $this->belongsDental;
    }

    /**
     * @param mixed $belongsDental
     * @return ListRN452MonitoredIndicators
     */
    public function setBelongsDental($belongsDental)
    {
        $this->belongsDental = $belongsDental;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBelongsSelfManagement()
    {
        return $this->belongsSelfManagement;
    }

    /**
     * @param mixed $belongsSelfManagement
     * @return ListRN452MonitoredIndicators
     */
    public function setBelongsSelfManagement($belongsSelfManagement)
    {
        $this->belongsSelfManagement = $belongsSelfManagement;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMonitoredIndicators()
    {
        return $this->monitoredIndicators;
    }

    /**
     * @param mixed $monitoredIndicators
     * @return ListRN452MonitoredIndicators
     */
    public function setMonitoredIndicators($monitoredIndicators)
    {
        $this->monitoredIndicators = $monitoredIndicators;
        return $this;
    }
}