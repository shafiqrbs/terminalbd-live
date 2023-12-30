<?php

namespace Appstore\Bundle\AccountingBundle\Entity;

use Appstore\Bundle\BusinessBundle\Entity\BusinessPurchase;
use Appstore\Bundle\DoctorPrescriptionBundle\Entity\DpsInvoice;
use Appstore\Bundle\DoctorPrescriptionBundle\Entity\DpsTreatmentPlan;
use Appstore\Bundle\HospitalBundle\Entity\DoctorInvoice;
use Appstore\Bundle\HospitalBundle\Entity\Invoice;
use Appstore\Bundle\HospitalBundle\Entity\InvoiceTransaction;
use Appstore\Bundle\InventoryBundle\Entity\Purchase;
use Appstore\Bundle\MedicineBundle\Entity\MedicinePurchase;
use Appstore\Bundle\MedicineBundle\Entity\MedicineSales;
use Doctrine\ORM\Mapping as ORM;
use Setting\Bundle\ToolBundle\Entity\Bank;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Setting\Bundle\ToolBundle\Entity\InvoiceModule;

/**
 * AccountBank
 *
 *
 * @ORM\Table(name="account_bank")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\AccountingBundle\Repository\AccountBankRepository")
 */
class AccountBank
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
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\GlobalOption", inversedBy="accountBank")
     **/
    protected $globalOption;

    /**
     * @ORM\OneToMany(targetEntity="Setting\Bundle\ToolBundle\Entity\InvoiceModule", mappedBy="accountBank" )
     **/
    private  $invoiceModules;

    /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\Bank", inversedBy="accountBanks" )
     **/
    private  $bank;

    /**
     * @ORM\OneToOne(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountHead", mappedBy="accountBank" )
     **/
    private  $accountHead;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\Purchase", mappedBy="accountBank" )
     **/
    private  $purchases;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\Sales", mappedBy="accountBank" )
     **/
    private  $sales;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountJournal", mappedBy="accountBank"  )
     **/
    private  $accountJournals;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountBalanceTransfer", mappedBy="fromAccountBank"  )
     **/
    private  $fromBalanceTransfer;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountBalanceTransfer", mappedBy="toAccountBank"  )
     **/
    private  $toBalanceTransfer;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountCash", mappedBy="accountBank"  )
     **/
    private  $accountCashes;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountPurchase", mappedBy="accountBank"  )
     **/
    private  $accountPurchases;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountPurchaseCommission", mappedBy="accountBank"  )
     **/
    private  $accountPurchaseCommissions;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountSales", mappedBy="accountBank"  )
     **/
    private  $accountSales;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountSalesAdjustment", mappedBy="accountBank"  )
     **/
    private  $accountSalesAdjustment;


    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\Expenditure", mappedBy="accountBank"  )
     **/
    private  $expendituries;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\PaymentSalary", mappedBy="accountBank"  )
     **/
    private  $paymentSalaries;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\PettyCash", mappedBy="accountBank"  )
     **/
    private  $pettyCash;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\HospitalBundle\Entity\Invoice", mappedBy="accountBank")
     */
    protected $hmsInvoices;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\DoctorPrescriptionBundle\Entity\DpsInvoice", mappedBy="accountBank")
     */
    protected $dpsInvoices;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\HospitalBundle\Entity\DoctorInvoice", mappedBy="accountBank" )
     */
    protected $doctorInvoices;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\HospitalBundle\Entity\InvoiceTransaction", mappedBy="accountBank" )
     */
    protected $invoiceTransactions;


     /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\BusinessBundle\Entity\BusinessPurchase", mappedBy="accountBank" )
     */
    protected $businessPurchases;



    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\MedicineBundle\Entity\MedicineSales", mappedBy="accountBank" )
     */
    protected $medicineSales;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\MedicineBundle\Entity\MedicinePurchase", mappedBy="accountBank" )
     */
    protected $medicinePurchases;


    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\RestaurantBundle\Entity\Invoice", mappedBy="accountBank" )
     */
    protected $restaurantInvoices;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\RestaurantBundle\Entity\Purchase", mappedBy="accountBank" )
     */
    protected $restaurantPurchase;


    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\DoctorPrescriptionBundle\Entity\DpsTreatmentPlan", mappedBy="accountBank" )
     */
    protected $dpsTreatmentPlans;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\BusinessBundle\Entity\BusinessInvoice", mappedBy="accountBank" )
     */
    protected $businessInvoice;



    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;


    /**
     * @var string
     *
     * @ORM\Column(name="accountOwner", type="string", length=255, nullable=true)
     */
    private $accountOwner;

    /**
     * @var string
     *
     * @ORM\Column(name="branch", type="string", length=255, nullable=true)
     */
    private $branch;

    /**
     * @var float
     *
     * @ORM\Column(name="serviceCharge", type="float", nullable=true)
     */
    private $serviceCharge = 0;


    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=255, nullable=true)
     */
    private $address;


    /**
     * @var string
     *
     * @ORM\Column(name="accountNo", type="string", length=255)
     */
    private $accountNo;

    /**
     * @var string
     *
     * @ORM\Column(name="accountType", type="string", length=100)
     */
    private $accountType;

    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="boolean")
     */
    private $status = true;


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
     * Set accountNo
     *
     * @param string $accountNo
     *
     * @return AccountBank
     */
    public function setAccountNo($accountNo)
    {
        $this->accountNo = $accountNo;

        return $this;
    }

    /**
     * Get accountNo
     *
     * @return string mixed
     */
    public function getAccountNo()
    {
        return $this->accountNo;
    }

    /**
     * Set status
     *
     * @param boolean $status
     *
     * @return AccountBank
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return boolean
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return Bank
     */
    public function getBank()
    {
        return $this->bank;
    }

    /**
     * @param Bank $bank
     */
    public function setBank($bank)
    {
        $this->bank = $bank;
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
     * @return AccountJournal
     */
    public function getAccountJournals()
    {
        return $this->accountJournals;
    }

    /**
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param string $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * @return string
     */
    public function getBranch()
    {
        return $this->branch;
    }

    /**
     * @param string $branch
     */
    public function setBranch($branch)
    {
        $this->branch = $branch;
    }

    /**
     * @return AccountCash
     */
    public function getAccountCashes()
    {
        return $this->accountCashes;
    }

    /**
     * @return AccountPurchase
     */
    public function getAccountPurchases()
    {
        return $this->accountPurchases;
    }

    /**
     * @return mixed
     */
    public function getAccountSales()
    {
        return $this->accountSales;
    }

    /**
     * @return mixed
     */
    public function getSales()
    {
        return $this->sales;
    }

    /**
     * @return mixed
     */
    public function getExpendituries()
    {
        return $this->expendituries;
    }

    /**
     * @return PaymentSalary
     */
    public function getPaymentSalaries()
    {
        return $this->paymentSalaries;
    }

    /**
     * @param PaymentSalary $paymentSalaries
     */
    public function setPaymentSalaries($paymentSalaries)
    {
        $this->paymentSalaries = $paymentSalaries;
    }

    /**
     * @return PettyCash
     */
    public function getPettyCash()
    {
        return $this->pettyCash;
    }

    /**
     * @param PettyCash $pettyCash
     */
    public function setPettyCash($pettyCash)
    {
        $this->pettyCash = $pettyCash;
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
     * @return Order
     */
    public function getOrders()
    {
        return $this->orders;
    }

    /**
     * @return string
     */
    public function getAccountType()
    {
        return $this->accountType;
    }

    /**
     * @param string $accountType
     */
    public function setAccountType($accountType)
    {
        $this->accountType = $accountType;
    }

    /**
     * @return string
     */
    public function getAccountOwner()
    {
        return $this->accountOwner;
    }

    /**
     * @param string $accountOwner
     */
    public function setAccountOwner($accountOwner)
    {
        $this->accountOwner = $accountOwner;
    }


    /**
     * @return Purchase
     */
    public function getPurchases()
    {
        return $this->purchases;
    }

    /**
     * @return AccountOnlineOrder
     */
    public function getAccountOnlineOrders()
    {
        return $this->accountOnlineOrders;
    }


    /**
     * @return Invoice
     */
    public function getHmsInvoices()
    {
        return $this->hmsInvoices;
    }

    /**
     * @return DoctorInvoice
     */
    public function getDoctorInvoices()
    {
        return $this->doctorInvoices;
    }

    /**
     * @return InvoiceTransaction
     */
    public function getInvoiceTransactions()
    {
        return $this->invoiceTransactions;
    }

    /**
     * @return PreOrderPayment
     */
    public function getPreOrderPayments()
    {
        return $this->preOrderPayments;
    }

    /**
     * @return OrderPayment
     */
    public function getOrderPayments()
    {
        return $this->orderPayments;
    }

    /**
     * @return mixed
     */
    public function getRestaurantInvoices()
    {
        return $this->restaurantInvoices;
    }

    /**
     * @return InvoiceModule
     */
    public function getInvoiceModules()
    {
        return $this->invoiceModules;
    }

    /**
     * @return MedicineSales
     */
    public function getMedicineSales()
    {
        return $this->medicineSales;
    }

    /**
     * @return DpsInvoice
     */
    public function getDpsInvoices()
    {
        return $this->dpsInvoices;
    }

    /**
     * @return BusinessPurchase
     */
    public function getBusinessPurchases()
    {
        return $this->businessPurchases;
    }

    /**
     * @return DpsTreatmentPlan
     */
    public function getDpsTreatmentPlans()
    {
        return $this->dpsTreatmentPlans;
    }

	/**
	 * @return MedicinePurchase
	 */
	public function getMedicinePurchases() {
		return $this->medicinePurchases;
	}

	/**
	 * @return AccountBalanceTransfer
	 */
	public function getFromBalanceTransfer() {
		return $this->fromBalanceTransfer;
	}

	/**
	 * @return AccountBalanceTransfer
	 */
	public function getToBalanceTransfer() {
		return $this->toBalanceTransfer;
	}

    /**
     * @return float
     */
    public function getServiceCharge()
    {
        return $this->serviceCharge;
    }

    /**
     * @param float $serviceCharge
     */
    public function setServiceCharge(float $serviceCharge)
    {
        $this->serviceCharge = $serviceCharge;
    }

    /**
     * @return AccountSalesAdjustment
     */
    public function getAccountSalesAdjustment()
    {
        return $this->accountSalesAdjustment;
    }

    /**
     * @return AccountPurchaseCommission
     */
    public function getAccountPurchaseCommissions()
    {
        return $this->accountPurchaseCommissions;
    }

}

