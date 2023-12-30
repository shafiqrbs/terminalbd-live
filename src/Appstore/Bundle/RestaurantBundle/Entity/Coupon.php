<?php

namespace Appstore\Bundle\RestaurantBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Coupon
 *
 * @ORM\Table("restaurant_coupon")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\RestaurantBundle\Repository\CouponRepository")
 * @UniqueEntity(fields="couponCode",message="This coupon code already existing,Please try again.")
 */

class Coupon
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
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\RestaurantBundle\Entity\RestaurantConfig", inversedBy="coupons")
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private $restaurantConfig;


    /**
     * @var float
     *
     * @ORM\Column(name="amount", type="float", nullable = true)
     */
    private $amount;

    /**
     * @var float
     *
     * @ORM\Column(name="amountLimit", type="float", nullable = true)
     */
    private $amountLimit;


    /**
     * @var float
     *
     * @ORM\Column(name="validAmount", type="float", nullable = true)
     */
    private $validAmount;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;


    /**
     * @var string
     *
     * @ORM\Column(name="couponCode", type="string", length=255)
     */
    private $couponCode;


    /**
     * @var integer
     *
     * @ORM\Column(name="quantity", type="integer", length = 5, nullable = true)
     */
    private $quantity;


    /**
     * @var integer
     *
     * @ORM\Column(name="remainingQuantity", type="integer", length=5, nullable = true)
     */
    private $remainingQuantity;


    /**
     * @var boolean
     *
     * @ORM\Column(name="percentage", type="boolean")
     */
    private $percentage = false;


    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="boolean")
     */
    private $status = true;

    /**
     * @var \DateTime
     * @ORM\Column(name="startDate", type="datetime")
     */
    private $startDate;


    /**
     * @var \DateTime
     * @ORM\Column(name="endDate", type="datetime")
     */
    private $endDate;


    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updated_at", type="datetime")
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
     * Set name
     *
     * @param string $name
     *
     * @return string
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
     * @return bool
     */
    public function getStatus()
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
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @param \DateTime $startDate
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;
    }

    /**
     * @return \DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @param \DateTime $endDate
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;
    }


    /**
     * @return float
     */
    public function getValidAmount()
    {
        return $this->validAmount;
    }

    /**
     * @param float $validAmount
     */
    public function setValidAmount($validAmount)
    {
        $this->validAmount = $validAmount;
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

    /**
     * @return float
     */
    public function getAmountLimit()
    {
        return $this->amountLimit;
    }

    /**
     * @param float $amountLimit
     */
    public function setAmountLimit($amountLimit)
    {
        $this->amountLimit = $amountLimit;
    }

    /**
     * @return boolean
     */
    public function getPercentage()
    {
        return $this->percentage;
    }

    /**
     * @param boolean $percentage
     */
    public function setPercentage($percentage)
    {
        $this->percentage = $percentage;
    }

    /**
     * @return string
     */
    public function getCouponCode()
    {
        return $this->couponCode;
    }

    /**
     * @param string $couponCode
     */
    public function setCouponCode($couponCode)
    {
        $this->couponCode = $couponCode;
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
     * @return mixed
     */
    public function getRestaurantConfig()
    {
        return $this->restaurantConfig;
    }

    /**
     * @param mixed $restaurantConfig
     */
    public function setRestaurantConfig($restaurantConfig)
    {
        $this->restaurantConfig = $restaurantConfig;
    }


}

