<?php

namespace Terminalbd\PosBundle\Entity;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * SalesItem
 *
 * @ORM\Table(name="pos_item")
 * @ORM\Entity(repositoryClass="Terminalbd\PosBundle\Repository\PosItemRepository")
 */
class PosItem
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
     * @ORM\ManyToOne(targetEntity="Terminalbd\PosBundle\Entity\Pos", inversedBy="posItems" )
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private  $pos;


    /**
     * @var integer
     *
     * @ORM\Column(name="itemId", type="integer", nullable=true)
     */
    private $itemId;

    /**
     * @var float
     *
     * @ORM\Column(name="name", type="string", nullable=true)
     */
    private $name;


    /**
     * @var float
     *
     * @ORM\Column(name="unit", type="string", nullable=true)
     */
    private $unit;


    /**
     * @var float
     *
     * @ORM\Column(name="quantity", type="float")
     */
    private $quantity;


    /**
     * @var float
     *
     * @ORM\Column(name="price", type="float")
     */
    private $price;

    /**
     * @var float
     *
     * @ORM\Column(name="discountPrice", type="float", nullable=true)
     */
    private $discountPrice;

    /**
     * @var float
     *
     * @ORM\Column(name="subTotal", type="float")
     */
    private $subTotal;


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
     * @return mixed
     */
    public function getPos()
    {
        return $this->pos;
    }

    /**
     * @param mixed $pos
     */
    public function setPos($pos)
    {
        $this->pos = $pos;
    }

    /**
     * @return int
     */
    public function getItemId()
    {
        return $this->itemId;
    }

    /**
     * @param int $itemId
     */
    public function setItemId($itemId)
    {
        $this->itemId = $itemId;
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
     * @return float
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param float $name
     */
    public function setName($name)
    {
        $this->name = $name;
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

    /**
     * @return float
     */
    public function getUnit()
    {
        return $this->unit;
    }

    /**
     * @param float $unit
     */
    public function setUnit($unit)
    {
        $this->unit = $unit;
    }


}

