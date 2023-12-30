<?php

namespace Appstore\Bundle\AccountingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;

/**
 * AccountingConfig
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Appstore\Bundle\AccountingBundle\Repository\AccountingConfigRepository")
 */
class AccountingConfig
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
     * @ORM\OneToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\GlobalOption", inversedBy="accountingConfig" , cascade={"persist", "remove"})
     **/

    private $globalOption;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountHead")
     **/
    private  $capitalInvestor;


    /**
     * @var text
     *
     * @ORM\Column(name="address", type="text", length=255, nullable=true)
     */
    private $address;


    /**
     * @var boolean
     *
     * @ORM\Column(name="accountClose", type="boolean",  nullable=true)
     */
    private $accountClose = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="purchase", type="boolean",  nullable=true)
     */
    private $purchase = false;

    /**
     * @var smallint
     *
     * @ORM\Column(name="invoiceWidth", type="smallint",  nullable=true)
     */
    private $invoiceWidth = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="invoiceHeight", type="integer", nullable = true)
     */
    private $invoiceHeight = 0;



    /**
     * @var smallint
     *
     * @ORM\Column(name="printMarginTop", type="smallint",  nullable=true)
     */
    private $printTopMargin = 0;


    /**
     * @var smallint
     *
     * @ORM\Column(name="printMarginBottom", type="smallint",  nullable=true)
     */
    private $printMarginBottom = 0;


    /**
     * @var boolean
     *
     * @ORM\Column(name="isPrintHeader", type="boolean",  nullable=true)
     */
    private $isPrintHeader = true;



    /**
     * @var boolean
     *
     * @ORM\Column(name="isPrintFooter", type="boolean",  nullable=true)
     */
    private $isPrintFooter = true;


    /**
     * @var smallint
     *
     * @ORM\Column(name="printLeftMargin", type="smallint", nullable = true)
     */
    private $printLeftMargin = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="borderWidth", type="smallint", length=2, nullable = true)
     */
    private $borderWidth = 0;


    /**
     * @var string
     *
     * @ORM\Column(name="borderColor", type="string", length=25,nullable = true)
     */
    private $borderColor;


    /**
     * @var string
     *
     * @ORM\Column(name="bodyFontSize", type="string", length=10,nullable = true)
     */
    private $bodyFontSize;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isPowered", type="boolean",  nullable=true)
     */
    private $isPowered = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="salesReceiveSms", type="boolean",  nullable=true)
     */
    private $salesReceiveSms = false;

     /**
     * @var boolean
     *
     * @ORM\Column(name="customPrint", type="boolean",  nullable=true)
     */
    private $customPrint = false;

    /**
     * @var string
     *
     * @ORM\Column(name="businessMode", type="string", length=100,nullable = true)
     */
    private $businessMode = 'Proprietorship';


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        return $this->id = $id;
    }

    /**
     * @return GlobalOption
     */
    public function getGlobalOption()
    {
        return $this->globalOption;
    }

    /**
     * @param GlobalOption $globalOption
     */
    public function setGlobalOption($globalOption)
    {
        $this->globalOption = $globalOption;
    }

    /**
     * @return bool
     */
    public function isPurchase()
    {
        return $this->purchase;
    }

    /**
     * @param bool $purchase
     */
    public function setPurchase(bool $purchase)
    {
        $this->purchase = $purchase;
    }

    /**
     * @return bool
     */
    public function isAccountClose()
    {
        return $this->accountClose;
    }

    /**
     * @param bool $accountClose
     */
    public function setAccountClose(bool $accountClose)
    {
        $this->accountClose = $accountClose;
    }

    /**
     * @return smallint
     */
    public function getInvoiceWidth()
    {
        return $this->invoiceWidth;
    }

    /**
     * @param smallint $invoiceWidth
     */
    public function setInvoiceWidth($invoiceWidth)
    {
        $this->invoiceWidth = $invoiceWidth;
    }

    /**
     * @return int
     */
    public function getInvoiceHeight()
    {
        return $this->invoiceHeight;
    }

    /**
     * @param int $invoiceHeight
     */
    public function setInvoiceHeight($invoiceHeight)
    {
        $this->invoiceHeight = $invoiceHeight;
    }

    /**
     * @return smallint
     */
    public function getPrintTopMargin()
    {
        return $this->printTopMargin;
    }

    /**
     * @param smallint $printTopMargin
     */
    public function setPrintTopMargin($printTopMargin)
    {
        $this->printTopMargin = $printTopMargin;
    }

    /**
     * @return smallint
     */
    public function getPrintMarginBottom()
    {
        return $this->printMarginBottom;
    }

    /**
     * @param smallint $printMarginBottom
     */
    public function setPrintMarginBottom($printMarginBottom)
    {
        $this->printMarginBottom = $printMarginBottom;
    }

    /**
     * @return bool
     */
    public function isPrintHeader()
    {
        return $this->isPrintHeader;
    }

    /**
     * @param bool $isPrintHeader
     */
    public function setIsPrintHeader($isPrintHeader)
    {
        $this->isPrintHeader = $isPrintHeader;
    }

    /**
     * @return bool
     */
    public function isPrintFooter()
    {
        return $this->isPrintFooter;
    }

    /**
     * @param bool $isPrintFooter
     */
    public function setIsPrintFooter($isPrintFooter)
    {
        $this->isPrintFooter = $isPrintFooter;
    }

    /**
     * @return string
     */
    public function getBorderWidth()
    {
        return $this->borderWidth;
    }

    /**
     * @param string $borderWidth
     */
    public function setBorderWidth($borderWidth)
    {
        $this->borderWidth = $borderWidth;
    }

    /**
     * @return string
     */
    public function getBorderColor()
    {
        return $this->borderColor;
    }

    /**
     * @param string $borderColor
     */
    public function setBorderColor($borderColor)
    {
        $this->borderColor = $borderColor;
    }

    /**
     * @return string
     */
    public function getBodyFontSize()
    {
        return $this->bodyFontSize;
    }

    /**
     * @param string $bodyFontSize
     */
    public function setBodyFontSize($bodyFontSize)
    {
        $this->bodyFontSize = $bodyFontSize;
    }

    /**
     * @return bool
     */
    public function isPowered()
    {
        return $this->isPowered;
    }

    /**
     * @param bool $isPowered
     */
    public function setIsPowered($isPowered)
    {
        $this->isPowered = $isPowered;
    }

    /**
     * @return smallint
     */
    public function getPrintLeftMargin()
    {
        return $this->printLeftMargin;
    }

    /**
     * @param smallint $printLeftMargin
     */
    public function setPrintLeftMargin($printLeftMargin)
    {
        $this->printLeftMargin = $printLeftMargin;
    }

    /**
     * @return text
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param text $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * @return AccountHead
     */
    public function getCapitalInvestor()
    {
        return $this->capitalInvestor;
    }

    /**
     * @param AccountHead $capitalInvestor
     */
    public function setCapitalInvestor($capitalInvestor)
    {
        $this->capitalInvestor = $capitalInvestor;
    }

    /**
     * @return bool
     */
    public function isSalesReceiveSms()
    {
        return $this->salesReceiveSms;
    }

    /**
     * @param bool $salesReceiveSms
     */
    public function setSalesReceiveSms($salesReceiveSms)
    {
        $this->salesReceiveSms = $salesReceiveSms;
    }

    /**
     * @return string
     */
    public function getBusinessMode()
    {
        return $this->businessMode;
    }

    /**
     * @param string $businessMode
     */
    public function setBusinessMode($businessMode)
    {
        $this->businessMode = $businessMode;
    }

    /**
     * @return bool
     */
    public function isCustomPrint()
    {
        return $this->customPrint;
    }

    /**
     * @param bool $customPrint
     */
    public function setCustomPrint($customPrint)
    {
        $this->customPrint = $customPrint;
    }


}

