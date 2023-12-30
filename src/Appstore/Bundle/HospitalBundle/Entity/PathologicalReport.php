<?php

namespace Appstore\Bundle\HospitalBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;

/**
 * PathologicalReport
 *
 * @ORM\Table( name ="hms_pathological_report")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\HospitalBundle\Repository\PathologicalReportRepository")
 */
class PathologicalReport
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="PathologicalReport", inversedBy="children", cascade={"detach","merge"})
     * @ORM\JoinColumn(name="parent", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $parent;

    /**
     * @ORM\OneToMany(targetEntity="PathologicalReport" , mappedBy="parent")
     * @ORM\OrderBy({"sorting" = "ASC"})
     **/
    private $children;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\HospitalBundle\Entity\HospitalConfig", inversedBy="pathologicalReport")
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private $hospitalConfig;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\HospitalBundle\Entity\Particular", inversedBy="pathologicalReports")
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private $particular;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\HospitalBundle\Entity\InvoicePathologicalReport", mappedBy="pathologicalReport")
     **/
    private $invoicePathologicalReports;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=200, nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="parentName", type="string", length=200, nullable=true)
     */
    private $parentName;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=10, nullable=true)
     */
    private $code;

    /**
     * @var int
     *
     * @ORM\Column(name="sorting", type="smallint", length=2, nullable=true)
     */
    private $sorting;

    /**
     * @var string
     *
     * @ORM\Column(name="referenceValue", type="text", nullable=true)
     */
    private $referenceValue;

     /**
     * @var string
     *
     * @ORM\Column(name="sampleValue", type="text", nullable=true)
     */
    private $sampleValue;

    /**
     * @var string
     *
     * @ORM\Column(name="unit", type="string", length=50, nullable=true)
     */
    private $unit;

    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="boolean" )
     */
    private $status= true;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
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
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param mixed $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }


    /**
     * @return bool
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param bool $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }


    /**
     * @return HospitalConfig
     */
    public function getHospitalConfig()
    {
        return $this->hospitalConfig;
    }

    /**
     * @param HospitalConfig $hospitalConfig
     */
    public function setHospitalConfig($hospitalConfig)
    {
        $this->hospitalConfig = $hospitalConfig;
    }

    /**
     * @return mixed
     */
    public function getParticular()
    {
        return $this->particular;
    }

    /**
     * @param mixed $particular
     */
    public function setParticular($particular)
    {
        $this->particular = $particular;
    }

    /**
     * @return PathologicalReport
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param PathologicalReport $parent
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    /**
     * @return PathologicalReport
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @return string
     */
    public function getReferenceValue()
    {
        return $this->referenceValue;
    }

    /**
     * @param string $referenceValue
     */
    public function setReferenceValue($referenceValue)
    {
        $this->referenceValue = $referenceValue;
    }

    /**
     * @return string
     */
    public function getUnit()
    {
        return $this->unit;
    }

    /**
     * @param string $unit
     */
    public function setUnit($unit)
    {
        $this->unit = $unit;
    }

    /**
     * @return mixed
     */
    public function getInvoicePathologicalReports()
    {
        return $this->invoicePathologicalReports;
    }

    /**
     * @return int
     */
    public function getSorting()
    {
        return $this->sorting;
    }

    /**
     * @param int $sorting
     */
    public function setSorting($sorting)
    {
        $this->sorting = $sorting;
    }

    /**
     * @return string
     */
    public function getParentName()
    {
        return $this->parentName;
    }

    /**
     * @param string $parentName
     */
    public function setParentName($parentName)
    {
        $this->parentName = $parentName;
    }

    /**
     * @return string
     */
    public function getSampleValue()
    {
        return $this->sampleValue;
    }

    /**
     * @param string $sampleValue
     */
    public function setSampleValue($sampleValue)
    {
        $this->sampleValue = $sampleValue;
    }




}

