<?php

namespace Appstore\Bundle\InventoryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Setting\Bundle\ToolBundle\Entity\ProductUnit;

/**
 * GoodsItem
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Appstore\Bundle\InventoryBundle\Repository\GoodsItemRepository")
 */
class GoodsItem
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
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\InventoryBundle\Entity\PurchaseVendorItem", inversedBy="goodsItems" )
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private  $purchaseVendorItem;


    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\InventoryBundle\Entity\ItemSize", inversedBy="goodsItems")
     */
    protected $size;

    /**
     * @ORM\ManyToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\ItemColor", inversedBy="goodsItem")
     */
    protected $colors;

    /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\ProductUnit", inversedBy="goodsItem" )
     **/
    private  $productUnit;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", nullable = true)
     */
    private $name;


    /**
     * @var string
     *
     * @ORM\Column(name="quantity", type="integer", nullable = true)
     */
    private $quantity;

    /**
     * @var string
     *
     * @ORM\Column(name="salesQuantity", type="integer", nullable = true)
     */
    private $salesQuantity;

     /**
     * @var string
     *
     * @ORM\Column(name="salesPrice", type="decimal", nullable = true)
     */
    private $salesPrice;

    /**
     * @var string
     *
     * @ORM\Column(name="purchasePrice", type="decimal", nullable = true)
     */
    private $purchasePrice;

    /**
     * @var string
     *
     * @ORM\Column(name="discountPrice", type="decimal", nullable = true)
     */
    private $discountPrice;

    /**
     * @var boolean
     *
     * @ORM\Column(name="masterItem", type="boolean", nullable=true)
     */
    private $masterItem = false;


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
    public function getSalesPrice()
    {
        return $this->salesPrice;
    }

    /**
     * @param string $salesPrice
     */
    public function setSalesPrice($salesPrice)
    {
        $this->salesPrice = $salesPrice;
    }

    /**
     * @return mixed
     */
    public function getPurchaseVendorItem()
    {
        return $this->purchaseVendorItem;
    }

    /**
     * @param mixed $purchaseVendorItem
     */
    public function setPurchaseVendorItem($purchaseVendorItem)
    {
        $this->purchaseVendorItem = $purchaseVendorItem;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
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
     * @return boolean
     */
    public function getMasterItem()
    {
        return $this->masterItem;
    }

    /**
     * @param boolean $masterItem
     */
    public function setMasterItem($masterItem)
    {
        $this->masterItem = $masterItem;
    }

    /**
     * @return OrderItem
     */
    public function getOrderItems()
    {
        return $this->orderItems;
    }

    /**
     * @return string
     */
    public function getPurchasePrice()
    {
        return $this->purchasePrice;
    }

    /**
     * @param string $purchasePrice
     */
    public function setPurchasePrice($purchasePrice)
    {
        $this->purchasePrice = $purchasePrice;
    }

    /**
     * @return Color
     */
    public function getColors()
    {
        return $this->colors;
    }

    /**
     * @param Color $colors
     */
    public function setColors($colors)
    {
        $this->colors = $colors;
    }

    /**
     * @return string
     */
    public function getDiscountPrice()
    {
        return $this->discountPrice;
    }

    /**
     * @param string $discountPrice
     */
    public function setDiscountPrice($discountPrice)
    {
        $this->discountPrice = $discountPrice;
    }

    /**
     * @return string
     */
    public function getSalesQuantity()
    {
        return $this->salesQuantity;
    }

    /**
     * @param string $salesQuantity
     */
    public function setSalesQuantity($salesQuantity)
    {
        $this->salesQuantity = $salesQuantity;
    }

    /**
     * @return ProductUnit
     */
    public function getProductUnit()
    {
        return $this->productUnit;
    }

    /**
     * @param ProductUnit $productUnit
     */
    public function setProductUnit($productUnit)
    {
        $this->productUnit = $productUnit;
    }


}

