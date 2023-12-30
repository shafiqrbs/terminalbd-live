<?php

namespace Appstore\Bundle\RestaurantBundle\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * ProductionElement
 *
 * @ORM\Table(name ="restaurant_value_added")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\RestaurantBundle\Repository\ProductionValueAddedRepository")
 */
class ProductionValueAdded
{
    /**
     * @var integer
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;


    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\RestaurantBundle\Entity\Particular", inversedBy="productionValues" )
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private  $productionItem;


    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\RestaurantBundle\Entity\Particular")
     **/
    private  $valueAdded;


    /**
     * @var float
     *
     * @ORM\Column(name="amount", type="float", nullable = true)
     */
    private $amount = 0;


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
    public function getProductionItem()
    {
        return $this->productionItem;
    }

    /**
     * @param mixed $productionItem
     */
    public function setProductionItem($productionItem)
    {
        $this->productionItem = $productionItem;
    }

    /**
     * @return mixed
     */
    public function getValueAdded()
    {
        return $this->valueAdded;
    }

    /**
     * @param mixed $valueAdded
     */
    public function setValueAdded($valueAdded)
    {
        $this->valueAdded = $valueAdded;
    }

    /**
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param float $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

}

