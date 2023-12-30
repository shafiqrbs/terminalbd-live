<?php

namespace Setting\Bundle\ToolBundle\Entity;

use Appstore\Bundle\AccountingBundle\Entity\AccountCash;
use Appstore\Bundle\AccountingBundle\Entity\AccountPurchase;
use Appstore\Bundle\AccountingBundle\Entity\AccountPurchaseCommission;
use Appstore\Bundle\AccountingBundle\Entity\AccountSales;
use Appstore\Bundle\AccountingBundle\Entity\Expenditure;
use Appstore\Bundle\AccountingBundle\Entity\PaymentSalary;
use Appstore\Bundle\AccountingBundle\Entity\PettyCash;
use Appstore\Bundle\BusinessBundle\Entity\BusinessInvoice;
use Appstore\Bundle\HospitalBundle\Entity\DoctorInvoice;
use Appstore\Bundle\HospitalBundle\Entity\Invoice;
use Appstore\Bundle\HospitalBundle\Entity\InvoiceTransaction;
use Appstore\Bundle\InventoryBundle\Entity\Purchase;
use Appstore\Bundle\InventoryBundle\Entity\Sales;
use Appstore\Bundle\MedicineBundle\Entity\MedicinePurchase;
use Appstore\Bundle\MedicineBundle\Entity\MedicineSales;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * TransactionMethod
 *
 * @ORM\Table()
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Setting\Bundle\ToolBundle\Repository\TransactionMethodRepository")
 */
