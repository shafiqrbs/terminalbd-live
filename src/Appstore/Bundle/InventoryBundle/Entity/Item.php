<?php

namespace Appstore\Bundle\InventoryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Item
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Appstore\Bundle\InventoryBundle\Repository\ItemRepository")
 * @UniqueEntity(fields={"name","inventoryConfig"}, message="This name must be unique")
 * @UniqueEntity(fields={"barcode","inventoryConfig"}, message="This barcode must be unique")
 * @ORM\HasLifecycleCallbacks
 */


class Item
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
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\InventoryBundle\Entity\InventoryConfig", inversedBy="items" )
     **/
    private  $inventoryConfig;


    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\InventoryBundle\Entity\Vendor", inversedBy="items" )
     **/
    private  $vendor;

    /**
     * @ORM\ManyToOne(targetEntity="Product\Bundle\ProductBundle\Entity\ItemGroup")
     **/
    private  $itemGroup;

     /**
     * @ORM\ManyToOne(targetEntity="Product\Bundle\ProductBundle\Entity\Category",inversedBy="items")
     **/
    private  $category;

     /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\ProductUnit")
     **/
    private  $itemUnit;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\PurchaseItem", mappedBy="item" , cascade={"remove"} )
     * @ORM\OrderBy({"id" = "DESC"})
     */
    protected $purchaseItems;


    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\PurchaseItemSerial", mappedBy="item" , cascade={"remove"} )
     * @ORM\OrderBy({"id" = "DESC"})
     */
    protected $itemSerials;


    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\StockItem", mappedBy="item" , cascade={"remove"} )
     */
    protected $stockItems;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\SalesItem", mappedBy="item" , cascade={"remove"} )
     */
    protected $salesItems;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\BranchInvoiceItem", mappedBy="item" , cascade={"remove"} )
     */
    protected $branchInvoiceItems;

     /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\Damage", mappedBy="item" , cascade={"remove"} )
     */
    protected $damages;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\DeliveryItem", mappedBy="item" , cascade={"remove"} )
     */
    protected $deliveryItems;


    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\DeliveryReturn", mappedBy="item" , cascade={"remove"} )
     */
    protected $deliveryReturns;


    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255,nullable = true)
     */
    private $name;


    /**
     * @var string
     *
     * @ORM\Column(name="nameBn", type="string", length=255,nullable = true)
     */
    private $nameBn;


    /**
     * @var string
     *
     * @ORM\Column(name="sku", type="string", length=255,nullable = true)
     */
    private $sku;

     /**
     * @var string
     *
     * @ORM\Column(name="barcode", type="string", length=255,nullable = true)
     */
    private $barcode;

     /**
     * @var string
     *
     * @ORM\Column(name="model", type="string", length=255,nullable = true)
     */
    private $model;

    /**
     * @var string
     *
     * @ORM\Column(name="skuSlug", type="string", length=255,nullable = true)
     */
    private $skuSlug;

    /**
     * @var string
     *
     * @ORM\Column(name="skuWebSlug", type="string", length=255,nullable = true)
     */
    private $skuWebSlug;

    /**
     * @Gedmo\Slug(fields={"skuWebSlug"})
     * @Doctrine\ORM\Mapping\Column(length=255)
     */
    private $slug;

    /**
     * @var integer
     *
     * @ORM\Column(name="code", type="integer", nullable=true)
     */
    private $code;

    /**
     * @var integer
     *
     * @ORM\Column(name="quantity", type="integer", length=20, nullable=true)
     */
    private $quantity;

    /**
     * @var float
     *
     * @ORM\Column(name="purchaseQuantity", type="float", length=20, nullable=true)
     */
    private $purchaseQuantity;


    /**
     * @var float
     *
     * @ORM\Column(name="purchaseQuantityReturn", type="float", length=20, nullable=true)
     */
    private $purchaseQuantityReturn;

    /**
     * @var float
     *
     * @ORM\Column(name="purchaseQuantityReplace", type="float", length=20, nullable=true)
     */
    private $purchaseQuantityReplace;

    /**
     * @var float
     *
     * @ORM\Column(name="salesQuantity", type="float", length=20, nullable=true)
     */
    private $salesQuantity;

    /**
     * @var float
     *
     * @ORM\Column(name="adjustmentQuantity", type="float", length=20, nullable=true)
     */
    private $adjustmentQuantity;

    /**
     * @var float
     *
     * @ORM\Column(name="bonusAdjustment", type="float", length=20, nullable=true)
     */
    private $bonusAdjustment;

    /**
     * @var float
     *
     * @ORM\Column(name="salesQuantityReturn", type="float", length=20, nullable=true)
     */
    private $salesQuantityReturn;

    /**
     * @var float
     *
     * @ORM\Column(name="damageQuantity", type="float", length=20, nullable=true)
     */
    private $damageQuantity;

    /**
     * @var float
     *
     * @ORM\Column(name="onlineOrderQuantity", type="float", length=20, nullable=true)
     */
    private $onlineOrderQuantity;

    /**
     * @var float
     *
     * @ORM\Column(name="onlineOrderQuantityReturn", type="float", length=20, nullable=true)
     */
    private $onlineOrderQuantityReturn;


    /**
     * @var integer
     *
     * @ORM\Column(name="minQnt", type="integer", nullable=true)
     */
    private $minQnt;


    /**
     * @var integer
     *
     * @ORM\Column(name="maxQnt", type="integer",  nullable=true)
     */
    private $maxQnt;


    /**
     * @var integer
     *
     * @ORM\Column(name="openingQuantity", type="integer",  nullable=true)
     */
    private $openingQuantity;


    /**
     * @var integer
     *
     * @ORM\Column(name="remainingQnt", type="integer", nullable=true)
     */
    private $remainingQnt = 0;


    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updated", type="datetime")
     */
    private $updated;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\InventoryBundle\Entity\Product", inversedBy="items" )
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private  $masterItem;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\InventoryBundle\Entity\ItemColor", inversedBy="item" )
     **/
    private  $color;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\InventoryBundle\Entity\ItemSize", inversedBy="item" )
     **/
    private  $size;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\InventoryBundle\Entity\ItemBrand", inversedBy="items")
     */
    protected $brand;


    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="boolean", nullable=true)
     */
    private $status = true;

    /**
     * @var integer
     *
     * @ORM\Column(name="modelYear", type="integer", nullable=true)
     */
    private $modelYear;


    /**
     * @var float
     *
     * @ORM\Column(name="salesPrice", type="float", nullable=true)
     */
    private $salesPrice = 1;


    /**
     * @var float
     *
     * @ORM\Column(name="salesAvgPrice", type="float", nullable=true)
     */
    private $salesAvgPrice;


    /**
     * @var float
     *
     * @ORM\Column(name="price", type="float", nullable=true)
     */
    private $price;


    /**
     * @var float
     *
     * @ORM\Column(name="discountPrice", type="float", nullable=true)
     */
    private $discountPrice;


    /**
     * @var float
     *
     * @ORM\Column(name="purchasePrice", type="float", nullable=true)
     */
    private $purchasePrice =0;


    /**
     * @var float
     *
     * @ORM\Column(name="purchaseAvgPrice", type="float", nullable=true)
     */
    private $purchaseAvgPrice;


    /**
     * @var boolean
     *
     * @ORM\Column(name="isWeb", type="boolean")
     */
    private $isWeb = false;


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

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Item
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }


    /**
     * @return mixed
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param mixed $slug
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    }


    /**
     * @return string
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param string $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }

    /**
     * @return boolean
     */
    public function isStatus()
    {
        return $this->status;
    }

    /**
     * @param boolean $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }


    /**
     * @return int
     */
    public function getMinQnt()
    {
        return $this->minQnt;
    }

    /**
     * @param int $minQnt
     */
    public function setMinQnt($minQnt)
    {
        $this->minQnt = $minQnt;
    }

    /**
     * @return int
     */
    public function getMaxQnt()
    {
        return $this->maxQnt;
    }

    /**
     * @param int $maxQnt
     */
    public function setMaxQnt($maxQnt)
    {
        $this->maxQnt = $maxQnt;
    }

    /**
     * @return int
     */
    public function getRemainingQnt()
    {
        return $this->remainingQnt;
    }

    /**
     * @param int $remainingQnt
     */
    public function setRemainingQnt($remainingQnt)
    {
        $this->remainingQnt = $remainingQnt;
    }

    /**
     * @return mixed
     */
    public function getInventoryConfig()
    {
        return $this->inventoryConfig;
    }

    /**
     * @param mixed $inventoryConfig
     */
    public function setInventoryConfig($inventoryConfig)
    {
        $this->inventoryConfig = $inventoryConfig;
    }

    /**
     * @return mixed
     */
    public function getPurchaseItems()
    {
        return $this->purchaseItems;
    }

    /**
     * @param mixed $purchaseItems
     */
    public function setPurchaseItems($purchaseItems)
    {
        $this->purchaseItems = $purchaseItems;
    }

    /**
     * @return float
     */
    public function getPurchaseAvgPrice()
    {
        return $this->purchaseAvgPrice;
    }

    /**
     * @param float $purchaseAvgPrice
     */
    public function setPurchaseAvgPrice($purchaseAvgPrice)
    {
        $this->purchaseAvgPrice = $purchaseAvgPrice;
    }

     /**
     * @return string
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param string $quantity
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }


    /**
     * Sets file.
     *
     * @param WebTheme $file
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
    }

    /**
     * Get file.
     *
     * @return WebTheme
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
            : $this->getUploadDir().'/'.$this->path;
    }

    protected function getUploadRootDir()
    {
        return __DIR__.'/../../../../../web/'.$this->getUploadDir();
    }

    protected function getUploadDir()
    {
        //return 'uploads/files/inventory/purchase/'.$this->getPurchasePrice().'item/';
        return 'uploads/domain/'.$this->getInventoryConfig()->getGlobalOption()->getId().'/product/';
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
        $this->getFile()->move(
            $this->getUploadRootDir(),
            $this->getFile()->getClientOriginalName()
        );

        // set the path property to the filename where you've saved the file
        $this->path = $this->getFile()->getClientOriginalName();

        // clean up the file property as you won't need it anymore
        $this->file = null;
    }

    /**
     * @return mixed
     */
    public function getBrand()
    {
        return $this->brand;
    }

    /**
     * @param mixed $brand
     */
    public function setBrand($brand)
    {
        $this->brand = $brand;
    }

    /**
     * @return mixed
     */
    public function getVendor()
    {
        return $this->vendor;
    }

    /**
     * @param mixed $vendor
     */
    public function setVendor($vendor)
    {
        $this->vendor = $vendor;
    }

    /**
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * @param \DateTime $updated
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
    }


    /**
     * @return mixed
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * @param mixed $color
     */
    public function setColor($color)
    {
        $this->color = $color;
    }

    /**
     * @return mixed
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @param mixed $size
     */
    public function setSize($size)
    {
        $this->size = $size;
    }

    /**
     * @return string
     */
    public function getSku()
    {
        return $this->sku;
    }

    /**
     * @param string $sku
     */
    public function setSku($sku)
    {
        $this->sku = $sku;
    }

    /**
     * @return Product
     */
    public function getMasterItem()
    {
        return $this->masterItem;
    }

    /**
     * @param mixed $masterItem
     */
    public function setMasterItem($masterItem)
    {
        $this->masterItem = $masterItem;
    }

    /**
     * @return string
     */
    public function getSkuWebSlug()
    {
        return $this->skuWebSlug;
    }

    /**
     * @param string $skuWebSlug
     */
    public function setSkuWebSlug($skuWebSlug)
    {
        $this->skuWebSlug = $skuWebSlug;
    }

    /**
     * @return string
     */
    public function getSkuSlug()
    {
        return $this->skuSlug;
    }

    /**
     * @param string $skuSlug
     */
    public function setSkuSlug($skuSlug)
    {
        $this->skuSlug = $skuSlug;
    }

    /**
     * @return mixed
     */
    public function getSalesItems()
    {
        return $this->salesItems;
    }

    /**
     * @return float
     */
    public function getPurchaseQuantity()
    {
        return $this->purchaseQuantity;
    }

    /**
     * @param float $purchaseQuantity
     */
    public function setPurchaseQuantity($purchaseQuantity)
    {
        $this->purchaseQuantity = $purchaseQuantity;
    }

    /**
     * @return float
     */
    public function getPurchaseQuantityReturn()
    {
        return $this->purchaseQuantityReturn;
    }

    /**
     * @param float $purchaseQuantityReturn
     */
    public function setPurchaseQuantityReturn($purchaseQuantityReturn)
    {
        $this->purchaseQuantityReturn = $purchaseQuantityReturn;
    }

    /**
     * @return float
     */
    public function getSalesQuantity()
    {
        return $this->salesQuantity;
    }

    /**
     * @param float $salesQuantity
     */
    public function setSalesQuantity($salesQuantity)
    {
        $this->salesQuantity = $salesQuantity;
    }

    /**
     * @return float
     */
    public function getSalesQuantityReturn()
    {
        return $this->salesQuantityReturn;
    }

    /**
     * @param float $salesQuantityReturn
     */
    public function setSalesQuantityReturn($salesQuantityReturn)
    {
        $this->salesQuantityReturn = $salesQuantityReturn;
    }

    /**
     * @return integer
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param integer $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    public function  getItemBaseQuantity($process = null)
    {
        $quantity = 0;
        foreach($this->stockItems AS $item) {
            if ($item->getProcess() == $process ){
                $quantity += $item->getQuantity(); //$recipecost now $this->recipecost.
            }
        }
        return $quantity ;
    }

    /**
     * @return float
     */
    public function getPurchaseQuantityReplace()
    {
        return $this->purchaseQuantityReplace;
    }

    /**
     * @param float $purchaseQuantityReplace
     */
    public function setPurchaseQuantityReplace($purchaseQuantityReplace)
    {
        $this->purchaseQuantityReplace = $purchaseQuantityReplace;
    }

    /**
     * @return mixed
     */
    public function getStockItems()
    {
        return $this->stockItems;
    }


    /**
     * @param boolean $isWeb
     */
    public function setIsWeb($isWeb)
    {
        $this->isWeb = $isWeb;
    }


    /**
     * @return boolean $isWeb
     */
    public function getIsWeb()
    {
        return $this->isWeb;
    }

    public function  getAvgPurchasePrice()
    {
        $purchasePrice = 0;
        $i = 0;
        if(!$this->purchaseItems->isEmpty()) {
            foreach($this->purchaseItems AS $item) {
                $purchasePrice += $item->getPurchasePrice(); //$recipecost now $this->recipecost.
                $i++;
            }
            $avgPrice = round($purchasePrice/$i);
            return $avgPrice ;
        }
        return false;

    }

    public function  getAvgSalesPrice()
    {
        $salesPrice = 0;
        $i = 0;
        if(!$this->purchaseItems->isEmpty()) {
            foreach ($this->purchaseItems AS $item) {
                $salesPrice += $item->getSalesPrice(); //$recipecost now $this->recipecost.
                $i++;
            }
            $avgPrice = round($salesPrice / $i);
            return $avgPrice;
        }
        return false;
    }

    /**
     * @return float
     */
    public function getDamageQuantity()
    {
        return $this->damageQuantity;
    }

    /**
     * @param float $damageQuantity
     */
    public function setDamageQuantity($damageQuantity)
    {
        $this->damageQuantity = $damageQuantity;
    }

    /**
     * @return Damage
     */
    public function getDamages()
    {
        return $this->damages;
    }

    /**
     * @return BranchInvoiceItem
     */
    public function getBranchInvoiceItems()
    {
        return $this->branchInvoiceItems;
    }

    /**
     * @return float
     */
    public function getOnlineOrderQuantity()
    {
        return $this->onlineOrderQuantity;
    }

    /**
     * @param float $onlineOrderQuantity
     */
    public function setOnlineOrderQuantity($onlineOrderQuantity)
    {
        $this->onlineOrderQuantity = $onlineOrderQuantity;
    }

    /**
     * @return float
     */
    public function getOnlineOrderQuantityReturn()
    {
        return $this->onlineOrderQuantityReturn;
    }

    /**
     * @param float $onlineOrderQuantityReturn
     */
    public function setOnlineOrderQuantityReturn($onlineOrderQuantityReturn)
    {
        $this->onlineOrderQuantityReturn = $onlineOrderQuantityReturn;
    }

    /**
     * @return mixed
     */
    public function getSTRPadCode()
    {
        $code = str_pad($this->getCode(),3, '0', STR_PAD_LEFT);
        return $code;
    }

    /**
     * @return mixed
     */
    public function getMasterSTRPadCode()
    {
        $code = str_pad($this->getCode(),3, '0', STR_PAD_LEFT);
        return $code;
    }

    /**
     * @return DeliveryReturn
     */
    public function getDeliveryReturns()
    {
        return $this->deliveryReturns;
    }

    /**
     * @return mixed
     */
    public function getDeliveryItems()
    {
        return $this->deliveryItems;
    }

    public function getRemainingQuantity()
    {
        $remaingQnt = ($this->getPurchaseQuantity() + $this->getSalesQuantityReturn() + $this->getOnlineOrderQuantityReturn()) - ($this->getPurchaseQuantityReturn() + $this->getSalesQuantity() + $this->getDamageQuantity() + $this->getOnlineOrderQuantity());
        return $remaingQnt;
    }

    /**
     * @return float
     */
    public function getSalesPrice()
    {
        return $this->salesPrice;
    }

    /**
     * @param float $salesPrice
     */
    public function setSalesPrice($salesPrice)
    {
        $this->salesPrice = $salesPrice;
    }

    public function getItemName(){
        $unit ='';
        if($this->getMasterItem()->getProductUnit()){
            $unit = $this->getMasterItem()->getProductUnit()->getName();
        }
       return $this->getSku().'-'.$this->getMasterItem()->getName().'-'.$this->getRemainingQuantity().' '.$unit;
    }

    public function getItemSKUName(){
       return $this->getSku().' => '.$this->getName();
    }

    /**
     * @return string
     */
    public function getBarcode()
    {
        return $this->barcode;
    }

    /**
     * @param string $barcode
     */
    public function setBarcode($barcode)
    {
        $this->barcode = $barcode;
    }

    /**
     * @return float
     */
    public function getPurchasePrice()
    {
        return $this->purchasePrice;
    }

    /**
     * @param float $purchasePrice
     */
    public function setPurchasePrice($purchasePrice)
    {
        $this->purchasePrice = $purchasePrice;
    }

    /**
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param float $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }

    /**
     * @return float
     */
    public function getDiscountPrice()
    {
        return $this->discountPrice;
    }

    /**
     * @param float $discountPrice
     */
    public function setDiscountPrice($discountPrice)
    {
        $this->discountPrice = $discountPrice;
    }

    /**
     * @return mixed
     */
    public function getItemGroup()
    {
        return $this->itemGroup;
    }

    /**
     * @param mixed $itemGroup
     */
    public function setItemGroup($itemGroup)
    {
        $this->itemGroup = $itemGroup;
    }

    /**
     * @return mixed
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param mixed $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
    }

    /**
     * @return mixed
     */
    public function getItemUnit()
    {
        return $this->itemUnit;
    }

    /**
     * @param mixed $itemUnit
     */
    public function setItemUnit($itemUnit)
    {
        $this->itemUnit = $itemUnit;
    }

    /**
     * @return int
     */
    public function getModelYear()
    {
        return $this->modelYear;
    }

    /**
     * @param int $modelYear
     */
    public function setModelYear($modelYear)
    {
        $this->modelYear = $modelYear;
    }

    /**
     * @return string
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param string $model
     */
    public function setModel($model)
    {
        $this->model = $model;
    }

    /**
     * @return float
     */
    public function getSalesAvgPrice()
    {
        return $this->salesAvgPrice;
    }

    /**
     * @param float $salesAvgPrice
     */
    public function setSalesAvgPrice($salesAvgPrice)
    {
        $this->salesAvgPrice = $salesAvgPrice;
    }

    /**
     * @return float
     */
    public function getAdjustmentQuantity()
    {
        return $this->adjustmentQuantity;
    }

    /**
     * @param float $adjustmentQuantity
     */
    public function setAdjustmentQuantity($adjustmentQuantity)
    {
        $this->adjustmentQuantity = $adjustmentQuantity;
    }

    /**
     * @return float
     */
    public function getBonusAdjustment()
    {
        return $this->bonusAdjustment;
    }

    /**
     * @param float $bonusAdjustment
     */
    public function setBonusAdjustment($bonusAdjustment)
    {
        $this->bonusAdjustment = $bonusAdjustment;
    }

    /**
     * @return int
     */
    public function getOpeningQuantity()
    {
        return $this->openingQuantity;
    }

    /**
     * @param int $openingQuantity
     */
    public function setOpeningQuantity($openingQuantity)
    {
        $this->openingQuantity = $openingQuantity;
    }

    /**
     * @return mixed
     */
    public function getItemSerials()
    {
        return $this->itemSerials;
    }

    /**
     * @return mixed
     */
    public function getEcommerceItem()
    {
        return $this->ecommerceItem;
    }

    /**
     * @return string
     */
    public function getNameBn()
    {
        return $this->nameBn;
    }

    /**
     * @param string $nameBn
     */
    public function setNameBn($nameBn)
    {
        $this->nameBn = $nameBn;
    }




}

