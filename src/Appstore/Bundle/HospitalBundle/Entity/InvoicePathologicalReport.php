<?php

namespace Appstore\Bundle\HospitalBundle\Entity;

use Core\UserBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * InvoiceParticularReport
 *
 * @ORM\Table( name = "hms_invoice_pathological_report")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\HospitalBundle\Repository\InvoicePathologicalReportRepository")
 */
class InvoicePathologicalReport
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
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\HospitalBundle\Entity\InvoiceParticular", inversedBy="invoicePathologicalReports")
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private $invoiceParticular;

     /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\HospitalBundle\Entity\PathologicalReport", inversedBy="invoicePathologicalReports")
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private $pathologicalReport;

    /**
     * @var string
     *
     * @ORM\Column(name="result", type="string", length=100, nullable=true)
     */
    private $result;

    /**
     * @var string
     *
     * @ORM\Column(name="metaKey", type="string", length=255, nullable=true)
     */
    private $metaKey;

    /**
     * @var string
     *
     * @ORM\Column(name="metaValue", type="text", nullable=true)
     */
    private $metaValue;

    /**
     * @var text
     *
     * @ORM\Column(name="remark", type="text", nullable=true)
     */
    private $remark;




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
    public function getInvoiceParticular()
    {
        return $this->invoiceParticular;
    }

    /**
     * @param mixed $invoiceParticular
     */
    public function setInvoiceParticular($invoiceParticular)
    {
        $this->invoiceParticular = $invoiceParticular;
    }

    /**
     * @return string
     */
    public function getMetaKey()
    {
        return $this->metaKey;
    }

    /**
     * @param string $metaKey
     */
    public function setMetaKey($metaKey)
    {
        $this->metaKey = $metaKey;
    }

    /**
     * @return string
     */
    public function getMetaValue()
    {
        return $this->metaValue;
    }

    /**
     * @param string $metaValue
     */
    public function setMetaValue($metaValue)
    {
        $this->metaValue = $metaValue;
    }

    /**
     * @return mixed
     */
    public function getPathologicalReport()
    {
        return $this->pathologicalReport;
    }

    /**
     * @param mixed $pathologicalReport
     */
    public function setPathologicalReport($pathologicalReport)
    {
        $this->pathologicalReport = $pathologicalReport;
    }

    /**
     * @return text
     */
    public function getRemark()
    {
        return $this->remark;
    }

    /**
     * @param text $remark
     */
    public function setRemark($remark)
    {
        $this->remark = $remark;
    }

    /**
     * @return string
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @param string $result
     */
    public function setResult($result)
    {
        $this->result = $result;
    }


}

