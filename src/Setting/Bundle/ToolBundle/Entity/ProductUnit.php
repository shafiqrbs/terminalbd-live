<?php

namespace Setting\Bundle\ToolBundle\Entity;

use Appstore\Bundle\BusinessBundle\Entity\BusinessParticular;
use Appstore\Bundle\DoctorPrescriptionBundle\Entity\DpsParticular;
use Appstore\Bundle\HospitalBundle\Entity\Particular;
use Appstore\Bundle\InventoryBundle\Entity\Product;
use Appstore\Bundle\InventoryBundle\Entity\StockItem;
use Appstore\Bundle\MedicineBundle\Entity\MedicineMinimumStock;
use Appstore\Bundle\MedicineBundle\Entity\MedicineStock;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Setting\Bundle\ContentBundle\Entity\TradeItem;

/**
 * ProductUnit
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Setting\Bundle\ToolBundle\Repository\ProductUnitRepository")
 */

class ProductUnit
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
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\Product", mappedBy="productUnit")
     */
    protected $masterProducts;


    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\GoodsItem", mappedBy="productUnit")
     */
    protected $goodsItem;


    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\PurchaseVendorItem", mappedBy="productUnit")
     */
    protected $purchaseVendorItem;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\StockItem", mappedBy="unit")
     */
    protected $stockItems;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\HospitalBundle\Entity\Particular", mappedBy="unit" , cascade={"persist", "remove"})
     **/
    private $particulars;


    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\MedicineBundle\Entity\MedicineStock", mappedBy="unit" , cascade={"persist", "remove"})
     **/
    private $medicineStocks;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\MedicineBundle\Entity\MedicineStockHouse", mappedBy="unit" , cascade={"persist", "remove"})
     **/
    private $stockHouses;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\MedicineBundle\Entity\MedicineMinimumStock", mappedBy="unit" , cascade={"persist", "remove"})
     **/
    private $medicineMinimumStock;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\MedicineBundle\Entity\MedicinePrepurchaseItem", mappedBy="unit" , cascade={"persist", "remove"})
     **/
    private $medicinePrepurchaseItems;


    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\BusinessBundle\Entity\BusinessParticular", mappedBy="unit" , cascade={"persist", "remove"})
     **/
    private $businessParticulars;

	 /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\BusinessBundle\Entity\BusinessPurchaseItem", mappedBy="unit" , cascade={"persist", "remove"})
     **/
    private $businessPurchaseItems;

	/**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\DoctorPrescriptionBundle\Entity\DpsParticular", mappedBy="unit" , cascade={"persist", "remove"})
     **/
    private $dpsParticulars;


    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\RestaurantBundle\Entity\Particular", mappedBy="unit" , cascade={"persist", "remove"})
     **/
    private $restaurantProduct;


    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;


    /**
     * @var string
     *
     * @ORM\Column(name="nameBn", type="string", length=255)
     */
    private $nameBn;


    /**
     * @Gedmo\Slug(fields={"name"})
     * @Doctrine\ORM\Mapping\Column(length=255)
     */
    private $slug;


    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="boolean")
     */
    private $status=true;


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
     * @return ProductUnit
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
     * Set slug
     *
     * @param string $slug
     *
     * @return ProductUnit
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set status
     *
     * @param boolean $status
     * @return ProductUnit
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return boolean
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return Product
     */
    public function getMasterProducts()
    {
        return $this->masterProducts;
    }

    /**
     * @return StockItem
     */
    public function getStockItems()
    {
        return $this->stockItems;
    }

    /**
     * @return Particular
     */
    public function getParticulars()
    {
        return $this->particulars;
    }

    /**
     * @return DmsParticular
     */
    public function getDmsParticulars()
    {
        return $this->dmsParticulars;
    }

    /**
     * @return MedicineStock
     */
    public function getMedicineStocks()
    {
        return $this->medicineStocks;
    }

    /**
     * @return BusinessParticular
     */
    public function getBusinessParticulars()
    {
        return $this->businessParticulars;
    }

    /**
     * @return DpsParticular
     */
    public function getDpsParticulars()
    {
        return $this->dpsParticulars;
    }

    /**
     * @return MedicineMinimumStock
     */
    public function getMedicineMinimumStock()
    {
        return $this->medicineMinimumStock;
    }

    /**
     * @return \Appstore\Bundle\RestaurantBundle\Entity\Particular
     */
    public function getRestaurantProduct()
    {
        return $this->restaurantProduct;
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

