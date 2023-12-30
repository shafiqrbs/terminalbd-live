<?php

namespace Appstore\Bundle\RestaurantBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Setting\Bundle\ToolBundle\Entity\ProductUnit;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * BusinessStockHistory
 *
 * @ORM\Table( name = "restaurant_stock_history")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\RestaurantBundle\Repository\RestaurantStockHistoryRepository")
 */
class RestaurantStockHistory
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
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\RestaurantBundle\Entity\RestaurantConfig" , cascade={"detach","merge"} )
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private  $restaurantConfig;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\RestaurantBundle\Entity\Particular", inversedBy="stockHistory" )
     * @ORM\OrderBy({"sorting" = "ASC"})
     **/
    private $item;

     /**
      * @ORM\ManyToOne(targetEntity="Appstore\Bundle\RestaurantBundle\Entity\PurchaseItem", inversedBy="stockHistory",cascade={"detach","merge"})
      * @ORM\JoinColumn(onDelete="CASCADE")
      * @ORM\OrderBy({"sorting" = "ASC"})
      **/
    private $purchaseItem;


    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\RestaurantBundle\Entity\InvoiceParticular", cascade={"detach","merge"},inversedBy="stockHistory")
     * @ORM\JoinColumn(name="salesItem_id", referencedColumnName="id", nullable=true, onDelete="cascade")
     * @ORM\OrderBy({"sorting" = "ASC"})
     **/
    private  $salesItem;

     /**
      * @ORM\ManyToOne(targetEntity="Appstore\Bundle\RestaurantBundle\Entity\ProductionExpense", cascade={"detach","merge"},inversedBy="stockHistory")
      * @ORM\JoinColumn(onDelete="CASCADE")
      * @ORM\OrderBy({"sorting" = "ASC"})
      **/
    private $productionExpense;


    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\RestaurantBundle\Entity\RestaurantDamage")
     * @ORM\OrderBy({"sorting" = "ASC"})
     **/
    private $damageItem;


    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $process;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $quantity = 0;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $openingQuantity = 0;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $closingQuantity = 0;


    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $purchaseQuantity = 0;


     /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $purchaseReturnQuantity = 0;


     /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $salesQuantity = 0;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $salesReturnQuantity = 0;


    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $damageQuantity = 0;

     /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $productionQuantity = 0;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $bonusQuantity = 0;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $spoilQuantity = 0;



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
     * @return mixed
     */
    public function getBusinessConfig()
    {
        return $this->businessConfig;
    }

    /**
     * @param mixed $businessConfig
     */
    public function setBusinessConfig($businessConfig)
    {
        $this->businessConfig = $businessConfig;
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
     * @return mixed
     */
    public function getPurchaseItem()
    {
        return $this->purchaseItem;
    }

    /**
     * @param mixed $purchaseItem
     */
    public function setPurchaseItem($purchaseItem)
    {
        $this->purchaseItem = $purchaseItem;
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
    public function getDamageItem()
    {
        return $this->damageItem;
    }

    /**
     * @param mixed $damageItem
     */
    public function setDamageItem($damageItem)
    {
        $this->damageItem = $damageItem;
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
    public function getPurchaseReturnQuantity()
    {
        return $this->purchaseReturnQuantity;
    }

    /**
     * @param float $purchaseReturnQuantity
     */
    public function setPurchaseReturnQuantity($purchaseReturnQuantity)
    {
        $this->purchaseReturnQuantity = $purchaseReturnQuantity;
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
    public function getSalesReturnQuantity()
    {
        return $this->salesReturnQuantity;
    }

    /**
     * @param float $salesReturnQuantity
     */
    public function setSalesReturnQuantity($salesReturnQuantity)
    {
        $this->salesReturnQuantity = $salesReturnQuantity;
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
     * @return float
     */
    public function getBonusQuantity()
    {
        return $this->bonusQuantity;
    }

    /**
     * @param float $bonusQuantity
     */
    public function setBonusQuantity($bonusQuantity)
    {
        $this->bonusQuantity = $bonusQuantity;
    }

    /**
     * @return float
     */
    public function getSpoilQuantity()
    {
        return $this->spoilQuantity;
    }

    /**
     * @param float $spoilQuantity
     */
    public function setSpoilQuantity($spoilQuantity)
    {
        $this->spoilQuantity = $spoilQuantity;
    }

    /**
     * @return string
     */
    public function getProcess()
    {
        return $this->process;
    }

    /**
     * @param string $process
     */
    public function setProcess($process)
    {
        $this->process = $process;
    }

    /**
     * @return float
     */
    public function getOpeningQuantity()
    {
        return $this->openingQuantity;
    }

    /**
     * @param float $openingQuantity
     */
    public function setOpeningQuantity($openingQuantity)
    {
        $this->openingQuantity = $openingQuantity;
    }

    /**
     * @return float
     */
    public function getClosingQuantity()
    {
        return $this->closingQuantity;
    }

    /**
     * @param float $closingQuantity
     */
    public function setClosingQuantity($closingQuantity)
    {
        $this->closingQuantity = $closingQuantity;
    }

    /**
     * @return RestaurantConfig
     */
    public function getRestaurantConfig()
    {
        return $this->restaurantConfig;
    }

    /**
     * @param RestaurantConfig $restaurantConfig
     */
    public function setRestaurantConfig($restaurantConfig)
    {
        $this->restaurantConfig = $restaurantConfig;
    }

    /**
     * @return ProductionExpense
     */
    public function getProductionExpense()
    {
        return $this->productionExpense;
    }

    /**
     * @param ProductionExpense $productionExpense
     */
    public function setProductionExpense($productionExpense)
    {
        $this->productionExpense = $productionExpense;
    }

    /**
     * @return float
     */
    public function getProductionQuantity()
    {
        return $this->productionQuantity;
    }

    /**
     * @param float $productionQuantity
     */
    public function setProductionQuantity($productionQuantity)
    {
        $this->productionQuantity = $productionQuantity;
    }


}

