<?php

namespace Appstore\Bundle\RestaurantBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;

/**
 * DMSConfig
 *
 * @ORM\Table( name ="restaurant_config")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\RestaurantBundle\Repository\RestaurantConfigRepository")
 */
class RestaurantConfig
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
     * @ORM\OneToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\GlobalOption", inversedBy="restaurantConfig" , cascade={"persist", "remove"})
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private $globalOption;


    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\RestaurantBundle\Entity\Particular", mappedBy="restaurantConfig")
     **/
    private $particulars;


    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\RestaurantBundle\Entity\RestaurantAndroidProcess", mappedBy="restaurantConfig")
     **/
    private $androidProcesses;

     /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\RestaurantBundle\Entity\Coupon", mappedBy="restaurantConfig")
     **/
    private $coupons;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\RestaurantBundle\Entity\RestaurantTemporary", mappedBy="restaurantConfig")
     **/
    private $restaurantTemp;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\RestaurantBundle\Entity\Invoice", mappedBy="restaurantConfig")
     **/
    private $invoices;


    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\RestaurantBundle\Entity\Purchase", mappedBy="restaurantConfig")
     **/
    private $purchases;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\RestaurantBundle\Entity\Category", mappedBy="restaurantConfig")
     **/
    private $categories;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\RestaurantBundle\Entity\Reverse", mappedBy="restaurantConfig")
     **/
    private $reverses;

    /**
     * @var string
     *
     * @ORM\Column(name="salesMode", type="string", length=50,nullable = true)
     */
    private $salesMode = "grid";

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
     * @ORM\Column(name="sdPercentage", type="smallint",  nullable=true)
     */
    private $sdPercentage;

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
     * @var smallint
     *
     * @ORM\Column(name="discountType", type="string", length = 20,  nullable=true)
     */
    private $discountType = 'percentage';

     /**
     * @var smallint
     *
     * @ORM\Column(name="discountPercentage", type="smallint",  nullable=true)
     */
    private $discountPercentage = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="vatRegNo", type="string",  nullable=true)
     */
    private $vatRegNo;

     /**
     * @var string
     *
     * @ORM\Column(name="vatMode", type="string",  nullable=true)
     */
    private $vatMode;


     /**
     * @var string
     *
     * @ORM\Column(name="payFor", type="string",  nullable=true)
     */
    private $payFor;

     /**
     * @var boolean
     * @ORM\Column(type="boolean",  nullable=true)
     */
    private $printToken = false;

     /**
     * @var boolean
     * @ORM\Column(type="boolean",  nullable=true)
     */
    private $posPrint = true;

    /**
     * @var boolean
     * @ORM\Column(type="boolean",  nullable=true)
     */
    private $autoPayment = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isCustomer", type="boolean",  nullable=true)
     */
    private $isCustomer = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="systemReset", type="boolean",  nullable=true)
     */
    private $systemReset = false;


    /**
     * @var boolean
     *
     * @ORM\Column(name="isBranch", type="boolean",  nullable=true)
     */
    private $isBranch = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isStockHistory", type="boolean",  nullable=true)
     */
    private $isStockHistory = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="barcodePrint", type="boolean",  nullable=true)
     */
    private $barcodePrint = false;


    /**
     * @var boolean
     *
     * @ORM\Column(name="isBranchInvoice", type="boolean",  nullable=true)
     */
    private $isBranchInvoice = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="vatEnable", type="boolean",  nullable=true)
     */
    private $vatEnable = false;

     /**
     * @var boolean
     *
     * @ORM\Column(name="isProduction", type="boolean",  nullable=true)
     */
    private $isProduction = false;

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
     * @ORM\Column(name="reportHeight", type="integer", nullable = true)
     */
    private $reportHeight = 0;


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
     * @var string
     *
     * @ORM\Column(name="address", type="text", nullable = true)
     */
    private $address;

    /**
     * @var string
     *
     * @ORM\Column(name="invoiceNote", type="text", nullable = true)
     */
    private $invoiceNote;

     /**
     * @var string
     *
     * @ORM\Column(name="printFooterText", type="text", nullable = true)
     */
    private $printFooterText;

     /**
     * @var string
     *
     * @ORM\Column(name="templateCss", type="text", nullable = true)
     */
    private $templateCss;

    /**
     * @var boolean
     *
     * @ORM\Column(name="productImage", type="boolean",  nullable=true)
     */
    private $productImage = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="additionalServe", type="boolean",  nullable=true)
     */
    private $additionalServe = false;

     /**
     * @var boolean
     *
     * @ORM\Column(name="additionalTable", type="boolean",  nullable=true)
     */
    private $additionalTable = false;

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
     * @ORM\Column(name="kitchenPrint", type="boolean",  nullable=true)
     */
    private $kitchenPrint = false;

     /**
     * @var boolean
     *
     * @ORM\Column(name="deliveryPrint", type="boolean",  nullable=true)
     */
    private $deliveryPrint = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="tablePlan", type="boolean",  nullable=true)
     */
    private $tablePlan = false;


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
     * @return int
     */
    public function getReportHeight()
    {
        return $this->reportHeight;
    }

    /**
     * @param int $reportHeight
     */
    public function setReportHeight($reportHeight)
    {
        $this->reportHeight = $reportHeight;
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
     * @return mixed
     */
    public function getParticulars()
    {
        return $this->particulars;
    }

    /**
     * @return Invoice
     */
    public function getInvoices()
    {
        return $this->invoices;
    }

    /**
     * @return smallint
     */
    public function getDiscountType()
    {
        return $this->discountType;
    }

    /**
     * @param smallint $discountType
     */
    public function setDiscountType($discountType)
    {
        $this->discountType = $discountType;
    }

    /**
     * @return smallint
     */
    public function getDiscountPercentage()
    {
        return $this->discountPercentage;
    }

    /**
     * @param smallint $discountPercentage
     */
    public function setDiscountPercentage($discountPercentage)
    {
        $this->discountPercentage = $discountPercentage;
    }

    /**
     * @return Reverse
     */
    public function getReverses()
    {
        return $this->reverses;
    }

    /**
     * @return Category
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * @return string
     */
    public function getPayFor()
    {
        return $this->payFor;
    }

    /**
     * @param string $payFor
     */
    public function setPayFor(string $payFor)
    {
        $this->payFor = $payFor;
    }

    /**
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param string $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * @return RestaurantAndroidProcess
     */
    public function getAndroidProcesses()
    {
        return $this->androidProcesses;
    }

    /**
     * @return bool
     */
    public function isKitchenPrint()
    {
        return $this->kitchenPrint;
    }

    /**
     * @param bool $kitchenPrint
     */
    public function setKitchenPrint($kitchenPrint)
    {
        $this->kitchenPrint = $kitchenPrint;
    }

    /**
     * @return string
     */
    public function getInvoiceNote()
    {
        return $this->invoiceNote;
    }

    /**
     * @param string $invoiceNote
     */
    public function setInvoiceNote($invoiceNote)
    {
        $this->invoiceNote = $invoiceNote;
    }

    /**
     * @return string
     */
    public function getSalesMode()
    {
        return $this->salesMode;
    }

    /**
     * @param string $salesMode
     */
    public function setSalesMode($salesMode)
    {
        $this->salesMode = $salesMode;
    }

    /**
     * @return bool
     */
    public function isTablePlan()
    {
        return $this->tablePlan;
    }

    /**
     * @param bool $tablePlan
     */
    public function setTablePlan($tablePlan)
    {
        $this->tablePlan = $tablePlan;
    }

    /**
     * @return bool
     */
    public function isDeliveryPrint()
    {
        return $this->deliveryPrint;
    }

    /**
     * @param bool $deliveryPrint
     */
    public function setDeliveryPrint($deliveryPrint)
    {
        $this->deliveryPrint = $deliveryPrint;
    }

    /**
     * @return bool
     */
    public function isStockHistory()
    {
        return $this->isStockHistory;
    }

    /**
     * @param bool $isStockHistory
     */
    public function setIsStockHistory($isStockHistory)
    {
        $this->isStockHistory = $isStockHistory;
    }

    /**
     * @return bool
     */
    public function isPrintToken()
    {
        return $this->printToken;
    }

    /**
     * @param bool $printToken
     */
    public function setPrintToken($printToken)
    {
        $this->printToken = $printToken;
    }

    /**
     * @return bool
     */
    public function isProduction()
    {
        return $this->isProduction;
    }

    /**
     * @param bool $isProduction
     */
    public function setIsProduction($isProduction)
    {
        $this->isProduction = $isProduction;
    }

    /**
     * @return bool
     */
    public function isCustomer()
    {
        return $this->isCustomer;
    }

    /**
     * @param bool $isCustomer
     */
    public function setIsCustomer($isCustomer)
    {
        $this->isCustomer = $isCustomer;
    }

    /**
     * @return bool
     */
    public function isPosPrint()
    {
        return $this->posPrint;
    }

    /**
     * @param bool $posPrint
     */
    public function setPosPrint($posPrint)
    {
        $this->posPrint = $posPrint;
    }

    /**
     * @return bool
     */
    public function isAutoPayment()
    {
        return $this->autoPayment;
    }

    /**
     * @param bool $autoPayment
     */
    public function setAutoPayment($autoPayment)
    {
        $this->autoPayment = $autoPayment;
    }

    /**
     * @return smallint
     */
    public function getSdPercentage()
    {
        return $this->sdPercentage;
    }

    /**
     * @param smallint $sdPercentage
     */
    public function setSdPercentage($sdPercentage)
    {
        $this->sdPercentage = $sdPercentage;
    }

    /**
     * @return string
     */
    public function getTemplateCss()
    {
        return $this->templateCss;
    }

    /**
     * @param string $templateCss
     */
    public function setTemplateCss($templateCss)
    {
        $this->templateCss = $templateCss;
    }

    /**
     * @return bool
     */
    public function isProductImage()
    {
        return $this->productImage;
    }

    /**
     * @param bool $productImage
     */
    public function setProductImage($productImage)
    {
        $this->productImage = $productImage;
    }

    /**
     * @return bool
     */
    public function isAdditionalServe()
    {
        return $this->additionalServe;
    }

    /**
     * @param bool $additionalServe
     */
    public function setAdditionalServe($additionalServe)
    {
        $this->additionalServe = $additionalServe;
    }

    /**
     * @return bool
     */
    public function isAdditionalTable()
    {
        return $this->additionalTable;
    }

    /**
     * @param bool $additionalTable
     */
    public function setAdditionalTable($additionalTable)
    {
        $this->additionalTable = $additionalTable;
    }

    /**
     * @return string
     */
    public function getVatMode()
    {
        return $this->vatMode;
    }

    /**
     * @param string $vatMode
     */
    public function setVatMode($vatMode)
    {
        $this->vatMode = $vatMode;
    }

    /**
     * @return string
     */
    public function getPrintFooterText()
    {
        return $this->printFooterText;
    }

    /**
     * @param string $printFooterText
     */
    public function setPrintFooterText($printFooterText)
    {
        $this->printFooterText = $printFooterText;
    }

    /**
     * @return bool
     */
    public function isSystemReset()
    {
        return $this->systemReset;
    }

    /**
     * @param bool $systemReset
     */
    public function setSystemReset($systemReset)
    {
        $this->systemReset = $systemReset;
    }


}

