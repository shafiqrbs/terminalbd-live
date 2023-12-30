<?php

namespace Appstore\Bundle\AccountingBundle\Entity;

use Appstore\Bundle\BusinessBundle\Entity\BusinessInvoice;
use Appstore\Bundle\DoctorPrescriptionBundle\Entity\DpsInvoice;
use Appstore\Bundle\DomainUserBundle\Entity\Branches;
use Appstore\Bundle\HospitalBundle\Entity\Invoice;
use Appstore\Bundle\InventoryBundle\Entity\Sales;
use Appstore\Bundle\MedicineBundle\Entity\MedicineSales;
use Core\UserBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Setting\Bundle\ToolBundle\Entity\TransactionMethod;

/**
 * AccountSales
 *
 * @ORM\Table(name="account_sales")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\AccountingBundle\Repository\AccountSalesRepository")
 */
class AccountSales
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
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\GlobalOption", inversedBy="accountSales")
     **/

    protected $globalOption;

    /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\TransactionMethod", inversedBy="accountSales" )
     **/
    private  $transactionMethod;


    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountHead", inversedBy="accountSales" )
     **/
    private  $accountHead;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountBank", inversedBy="accountSales" )
     **/
    private  $accountBank;

    /**
     * @ORM\OneToOne(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountCash", mappedBy="accountSales", cascade={"remove"})
     **/
    private  $accountCash;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountMobileBank", inversedBy="accountSales" )
     **/
    private  $accountMobileBank;


    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\DomainUserBundle\Entity\Customer", inversedBy="accountSales" , cascade={"detach","merge"} )
     **/
    private  $customer;


    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\InventoryBundle\Entity\Sales", inversedBy="accountSales" )
     * @ORM\JoinColumn(name="sales_id", referencedColumnName="id", nullable=true, onDelete="cascade")
     **/
    private  $sales;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\DomainUserBundle\Entity\Branches", inversedBy="accountSales" )
     **/
    private  $branches;

    /**
     * @Gedmo\Blameable(on="create")
     * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User", inversedBy="accountSales" )
     **/
    private  $createdBy;

    /**
     * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User", inversedBy="salesApprove" )
     **/
    private  $approvedBy;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\HospitalBundle\Entity\Invoice", inversedBy="accountSales" )
     * @ORM\JoinColumn(name="hmsInvoice_id", referencedColumnName="id", nullable=true, onDelete="cascade")
     **/
    private  $hmsInvoices;


    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\DoctorPrescriptionBundle\Entity\DpsInvoice", inversedBy="accountSales" )
     * @ORM\JoinColumn(name="dpsInvoice_id", referencedColumnName="id", nullable=true, onDelete="cascade")
     **/
    private  $dpsInvoice;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\RestaurantBundle\Entity\Invoice", inversedBy="accountSales" )
     * @ORM\JoinColumn(name="restaurant_id", referencedColumnName="id", nullable=true, onDelete="cascade")
     **/
    private  $restaurantInvoice;

     /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\MedicineBundle\Entity\MedicineSales", inversedBy="accountSales" )
     * @ORM\JoinColumn(name="medicine_sales_id", referencedColumnName="id", nullable=true, onDelete="cascade")
     **/
    private  $medicineSales;

     /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\BusinessBundle\Entity\BusinessInvoice", inversedBy="accountSales" )
     **/
    private  $businessInvoice;


    /**
     * @var string
     *
     * @ORM\Column(name="processHead", type="string", length=50, nullable = true)
     */
    private $processHead;

    /**
     * @var string
     *
     * @ORM\Column(name="processType", type="string", length=50, nullable = true)
     */
    private $processType;

    /**
     * @var float
     *
     * @ORM\Column(name="totalAmount", type="float", nullable=true)
     */
    private $totalAmount = 0;

    /**
     * @var float
     *
     * @ORM\Column(name="tloPrice", type="float", nullable=true)
     */
    private $tloPrice = 0;


    /**
     * @var float
     *
     * @ORM\Column(name="amount", type="float" , nullable=true)
     */
    private $amount = 0;


     /**
     * @var boolean
     *
     * @ORM\Column(name="sms_alert", type="boolean" , nullable=true)
     */
    private $smsAlert = false;


     /**
     * @var float
     *
     * @ORM\Column(name="purchasePrice", type="float" , nullable=true)
     */
    private $purchasePrice = 0;

    /**
     * @var float
     *
     * @ORM\Column(name="vat", type="float" , nullable=true)
     */
    private $vat ;


    /**
     * @var float
     *
     * @ORM\Column(name="balance", type="float", nullable=true)
     */
    private $balance = 0;


    /**
     * @var string
     *
     * @ORM\Column(name="sourceInvoice", type="string", length=50, nullable=true)
     */
    private $sourceInvoice;

    /**
     * @var string
     *
     * @ORM\Column(name="accountRefNo", type="string", length=50, nullable=true)
     */
    private $accountRefNo;

    /**
     * @var string
     *
     * @ORM\Column(name="androidProcessId", type="string", length=50, nullable=true)
     */
    private $androidProcessId;

    /**
     * @var integer
     *
     * @ORM\Column(name="code", type="integer",  nullable=true)
     */
    private $code;

   /**
     * @var datetime
     *
     * @ORM\Column(name="receiveDate", type="datetime", nullable=true)
     */
    private $receiveDate;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @var \DateTime
     * @ORM\Column(name="updated", type="datetime", nullable = true)
     */
    private $updated;

    /**
     * @var string
     *
     * @ORM\Column(name="process", type="string", length=255, nullable = true)
     */
    private $process;

    /**
     * @var string
     *
     * @ORM\Column(name="remark", type="text",  nullable = true)
     */
    private $remark;



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
     * Set amount
     *
     * @param float $amount
     *
     * @return AccountSales
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
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
     * @return datetime
     */
    public function getReceiveDate()
    {
        return $this->receiveDate;
    }

    /**
     * @param datetime $receiveDate
     */
    public function setReceiveDate($receiveDate)
    {
        $this->receiveDate = $receiveDate;
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
     * @return Sales
     */
    public function getSales()
    {
        return $this->sales;
    }

    /**
     * @param Sales $sales
     */
    public function setSales($sales)
    {
        $this->sales = $sales;
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
     * @return mixed
     */
    public function getApprovedBy()
    {
        return $this->approvedBy;
    }

    /**
     * @param mixed $approvedBy
     */
    public function setApprovedBy($approvedBy)
    {
        $this->approvedBy = $approvedBy;
    }

    /**
     * @return float
     */
    public function getTotalAmount()
    {
        return $this->totalAmount;
    }


    /**
     * @return GlobalOption
     */
    public function getGlobalOption()
    {
        return $this->globalOption;
    }

    /**
     * @param GlobalOption $globalOption
     */
    public function setGlobalOption($globalOption)
    {
        $this->globalOption = $globalOption;
    }

    /**
     * @return string
     */
    public function getAccountRefNo()
    {
        return $this->accountRefNo;
    }

    /**
     * @param string $accountRefNo
     */
    public function setAccountRefNo($accountRefNo)
    {
        $this->accountRefNo = $accountRefNo;
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
     * @return AccountBank
     */
    public function getAccountBank()
    {
        return $this->accountBank;
    }

    /**
     * @param AccountBank $accountBank
     */
    public function setAccountBank($accountBank)
    {
        $this->accountBank = $accountBank;
    }

    /**
     * @return AccountCash
     */
    public function getAccountCash()
    {
        return $this->accountCash;
    }

    /**
     * @param AccountCash $accountCash
     */
    public function setAccountCash($accountCash)
    {
        $this->accountCash = $accountCash;
    }

    /**
     * @return TransactionMethod
     */
    public function getTransactionMethod()
    {
        return $this->transactionMethod;
    }

    /**
     * @param TransactionMethod $transactionMethod
     */
    public function setTransactionMethod($transactionMethod)
    {
        $this->transactionMethod = $transactionMethod;
    }

    /**
     * @return float
     */
    public function getBalance()
    {
        return $this->balance;
    }

    /**
     * @param float $balance
     */
    public function setBalance($balance)
    {
        $this->balance = $balance;
    }

    /**
     * @param float $totalAmount
     */
    public function setTotalAmount($totalAmount)
    {
        $this->totalAmount = $totalAmount;
    }

    /**
     * @return string
     */
    public function getProcessHead()
    {
        return $this->processHead;
    }

    /**
     * @param string $processHead
     */
    public function setProcessHead($processHead)
    {
        $this->processHead = $processHead;
    }

    /**
     * @return string
     */
    public function getRemark()
    {
        return $this->remark;
    }

    /**
     * @param string $remark
     */
    public function setRemark($remark)
    {
        $this->remark = $remark;
    }

    /**
     * @return Branches
     */
    public function getBranches()
    {
        return $this->branches;
    }

    /**
     * @param Branches $branches
     */
    public function setBranches($branches)
    {
        $this->branches = $branches;
    }

    /**
     * @return AccountMobileBank
     */
    public function getAccountMobileBank()
    {
        return $this->accountMobileBank;
    }

    /**
     * @param AccountMobileBank $accountMobileBank
     */
    public function setAccountMobileBank($accountMobileBank)
    {
        $this->accountMobileBank = $accountMobileBank;
    }

    /**
     * @return Invoice
     */
    public function getHmsInvoices()
    {
        return $this->hmsInvoices;
    }

    /**
     * @param Invoice $hmsInvoices
     */
    public function setHmsInvoices($hmsInvoices)
    {
        $this->hmsInvoices = $hmsInvoices;
    }

    /**
     * @return mixed
     */
    public function getRestaurantInvoice()
    {
        return $this->restaurantInvoice;
    }

    /**
     * @param mixed $restaurantInvoice
     */
    public function setRestaurantInvoice($restaurantInvoice)
    {
        $this->restaurantInvoice = $restaurantInvoice;
    }

    /**
     * @return DmsInvoice
     */
    public function getDmsInvoices()
    {
        return $this->dmsInvoices;
    }

    /**
     * @param DmsInvoice $dmsInvoices
     */
    public function setDmsInvoices($dmsInvoices)
    {
        $this->dmsInvoices = $dmsInvoices;
    }

    /**
     * @return MedicineSales
     */
    public function getMedicineSales()
    {
        return $this->medicineSales;
    }

    /**
     * @param MedicineSales $medicineSales
     */
    public function setMedicineSales($medicineSales)
    {
        $this->medicineSales = $medicineSales;
    }

    /**
     * @return DpsInvoice
     */
    public function getDpsInvoice()
    {
        return $this->dpsInvoice;
    }

    /**
     * @param DpsInvoice $dpsInvoice
     */
    public function setDpsInvoice($dpsInvoice)
    {
        $this->dpsInvoice = $dpsInvoice;
    }

    /**
     * @return string
     */
    public function getSourceInvoice()
    {
        return $this->sourceInvoice;
    }

    /**
     * @param string $sourceInvoice
     */
    public function setSourceInvoice($sourceInvoice)
    {
        $this->sourceInvoice = $sourceInvoice;
    }

    /**
     * @return float
     */
    public function getVat()
    {
        return $this->vat;
    }

    /**
     * @param float $vat
     */
    public function setVat($vat)
    {
        $this->vat = $vat;
    }

    /**
     * @return BusinessInvoice
     */
    public function getBusinessInvoice()
    {
        return $this->businessInvoice;
    }

    /**
     * @param BusinessInvoice $businessInvoice
     */
    public function setBusinessInvoice($businessInvoice)
    {
        $this->businessInvoice = $businessInvoice;
    }

    /**
     * @return string
     */
    public function getProcessType()
    {
        return $this->processType;
    }

    /**
     * @param string $processType
     */
    public function setProcessType($processType)
    {
        $this->processType = $processType;
    }

	/**
	 * @return HotelInvoice
	 */
	public function getHotelInvoice() {
		return $this->hotelInvoice;
	}

	/**
	 * @param HotelInvoice $hotelInvoice
	 */
	public function setHotelInvoice( $hotelInvoice ) {
		$this->hotelInvoice = $hotelInvoice;
	}

    /**
     * @return AccountHead
     */
    public function getAccountHead()
    {
        return $this->accountHead;
    }

    /**
     * @param AccountHead $accountHead
     */
    public function setAccountHead($accountHead)
    {
        $this->accountHead = $accountHead;
    }

    /**
     * @return \Appstore\Bundle\TallyBundle\Entity\Sales
     */
    public function getTallySales()
    {
        return $this->tallySales;
    }

    /**
     * @param \Appstore\Bundle\TallyBundle\Entity\Sales $tallySales
     */
    public function setTallySales($tallySales)
    {
        $this->tallySales = $tallySales;
    }

    /**
     * @return mixed
     */
    public function getAssetsSales()
    {
        return $this->assetsSales;
    }

    /**
     * @param mixed $assetsSales
     */
    public function setAssetsSales($assetsSales)
    {
        $this->assetsSales = $assetsSales;
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
    public function getTloPrice()
    {
        return $this->tloPrice;
    }

    /**
     * @param float $tloPrice
     */
    public function setTloPrice($tloPrice)
    {
        $this->tloPrice = $tloPrice;
    }



    /**
     * @return Order
     */
    public function getEcommerce()
    {
        return $this->ecommerce;
    }

    /**
     * @param Order $ecommerce
     */
    public function setEcommerce($ecommerce)
    {
        $this->ecommerce = $ecommerce;
    }

    /**
     * @return bool
     */
    public function isSmsAlert()
    {
        return $this->smsAlert;
    }

    /**
     * @param bool $smsAlert
     */
    public function setSmsAlert($smsAlert)
    {
        $this->smsAlert = $smsAlert;
    }

    /**
     * @return string
     */
    public function getAndroidProcessId()
    {
        return $this->androidProcessId;
    }

    /**
     * @param string $androidProcessId
     */
    public function setAndroidProcessId($androidProcessId)
    {
        $this->androidProcessId = $androidProcessId;
    }




}

