<?php

namespace Appstore\Bundle\MedicineBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;

/**
 * MedicineConfig
 *
 * @ORM\Table( name="medicine_config")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\MedicineBundle\Repository\MedicineConfigRepository")
 */
class MedicineConfig
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
     * @ORM\OneToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\GlobalOption", inversedBy="medicineConfig" , cascade={"persist", "remove"})
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private $globalOption;


    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\MedicineBundle\Entity\MedicineStock", mappedBy="medicineConfig")
     **/
    private $medicineStock;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\MedicineBundle\Entity\MedicineStockHouse", mappedBy="medicineConfig")
     **/
    private $stockHouses;

     /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\MedicineBundle\Entity\MedicineAndroidProcess", mappedBy="medicineConfig")
     **/
    private $androidProcesses;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\MedicineBundle\Entity\MedicineMinimumStock", mappedBy="medicineConfig")
     **/
    private $medicineMinimumStock;

     /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\MedicineBundle\Entity\MedicineReverse", mappedBy="medicineConfig")
     **/
    private $medicineReverses;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\MedicineBundle\Entity\MedicineParticular", mappedBy="medicineConfig")
     **/
    private $medicineParticulars;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\MedicineBundle\Entity\MedicineSales", mappedBy="medicineConfig")
     **/
    private $medicineSales;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\MedicineBundle\Entity\MedicineSalesReturn", mappedBy="medicineConfig")
     **/
    private $medicineSalesReturns;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\MedicineBundle\Entity\MedicineDamage", mappedBy="medicineConfig")
     **/
    private $medicineDamages;

     /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\MedicineBundle\Entity\MedicineSalesTemporary", mappedBy="medicineConfig")
     **/
    private $medicineSalesTemporary;

     /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\MedicineBundle\Entity\MedicinePurchase", mappedBy="medicineConfig")
     **/
    private $medicinePurchases;

     /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\MedicineBundle\Entity\MedicinePrepurchase", mappedBy="medicineConfig")
     **/
    private $medicinePrepurchase;

     /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\MedicineBundle\Entity\MedicineVendor", mappedBy="medicineConfig")
     * @ORM\OrderBy({"companyName" = "ASC"})
     **/
    private $medicineVendors;

    /**
     * @var string
     *
     * @ORM\Column(name="printer", type = "string", length = 50, nullable = true)
     */
    private $printer;

    /**
     * @var string
     *
     * @ORM\Column(name="invoicePrefix", type = "string", length = 50, nullable = true)
     */
    private $invoicePrefix;

    /**
     * @var string
     *
     * @ORM\Column(name="itemSearch", type = "string", length = 50, nullable = true)
     */
    private $itemSearch ='rack';

    /**
     * @var boolean
     *
     * @ORM\Column(name="printOff", type="boolean",  nullable=true)
     */
    private $printOff;

     /**
     * @var boolean
     *
     * @ORM\Column(name="invoicePrintLogo", type="boolean",  nullable=true)
     */
    private $invoicePrintLogo = true;

    /**
     * @var boolean
     *
     * @ORM\Column(name="posPrint", type="boolean",  nullable=true)
     */
    private $posPrint = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="purchaseItem", type="boolean",  nullable=true)
     */
    private $purchaseItem = false;

      /**
     * @var boolean
     *
     * @ORM\Column(name="inlineSales", type="boolean",  nullable=true)
     */
    private $inlineSales = false;

     /**
     * @var boolean
     *
     * @ORM\Column(name="printOutstanding", type="boolean",  nullable=true)
     */
    private $printOutstanding = false;


    /**
     * @var boolean
     *
     * @ORM\Column(name="isRemainingQuantity", type="boolean",  nullable=true)
     */
    private $isRemainingQuantity = false;

     /**
     * @var boolean
     *
     * @ORM\Column(name="isActiveQuantity", type="boolean",  nullable=true)
     */
    private $isActiveQuantity = false;


    /**
     * @var boolean
     *
     * @ORM\Column(name="invoiceActualPrice", type="boolean",  nullable=true)
     */
    private $invoiceActualPrice = false;

     /**
     * @var boolean
     *
     * @ORM\Column(name="salesAvgHide", type="boolean",  nullable=true)
     */
    private $salesAvgHide = false;

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
     * @var boolean
     *
     * @ORM\Column(name="profitLastpp", type="boolean",  nullable=true)
     */
    private $profitLastpp = false;

    /**
     * @var smallint
     *
     * @ORM\Column(name="printLeftMargin", type="smallint", nullable = true)
     */
    private $printLeftMargin = 0;

    /**
     * @var smallint
     *
     * @ORM\Column(name="printMarginTop", type="smallint",  nullable=true)
     */
    private $printTopMargin = 0;

    /**
     * @var smallint
     *
     * @ORM\Column(name="vatPercentage", type = "smallint",  nullable=true)
     */
    private $vatPercentage;

    /**
     * @var smallint
     *
     * @ORM\Column(name="shortQuantity", type = "smallint",  nullable=true)
     */
    private $shortQuantity;

    /**
     * @var smallint
     *
     * @ORM\Column(name="expiryDate", type = "smallint",  nullable=true)
     */
    private $expiryDate =1;


    /**
     * @var float
     *
     * @ORM\Column(name="instantVendorPercentage", type = "float",  nullable=true)
     */
    private $instantVendorPercentage = 12.5;

    /**
     * @var float
     *
     * @ORM\Column(name="tpPercent", type = "float",  nullable=true)
     */
    private $tpPercent = 12.5;

     /**
     * @var float
     *
     * @ORM\Column(name="tpVatPercent", type = "float",  nullable=true)
     */
    private $tpVatPercent = 17.4;

    /**
     * @var float
     *
     * @ORM\Column(name="vendorPercentage", type = "float",  nullable=true)
     */
    private $vendorPercentage = 8;

    /**
     * @var boolean
     *
     * @ORM\Column(name="vatEnable", type="boolean",  nullable=true)
     */
    private $vatEnable = false;


     /**
     * @var boolean
     *
     * @ORM\Column(name="searchSlug", type="boolean",  nullable=true)
     */
    private $searchSlug = false;


     /**
     * @var boolean
     *
     * @ORM\Column(name="autoPayment", type="boolean",  nullable=true)
     */
    private $autoPayment = false;


     /**
     * @var boolean
     *
     * @ORM\Column(name="openingQuantity", type="boolean",  nullable=true)
     */
    private $openingQuantity = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="systemReset", type="boolean",  nullable=true)
     */
    private $systemReset = false;


    /**
     * @var boolean
     *
     * @ORM\Column(name="regularPosPrint", type="boolean",  nullable=true)
     */
    private $regularPosPrint = false;


     /**
     * @var boolean
     *
     * @ORM\Column(name="isPrint", type="boolean",  nullable=true)
     */
    private $isPrint = false;


    /**
     * @var boolean
     *
     * @ORM\Column(name="isBranch", type="boolean",  nullable=true)
     */
    private $isBranch = false;

    /**
     * @var string
     *
     * @ORM\Column(name="vatRegNo", type="string",  nullable=true)
     */
    private $vatRegNo;

    /**
     * @var string
     *
     * @ORM\Column(name="currency", type="string",  nullable=true)
     */
    private $currency="৳";


    /**
     * @var string
     *
     * @ORM\Column(name="address", type="text",  nullable=true)
     */
    private $address;

    /**
     * @var string
     *
     * @ORM\Column(name="printCss", type="text",  nullable=true)
     */
    private $printCss;

    /**
     * @var string
     *
     * @ORM\Column(name="printFooterText", type="text",  nullable=true)
     */
    private $printFooterText;


    /**
     * @var boolean
     *
     * @ORM\Column(name="homeService",  type="boolean",  nullable=true)
     */
    private $homeService;

    /**
     * @var boolean
     *
     * @ORM\Column(name="customPrint",  type="boolean",  nullable=true)
     */
    private $customPrint;

    /**
     * @var boolean
     *
     * @ORM\Column(name="printDiscountPercent",  type="boolean",  nullable=true)
     */
    private $printDiscountPercent;

    /**
     * @var boolean
     *
     * @ORM\Column(name="printPreviousDue",  type="boolean",  nullable=true)
     */
    private $printPreviousDue;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isBarcode",  type="boolean",  nullable=true)
     */
    private $isBarcode;


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
     * @return bool
     */
    public function isVatEnable()
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
     * @return bool
     */
    public function isBranch()
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
    public function isHomeService()
    {
        return $this->homeService;
    }

    /**
     * @param bool $homeService
     */
    public function setHomeService($homeService)
    {
        $this->homeService = $homeService;
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
     * @return bool
     */
    public function isInvoicePrintLogo()
    {
        return $this->invoicePrintLogo;
    }

    /**
     * @param bool $invoicePrintLogo
     */
    public function setInvoicePrintLogo($invoicePrintLogo)
    {
        $this->invoicePrintLogo = $invoicePrintLogo;
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
     * @return MedicineStock
     */
    public function getMedicineStock()
    {
        return $this->medicineStock;
    }

    /**
     * @return MedicineSales
     */
    public function getMedicineSales()
    {
        return $this->medicineSales;
    }

    /**
     * @return MedicinePurchase
     */
    public function getMedicinePurchases()
    {
        return $this->medicinePurchases;
    }

    /**
     * @return MedicineVendor
     */
    public function getMedicineVendors()
    {
        return $this->medicineVendors;
    }

    /**
     * @return MedicineParticular
     */
    public function getMedicineParticulars()
    {
        return $this->medicineParticulars;
    }

    /**
     * @return MedicineReverse
     */
    public function getMedicineReverses()
    {
        return $this->medicineReverses;
    }

    /**
     * @return MedicineSalesTemporary
     */
    public function getMedicineSalesTemporary()
    {
        return $this->medicineSalesTemporary;
    }

    /**
     * @return MedicineDamage
     */
    public function getMedicineDamages()
    {
        return $this->medicineDamages;
    }

    /**
     * @return MedicineSalesReturn
     */
    public function getMedicineSalesReturns()
    {
        return $this->medicineSalesReturns;
    }

    /**
     * @return float
     */
    public function getInstantVendorPercentage()
    {
        return $this->instantVendorPercentage;
    }

    /**
     * @param float $instantVendorPercentage
     */
    public function setInstantVendorPercentage($instantVendorPercentage)
    {
        $this->instantVendorPercentage = $instantVendorPercentage;
    }

    /**
     * @return int
     */
    public function getVendorPercentage()
    {
        return $this->vendorPercentage;
    }

    /**
     * @param float $vendorPercentage
     */
    public function setVendorPercentage($vendorPercentage)
    {
        $this->vendorPercentage = $vendorPercentage;
    }

    /**
     * @return MedicineMinimumStock
     */
    public function getMedicineMinimumStock()
    {
        return $this->medicineMinimumStock;
    }

	/**
	 * @return MedicinePrepurchase
	 */
	public function getMedicinePrepurchase() {
		return $this->medicinePrepurchase;
	}

    /**
     * @return bool
     */
    public function getPosPrint()
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
     * @return int
     */
    public function getExpiryDate()
    {
        return $this->expiryDate;
    }

    /**
     * @param int $expiryDate
     */
    public function setExpiryDate(int $expiryDate)
    {
        $this->expiryDate = $expiryDate;
    }

    /**
     * @return MedicineAndroidProcess
     */
    public function getAndroidProcesses()
    {
        return $this->androidProcesses;
    }

    /**
     * @return bool
     */
    public function isOpeningQuantity()
    {
        return $this->openingQuantity;
    }

    /**
     * @param bool $openingQuantity
     */
    public function setOpeningQuantity($openingQuantity)
    {
        $this->openingQuantity = $openingQuantity;
    }

    /**
     * @return bool
     */
    public function isRegularPosPrint()
    {
        return $this->regularPosPrint;
    }

    /**
     * @param bool $regularPosPrint
     */
    public function setRegularPosPrint($regularPosPrint)
    {
        $this->regularPosPrint = $regularPosPrint;
    }

    /**
     * @return bool
     */
    public function isPrint()
    {
        return $this->isPrint;
    }

    /**
     * @param bool $isPrint
     */
    public function setIsPrint($isPrint)
    {
        $this->isPrint = $isPrint;
    }

    /**
     * @return bool
     */
    public function isInvoiceActualPrice()
    {
        return $this->invoiceActualPrice;
    }

    /**
     * @param bool $invoiceActualPrice
     */
    public function setInvoiceActualPrice($invoiceActualPrice)
    {
        $this->invoiceActualPrice = $invoiceActualPrice;
    }

    /**
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param string $currency
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }

    /**
     * @return bool
     */
    public function isPrintOutstanding()
    {
        return $this->printOutstanding;
    }

    /**
     * @param bool $printOutstanding
     */
    public function setPrintOutstanding($printOutstanding)
    {
        $this->printOutstanding = $printOutstanding;
    }

    /**
     * @return bool
     */
    public function isBarcode()
    {
        return $this->isBarcode;
    }

    /**
     * @param bool $isBarcode
     */
    public function setIsBarcode($isBarcode)
    {
        $this->isBarcode = $isBarcode;
    }

    /**
     * @return float
     */
    public function getTpPercent()
    {
        return $this->tpPercent;
    }

    /**
     * @param float $tpPercent
     */
    public function setTpPercent($tpPercent)
    {
        $this->tpPercent = $tpPercent;
    }

    /**
     * @return float
     */
    public function getTpVatPercent()
    {
        return $this->tpVatPercent;
    }

    /**
     * @param float $tpVatPercent
     */
    public function setTpVatPercent($tpVatPercent)
    {
        $this->tpVatPercent = $tpVatPercent;
    }

    /**
     * @return bool
     */
    public function isSearchSlug()
    {
        return $this->searchSlug;
    }

    /**
     * @param bool $searchSlug
     */
    public function setSearchSlug($searchSlug)
    {
        $this->searchSlug = $searchSlug;
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
    public function isPrintDiscountPercent()
    {
        return $this->printDiscountPercent;
    }

    /**
     * @param bool $printDiscountPercent
     */
    public function setPrintDiscountPercent($printDiscountPercent)
    {
        $this->printDiscountPercent = $printDiscountPercent;
    }

    /**
     * @return bool
     */
    public function isPrintPreviousDue()
    {
        return $this->printPreviousDue;
    }

    /**
     * @param bool $printPreviousDue
     */
    public function setPrintPreviousDue($printPreviousDue)
    {
        $this->printPreviousDue = $printPreviousDue;
    }

    /**
     * @return string
     */
    public function getPrintCss()
    {
        return $this->printCss;
    }

    /**
     * @param string $printCss
     */
    public function setPrintCss($printCss)
    {
        $this->printCss = $printCss;
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
     * @return bool
     */
    public function isPurchaseItem()
    {
        return $this->purchaseItem;
    }

    /**
     * @param bool $purchaseItem
     */
    public function setPurchaseItem($purchaseItem)
    {
        $this->purchaseItem = $purchaseItem;
    }

    /**
     * @return smallint
     */
    public function getShortQuantity()
    {
        return $this->shortQuantity;
    }

    /**
     * @param smallint $shortQuantity
     */
    public function setShortQuantity($shortQuantity)
    {
        $this->shortQuantity = $shortQuantity;
    }

    /**
     * @return bool
     */
    public function isProfitLastpp()
    {
        return $this->profitLastpp;
    }

    /**
     * @param bool $profitLastpp
     */
    public function setProfitLastpp($profitLastpp)
    {
        $this->profitLastpp = $profitLastpp;
    }

    /**
     * @return mixed
     */
    public function getStockHouses()
    {
        return $this->stockHouses;
    }

    /**
     * @param mixed $stockHouses
     */
    public function setStockHouses($stockHouses)
    {
        $this->stockHouses = $stockHouses;
    }

    /**
     * @return bool
     */
    public function isInlineSales()
    {
        return $this->inlineSales;
    }

    /**
     * @param bool $inlineSales
     */
    public function setInlineSales($inlineSales)
    {
        $this->inlineSales = $inlineSales;
    }

    /**
     * @return string
     */
    public function getItemSearch()
    {
        return $this->itemSearch;
    }

    /**
     * @param string $itemSearch
     */
    public function setItemSearch($itemSearch)
    {
        $this->itemSearch = $itemSearch;
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

    /**
     * @return bool
     */
    public function isPrintOff()
    {
        return $this->printOff;
    }

    /**
     * @param bool $printOff
     */
    public function setPrintOff($printOff)
    {
        $this->printOff = $printOff;
    }

    /**
     * @return bool
     */
    public function isRemainingQuantity()
    {
        return $this->isRemainingQuantity;
    }

    /**
     * @param bool $isRemainingQuantity
     */
    public function setIsRemainingQuantity($isRemainingQuantity)
    {
        $this->isRemainingQuantity = $isRemainingQuantity;
    }

    /**
     * @return bool
     */
    public function isActiveQuantity()
    {
        return $this->isActiveQuantity;
    }

    /**
     * @param bool $isActiveQuantity
     */
    public function setIsActiveQuantity($isActiveQuantity)
    {
        $this->isActiveQuantity = $isActiveQuantity;
    }

    /**
     * @return bool
     */
    public function isSalesAvgHide()
    {
        return $this->salesAvgHide;
    }

    /**
     * @param bool $salesAvgHide
     */
    public function setSalesAvgHide($salesAvgHide)
    {
        $this->salesAvgHide = $salesAvgHide;
    }


}

