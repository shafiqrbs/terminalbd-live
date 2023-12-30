<?php

namespace Appstore\Bundle\MedicineBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * MedicineSalesItem
 *
 * @ORM\Table( name = "medicine_sales_item")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\MedicineBundle\Repository\MedicineSalesItemRepository")
 */
class MedicineSalesItem
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;


    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\MedicineBundle\Entity\MedicineSales", inversedBy="medicineSalesItems")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @ORM\OrderBy({"id" = "ASC"})
     **/
    private $medicineSales;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\MedicineBundle\Entity\MedicineStock", inversedBy="medicineSalesItems", cascade={"persist"} )
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private $medicineStock;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\MedicineBundle\Entity\MedicinePurchaseItem", inversedBy="medicineSalesItems", cascade={"persist"} )
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private $medicinePurchaseItem;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\MedicineBundle\Entity\MedicineSalesReturn", mappedBy="medicineSalesItem", cascade={"persist"} )
     **/
    private $medicineSalesReturns;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\MedicineBundle\Entity\MedicineAndroidProcess")
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private  $androidProcess;

    /**
     * @var integer
     *
     * @ORM\Column(name="deviceSalesId", type="integer",nullable=true)
     */
    private $deviceSalesId;

    /**
     * @var string
     *
     * @ORM\Column(name="stockName", type="string", nullable=true)
     */
    private $stockName;

    /**
     * @var string
     *
     * @ORM\Column(name="barcode", type="string", nullable=true)
     */
    private $barcode;


    /**
     * @var integer
     *
     * @ORM\Column(name="purchaseItem", type="integer", nullable=true)
     */
    private $purchaseItem;


    /**
     * @var float
     *
     * @ORM\Column(name="quantity", type="float", nullable=true)
     */
    private $quantity;


    /**
     * @var float
     *
     * @ORM\Column(name="mrpPrice", type="float", nullable=true)
     */
    private $mrpPrice;

    /**
     * @var float
     *
     * @ORM\Column(name="salesPrice", type="float", nullable=true)
     */
    private $salesPrice;

    /**
     * @var string
     *
     * @ORM\Column(name="discountPrice", type="decimal", nullable=true)
     */
    private $discountPrice;


    /**
     * @var float
     *
     * @ORM\Column(name="purchasePrice", type="float", nullable=true)
     */
    private $purchasePrice;


     /**
     * @var float
     *
     * @ORM\Column(name="itemPercent", type="float", nullable=true)
     */
    private $itemPercent;


    /**
     * @var boolean
     *
     * @ORM\Column(name="customPrice", type="boolean")
     */
    private $customPrice = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isShort", type="boolean")
     */
    private $isShort = false;

    /**
     * @var float
     *
     * @ORM\Column(name="subTotal", type="float", nullable=true)
     */
    private $subTotal;

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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return MedicineSales
     */
    public function getMedicineSales()
    {
        return $this->medicineSales;
    }

    /**
     * @param MedicineSales $medicineSales
     */
    public function setMedicineSales($medicineSales)
    {
        $this->medicineSales = $medicineSales;
    }

    /**
     * @return MedicineStock
     */
    public function getMedicineStock()
    {
        return $this->medicineStock;
    }

    /**
     * @param MedicineStock $medicineStock
     */
    public function setMedicineStock($medicineStock)
    {
        $this->medicineStock = $medicineStock;
    }

    /**
     * @return MedicinePurchaseItem
     */
    public function getMedicinePurchaseItem()
    {
        return $this->medicinePurchaseItem;
    }

    /**
     * @param MedicinePurchaseItem $medicinePurchaseItem
     */
    public function setMedicinePurchaseItem($medicinePurchaseItem)
    {
        $this->medicinePurchaseItem = $medicinePurchaseItem;
    }


    /**
     * @return float
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param float $quantity
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
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
    public function getSubTotal()
    {
        return $this->subTotal;
    }

    /**
     * @param float $subTotal
     */
    public function setSubTotal($subTotal)
    {
        $this->subTotal = $subTotal;
    }

    /**
     * @return bool
     */
    public function isCustomPrice()
    {
        return $this->customPrice;
    }

    /**
     * @param bool $customPrice
     */
    public function setCustomPrice($customPrice)
    {
        $this->customPrice = $customPrice;
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
     * @return int
     */
    public function getBarcode()
    {
        return $this->barcode;
    }

    /**
     * @param int $barcode
     */
    public function setBarcode($barcode)
    {
        $this->barcode = $barcode;
    }


    /**
     * @return string
     */
    public function getStockName()
    {
        return $this->stockName;
    }

    /**
     * @param string $stockName
     */
    public function setStockName($stockName)
    {
        $this->stockName = $stockName;
    }


    /**
     * @return MedicineSalesReturn
     */
    public function getMedicineSalesReturns()
    {
        return $this->medicineSalesReturns;
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
     * @return MedicineAndroidProcess
     */
    public function getAndroidProcess()
    {
        return $this->androidProcess;
    }

    /**
     * @param MedicineAndroidProcess $androidProcess
     */
    public function setAndroidProcess($androidProcess)
    {
        $this->androidProcess = $androidProcess;
    }

    /**
     * @return float
     */
    public function getMrpPrice()
    {
        return $this->mrpPrice;
    }

    /**
     * @param float $mrpPrice
     */
    public function setMrpPrice($mrpPrice)
    {
        $this->mrpPrice = $mrpPrice;
    }

    /**
     * @return int
     */
    public function getPurchaseItem()
    {
        return $this->purchaseItem;
    }

    /**
     * @param int $purchaseItem
     */
    public function setPurchaseItem($purchaseItem)
    {
        $this->purchaseItem = $purchaseItem;
    }

    /**
     * @return bool
     */
    public function isShort()
    {
        return $this->isShort;
    }

    /**
     * @param bool $isShort
     */
    public function setIsShort($isShort)
    {
        $this->isShort = $isShort;
    }

    /**
     * @return float
     */
    public function getItemPercent()
    {
        return $this->itemPercent;
    }

    /**
     * @param float $itemPercent
     */
    public function setItemPercent($itemPercent)
    {
        $this->itemPercent = $itemPercent;
    }

    /**
     * @return int
     */
    public function getDeviceSalesId()
    {
        return $this->deviceSalesId;
    }

    /**
     * @param int $deviceSalesId
     */
    public function setDeviceSalesId($deviceSalesId)
    {
        $this->deviceSalesId = $deviceSalesId;
    }




}

