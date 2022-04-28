<?php


namespace App\Basics\RN452;

/**
 * @Entity
 * @Table(name="rn_452_monitored_indicators")
 */
class RN452MonitoredIndicators
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * Muitos indicadores pertencem a uma lista de indicadores
     * @ManyToOne(targetEntity="App\Basics\RN452\Lists\ListRN452MonitoredIndicators", inversedBy="monitoredIndicators")
     * @JoinColumn(name="id_monitored_indicators", referencedColumnName="id")
     */
    private $listOfMonitoredIndicators;

    /**
     * Muitos indicadores pertencem a uma acreditação
     * @ManyToOne(targetEntity="RN452", inversedBy="monitoredIndicators")
     * @JoinColumn(name="id_rn_452", referencedColumnName="id")
     */
    private $rn452;

    /**
     * @Column(type="boolean", nullable=true)
     */
    private $itHas;

    public function convertArray()
    {
        return [
            "id" => $this->id,
            "listOfMonitoredIndicators" => $this->listOfMonitoredIndicators->convertArray(),
            "itHas" => $this->itHas,
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
     * @return RN452MonitoredIndicators
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getListOfMonitoredIndicators()
    {
        return $this->listOfMonitoredIndicators;
    }

    /**
     * @param mixed $listOfMonitoredIndicators
     * @return RN452MonitoredIndicators
     */
    public function setListOfMonitoredIndicators($listOfMonitoredIndicators)
    {
        $this->listOfMonitoredIndicators = $listOfMonitoredIndicators;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRn452()
    {
        return $this->rn452;
    }

    /**
     * @param mixed $rn452
     * @return RN452MonitoredIndicators
     */
    public function setRn452($rn452)
    {
        $this->rn452 = $rn452;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getItHas()
    {
        return $this->itHas;
    }

    /**
     * @param mixed $itHas
     * @return RN452MonitoredIndicators
     */
    public function setItHas($itHas)
    {
        $this->itHas = $itHas;
        return $this;
    }
}