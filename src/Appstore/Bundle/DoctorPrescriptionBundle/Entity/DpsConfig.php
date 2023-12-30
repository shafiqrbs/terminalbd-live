<?php

namespace Appstore\Bundle\DoctorPrescriptionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;

/**
 * DMSConfig
 *
 * @ORM\Table( name ="dps_config")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\DoctorPrescriptionBundle\Repository\DpsConfigRepository")
 */
class DpsConfig
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
     * @ORM\OneToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\GlobalOption", inversedBy="dpsConfig" , cascade={"persist", "remove"})
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private $globalOption;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\DoctorPrescriptionBundle\Entity\DpsInvoice", mappedBy="dpsConfig", cascade={"persist", "remove"})
     **/
    private $dpsInvoices;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\DoctorPrescriptionBundle\Entity\DpsService", mappedBy="dpsConfig")
     **/
    private $dpsServices;


    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\DoctorPrescriptionBundle\Entity\DpsParticular", mappedBy="dpsConfig")
     **/
    private $dpsParticulars;


    /**
     * @var string
     *
     * @ORM\Column(name="printer", type="string", length=50,nullable = true)
     */
    private $printer;

    /**
     * @var smallint
     *
     * @ORM\Column(name="vatPercentage", type="smallint",  nullable=true)
     */
    private $vatPercentage;

    /**
     * @var smallint
     *
     * @ORM\Column(name="fontSizeLabel", type="smallint",  nullable=true)
     */
    private $fontSizeLabel;

    /**
     * @var smallint
     *
     * @ORM\Column(name="fontSizeValue", type="smallint",  nullable=true)
     */
    private $fontSizeValue;

    /**
     * @var string
     *
     * @ORM\Column(name="vatRegNo", type="string",  nullable=true)
     */
    private $vatRegNo;

    /**
     * @var boolean
     *
     * @ORM\Column(name="showTransaction", type="boolean",  nullable=true)
     */
    private $showTransaction = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isDefaultMedicine", type="boolean",  nullable=true)
     */
    private $isDefaultMedicine = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="barcodePrint", type="boolean",  nullable=true)
     */
    private $barcodePrint = false;


    /**
     * @var boolean
     *
     * @ORM\Column(name="medicineUsage", type="boolean",  nullable=true)
     */
    private $medicineUsage = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="vatEnable", type="boolean",  nullable=true)
     */
    private $vatEnable = false;

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
     * @var string
     *
     * @ORM\Column(name="headerLeftWidth", type="string",  nullable=true)
     */
    private $headerLeftWidth = 0;


    /**
     * @var string
     *
     * @ORM\Column(name="headerRightWidth", type="string",  nullable=true)
     */
    private $headerRightWidth = 0;

    /**
     * @var smallint
     *
     * @ORM\Column(name="printMarginReportTop", type="smallint",  nullable=true)
     */
    private $printMarginReportTop = 0;

    /**
     * @var smallint
     *
     * @ORM\Column(name="printMarginReportLeft", type="smallint",  nullable=true)
     */
    private $printMarginReportLeft = 0;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isPrintHeader", type="boolean",  nullable=true)
     */
    private $isPrintHeader = true;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isInvoiceTitle", type="boolean",  nullable=true)
     */
    private $isInvoiceTitle = true;


    /**
     * @var boolean
     *
     * @ORM\Column(name="isPrintFooter", type="boolean",  nullable=true)
     */
    private $isPrintFooter = true;


    /**
     * @var boolean
     *
     * @ORM\Column(name="barcodeColor", type="boolean" ,  nullable=true)
     */
    private $barcodeColor;


    /**
     * @var boolean
     *
     * @ORM\Column(name="barcodeSize", type="boolean",  nullable=true)
     */
    private $barcodeSize;


    /**
     * @var string
     *
     * @ORM\Column(name="barcodeText", type="string", length=255,nullable = true)
     */
    private $barcodeText;

    /**
     * @var string
     *
     * @ORM\Column(name="invoicePrefix", type="string", length=10,nullable = true)
     */
    private $invoicePrefix;

    /**
     * @var array
     *
     * @ORM\Column(name="invoiceProcess", type="array", nullable = true)
     */
    private $invoiceProcess;

    /**
     * @var string
     *
     * @ORM\Column(name="customerPrefix", type="string", length=10,nullable = true)
     */
    private $customerPrefix;


    /**
     * @var string
     *
     * @ORM\Column(name="bodyFontSize", type="string", length=10,nullable = true)
     */
    private $bodyFontSize;

    /**
     * @var string
     *
     * @ORM\Column(name="sidebarFontSize", type="string", length=10,nullable = true)
     */
    private $sidebarFontSize;

    /**
     * @var string
     *
     * @ORM\Column(name="invoiceFontSize", type="string", length=10,nullable = true)
     */
    private $invoiceFontSize;

    /**
     * @var smallint
     *
     * @ORM\Column(name="barcodeWidth", type="smallint", nullable = true)
     */
    private $barcodeWidth = 140;

    /**
     * @var smallint
     *
     * @ORM\Column(name="barcodeMargin", type="smallint", nullable = true)
     */
    private $barcodeMargin = 0;

    /**
     * @var smallint
     *
     * @ORM\Column(name="barcodeBorder", type="smallint", nullable = true)
     */
    private $barcodeBorder = 0;

    /**
     * @var smallint
     *
     * @ORM\Column(name="barcodePadding", type="smallint", nullable = true)
     */
    private $barcodePadding = 0;

    /**
     * @var smallint
     *
     * @ORM\Column(name="printLeftMargin", type="smallint", nullable = true)
     */
    private $printLeftMargin = 0;

    /**
     * @var smallint
     *
     * @ORM\Column(name="barcodeHeight", type="smallint", nullable = true)
     */
    private $barcodeHeight = 80;

    /**
     * @var integer
     *
     * @ORM\Column(name="invoiceHeight", type="integer", nullable = true)
     */
    private $invoiceHeight = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="leftTopMargin", type="integer", nullable = true)
     */
    private $leftTopMargin = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="bodyTopMargin", type="integer", nullable = true)
     */
    private $bodyTopMargin = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="sidebarWidth", type="string", nullable = true)
     */
    private $sidebarWidth = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="bodyWidth", type="string", nullable = true)
     */
    private $bodyWidth = 0;


    /**
     * @var smallint
     *
     * @ORM\Column(name="barcodeThickness", type="smallint", nullable = true)
     */
    private $barcodeThickness = 30;

    /**
     * @var smallint
     *
     * @ORM\Column(name="barcodeFontSize", type="smallint", nullable = true)
     */
    private $barcodeFontSize = 8;

    /**
     * @var smallint
     *
     * @ORM\Column(name="barcodeScale", type="smallint", nullable = true)
     */
    private $barcodeScale = 1;

    /**
     * @var boolean
     *
     * @ORM\Column(name="invoicePrintLogo", type="boolean",  nullable=true)
     */
    private $invoicePrintLogo = true;

    /**
     * @var boolean
     *
     * @ORM\Column(name="printInstruction", type="boolean",  nullable=true)
     */
    private $printInstruction = true;

    /**
     * @var boolean
     *
     * @ORM\Column(name="customPrescription", type="boolean",  nullable=true)
     */
    private $customPrescription = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="showAccessories", type="boolean",  nullable=true)
     */
     private $showAccessories = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="investigation", type="boolean",  nullable=true)
     */
     private $investigation = true;




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
     * @return smallint
     */
    public function getBarcodeScale()
    {
        return $this->barcodeScale;
    }

    /**
     * @param smallint $barcodeScale
     */
    public function setBarcodeScale($barcodeScale)
    {
        $this->barcodeScale = $barcodeScale;
    }

    /**
     * @return smallint
     */
    public function getBarcodeFontSize()
    {
        return $this->barcodeFontSize;
    }

    /**
     * @param smallint $barcodeFontSize
     */
    public function setBarcodeFontSize($barcodeFontSize)
    {
        $this->barcodeFontSize = $barcodeFontSize;
    }

    /**
     * @return smallint
     */
    public function getBarcodeThickness()
    {
        return $this->barcodeThickness;
    }

    /**
     * @param smallint $barcodeThickness
     */
    public function setBarcodeThickness($barcodeThickness)
    {
        $this->barcodeThickness = $barcodeThickness;
    }

    /**
     * @return smallint
     */
    public function getBarcodeHeight()
    {
        return $this->barcodeHeight;
    }

    /**
     * @param smallint $barcodeHeight
     */
    public function setBarcodeHeight($barcodeHeight)
    {
        $this->barcodeHeight = $barcodeHeight;
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
     * @return smallint
     */
    public function getBarcodePadding()
    {
        return $this->barcodePadding;
    }

    /**
     * @param smallint $barcodePadding
     */
    public function setBarcodePadding($barcodePadding)
    {
        $this->barcodePadding = $barcodePadding;
    }

    /**
     * @return smallint
     */
    public function getBarcodeBorder()
    {
        return $this->barcodeBorder;
    }

    /**
     * @param smallint $barcodeBorder
     */
    public function setBarcodeBorder($barcodeBorder)
    {
        $this->barcodeBorder = $barcodeBorder;
    }

    /**
     * @return smallint
     */
    public function getBarcodeMargin()
    {
        return $this->barcodeMargin;
    }

    /**
     * @param smallint $barcodeMargin
     */
    public function setBarcodeMargin($barcodeMargin)
    {
        $this->barcodeMargin = $barcodeMargin;
    }

    /**
     * @return smallint
     */
    public function getBarcodeWidth()
    {
        return $this->barcodeWidth;
    }

    /**
     * @param smallint $barcodeWidth
     */
    public function setBarcodeWidth($barcodeWidth)
    {
        $this->barcodeWidth = $barcodeWidth;
    }

    /**
     * @return string
     */
    public function getBarcodeText()
    {
        return $this->barcodeText;
    }

    /**
     * @param string $barcodeText
     */
    public function setBarcodeText($barcodeText)
    {
        $this->barcodeText = $barcodeText;
    }

    /**
     * @return smallint
     */
    public function getBarcodeBrandVendor()
    {
        return $this->barcodeBrandVendor;
    }

    /**
     * @param smallint $barcodeBrandVendor
     */
    public function setBarcodeBrandVendor($barcodeBrandVendor)
    {
        $this->barcodeBrandVendor = $barcodeBrandVendor;
    }

    /**
     * @return bool
     */
    public function isBarcodeSize()
    {
        return $this->barcodeSize;
    }

    /**
     * @param bool $barcodeSize
     */
    public function setBarcodeSize($barcodeSize)
    {
        $this->barcodeSize = $barcodeSize;
    }

    /**
     * @return bool
     */
    public function isBarcodeColor()
    {
        return $this->barcodeColor;
    }

    /**
     * @param bool $barcodeColor
     */
    public function setBarcodeColor($barcodeColor)
    {
        $this->barcodeColor = $barcodeColor;
    }

    /**
     * @return bool
     */
    public function isIsPrintFooter()
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
     * @return bool
     */
    public function isIsPrintHeader()
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
     * @return string
     */
    public function getPrinter()
    {
        return $this->printer;
    }

    /**
     * @param string $printer
     */
    public function setPrinter($printer)
    {
        $this->printer = $printer;
    }

    /**
     * @return smallint
     */
    public function getVatPercentage()
    {
        return $this->vatPercentage;
    }

    /**
     * @param smallint $vatPercentage
     */
    public function setVatPercentage($vatPercentage)
    {
        $this->vatPercentage = $vatPercentage;
    }

    /**
     * @return string
     */
    public function getVatRegNo()
    {
        return $this->vatRegNo;
    }

    /**
     * @param string $vatRegNo
     */
    public function setVatRegNo($vatRegNo)
    {
        $this->vatRegNo = $vatRegNo;
    }

    /**
     * @return bool
     */
    public function isIsBranch()
    {
        return $this->isBranch;
    }

    /**
     * @param bool $isBranch
     */
    public function setIsBranch($isBranch)
    {
        $this->isBranch = $isBranch;
    }

    /**
     * @return bool
     */
    public function isBarcodePrint()
    {
        return $this->barcodePrint;
    }

    /**
     * @param bool $barcodePrint
     */
    public function setBarcodePrint($barcodePrint)
    {
        $this->barcodePrint = $barcodePrint;
    }

    /**
     * @return bool
     */
    public function getIsBranchInvoice()
    {
        return $this->isBranchInvoice;
    }

    /**
     * @param bool $isBranchInvoice
     */
    public function setIsBranchInvoice($isBranchInvoice)
    {
        $this->isBranchInvoice = $isBranchInvoice;
    }

    /**
     * @return bool
     */
    public function getVatEnable()
    {
        return $this->vatEnable;
    }

    /**
     * @param bool $vatEnable
     */
    public function setVatEnable($vatEnable)
    {
        $this->vatEnable = $vatEnable;
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
     * @return boolean
     */
    public function isInvoicePrintLogo()
    {
        return $this->invoicePrintLogo;
    }

    /**
     * @param boolean $invoicePrintLogo
     */
    public function setInvoicePrintLogo($invoicePrintLogo)
    {
        $this->invoicePrintLogo = $invoicePrintLogo;
    }

    /**
     * @return bool
     */
    public function getPrintInstruction()
    {
        return $this->printInstruction;
    }

    /**
     * @param bool $printInstruction
     */
    public function setPrintInstruction($printInstruction)
    {
        $this->printInstruction = $printInstruction;
    }

    /**
     * @return string
     */
    public function getInvoicePrefix()
    {
        return $this->invoicePrefix;
    }

    /**
     * @param string $invoicePrefix
     */
    public function setInvoicePrefix($invoicePrefix)
    {
        $this->invoicePrefix = $invoicePrefix;
    }

    /**
     * @return string
     */
    public function getCustomerPrefix()
    {
        return $this->customerPrefix;
    }

    /**
     * @param string $customerPrefix
     */
    public function setCustomerPrefix($customerPrefix)
    {
        $this->customerPrefix = $customerPrefix;
    }

    /**
     * @return smallint
     */
    public function getPrintMarginReportTop()
    {
        return $this->printMarginReportTop;
    }

    /**
     * @param smallint $printMarginReportTop
     */
    public function setPrintMarginReportTop($printMarginReportTop)
    {
        $this->printMarginReportTop = $printMarginReportTop;
    }

    /**
     * @return bool
     */
    public function getIsInvoiceTitle()
    {
        return $this->isInvoiceTitle;
    }

    /**
     * @param bool $isInvoiceTitle
     */
    public function setIsInvoiceTitle($isInvoiceTitle)
    {
        $this->isInvoiceTitle = $isInvoiceTitle;
    }


    /**
     * @return array
     */
    public function getInvoiceProcess()
    {
        return $this->invoiceProcess;
    }

    /**
     * @param array $invoiceProcess
     */
    public function setInvoiceProcess($invoiceProcess)
    {
        $this->invoiceProcess = $invoiceProcess;
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
    public function getPrintMarginReportLeft()
    {
        return $this->printMarginReportLeft;
    }

    /**
     * @param smallint $printMarginReportLeft
     */
    public function setPrintMarginReportLeft($printMarginReportLeft)
    {
        $this->printMarginReportLeft = $printMarginReportLeft;
    }

    /**
     * @return smallint
     */
    public function getFontSizeLabel()
    {
        return $this->fontSizeLabel;
    }

    /**
     * @param smallint $fontSizeLabel
     */
    public function setFontSizeLabel($fontSizeLabel)
    {
        $this->fontSizeLabel = $fontSizeLabel;
    }

    /**
     * @return smallint
     */
    public function getFontSizeValue()
    {
        return $this->fontSizeValue;
    }

    /**
     * @param smallint $fontSizeValue
     */
    public function setFontSizeValue($fontSizeValue)
    {
        $this->fontSizeValue = $fontSizeValue;
    }

    /**
     * @return DpsInvoice
     */
    public function getDpsInvoices()
    {
        return $this->dpsInvoices;
    }

    /**
     * @return DpsDoctorInvoice
     */
    public function getDpsDoctorInvoices()
    {
        return $this->dpsDoctorInvoices;
    }

    /**
     * @return DpsVendor
     */
    public function getDpsVendors()
    {
        return $this->dpsVendors;
    }

    /**
     * @return DpsPurchase
     */
    public function getDpsPurchases()
    {
        return $this->dpsPurchases;
    }

    /**
     * @return DpsParticular
     */
    public function getDpsParticulars()
    {
        return $this->dpsParticulars;
    }

    /**
     * @return DpsService
     */
    public function getDpsServices()
    {
        return $this->dpsServices;
    }

    /**
     * @return bool
     */
    public function getIsDefaultMedicine()
    {
        return $this->isDefaultMedicine;
    }

    /**
     * @param bool $isDefaultMedicine
     */
    public function setIsDefaultMedicine($isDefaultMedicine)
    {
        $this->isDefaultMedicine = $isDefaultMedicine;
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
     * @return string
     */
    public function getSidebarFontSize()
    {
        return $this->sidebarFontSize;
    }

    /**
     * @param string $sidebarFontSize
     */
    public function setSidebarFontSize($sidebarFontSize)
    {
        $this->sidebarFontSize = $sidebarFontSize;
    }

    /**
     * @return string
     */
    public function getInvoiceFontSize()
    {
        return $this->invoiceFontSize;
    }

    /**
     * @param string $invoiceFontSize
     */
    public function setInvoiceFontSize($invoiceFontSize)
    {
        $this->invoiceFontSize = $invoiceFontSize;
    }

    /**
     * @return bool
     */
    public function isCustomPrescription()
    {
        return $this->customPrescription;
    }

    /**
     * @param bool $customPrescription
     */
    public function setCustomPrescription($customPrescription)
    {
        $this->customPrescription = $customPrescription;
    }

    /**
     * @return int
     */
    public function getBodyTopMargin()
    {
        return $this->bodyTopMargin;
    }

    /**
     * @param int $bodyTopMargin
     */
    public function setBodyTopMargin($bodyTopMargin)
    {
        $this->bodyTopMargin = $bodyTopMargin;
    }

    /**
     * @return int
     */
    public function getLeftTopMargin()
    {
        return $this->leftTopMargin;
    }

    /**
     * @param int $leftTopMargin
     */
    public function setLeftTopMargin($leftTopMargin)
    {
        $this->leftTopMargin = $leftTopMargin;
    }


    /**
     * @return bool
     */
    public function isMedicineUsage()
    {
        return $this->medicineUsage;
    }

    /**
     * @param bool $medicineUsage
     */
    public function setMedicineUsage($medicineUsage)
    {
        $this->medicineUsage = $medicineUsage;
    }

    /**
     * @return bool
     */
    public function isShowAccessories()
    {
        return $this->showAccessories;
    }

    /**
     * @param bool $showAccessories
     */
    public function setShowAccessories($showAccessories)
    {
        $this->showAccessories = $showAccessories;
    }

    /**
     * @return DpsInvoiceAccessories
     */
    public function getDpsInvoiceAccessories()
    {
        return $this->dpsInvoiceAccessories;
    }

    /**
     * @return string
     */
    public function getHeaderLeftWidth()
    {
        return $this->headerLeftWidth;
    }

    /**
     * @param string $headerLeftWidth
     */
    public function setHeaderLeftWidth($headerLeftWidth)
    {
        $this->headerLeftWidth = $headerLeftWidth;
    }

    /**
     * @return string
     */
    public function getHeaderRightWidth()
    {
        return $this->headerRightWidth;
    }

    /**
     * @param string $headerRightWidth
     */
    public function setHeaderRightWidth($headerRightWidth)
    {
        $this->headerRightWidth = $headerRightWidth;
    }

    /**
     * @return string
     */
    public function getSidebarWidth()
    {
        return $this->sidebarWidth;
    }

    /**
     * @param string $sidebarWidth
     */
    public function setSidebarWidth($sidebarWidth)
    {
        $this->sidebarWidth = $sidebarWidth;
    }

    /**
     * @return string
     */
    public function getBodyWidth()
    {
        return $this->bodyWidth;
    }

    /**
     * @param string $bodyWidth
     */
    public function setBodyWidth($bodyWidth)
    {
        $this->bodyWidth = $bodyWidth;
    }

    /**
     * @return bool
     */
    public function getShowTransaction()
    {
        return $this->showTransaction;
    }

    /**
     * @param bool $showTransaction
     */
    public function setShowTransaction($showTransaction)
    {
        $this->showTransaction = $showTransaction;
    }

    /**
     * @return bool
     */
    public function isInvestigation()
    {
        return $this->investigation;
    }

    /**
     * @param bool $investigation
     */
    public function setInvestigation($investigation)
    {
        $this->investigation = $investigation;
    }


}

