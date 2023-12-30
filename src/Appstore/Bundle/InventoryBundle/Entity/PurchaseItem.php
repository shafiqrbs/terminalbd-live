<?php

namespace Appstore\Bundle\InventoryBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * PurchaseItem
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Appstore\Bundle\InventoryBundle\Repository\PurchaseItemRepository")
 */
class PurchaseItem
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
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\InventoryBundle\Entity\InventoryConfig")
     **/
    private  $inventoryConfig;


    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\InventoryBundle\Entity\Item", inversedBy="purchaseItems" )
     **/
    private  $item;


    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\StockItem", mappedBy="purchaseItem" , cascade={"remove"} )
     **/
    private  $stockItem;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\InventoryBundle\Entity\Purchase", inversedBy="purchaseItems" )
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private  $purchase;


    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\InventoryBundle\Entity\PurchaseVendorItem", inversedBy="purchaseItems")
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private  $purchaseVendorItem;


    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\PurchaseReturnItem", mappedBy="purchaseItem"  , cascade={"remove"} )
     **/
    private  $purchaseReturnItem;


    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\SalesItem", mappedBy="purchaseItem" )
     **/
    private  $salesItems;

     /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\PurchaseItemSerial", mappedBy="purchaseItem" )
     **/
    private  $itemSerials;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\BranchInvoiceItem", mappedBy="purchaseItem" , cascade={"remove"} )
     **/
    private  $branchInvoiceItems;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\DeliveryReturn", mappedBy="purchaseItem" , cascade={"remove"} )
     */
    protected $deliveryReturns;

     /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\DeliveryItem", mappedBy="purchaseItem" , cascade={"remove"} )
     */
    protected $deliveryItems;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\Damage", mappedBy="purchaseItem" , cascade={"remove"}  )
     **/
    private  $damages;


    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable = true)
     */
    private $name;


    /**
     * @var float
     *
     * @ORM\Column(name="quantity", type="float")
     */
    private $quantity;

    /**
     * @var float
     *
     * @ORM\Column(name="purchasePrice", type="float")
     */
    private $purchasePrice;


    /**
     * @var integer
     *
     * @ORM\Column(name="code", type="integer", nullable = true)
     */
    private $code;

    /**
     * @var float
     *
     * @ORM\Column(name="purchaseSubTotal", type="float", nullable = true)
     */
    private $purchaseSubTotal;

    /**
     * @var float
     *
     * @ORM\Column(name="salesPrice", type="float", nullable = true)
     */
    private $salesPrice;
    /**
     * @var float
     *
     * @ORM\Column(name="salesSubTotal", type="float", nullable = true)
     */
    private $salesSubTotal;

    /**
     * @var float
     *
     * @ORM\Column(name="webPrice", type="float", nullable = true)
     */
    private $webPrice;
    /**
     * @var float
     *
     * @ORM\Column(name="webSubTotal", type="float", nullable = true)
     */
    private $webSubTotal;

    /**
     * @var string
     *
     * @ORM\Column(name="barcode", type="string",  nullable = true)
     */
    private $barcode;

    /**
     * @var text
     *
     * @ORM\Column(name="serialNo", type="text", nullable = true)
     */
    private $serialNo;

     /**
     * @var text
     *
     * @ORM\Column(name="mode", type="text", nullable = true)
     */
    private $mode;

    /**
     * @var string
     *
     * @ORM\Column(name="assuranceType", type="string", length=50, nullable = true)
     */
    private $assuranceType;

    /**
     * @var string
     *
     * @ORM\Column(name="assuranceFromVendor", type="string", length=100, nullable = true)
     */
    private $assuranceFromVendor;

    /**
     * @var string
     *
     * @ORM\Column(name="assuranceToCustomer", type="string", length=100, nullable = true)
     */
    private $assuranceToCustomer;


    /**
     * @var datetime
     *
     * @ORM\Column(name="expiredDate", type="datetime", nullable=true)
     */
    private $expiredDate;


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
     * Set quantity
     *
     * @param integer $quantity
     *
     * @return PurchaseItem
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
     * Set purchasePrice
     *
     * @param float $purchasePrice
     *
     * @return Purchase
     */
    public function setPurchasePrice($purchasePrice)
    {
        $this->purchasePrice = $purchasePrice;

        return $this;
    }

    /**
     * Get purchasePrice
     *
     * @return float
     */
    public function getPurchasePrice()
    {
        return $this->purchasePrice;
    }




    /**
     * Set salesPrice
     *
     * @param float $salesPrice
     *
     * @return PurchaseItem
     */
    public function setSalesPrice($salesPrice)
    {
        $this->salesPrice = $salesPrice;

        return $this;
    }

    /**
     * Get salesPrice
     *
     * @return float
     */
    public function getSalesPrice()
    {
        return $this->salesPrice;
    }

    /**
     * Set purchaseSubTotal
     *
     * @param float $purchaseSubTotal
     *
     * @return PurchaseItem
     */
    public function setPurchaseSubTotal($purchaseSubTotal)
    {
        $this->purchaseSubTotal = $purchaseSubTotal;

        return $this;
    }

    /**
     * Get purchaseSubTotal
     *
     * @return float
     */
    public function getPurchaseSubTotal()
    {
        return $this->purchaseSubTotal;
    }

    /**
     * Set salesSubTotal
     *
     * @param float $salesSubTotal
     *
     * @return PurchaseItem
     */
    public function setSalesSubTotal($salesSubTotal)
    {
        $this->salesSubTotal = $salesSubTotal;

        return $this;
    }

    /**
     * Get salesSubTotal
     *
     * @return float
     */
    public function getSalesSubTotal()
    {
        return $this->salesSubTotal;
    }

    /**
     * @return Item
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
     * @return float
     */
    public function getWebPrice()
    {
        return $this->webPrice;
    }

    /**
     * @param float $webPrice
     */
    public function setWebPrice($webPrice)
    {
        $this->webPrice = $webPrice;
    }

    /**
     * @return float
     */
    public function getWebSubTotal()
    {
        return $this->webSubTotal;
    }

    /**
     * @param float $webSubTotal
     */
    public function setWebSubTotal($webSubTotal)
    {
        $this->webSubTotal = $webSubTotal;
    }

    /**
     * @return mixed
     */
    public function getPurchase()
    {
        return $this->purchase;
    }

    /**
     * @param mixed $purchase
     */
    public function setPurchase($purchase)
    {
        $this->purchase = $purchase;
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
     * @return StockItem
     */
    public function getStockItem()
    {
        return $this->stockItem;
    }

    /**
     * @return SalesItem
     */
    public function getSalesItems()
    {
        return $this->salesItems;
    }

    /**
     * @return PurchaseVendorItem
     */
    public function getPurchaseVendorItem()
    {
        return $this->purchaseVendorItem;
    }

    /**
     * @param PurchaseVendorItem $purchaseVendorItem
     */
    public function setPurchaseVendorItem($purchaseVendorItem)
    {
        $this->purchaseVendorItem = $purchaseVendorItem;
    }

    /**
     * @return PurchaseReturnItem
     */
    public function getPurchaseReturnItem()
    {
        return $this->purchaseReturnItem;
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

    public function  getItemStock()
    {
        $quantity = 0;
        $i = 0;
        if(!$this->stockItem->isEmpty()) {
            foreach ($this->stockItem AS $item) {
                $quantity += $item->getQuantity(); //$recipecost now $this->recipecost.
                $i++;
            }
            return $quantity;
        }
        return 0;
    }

    /**
     * @return Damage
     */
    public function getDamages()
    {
        return $this->damages;
    }

    /**
     * @return mixed
     */
    public function getBranchInvoiceItems()
    {
        return $this->branchInvoiceItems;
    }

    /**
     * @param mixed $branchInvoiceItems
     */
    public function setBranchInvoiceItems($branchInvoiceItems)
    {
        $this->branchInvoiceItems = $branchInvoiceItems;
    }

    public function getStockItemQuantity()
    {
        $stockQnt = 0;
        foreach ($this->getStockItem() as $stock){
            $stockQnt += $stock->getQuantity();
        }
        return $stockQnt;
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


    /**
     * @return string
     */
    public function getAssuranceType()
    {
        return $this->assuranceType;
    }

    /**
     * @param string $assuranceType
     */
    public function setAssuranceType($assuranceType)
    {
        $this->assuranceType = $assuranceType;
    }

    /**
     * @return string
     */
    public function getAssuranceFromVendor()
    {
        return $this->assuranceFromVendor;
    }

    /**
     * @param string $assuranceFromVendor
     */
    public function setAssuranceFromVendor($assuranceFromVendor)
    {
        $this->assuranceFromVendor = $assuranceFromVendor;
    }

    /**
     * @return string
     */
    public function getAssuranceToCustomer()
    {
        return $this->assuranceToCustomer;
    }

    /**
     * @param string $assuranceToCustomer
     */
    public function setAssuranceToCustomer($assuranceToCustomer)
    {
        $this->assuranceToCustomer = $assuranceToCustomer;
    }

    /**
     * @return datetime
     */
    public function getExpiredDate()
    {
        return $this->expiredDate;
    }

    /**
     * @param datetime $expiredDate
     */
    public function setExpiredDate($expiredDate)
    {
        $this->expiredDate = $expiredDate;
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
     * @return text
     */
    public function getSerialNo()
    {
        return $this->serialNo;
    }

    /**
     * @param text $serialNo
     */
    public function setSerialNo($serialNo)
    {
        $this->serialNo = $serialNo;
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
    public function getItemSerials()
    {
        return $this->itemSerials;
    }

    /**
     * @return text
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * @param text $mode
     */
    public function setMode($mode)
    {
        $this->mode = $mode;
    }


}

