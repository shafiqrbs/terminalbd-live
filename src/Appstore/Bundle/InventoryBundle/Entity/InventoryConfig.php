<?php

namespace Appstore\Bundle\InventoryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Product\Bundle\ProductBundle\Entity\Category;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * InventoryConfig
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Appstore\Bundle\InventoryBundle\Repository\InventoryConfigRepository")
 */
class InventoryConfig
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
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\Reverse", mappedBy="inventoryConfig" , cascade={"persist", "remove"})
     */
    protected $reverses;

    /**
     * @ORM\OneToMany(targetEntity="Product\Bundle\ProductBundle\Entity\Category", mappedBy="inventoryConfig" , cascade={"persist", "remove"})
     */
    protected $categories;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\Product", mappedBy="inventoryConfig" , cascade={"persist", "remove"})
     */
    protected $products;

     /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\InventoryAndroidProcess", mappedBy="inventoryConfig" , cascade={"persist", "remove"})
     */
    protected $androidProcesses;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\PrePurchaseItem", mappedBy="inventoryConfig" , cascade={"persist", "remove"})
     */
    protected $prePurchaseItem;


    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\StockItem", mappedBy="inventoryConfig" , cascade={"persist", "remove"})
     */
    protected $stockItems;


    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\Vendor", mappedBy="inventoryConfig" , cascade={"persist", "remove"})
     */
    protected $vendors;


    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\Item", mappedBy="inventoryConfig" , cascade={"persist", "remove"})
     */
    protected $items;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\ItemBrand", mappedBy="inventoryConfig" , cascade={"persist", "remove"})
     */
    protected $brand;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\ItemColor", mappedBy="inventoryConfig" , cascade={"persist", "remove"})
     */
    protected $color;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\ItemSize", mappedBy="inventoryConfig" , cascade={"persist", "remove"})
     */
    protected $size;


    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\PurchaseVendorItem", mappedBy="inventoryConfig" , cascade={"persist", "remove"})
     */
    protected $purchaseVendorItems;

    /**
     * @ORM\OneToOne(targetEntity="Appstore\Bundle\InventoryBundle\Entity\ItemTypeGrouping", mappedBy="inventoryConfig" , cascade={"persist", "remove"})
     */
    protected $itemTypeGrouping;


     /**
     * @ORM\OneToOne(targetEntity="Appstore\Bundle\InventoryBundle\Entity\ItemSizeGroup", mappedBy="inventoryConfig" , cascade={"persist", "remove"})
     */
    protected $sizeGroup;


    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\WareHouse", mappedBy="inventoryConfig" , cascade={"persist", "remove"})
     */
    protected $wareHouses;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\Sales", mappedBy="inventoryConfig" , cascade={"persist", "remove"})
     */
    protected $sales;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\Purchase", mappedBy="inventoryConfig" , cascade={"persist", "remove"})
     */
    protected $purchases;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\Delivery", mappedBy="inventoryConfig" , cascade={"persist", "remove"})
     */
    protected $deliveries;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\DeliveryReturn", mappedBy="inventoryConfig" , cascade={"persist", "remove"})
     */
    protected $deliveryReturns;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\BranchInvoice", mappedBy="inventoryConfig" , cascade={"persist", "remove"})
     */
    protected $branchInvoices;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\SalesImport", mappedBy="inventoryConfig" , cascade={"persist", "remove"})
     */
    protected $salesImports;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\SalesReturn", mappedBy="inventoryConfig" , cascade={"persist", "remove"})
     */
    protected $salesReturn;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\PurchaseReturn", mappedBy="inventoryConfig" , cascade={"persist", "remove"})
     */
    protected $purchaseReturn;


    /**
     * @ORM\OneToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\GlobalOption", inversedBy="inventoryConfig" , cascade={"persist", "remove"})
     **/

    private $globalOption;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\ExcelImporter", mappedBy="inventoryConfig" , cascade={"persist", "remove"})
     */
    protected $excelImporters;


    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\Damage", mappedBy="inventoryConfig" , cascade={"persist", "remove"})
     */
    protected $damages;

    /**
     * @var string
     *
     * @ORM\Column(name="shopName", type="string",  nullable=true)
     */
    private $shopName;


    /**
     * @var string
     *
     * @ORM\Column(name="address", type="text", nullable = true)
     */
    private $address;


    /**
     * @var string
     *
     * @ORM\Column(name="printFooterText", type="text", nullable = true)
     */
    private $printFooterText;


    /**
     * @var string
     *
     * @ORM\Column(name="printer", type="string", length=50,nullable = true)
     */
    private $printer;

    /**
     * @var string
     *
     * @ORM\Column(name="vatMode", type="string", length=50,nullable = true)
     */
    private $vatMode;

    /**
     * @var string
     *
     * @ORM\Column(name="invoiceNote", type="string", length=50,nullable = true)
     */
    private $invoiceNote;

    /**
     * @var string
     *
     * @ORM\Column(name="templateCss", type="text", nullable = true)
     */
    private $templateCss;

    /**
     * @var string
     *
     * @ORM\Column(name="onlineSalesPrinter", type="string", length=50,nullable = true)
     */
    private $onlineSalesPrinter;


    /**
     * @var smallint
     *
     * @ORM\Column(name="salesReturnDayLimit", type="smallint",  nullable=true)
     */
    private $salesReturnDayLimit = 5;

    /**
     * @var smallint
     *
     * @ORM\Column(name="vatPercentage", type="smallint",  nullable=true)
     */
    private $vatPercentage;

    /**
     * @var string
     *
     * @ORM\Column(name="vatRegNo", type="string",  nullable=true)
     */
    private $vatRegNo;


    /**
     * @var string
     *
     * @ORM\Column(name="deliveryProcess", type="string", nullable=true )
     */
    private $deliveryProcess;


    /**
     * @var boolean
     *
     * @ORM\Column(name="isBranch", type="boolean",  nullable=true)
     */
    private $isBranch = false;

     /**
     * @var boolean
     *
     * @ORM\Column(name="isInvoice", type="boolean",  nullable=true)
     */
    private $isInvoice = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="barcodePrint", type="boolean",  nullable=true)
     */
    private $barcodePrint = false;

     /**
     * @var boolean
     *
     * @ORM\Column(name="androidSales", type="boolean",  nullable=true)
     */
    private $androidSales = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="autoPayment", type="boolean",  nullable=true)
     */
    private $autoPayment = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="invoicePrintUserMobile", type="boolean",  nullable=true)
     */
    private $invoicePrintUserMobile = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="invoicePrintLogo", type="boolean",  nullable=true)
     */
    private $invoicePrintLogo = true;

    /**
     * @var boolean
     *
     * @ORM\Column(name="barcodePriceHide", type="boolean",  nullable=true)
     */
    private $barcodePriceHide = false;

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
     * @ORM\Column(name="customPrint", type="boolean",  nullable=true)
     */
    private $customPrint = false;

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
     * @var boolean
     *
     * @ORM\Column(name="isItem", type="boolean",  nullable=true)
     */
    private $isItem = true;

    /**
     * @var boolean
     *
     * @ORM\Column(name="emptySales", type="boolean",  nullable=true)
     */
    private $emptySales = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="showSalesPrice", type="boolean",  nullable=true)
     */
    private $showSalesPrice = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="cartImage", type="boolean",  nullable=true)
     */
    private $cartImage = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="systemReset", type="boolean",  nullable=true)
     */
    private $systemReset = false;


    /**
     * @var boolean
     *
     * @ORM\Column(name="isColor", type="boolean" ,  nullable=true)
     */
    private $isColor;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isCategory", type="boolean" ,  nullable=true)
     */
    private $isCategory;


    /**
     * @var boolean
     *
     * @ORM\Column(name="isSize", type="boolean",  nullable=true)
     */
    private $isSize;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isModel", type="boolean",  nullable=true)
     */
    private $isModel;


    /**
     * @var boolean
     *
     * @ORM\Column(name="isVendor", type="boolean",  nullable=true)
     */
    private $isVendor;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isBrand", type="boolean",  nullable=true)
     */
    private $isBrand;

    /**
     * @var boolean
     *
     * @ORM\Column(name="barcodeColor", type="boolean" ,  nullable=true)
     */
    private $barcodeColor;


    /**
     * @var boolean
     *
     * @ORM\Column(name="barcodeItem", type="boolean",  nullable=true)
     */
    private $barcodeItem;

     /**
     * @var boolean
     *
     * @ORM\Column(name="barcodeSize", type="boolean",  nullable=true)
     */
    private $barcodeSize;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isAttribute", type="boolean",  nullable=true)
     */
    private $isAttribute = false;


    /**
     * @var smallint
     *
     * @ORM\Column(name="barcodeBrandVendor", type="smallint",  nullable=true)
     */
    private $barcodeBrandVendor = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="usingBarcode", type="string", length=30,nullable = true)
     */
    private $usingBarcode='item';

    /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\Currency")
     */
    private $currency;


	/**
     * @var string
     *
     * @ORM\Column(name="usingSalesPrice", type="string", length=100,nullable = true)
     */
    private $usingSalesPrice = 'item';


     /**
     * @var string
     *
     * @ORM\Column(name="barcodeText", type="string", length=100,nullable = true)
     */
    private $barcodeText;

     /**
     * @var string
     *
     * @ORM\Column(name="salesMode", type="string", length=100,nullable = true)
     */
    private $salesMode;

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
     * @ORM\Column(name="barcodePageTopMargin", type="smallint", nullable = true)
     */
    private $barcodePageTopMargin = 0;

    /**
     * @var smallint
     *
     * @ORM\Column(name="barcodePageLeftMargin", type="smallint", nullable = true)
     */
    private $barcodePageLeftMargin = 0;

     /**
     * @var smallint
     *
     * @ORM\Column(name="barcodePerRow", type="smallint", nullable = true)
     */
    private $barcodePerRow = 5;

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
     * @ORM\Column(name="isPos", type="boolean")
     */
    private $isPos = false;


    /**
     * @var boolean
     *
     * @ORM\Column(name="removeImage", type="boolean")
     */
    private $removeImage = false;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $path;

    /**
     * @Assert\File(maxSize="8388608")
     */
    protected $file;


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
     * @return mixed
     */
    public function getVendors()
    {
        return $this->vendors;
    }

    /**
     * @return Category
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * @return ItemTypeGrouping
     */
    public function getItemTypeGrouping()
    {
        return $this->itemTypeGrouping;
    }

    /**
     * @return Purchase
     */
    public function getPurchases()
    {
        return $this->purchases;
    }

    /**
     * @return StockItem
     */
    public function getStockItems()
    {
        return $this->stockItems;
    }

    /**
     * @return WareHouse
     */
    public function getWareHouses()
    {
        return $this->wareHouses;
    }

    /**
     * @return Product
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * @return Sales
     */
    public function getSales()
    {
        return $this->sales;
    }

    /**
     * @return PurchaseReturn
     */
    public function getPurchaseReturn()
    {
        return $this->purchaseReturn;
    }

    /**
     * @return smallint
     */
    public function getSalesReturnDayLimit()
    {
        return $this->salesReturnDayLimit;
    }

    /**
     * @param smallint $salesReturnDayLimit
     */
    public function setSalesReturnDayLimit($salesReturnDayLimit)
    {
        $this->salesReturnDayLimit = $salesReturnDayLimit;
    }

    /**
     * @return ExcelImporter
     */
    public function getExcelImporters()
    {
        return $this->excelImporters;
    }



    /**
     * @return boolean
     */
    public function getIsItem()
    {
        return $this->isItem;
    }

    /**
     * @param boolean $isItem
     */
    public function setIsItem($isItem)
    {
        $this->isItem = $isItem;
    }

    /**
     * @return boolean
     */
    public function getIsColor()
    {
        return $this->isColor;
    }

    /**
     * @param boolean $isColor
     */
    public function setIsColor($isColor)
    {
        $this->isColor = $isColor;
    }

    /**
     * @return boolean
     */
    public function getIsSize()
    {
        return $this->isSize;
    }

    /**
     * @param boolean $isSize
     */
    public function setIsSize($isSize)
    {
        $this->isSize = $isSize;
    }

    /**
     * @return boolean
     */
    public function getIsVendor()
    {
        return $this->isVendor;
    }

    /**
     * @param boolean $isVendor
     */
    public function setIsVendor($isVendor)
    {
        $this->isVendor = $isVendor;
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
     * @return boolean
     */
    public function getVatEnable()
    {
        return $this->vatEnable;
    }

    /**
     * @param boolean $vatEnable
     */
    public function setVatEnable($vatEnable)
    {
        $this->vatEnable = $vatEnable;
    }


    /**
     * @return boolean
     */
    public function getIsBrand()
    {
        return $this->isBrand;
    }

    /**
     * @param boolean $isBrand
     */
    public function setIsBrand($isBrand)
    {
        $this->isBrand = $isBrand;
    }


    /**
     * @return Damage
     */
    public function getDamages()
    {
        return $this->damages;
    }

    /**
     * @return PurchaseVendorItem
     */
    public function getPurchaseVendorItems()
    {
        return $this->purchaseVendorItems;
    }


    /**
     * @return boolean
     */
    public function getIsBranchInvoice()
    {
        return $this->isBranchInvoice;
    }

    /**
     * @param boolean $isBranchInvoice
     */
    public function setIsBranchInvoice($isBranchInvoice)
    {
        $this->isBranchInvoice = $isBranchInvoice;
    }

    /**
     * @return boolean
     */
    public function getIsBranch()
    {
        return $this->isBranch;
    }

    /**
     * @param boolean $isBranch
     */
    public function setIsBranch($isBranch)
    {
        $this->isBranch = $isBranch;
    }

    /**
     * @return Delivery
     */
    public function getDeliveries()
    {
        return $this->deliveries;
    }

    /**
     * @return boolean
     */
    public function getBarcodeColor()
    {
        return $this->barcodeColor;
    }

    /**
     * @param boolean $barcodeColor
     */
    public function setBarcodeColor($barcodeColor)
    {
        $this->barcodeColor = $barcodeColor;
    }

    /**
     * @return boolean
     */
    public function getBarcodeSize()
    {
        return $this->barcodeSize;
    }

    /**
     * @param boolean $barcodeSize
     */
    public function setBarcodeSize($barcodeSize)
    {
        $this->barcodeSize = $barcodeSize;
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
     * @return boolean
     */
    public function isModel()
    {
        return $this->isModel;
    }

    /**
     * @param boolean $isModel
     */
    public function setIsModel($isModel)
    {
        $this->isModel = $isModel;
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
     * @return boolean
     */
    public function getIsPrintHeader()
    {
        return $this->isPrintHeader;
    }

    /**
     * @param boolean $isPrintHeader
     */
    public function setIsPrintHeader($isPrintHeader)
    {
        $this->isPrintHeader = $isPrintHeader;
    }

    /**
     * @return boolean
     */
    public function getIsPrintFooter()
    {
        return $this->isPrintFooter;
    }

    /**
     * @param boolean $isPrintFooter
     */
    public function setIsPrintFooter($isPrintFooter)
    {
        $this->isPrintFooter = $isPrintFooter;
    }

    /**
     * @return bool
     */
    public function getBarcodePrint()
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
     * @return string
     */
    public function getPrinter()
    {
        return $this->printer;
    }

    /**
     * @param string $printer
     * Printer
     * Pos Printer
     * Save
     */
    public function setPrinter($printer)
    {
        $this->printer = $printer;
    }

    /**
     * @return string
     */
    public function getShopName()
    {
        return $this->shopName;
    }

    /**
     * @param string $shopName
     */
    public function setShopName($shopName)
    {
        $this->shopName = $shopName;
    }

    /**
     * @return DeliveryReturn
     */
    public function getDeliveryReturns()
    {
        return $this->deliveryReturns;
    }

    /**
     * @return string
     */
    public function getOnlineSalesPrinter()
    {
        return $this->onlineSalesPrinter;
    }

    /**
     * @param string $onlineSalesPrinter
     */
    public function setOnlineSalesPrinter($onlineSalesPrinter)
    {
        $this->onlineSalesPrinter = $onlineSalesPrinter;
    }

    /**
     * @return ItemSizeGroup
     */
    public function getSizeGroup()
    {
        return $this->sizeGroup;
    }

    /**
     * @return boolean
     */
    public function getInvoicePrintUserMobile()
    {
        return $this->invoicePrintUserMobile;
    }

    /**
     * @param boolean $invoicePrintUserMobile
     */
    public function setInvoicePrintUserMobile($invoicePrintUserMobile)
    {
        $this->invoicePrintUserMobile = $invoicePrintUserMobile;
    }

    /**
     * @return boolean
     */
    public function getInvoicePrintLogo()
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
    public function isAttribute()
    {
        return $this->isAttribute;
    }

    /**
     * @param bool $isAttribute
     */
    public function setIsAttribute($isAttribute)
    {
        $this->isAttribute = $isAttribute;
    }

    /**
     * @return string
     */
    public function getDeliveryProcess()
    {
        return $this->deliveryProcess;
    }

    /**
     * @param string $deliveryProcess
     */
    public function setDeliveryProcess($deliveryProcess)
    {
        $this->deliveryProcess = $deliveryProcess;
    }

    /**
     * @return Reverse
     */
    public function getReverses()
    {
        return $this->reverses;
    }

    /**
     * @return smallint
     */
    public function getBarcodePageTopMargin()
    {
        return $this->barcodePageTopMargin;
    }

    /**
     * @param smallint $barcodePageTopMargin
     */
    public function setBarcodePageTopMargin($barcodePageTopMargin)
    {
        $this->barcodePageTopMargin = $barcodePageTopMargin;
    }

    /**
     * @return smallint
     */
    public function getBarcodePageLeftMargin()
    {
        return $this->barcodePageLeftMargin;
    }

    /**
     * @param smallint $barcodePageLeftMargin
     */
    public function setBarcodePageLeftMargin($barcodePageLeftMargin)
    {
        $this->barcodePageLeftMargin = $barcodePageLeftMargin;
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
     * @return bool
     */
    public function getBarcodePriceHide()
    {
        return $this->barcodePriceHide;
    }

    /**
     * @param bool $barcodePriceHide
     */
    public function setBarcodePriceHide($barcodePriceHide)
    {
        $this->barcodePriceHide = $barcodePriceHide;
    }

    /**
     * @return PrePurchaseItem
     */
    public function getPrePurchaseItem()
    {
        return $this->prePurchaseItem;
    }

	/**
	 * @return string
	 */
	public function getUsingBarcode(){
		return $this->usingBarcode;
	}

	/**
	 * @param string $usingBarcode
	 */
	public function setUsingBarcode($usingBarcode ) {
		$this->usingBarcode = $usingBarcode;
	}

	/**
	 * @return string
	 */
	public function getUsingSalesPrice(){
		return $this->usingSalesPrice;
	}

	/**
	 * @param string $usingSalesPrice
	 */
	public function setUsingSalesPrice($usingSalesPrice ) {
		$this->usingSalesPrice = $usingSalesPrice;
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
     * @return bool
     */
    public function isCartImage()
    {
        return $this->cartImage;
    }

    /**
     * @param bool $cartImage
     */
    public function setCartImage($cartImage)
    {
        $this->cartImage = $cartImage;
    }


    /**
     * Sets file.
     *
     * @param InventoryConfig $file
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
    }

    /**
     * Get file.
     *
     * @return InventoryConfig
     */
    public function getFile()
    {
        return $this->file;
    }

    public function getAbsolutePath()
    {
        return null === $this->path
            ? null
            : $this->getUploadRootDir().'/'.$this->path;
    }

    public function getWebPath()
    {
        return null === $this->path
            ? null
            : $this->getUploadDir().'/' . $this->path;
    }



    protected function getUploadRootDir()
    {
        return __DIR__.'/../../../../../web/'.$this->getUploadDir();
    }

    protected function getUploadDir()
    {
        return 'uploads/domain/'.$this->getGlobalOption()->getId().'/inventory';
    }

    public function removeUpload()
    {
        if ($file = $this->getAbsolutePath()) {
            unlink($file);
            $this->path = null ;
        }
    }

    public function upload()
    {
        // the file property can be empty if the field is not required
        if (null === $this->getFile()) {
            return;
        }

        // use the original file name here but you should
        // sanitize it at least to avoid any security issues

        // move takes the target directory and then the
        // target filename to move to
        $filename = date('YmdHmi') . "_" . $this->getFile()->getClientOriginalName();
        $this->getFile()->move(
            $this->getUploadRootDir(),
            $filename
        );

        // set the path property to the filename where you've saved the file
        $this->path = $filename ;

        // clean up the file property as you won't need it anymore
        $this->file = null;
    }

    /**
     * @return boolean
     */
    public function getRemoveImage()
    {
        return $this->removeImage;
    }

    /**
     * @param boolean $removeImage
     */
    public function setRemoveImage($removeImage)
    {
        $this->removeImage = $removeImage;
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
    public function isPos()
    {
        return $this->isPos;
    }

    /**
     * @param bool $isPos
     */
    public function setIsPos($isPos)
    {
        $this->isPos = $isPos;
    }

    /**
     * @return mixed
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param mixed $currency
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }

    /**
     * @return bool
     */
    public function isBarcodeItem()
    {
        return $this->barcodeItem;
    }

    /**
     * @param bool $barcodeItem
     */
    public function setBarcodeItem($barcodeItem)
    {
        $this->barcodeItem = $barcodeItem;
    }

    /**
     * @return smallint
     */
    public function getBarcodePerRow()
    {
        return $this->barcodePerRow;
    }

    /**
     * @param smallint $barcodePerRow
     */
    public function setBarcodePerRow($barcodePerRow)
    {
        $this->barcodePerRow = $barcodePerRow;
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
    public function isAndroidSales()
    {
        return $this->androidSales;
    }

    /**
     * @param bool $androidSales
     */
    public function setAndroidSales($androidSales)
    {
        $this->androidSales = $androidSales;
    }

    /**
     * @return bool
     */
    public function isInvoice()
    {
        return $this->isInvoice;
    }

    /**
     * @param bool $isInvoice
     */
    public function setIsInvoice($isInvoice)
    {
        $this->isInvoice = $isInvoice;
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
    public function isCategory()
    {
        return $this->isCategory;
    }

    /**
     * @param bool $isCategory
     */
    public function setIsCategory($isCategory)
    {
        $this->isCategory = $isCategory;
    }

    /**
     * @return bool
     */
    public function isEmptySales()
    {
        return $this->emptySales;
    }

    /**
     * @param bool $emptySales
     */
    public function setEmptySales($emptySales)
    {
        $this->emptySales = $emptySales;
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
    public function isShowSalesPrice()
    {
        return $this->showSalesPrice;
    }

    /**
     * @param bool $showSalesPrice
     */
    public function setShowSalesPrice($showSalesPrice)
    {
        $this->showSalesPrice = $showSalesPrice;
    }



}

