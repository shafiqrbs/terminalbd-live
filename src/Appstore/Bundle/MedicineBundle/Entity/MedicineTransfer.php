<?php

namespace Appstore\Bundle\MedicineBundle\Entity;

use Appstore\Bundle\AccountingBundle\Entity\AccountBank;
use Appstore\Bundle\AccountingBundle\Entity\AccountMobileBank;
use Appstore\Bundle\AccountingBundle\Entity\AccountPurchase;
use Core\UserBundle\Entity\User;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Setting\Bundle\ToolBundle\Entity\Bank;
use Setting\Bundle\ToolBundle\Entity\TransactionMethod;

/**
 * MedicineTransfer
 *
 * @ORM\Table( name ="medicine_transfer")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\MedicineBundle\Repository\MedicineTransferRepository")
 */
class MedicineTransfer
{
    /**
     * @var integer
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;


    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\MedicineBundle\Entity\MedicineConfig", inversedBy="medicineTransfers" , cascade={"detach","merge"} )
     **/
    private  $medicineConfig;


    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\MedicineBundle\Entity\MedicineTransferItem", mappedBy="medicineTransfer" , cascade={"detach","merge"} )
     **/
    private  $medicineTransferItems;


    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\DomainUserBundle\Entity\Customer", inversedBy="medicineTransfers" , cascade={"detach","merge"} )
     **/
    private  $customer;


    /**
     * @Gedmo\Blameable(on="create")
     * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User", inversedBy="medicinePurchaseReturn" )
     **/
    private  $createdBy;


	/**
	 * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User", inversedBy="medicineTransfersReturnApprovedBy" )
	 **/
	private  $approvedBy;


	/**
	 * @var string
	 *
	 * @ORM\Column(name="discountType", type="string", length=20, nullable=true)
	 */
	private $discountType ='percentage';


	/**
	 * @var float
	 *
	 * @ORM\Column(name="discount", type="float", nullable=true)
	 */
	private $discount;

	/**
	 * @var boolean
	 *
	 * @ORM\Column(name="adjusted", type="boolean", nullable=true)
	 */
	private $adjusted=false;


	/**
	 * @var float
	 *
	 * @ORM\Column(name="discountCalculation", type="float" , nullable=true)
	 */
	private $discountCalculation;

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
     * @var string
     *
     * @ORM\Column(name="invoice", type="string", length=255, nullable=true)
     */
    private $invoice;

    /**
     * @var integer
     *
     * @ORM\Column(name="code", type="integer",  nullable=true)
     */
    private $code;

    /**
     * @var float
     *
     * @ORM\Column(name="subTotal", type="float", nullable=true)
     */
    private $subTotal = 0;

    /**
     * @var float
     *
     * @ORM\Column(name="total", type="float", nullable=true)
     */
    private $total = 0;

    /**
     * @var float
     *
     * @ORM\Column(name="payment", type="float", nullable=true)
     */
    private $payment = 0;

    /**
     * @var float
     *
     * @ORM\Column(name="due", type="float", nullable=true)
     */
    private $due = 0;



    /**
     * @var string
     *
     * @ORM\Column(name="process", type="string", nullable=true)
     */
    private $process = "created";

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
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }


    /**
     * @return User
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * @param User $createdBy
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;
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
     * created
     * progress
     * complete
     * approved
     */
    public function setProcess($process)
    {
        $this->process = $process;
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
     * @return MedicineConfig
     */
    public function getMedicineConfig()
    {
        return $this->medicineConfig;
    }

    /**
     * @param MedicineConfig $medicineConfig
     */
    public function setMedicineConfig($medicineConfig)
    {
        $this->medicineConfig = $medicineConfig;
    }



    /**
     * @return User
     */
    public function getApprovedBy()
    {
        return $this->approvedBy;
    }

    /**
     * @param User $approvedBy
     */
    public function setApprovedBy($approvedBy)
    {
        $this->approvedBy = $approvedBy;
    }

    /**
     * @return string
     */
    public function getInvoice()
    {
        return $this->invoice;
    }

    /**
     * @param string $invoice
     */
    public function setInvoice($invoice)
    {
        $this->invoice = $invoice;
    }

    /**
     * @return int
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param int $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

	/**
	 * @return float
	 */
	public function getTotal(){
		return $this->total;
	}

	/**
	 * @param float $total
	 */
	public function setTotal( float $total ) {
		$this->total = $total;
	}

	/**
	 * @return float
	 */
	public function getDiscountCalculation(){
		return $this->discountCalculation;
	}

	/**
	 * @param float $discountCalculation
	 */
	public function setDiscountCalculation( float $discountCalculation ) {
		$this->discountCalculation = $discountCalculation;
	}

	/**
	 * @return float
	 */
	public function getDiscount(){
		return $this->discount;
	}

	/**
	 * @param float $discount
	 */
	public function setDiscount( float $discount ) {
		$this->discount = $discount;
	}

	/**
	 * @return string
	 */
	public function getDiscountType(){
		return $this->discountType;
	}

	/**
	 * @param string $discountType
	 */
	public function setDiscountType( string $discountType ) {
		$this->discountType = $discountType;
	}


	/**
	 * @return bool
	 */
	public function isAdjusted(){
		return $this->adjusted;
	}

	/**
	 * @param bool $adjusted
	 */
	public function setAdjusted( bool $adjusted ) {
		$this->adjusted = $adjusted;
	}


    /**
     * @return mixed
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * @param mixed $customer
     */
    public function setCustomer($customer)
    {
        $this->customer = $customer;
    }

    /**
     * @return MedicineTransferItem
     */
    public function getMedicineTransferItems()
    {
        return $this->medicineTransferItems;
    }

    /**
     * @return float
     */
    public function getPayment()
    {
        return $this->payment;
    }

    /**
     * @param float $payment
     */
    public function setPayment($payment)
    {
        $this->payment = $payment;
    }

    /**
     * @return float
     */
    public function getDue()
    {
        return $this->due;
    }

    /**
     * @param float $due
     */
    public function setDue($due)
    {
        $this->due = $due;
    }


}

