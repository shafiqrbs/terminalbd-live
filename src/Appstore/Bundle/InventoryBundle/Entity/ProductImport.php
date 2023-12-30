<?php

namespace Appstore\Bundle\InventoryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProductImport
 *
 * @ORM\Table(name ="product_import")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\InventoryBundle\Repository\ProductImportRepository")
 */
class ProductImport
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="web", type="string", length=255)
     */
    private $web;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=255)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="vendor_code", type="string", length=255)
     */
    private $vendorCode;

    /**
     * @var string
     *
     * @ORM\Column(name="category", type="string", length=255)
     */
    private $category;

    /**
     * @var string
     *
     * @ORM\Column(name="quantity", type="string", length=255)
     */
    private $quantity;

    /**
     * @var string
     *
     * @ORM\Column(name="purchase_price", type="string", length=255)
     */
    private $purchasePrice;

    /**
     * @var string
     *
     * @ORM\Column(name="sales_price", type="string", length=255)
     */
    private $salesPrice;

    /**
     * @var string
     *
     * @ORM\Column(name="memo_no", type="string", length=255)
     */
    private $memoNo;

    /**
     * @var string
     *
     * @ORM\Column(name="rack", type="string", length=255)
     */
    private $rack;

    /**
     * @var string
     *
     * @ORM\Column(name="unit", type="string", length=255)
     */
    private $unit;

    /**
     * @var string
     *
     * @ORM\Column(name="color", type="string", length=255)
     */
    private $color;

    /**
     * @var string
     *
     * @ORM\Column(name="dimension", type="string", length=255)
     */
    private $dimension;

    /**
     * @var string
     *
     * @ORM\Column(name="extra_1", type="string", length=255)
     */
    private $extra1;


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
     * @return ProductImport
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
     * Set web
     *
     * @param string $web
     *
     * @return ProductImport
     */
    public function setWeb($web)
    {
        $this->web = $web;

        return $this;
    }

    /**
     * Get web
     *
     * @return string
     */
    public function getWeb()
    {
        return $this->web;
    }

    /**
     * Set code
     *
     * @param string $code
     *
     * @return ProductImport
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set vendorCode
     *
     * @param string $vendorCode
     *
     * @return ProductImport
     */
    public function setVendorCode($vendorCode)
    {
        $this->vendorCode = $vendorCode;

        return $this;
    }

    /**
     * Get vendorCode
     *
     * @return string
     */
    public function getVendorCode()
    {
        return $this->vendorCode;
    }

    /**
     * Set category
     *
     * @param string $category
     *
     * @return ProductImport
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set quantity
     *
     * @param string $quantity
     *
     * @return ProductImport
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return string
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set purchasePrice
     *
     * @param string $purchasePrice
     *
     * @return ProductImport
     */
    public function setPurchasePrice($purchasePrice)
    {
        $this->purchasePrice = $purchasePrice;

        return $this;
    }

    /**
     * Get purchasePrice
     *
     * @return string
     */
    public function getPurchasePrice()
    {
        return $this->purchasePrice;
    }

    /**
     * Set salesPrice
     *
     * @param string $salesPrice
     *
     * @return ProductImport
     */
    public function setSalesPrice($salesPrice)
    {
        $this->salesPrice = $salesPrice;

        return $this;
    }

    /**
     * Get salesPrice
     *
     * @return string
     */
    public function getSalesPrice()
    {
        return $this->salesPrice;
    }

    /**
     * Set memoNo
     *
     * @param string $memoNo
     *
     * @return ProductImport
     */
    public function setMemoNo($memoNo)
    {
        $this->memoNo = $memoNo;

        return $this;
    }

    /**
     * Get memoNo
     *
     * @return string
     */
    public function getMemoNo()
    {
        return $this->memoNo;
    }

    /**
     * Set rack
     *
     * @param string $rack
     *
     * @return ProductImport
     */
    public function setRack($rack)
    {
        $this->rack = $rack;

        return $this;
    }

    /**
     * Get rack
     *
     * @return string
     */
    public function getRack()
    {
        return $this->rack;
    }

    /**
     * Set unit
     *
     * @param string $unit
     *
     * @return ProductImport
     */
    public function setUnit($unit)
    {
        $this->unit = $unit;

        return $this;
    }

    /**
     * Get unit
     *
     * @return string
     */
    public function getUnit()
    {
        return $this->unit;
    }

    /**
     * Set color
     *
     * @param string $color
     *
     * @return ProductImport
     */
    public function setColor($color)
    {
        $this->color = $color;

        return $this;
    }

    /**
     * Get color
     *
     * @return string
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * Set dimension
     *
     * @param string $dimension
     *
     * @return ProductImport
     */
    public function setDimension($dimension)
    {
        $this->dimension = $dimension;

        return $this;
    }

    /**
     * Get dimension
     *
     * @return string
     */
    public function getDimension()
    {
        return $this->dimension;
    }

    /**
     * Set extra1
     *
     * @param string $extra1
     *
     * @return ProductImport
     */
    public function setExtra1($extra1)
    {
        $this->extra1 = $extra1;

        return $this;
    }

    /**
     * Get extra1
     *
     * @return string
     */
    public function getExtra1()
    {
        return $this->extra1;
    }
}

