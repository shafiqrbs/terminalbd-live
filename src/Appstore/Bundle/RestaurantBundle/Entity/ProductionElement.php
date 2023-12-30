<?php

namespace Appstore\Bundle\RestaurantBundle\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * HmsPurchaseItem
 *
 * @ORM\Table(name ="restaurant_production_element")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\RestaurantBundle\Repository\ProductionElementRepository")
 */
class ProductionElement
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
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\RestaurantBundle\Entity\Particular", inversedBy="productionElements" )
     **/
    private  $material;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\RestaurantBundle\Entity\Particular", inversedBy="productionItems" )
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private  $item;

    /**
     * @var float
     *
     * @ORM\Column(name="quantity", type="float")
     */
    private $quantity;


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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * @param integer $quantity
     */

    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
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
     * @return Particular
     */
    public function getMaterial()
    {
        return $this->material;
    }

    /**
     * @param Particular $material
     */
    public function setMaterial($material)
    {
        $this->material = $material;
    }

    /**
     * @return Particular
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * @param Particular $item
     */
    public function setItem($item)
    {
        $this->item = $item;
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


}

