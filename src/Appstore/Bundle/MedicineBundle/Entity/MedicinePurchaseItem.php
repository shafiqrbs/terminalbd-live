<?php

namespace Appstore\Bundle\MedicineBundle\Entity;

use Appstore\Bundle\MedicineBundle\Entity\MedicineStock;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * MedicinePurchaseItem
 *
 * @ORM\Table(name ="medicine_purchase_item")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\MedicineBundle\Repository\MedicinePurchaseItemRepository")
 */
class MedicinePurchaseItem
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
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\MedicineBundle\Entity\MedicineStock", inversedBy="medicinePurchaseItems" )
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private  $medicineStock;


    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\MedicineBundle\Entity\MedicinePurchase", inversedBy="medicinePurchaseItems" )
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private  $medicinePurchase;

     /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\MedicineBundle\Entity\MedicineSalesItem", mappedBy="medicinePurchaseItem" )
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private  $medicineSalesItems;

     /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\MedicineBundle\Entity\MedicineSalesReturn", mappedBy="medicinePurchaseItem" )
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private  $medicineSalesReturns;

     /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\MedicineBundle\Entity\MedicinePurchaseReturnItem", mappedBy="medicinePurchaseItem" )
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private  $medicinePurchaseReturnItems;

     /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\MedicineBundle\Entity\MedicineDamage", mappedBy="medicinePurchaseItem" )
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private  $medicineDamages;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\MedicineBundle\Entity\MedicineSalesTemporary", mappedBy="medicinePurchaseItem" )
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private  $medicineSalesTemporary;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\MedicineBundle\Entity\MedicineAndroidProcess", inversedBy="medicinePurchaseItem" )
     **/
    private  $androidProcess;

    /**
     * @var string
     *
     * @ORM\Column(name="stockName", type="string", nullable=true)
     */
    private $stockName;

    /**
     * @var integer
     *
     * @ORM\Column(name="quantity", type="integer",nullable=true)
     */
    private $quantity;

    /**
     * @var integer
     *
     * @ORM\Column(name="bonusQuantity", type="integer", nullable=true)
     */
    private $bonusQuantity;



    /**
     * @var integer
     *
     * @ORM\Column(name="pack", type="integer",nullable=true)
     */
    private $pack;

    /**
     * @var integer
     *
     * @ORM\Column(name="salesQuantity", type="integer",nullable=true)
     */
    private $salesQuantity;

    /**
     * @var integer
     *
     * @ORM\Column(name="salesReturnQuantity", type="integer",nullable=true)
     */
    private $salesReturnQuantity;

    /**
     * @var integer
     *
     * @ORM\Column(name="salesReplaceQuantity", type="integer",nullable=true)
     */
    private $salesReplaceQuantity;

    /**
     * @var integer
     *
     * @ORM\Column(name="purchaseReturnQuantity", type="integer",nullable=true)
     */
    private $purchaseReturnQuantity;

    /**
     * @var integer
     *
     * @ORM\Column(name="damageQuantity", type="integer",nullable=true)
     */
    private $damageQuantity;


    /**
     * @var integer
     *
     * @ORM\Column(name="remainingQuantity", type="integer",nullable=true)
     */
    private $remainingQuantity;


    /**
     * @var float
     *
     * @ORM\Column(name="purchasePrice", type="float", nullable = true)
     */
    private $purchasePrice;

    /**
     * @var float
     *
     * @ORM\Column(name="tp", type="float", nullable = true)
     */
    private $tp;

    /**
     * @var float
     *
     * @ORM\Column(name="actualPurchasePrice", type="float", nullable = true)
     */
    private $actualPurchasePrice;


     /**
     * @var float
     *
     * @ORM\Column(name="itemPercent", type="float", nullable = true)
     */
    private $itemPercent;


    /**
     * @var integer
     *
     * @ORM\Column(name="code", type="integer", nullable = true)
     */
    private $code;


    /**
     * @var float
     *
     * @ORM\Column(name="salesPrice", type="float", nullable = true)
     */
    private $salesPrice;

    /**
     * @var float
     *
     * @ORM\Column(name="purchaseSubTotal", type="float", nullable = true)
     */
    private $purchaseSubTotal;


    /**
     * @var string
     *
     * @ORM\Column(name="barcode", type="string",  nullable = true)
     */
    private $barcode;

    /**
     * @var datetime
     *
     * @ORM\Column(name="expirationDate", type="datetime", nullable=true)
     */
    private $expirationDate;

    /**
     * @var datetime
     *
     * @ORM\Column(name="expirationStartDate", type="datetime", nullable=true)
     */
    private $expirationStartDate;

    /**
     * @var datetime
     *
     * @ORM\Column(name="expirationEndDate", type="datetime", nullable=true)
     */
    private $expirationEndDate;

    /**
     * @var string
     *
     * @ORM\Column(name="batchno", type="string", nullable=true)
     */
    private $batchno;

    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="boolean",  nullable=true)
     */
    private $status = false;



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
     * @return MedicinePurchaseItem
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
     * @return MedicinePurchase
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
     * @return MedicinePurchaseItem
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


    /**
     * @return float
     */
    public function getPurchaseSubTotal()
    {
        return $this->purchaseSubTotal;
    }

    /**
     * @param float $purchaseSubTotal
     */
    public function setPurchaseSubTotal($purchaseSubTotal)
    {
        $this->purchaseSubTotal = $purchaseSubTotal;
    }

    /**
     * @return MedicinePurchase
     */
    public function getMedicinePurchase()
    {
        return $this->medicinePurchase;
    }

    /**
     * @param MedicinePurchase $medicinePurchase
     */
    public function setMedicinePurchase($medicinePurchase)
    {
        $this->medicinePurchase = $medicinePurchase;
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
     * @return DateTime
     */
    public function getExpirationDate()
    {
        return $this->expirationDate;
    }

    /**
     * @param DateTime $expirationDate
     */
    public function setExpirationDate($expirationDate)
    {
        $this->expirationDate = $expirationDate;
    }

    /**
     * @return int
     */
    public function getSalesQuantity()
    {
        return $this->salesQuantity;
    }

    /**
     * @param int $salesQuantity
     */
    public function setSalesQuantity($salesQuantity)
    {
        $this->salesQuantity = $salesQuantity;
    }

    /**
     * @return int
     */
    public function getSalesReturnQuantity()
    {
        return $this->salesReturnQuantity;
    }

    /**
     * @param int $salesReturnQuantity
     */
    public function setSalesReturnQuantity($salesReturnQuantity)
    {
        $this->salesReturnQuantity = $salesReturnQuantity;
    }

    /**
     * @return int
     */
    public function getSalesReplaceQuantity()
    {
        return $this->salesReplaceQuantity;
    }

    /**
     * @param int $salesReplaceQuantity
     */
    public function setSalesReplaceQuantity($salesReplaceQuantity)
    {
        $this->salesReplaceQuantity = $salesReplaceQuantity;
    }

    /**
     * @return int
     */
    public function getPurchaseReturnQuantity()
    {
        return $this->purchaseReturnQuantity;
    }

    /**
     * @param int $purchaseReturnQuantity
     */
    public function setPurchaseReturnQuantity($purchaseReturnQuantity)
    {
        $this->purchaseReturnQuantity = $purchaseReturnQuantity;
    }

    /**
     * @return int
     */
    public function getDamageQuantity()
    {
        return $this->damageQuantity;
    }

    /**
     * @param int $damageQuantity
     */
    public function setDamageQuantity($damageQuantity)
    {
        $this->damageQuantity = $damageQuantity;
    }

    /**
     * @return int
     */
    public function getRemainingQuantity()
    {
        return $this->remainingQuantity;
    }

    /**
     * @param int $remainingQuantity
     */
    public function setRemainingQuantity($remainingQuantity)
    {
        $this->remainingQuantity = $remainingQuantity;
    }

    /**
     * @return MedicineSalesItem
     */
    public function getMedicineSalesItems()
    {
        return $this->medicineSalesItems;
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
     * @return DateTime
     */
    public function getExpirationStartDate()
    {
        return $this->expirationStartDate;
    }

    /**
     * @param DateTime $expirationStartDate
     */
    public function setExpirationStartDate($expirationStartDate)
    {
        $this->expirationStartDate = $expirationStartDate;
    }

    /**
     * @return DateTime
     */
    public function getExpirationEndDate()
    {
        return $this->expirationEndDate;
    }

    /**
     * @param DateTime $expirationEndDate
     */
    public function setExpirationEndDate($expirationEndDate)
    {
        $this->expirationEndDate = $expirationEndDate;
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
    public function getActualPurchasePrice()
    {
        return $this->actualPurchasePrice;
    }

    /**
     * @param float $actualPurchasePrice
     */
    public function setActualPurchasePrice($actualPurchasePrice)
    {
        $this->actualPurchasePrice = $actualPurchasePrice;
    }

    /**
     * @return MedicinePurchaseReturnItem
     */
    public function getMedicinePurchaseReturnItems()
    {
        return $this->medicinePurchaseReturnItems;
    }

    /**
     * @return int
     */
    public function getPack()
    {
        return $this->pack;
    }

    /**
     * @param int $pack
     */
    public function setPack($pack)
    {
        $this->pack = $pack;
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
    public function getTp()
    {
        return $this->tp;
    }

    /**
     * @param float $tp
     */
    public function setTp($tp)
    {
        $this->tp = $tp;
    }

    /**
     * @return int
     */
    public function getBonusQuantity()
    {
        return $this->bonusQuantity;
    }

    /**
     * @param int $bonusQuantity
     */
    public function setBonusQuantity($bonusQuantity)
    {
        $this->bonusQuantity = $bonusQuantity;
    }

    /**
     * @return bool
     */
    public function isStatus()
    {
        return $this->status;
    }

    /**
     * @param bool $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getBatchno()
    {
        return $this->batchno;
    }

    /**
     * @param string $batchno
     */
    public function setBatchno($batchno)
    {
        $this->batchno = $batchno;
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




}

