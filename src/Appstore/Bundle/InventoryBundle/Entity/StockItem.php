<?php

namespace Appstore\Bundle\InventoryBundle\Entity;


use Core\UserBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Setting\Bundle\ToolBundle\Entity\ProductUnit;

/**
 * StockItem
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Appstore\Bundle\InventoryBundle\Repository\StockItemRepository")
 */
class StockItem
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
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\InventoryBundle\Entity\InventoryConfig", inversedBy="stockItems" )
     **/
    protected  $inventoryConfig;



    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\InventoryBundle\Entity\PurchaseItem", inversedBy="stockItem")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    protected $purchaseItem;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\InventoryBundle\Entity\PurchaseReturnItem", inversedBy="stockItems")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    protected $purchaseReturnItem;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\InventoryBundle\Entity\PurchaseReturnItem", inversedBy="stockReplaceItems")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    protected $purchaseReplaceItem;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\InventoryBundle\Entity\Damage", inversedBy="stockItems")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    protected $damage;


    /**
     * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User", inversedBy="stockItems")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    protected $createdBy;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\InventoryBundle\Entity\SalesItem", inversedBy="stockItem")
     */
    protected $salesItem;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\InventoryBundle\Entity\SalesReturnItem", inversedBy="stockReturnItems")
     */
    protected $salesReturnItem;

     /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\InventoryBundle\Entity\SalesReturnItem", inversedBy="stockReplaceItems")
     */
    protected $salesItemReplace;


    /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\LocationBundle\Entity\Country", inversedBy="stockItems")
     */
    protected $country;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\InventoryBundle\Entity\Vendor", inversedBy="stockItems")
     */
    protected $vendor;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\InventoryBundle\Entity\ItemBrand", inversedBy="stockItems")
     */
    protected $brand;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\InventoryBundle\Entity\ItemSize", inversedBy="stockItems")
     */
    protected $size;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\InventoryBundle\Entity\ItemColor", inversedBy="stockItems")
     */
    protected $color;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\InventoryBundle\Entity\Product", inversedBy="stockItems")
     */
    protected $product;

    /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\ProductUnit", inversedBy="stockItems" )
     **/
    private  $unit;

    /**
     * @ORM\ManyToOne(targetEntity="Product\Bundle\ProductBundle\Entity\Category", inversedBy="stockItems" )
     **/
    private  $category;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\InventoryBundle\Entity\Item", inversedBy="stockItems")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    protected $item;


    /**
     * @var string
     *
     * @ORM\Column(name="productName", type="string", nullable = true)
     */
    private $productName;

    /**
     * @var string
     *
     * @ORM\Column(name="VendorName", type="string", nullable = true)
     */
    private $vendorName;

    /**
     * @var string
     *
     * @ORM\Column(name="brandName", type="string", nullable = true)
     */
    private $brandName;

    /**
     * @var string
     *
     * @ORM\Column(name="sizeName", type="string", nullable = true)
     */
    private $sizeName;

    /**
     * @var string
     *
     * @ORM\Column(name="colorName", type="string", nullable = true)
     */
    private $colorName;

    /**
     * @var string
     *
     * @ORM\Column(name="unitName", type="string", nullable = true)
     */
    private $unitName;

    /**
     * @var string
     *
     * @ORM\Column(name="categoryName", type="string", nullable = true)
     */
    private $categoryName;


    /**
     * @var integer
     *
     * @ORM\Column(name="quantity", type="integer")
     */
    private $quantity;

    /**
     * @var float
     *
     * @ORM\Column(name="price", type="float",nullable=true)
     */
    private $price;

    /**
     * @var string
     *
     * @ORM\Column(name="process", type="string")
     */
    private $process;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;



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
     * Set quantity
     *
     * @param integer $quantity
     *
     * @return StockItem
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return integer
     */
    public function getQuantity()
    {
        return $this->quantity;
    }


    /**
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param \DateTime $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
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
     * @return mixed
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param mixed $country
     */
    public function setCountry($country)
    {
        $this->country = $country;
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
    public function getItem()
    {
        return $this->item;
    }

    /**
     * @param mixed $item
     */
    public function setItem($item)
    {
        $this->item = $item;
    }

    /**
     * @return User
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * @param User $createdBy
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;
    }



    /**
     * @return PurchaseItem
     */
    public function getPurchaseItem()
    {
        return $this->purchaseItem;
    }

    /**
     * @param PurchaseItem $purchaseItem
     */
    public function setPurchaseItem($purchaseItem)
    {
        $this->purchaseItem = $purchaseItem;
    }

    /**
     * @return string
     */
    public function getProcess()
    {
        return $this->process;
    }

    /**
     * @param int $process
     * purchase         = 1
     * purchase return  = 2
     * sales            = 3
     * sales return     = 4
     * reject           = 5
     * damage          = 6
     */


    public function setProcess($process)
    {
        $this->process = $process;
    }

    /**
     * @return mixed
     */
    public function getSalesItem()
    {
        return $this->salesItem;
    }

    /**
     * @param mixed $salesItem
     */
    public function setSalesItem($salesItem)
    {
        $this->salesItem = $salesItem;
    }

    /**
     * @return mixed
     */
    public function getSalesReturnItem()
    {
        return $this->salesReturnItem;
    }

    /**
     * @param mixed $salesReturnItem
     */
    public function setSalesReturnItem($salesReturnItem)
    {
        $this->salesReturnItem = $salesReturnItem;
    }

    /**
     * @return mixed
     */
    public function getPurchaseReturnItem()
    {
        return $this->purchaseReturnItem;
    }

    /**
     * @param mixed $purchaseReturnItem
     */
    public function setPurchaseReturnItem($purchaseReturnItem)
    {
        $this->purchaseReturnItem = $purchaseReturnItem;
    }

    /**
     * @return mixed
     */
    public function getPurchaseReplaceItem()
    {
        return $this->purchaseReplaceItem;
    }

    /**
     * @param mixed $purchaseReplaceItem
     */
    public function setPurchaseReplaceItem($purchaseReplaceItem)
    {
        $this->purchaseReplaceItem = $purchaseReplaceItem;
    }

    /**
     * @return mixed
     */
    public function getSalesItemReplace()
    {
        return $this->salesItemReplace;
    }

    /**
     * @param mixed $salesItemReplace
     */
    public function setSalesItemReplace($salesItemReplace)
    {
        $this->salesItemReplace = $salesItemReplace;
    }

    /**
     * @return mixed
     */
    public function getDamage()
    {
        return $this->damage;
    }

    /**
     * @param mixed $damage
     */
    public function setDamage($damage)
    {
        $this->damage = $damage;
    }

    /**
     * @return OrderItem
     */
    public function getOrderItem()
    {
        return $this->orderItem;
    }

    /**
     * @param OrderItem $orderItem
     */
    public function setOrderItem($orderItem)
    {
        $this->orderItem = $orderItem;
    }

    /**
     * @return ProductUnit
     */
    public function getUnit()
    {
        return $this->unit;
    }

    /**
     * @param ProductUnit $unit
     */
    public function setUnit($unit)
    {
        $this->unit = $unit;
    }

    /**
     * @return Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @param Product $product
     */
    public function setProduct($product)
    {
        $this->product = $product;
    }

    /**
     * @return ItemBrand
     */
    public function getBrand()
    {
        return $this->brand;
    }

    /**
     * @param ItemBrand $brand
     */
    public function setBrand($brand)
    {
        $this->brand = $brand;
    }

    /**
     * @return ItemSize
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @param ItemSize $size
     */
    public function setSize($size)
    {
        $this->size = $size;
    }

    /**
     * @return ItemColor
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * @param ItemColor $color
     */
    public function setColor($color)
    {
        $this->color = $color;
    }

    /**
     * @return string
     */
    public function getProductName()
    {
        return $this->productName;
    }

    /**
     * @param string $productName
     */
    public function setProductName($productName)
    {
        $this->productName = $productName;
    }

    /**
     * @return string
     */
    public function getVendorName()
    {
        return $this->vendorName;
    }

    /**
     * @param string $vendorName
     */
    public function setVendorName($vendorName)
    {
        $this->vendorName = $vendorName;
    }

    /**
     * @return string
     */
    public function getBrandName()
    {
        return $this->brandName;
    }

    /**
     * @param string $brandName
     */
    public function setBrandName($brandName)
    {
        $this->brandName = $brandName;
    }

    /**
     * @return string
     */
    public function getSizeName()
    {
        return $this->sizeName;
    }

    /**
     * @param string $sizeName
     */
    public function setSizeName($sizeName)
    {
        $this->sizeName = $sizeName;
    }

    /**
     * @return string
     */
    public function getColorName()
    {
        return $this->colorName;
    }

    /**
     * @param string $colorName
     */
    public function setColorName($colorName)
    {
        $this->colorName = $colorName;
    }

    /**
     * @return string
     */
    public function getUnitName()
    {
        return $this->unitName;
    }

    /**
     * @param string $unitName
     */
    public function setUnitName($unitName)
    {
        $this->unitName = $unitName;
    }

    /**
     * @return string
     */
    public function getCategoryName()
    {
        return $this->categoryName;
    }

    /**
     * @param string $categoryName
     */
    public function setCategoryName($categoryName)
    {
        $this->categoryName = $categoryName;
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

}

