<?php

namespace Appstore\Bundle\InventoryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PurchaseReturnItem
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Appstore\Bundle\InventoryBundle\Repository\PurchaseReturnItemRepository")
 */
class PurchaseReturnItem
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
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\InventoryBundle\Entity\PurchaseReturn", inversedBy="purchaseReturnItems" )
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private  $purchaseReturn;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\InventoryBundle\Entity\PurchaseItem", inversedBy="purchaseReturnItem")
     **/
    private  $purchaseItem;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\StockItem", mappedBy="purchaseReturnItem" , cascade={"remove"})
     **/
    protected $stockItems;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\StockItem", mappedBy="purchaseReplaceItem" , cascade={"remove"})
     **/
    protected $stockReplaceItems;

    /**
     * @var integer
     *
     * @ORM\Column(name="quantity", type="integer", length=5 , nullable = true)
     */
    private $quantity;

    /**
     * @var integer
     *
     * @ORM\Column(name="replaceQuantity", type="integer", length=5 , nullable = true)
     */
    private $replaceQuantity;

    /**
     * @var float
     *
     * @ORM\Column(name="price", type="float" , nullable = true)
     */
    private $price;

    /**
     * @var float
     *
     * @ORM\Column(name="subTotal", type="float" , nullable = true)
     */
    private $subTotal;

    /**
     * @var float
     *
     * @ORM\Column(name="replaceSubTotal", type="float" , nullable = true)
     */
    private $replaceSubTotal;



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
     * Set price
     *
     * @param float $price
     *
     * @return PurchaseReturnItem
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }

    /**
     * @return mixed
     */
    public function getPurchaseReturn()
    {
        return $this->purchaseReturn;
    }

    /**
     * @param mixed $purchaseReturn
     */
    public function setPurchaseReturn($purchaseReturn)
    {
        $this->purchaseReturn = $purchaseReturn;
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
     * @return int
     */
    public function getReplaceQuantity()
    {
        return $this->replaceQuantity;
    }

    /**
     * @param int $replaceQuantity
     */
    public function setReplaceQuantity($replaceQuantity)
    {
        $this->replaceQuantity = $replaceQuantity;
    }

    /**
     * @return float
     */
    public function getReplaceSubTotal()
    {
        return $this->replaceSubTotal;
    }

    /**
     * @param float $replaceSubTotal
     */
    public function setReplaceSubTotal($replaceSubTotal)
    {
        $this->replaceSubTotal = $replaceSubTotal;
    }


}

