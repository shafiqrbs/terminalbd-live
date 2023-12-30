<?php

namespace Appstore\Bundle\HospitalBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HmsStockOutItem
 *
 * @ORM\Table(name ="hms_stock_out_item")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\HospitalBundle\Repository\HmsStockOutItemRepository")
 */
class HmsStockOutItem
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
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\HospitalBundle\Entity\Particular", inversedBy="stockOutItems" )
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private  $particular;


    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\HospitalBundle\Entity\HmsStockOut", inversedBy="stockOutItems" )
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private  $stockOut;


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
     * @var float
     *
     * @ORM\Column(name="salesSubTotal", type="float", nullable = true)
     */
    private $salesSubTotal;


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
     * @return HmsStockOutItem
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
	 * @return HmsStockOut
	 */
	public function getStockOut() {
		return $this->stockOut;
	}

	/**
	 * @param HmsStockOut $stockOut
	 */
	public function setStockOut( $stockOut ) {
		$this->stockOut = $stockOut;
	}

	/**
	 * @return float
	 */
	public function getPurchasePrice(){
		return $this->purchasePrice;
	}

	/**
	 * @param float $purchasePrice
	 */
	public function setPurchasePrice( float $purchasePrice ) {
		$this->purchasePrice = $purchasePrice;
	}

	/**
	 * @return float
	 */
	public function getSalesPrice(){
		return $this->salesPrice;
	}

	/**
	 * @param float $salesPrice
	 */
	public function setSalesPrice( float $salesPrice ) {
		$this->salesPrice = $salesPrice;
	}

	/**
	 * @return float
	 */
	public function getPurchaseSubTotal(){
		return $this->purchaseSubTotal;
	}

	/**
	 * @param float $purchaseSubTotal
	 */
	public function setPurchaseSubTotal( float $purchaseSubTotal ) {
		$this->purchaseSubTotal = $purchaseSubTotal;
	}

	/**
	 * @return float
	 */
	public function getSalesSubTotal() {
		return $this->salesSubTotal;
	}

	/**
	 * @param float $salesSubTotal
	 */
	public function setSalesSubTotal( float $salesSubTotal ) {
		$this->salesSubTotal = $salesSubTotal;
	}

	/**
	 * @return Particular
	 */
	public function getParticular() {
		return $this->particular;
	}

	/**
	 * @param Particular $particular
	 */
	public function setParticular( $particular ) {
		$this->particular = $particular;
	}


}