class TransactionMethod
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
     * @ORM\OneToMany(targetEntity="Setting\Bundle\ToolBundle\Entity\InvoiceSmsEmail", mappedBy="transactionMethod" )
     **/
    private  $invoiceSmsEmails;

    /**
     * @ORM\OneToMany(targetEntity="Setting\Bundle\ToolBundle\Entity\InvoiceModule", mappedBy="transactionMethod" )
     **/
    private  $invoiceModules;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\Sales", mappedBy="transactionMethod" , cascade={"persist", "remove"})
     */
    protected $sales;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\Purchase", mappedBy="transactionMethod" , cascade={"persist", "remove"})
     */
    protected $purchase;


    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountCash", mappedBy="transactionMethod" , cascade={"persist", "remove"})
     */
    protected $accountCashes;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountJournal", mappedBy="transactionMethod" , cascade={"persist", "remove"})
     */
    protected $accountJournals;

     /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountSalesAdjustment", mappedBy="transactionMethod" , cascade={"persist", "remove"})
     */
    protected $accountSalesAdjustment;

     /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountBalanceTransfer", mappedBy="fromTransactionMethod" , cascade={"persist", "remove"})
     */
    protected $fromBalanceTransfer;

     /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountBalanceTransfer", mappedBy="toTransactionMethod" , cascade={"persist", "remove"})
     */
    protected $toBalanceTransfer;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountPurchaseReturn", mappedBy="transactionMethod" , cascade={"persist", "remove"})
     */
    protected $accountPurchaseReturns;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountPurchase", mappedBy="transactionMethod" , cascade={"persist", "remove"})
     */
    protected $accountPurchases;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountPurchaseCommission", mappedBy="transactionMethod" , cascade={"persist", "remove"})
     */
    protected $accountPurchaseCommissions;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountSales", mappedBy="transactionMethod" , cascade={"persist", "remove"})
     */
    protected $accountSales;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountSalesReturn", mappedBy="transactionMethod" , cascade={"persist", "remove"})
     */
    protected $accountSalesReturns;


    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\Expenditure", mappedBy="transactionMethod" , cascade={"persist", "remove"})
     */
    protected $expendituries;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\PaymentSalary", mappedBy="transactionMethod" , cascade={"persist", "remove"})
     */
    protected $paymentSalaries;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\PettyCash", mappedBy="transactionMethod" , cascade={"persist", "remove"})
     */
    protected $pettyCash;


    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\HospitalBundle\Entity\Invoice", mappedBy="transactionMethod" , cascade={"persist", "remove"})
     */
    protected $hmsInvoices;

     /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\DoctorPrescriptionBundle\Entity\DpsInvoice", mappedBy="transactionMethod" , cascade={"persist", "remove"})
     */
    protected $dpsInvoices;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\HospitalBundle\Entity\DoctorInvoice", mappedBy="transactionMethod" , cascade={"persist", "remove"})
     */
    protected $doctorInvoices;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\HospitalBundle\Entity\InvoiceTransaction", mappedBy="transactionMethod" )
     */
    protected $invoiceTransactions;


     /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\MedicineBundle\Entity\MedicinePurchase", mappedBy="transactionMethod" )
     */
    protected $medicinePurchase;


    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\RestaurantBundle\Entity\Invoice", mappedBy="transactionMethod" )
     */
    protected $restaurantInvoice;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\RestaurantBundle\Entity\Purchase", mappedBy="transactionMethod" )
     */
    protected $restaurantPurchase;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\BusinessBundle\Entity\BusinessPurchase", mappedBy="transactionMethod" )
     */
    protected $businessPurchase;

     /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\BusinessBundle\Entity\BusinessInvoice", mappedBy="transactionMethod" )
     */
    protected $businessInvoice;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\DoctorPrescriptionBundle\Entity\DpsTreatmentPlan", mappedBy="transactionMethod" )
     */
    protected $dpsTreatmentPlans;

     /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\MedicineBundle\Entity\MedicineSales", mappedBy="transactionMethod" )
     */
    protected $medicineSales;


    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;


    /**
     * @Gedmo\Slug(fields={"name"})
     * @ORM\Column(length=255, unique=true)
     */
    private $slug;

    /**
     * @var array
     *
     * @ORM\Column(name="transactionFor", type="array", nullable=true)
     */
    private $transactionFor;

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
     * Set name
     *
     * @param string $name
     *
     * @return TransactionMethod
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
     * Set status
     *
     * @param boolean $status
     *
     * @return TransactionMethod
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
     * @return mixed
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param mixed $slug
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    /**
     * @return string
     */
    public function getTransactionFor()
    {
        return $this->transactionFor;
    }

    /**
     * @param string $transactionFor
     * Inventory
     * Accounting
     * Online Payment
     * Others
     */

    public function setTransactionFor($transactionFor)
    {
        $this->transactionFor = $transactionFor;
    }

    /**
     * @return AccountCash
     */
    public function getAccountCashes()
    {
        return $this->accountCashes;
    }

    /**
     * @return PaymentSalary
     */
    public function getPaymentSalaries()
    {
        return $this->paymentSalaries;
    }

    /**
     * @return AccountPurchase
     */
    public function getAccountPurchases()
    {
        return $this->accountPurchases;
    }

    /**
     * @return AccountSales
     */
    public function getAccountSales()
    {
        return $this->accountSales;
    }


    /**
     * @return PettyCash
     */
    public function getPettyCash()
    {
        return $this->pettyCash;
    }

    /**
     * @return Expenditure
     */
    public function getExpendituries()
    {
        return $this->expendituries;
    }

    /**
     * @return Sales
     */
    public function getSales()
    {
        return $this->sales;
    }

    /**
     * @return mixed
     */
    public function getAccountPurchaseReturns()
    {
        return $this->accountPurchaseReturns;
    }


    /**
     * @return Purchase
     */
    public function getPurchase()
    {
        return $this->purchase;
    }

    /**
     * @return InvoiceSmsEmail
     */
    public function getInvoiceSmsEmails()
    {
        return $this->invoiceSmsEmails;
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
     * @return DmsInvoice
     */
    public function getDmsInvoice()
    {
        return $this->dmsInvoice;
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
     * @return DmsPurchase
     */
    public function getDmsPurchase()
    {
        return $this->dmsPurchase;
    }

    /**
     * @return MedicinePurchase
     */
    public function getMedicinePurchase()
    {
        return $this->medicinePurchase;
    }

    /**
     * @return BusinessInvoice
     */
    public function getBusinessInvoice()
    {
        return $this->businessInvoice;
    }

	/**
	 * @return HotelInvoice
	 */
	public function getHotelInvoice() {
		return $this->hotelInvoice;
	}

	/**
	 * @return HotelPurchase
	 */
	public function getHotelPurchase() {
		return $this->hotelPurchase;
	}

    /**
     * @return AccountPurchaseCommission
     */
    public function getAccountPurchaseCommissions()
    {
        return $this->accountPurchaseCommissions;
    }
}

