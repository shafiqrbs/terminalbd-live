<?php

namespace Appstore\Bundle\HospitalBundle\Entity;

use Appstore\Bundle\AccountingBundle\Entity\AccountHmsStockOut;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * HmsStock
 *
 * @ORM\Table( name ="hms_stock")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\HospitalBundle\Repository\HmsStockOutRepository")
 */
class HmsStock
{
    /**
     * @var integer
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;


    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\HospitalBundle\Entity\HospitalConfig")
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private  $hospitalConfig;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255,nullable = true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=255,nullable = true)
     */
    private $slug;

    /**
     * @var integer
     *
     * @ORM\Column(name="openingQuantity", type="integer", nullable=true)
     */
    private $openingQuantity;

    /**
     * @var integer
     *
     * @ORM\Column(name="minQuantity", type="integer", nullable=true)
     */
    private $minQuantity = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="maxQuantity", type="integer", nullable=true)
     */
    private $maxQuantity;

    /**
     * @var integer
     *
     * @ORM\Column(name="bonusQuantity", type="integer", nullable=true)
     */
    private $bonusQuantity;

    /**
     * @var integer
     *
     * @ORM\Column(name="bonusAdjustment", type="integer", nullable=true)
     */
    private $bonusAdjustment;

    /**
     * @var integer
     *
     * @ORM\Column(name="adjustmentQuantity", type="integer", nullable=true)
     */
    private $adjustmentQuantity;

    /**
     * @var integer
     *
     * @ORM\Column(name="reorderQuantity", type="integer", nullable=true)
     */
    private $reorderQuantity;

    /**
     * @var integer
     *
     * @ORM\Column(name="remainingQuantity", type="integer", nullable=true)
     */
    private $remainingQuantity = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="purchaseQuantity", type="integer", nullable=true)
     */
    private $purchaseQuantity;

    /**
     * @var integer
     *
     * @ORM\Column(name="purchaseReturnQuantity", type="integer", nullable=true)
     */
    private $purchaseReturnQuantity=0;

    /**
     * @var integer
     *
     * @ORM\Column(name="salesQuantity", type="integer", nullable=true)
     */
    private $salesQuantity = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="salesReturnQuantity", type="integer", nullable=true)
     */
    private $salesReturnQuantity = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="damageQuantity", type="integer", nullable=true)
     */
    private $damageQuantity = 0;

    /**
     * @var float
     *
     * @ORM\Column(name="purchasePrice", type="float", nullable=true)
     */
    private $purchasePrice;

    /**
     * @var float
     *
     * @ORM\Column(name="averagePurchasePrice", type="float", nullable=true)
     */
    private $averagePurchasePrice = 0;


    /**
     * @var float
     *
     * @ORM\Column(name="salesPrice", type="float",  nullable=true)
     */
    private $salesPrice;

    /**
     * @var float
     *
     * @ORM\Column(name="averageSalesPrice", type="float", nullable=true)
     */
    private $averageSalesPrice = 0;


    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="boolean",  nullable=true)
     */
    private $status = true;


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
     * @return mixed
     */
    public function getHospitalConfig()
    {
        return $this->hospitalConfig;
    }

    /**
     * @param mixed $hospitalConfig
     */
    public function setHospitalConfig($hospitalConfig)
    {
        $this->hospitalConfig = $hospitalConfig;
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
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
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
     * @return int
     */
    public function getMinQuantity()
    {
        return $this->minQuantity;
    }

    /**
     * @param int $minQuantity
     */
    public function setMinQuantity($minQuantity)
    {
        $this->minQuantity = $minQuantity;
    }

    /**
     * @return int
     */
    public function getMaxQuantity()
    {
        return $this->maxQuantity;
    }

    /**
     * @param int $maxQuantity
     */
    public function setMaxQuantity($maxQuantity)
    {
        $this->maxQuantity = $maxQuantity;
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
     * @return int
     */
    public function getBonusAdjustment()
    {
        return $this->bonusAdjustment;
    }

    /**
     * @param int $bonusAdjustment
     */
    public function setBonusAdjustment($bonusAdjustment)
    {
        $this->bonusAdjustment = $bonusAdjustment;
    }

    /**
     * @return int
     */
    public function getAdjustmentQuantity()
    {
        return $this->adjustmentQuantity;
    }

    /**
     * @param int $adjustmentQuantity
     */
    public function setAdjustmentQuantity($adjustmentQuantity)
    {
        $this->adjustmentQuantity = $adjustmentQuantity;
    }

    /**
     * @return int
     */
    public function getReorderQuantity()
    {
        return $this->reorderQuantity;
    }

    /**
     * @param int $reorderQuantity
     */
    public function setReorderQuantity($reorderQuantity)
    {
        $this->reorderQuantity = $reorderQuantity;
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
     * @return int
     */
    public function getPurchaseQuantity()
    {
        return $this->purchaseQuantity;
    }

    /**
     * @param int $purchaseQuantity
     */
    public function setPurchaseQuantity($purchaseQuantity)
    {
        $this->purchaseQuantity = $purchaseQuantity;
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
    public function getAveragePurchasePrice()
    {
        return $this->averagePurchasePrice;
    }

    /**
     * @param float $averagePurchasePrice
     */
    public function setAveragePurchasePrice($averagePurchasePrice)
    {
        $this->averagePurchasePrice = $averagePurchasePrice;
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
    public function getAverageSalesPrice()
    {
        return $this->averageSalesPrice;
    }

    /**
     * @param float $averageSalesPrice
     */
    public function setAverageSalesPrice($averageSalesPrice)
    {
        $this->averageSalesPrice = $averageSalesPrice;
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



}

