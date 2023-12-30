<?php

namespace Appstore\Bundle\AccountingBundle\Entity;

use Appstore\Bundle\DomainUserBundle\Entity\Customer;
use Appstore\Bundle\InventoryBundle\Entity\Vendor;
use Appstore\Bundle\MedicineBundle\Entity\MedicineVendor;
use Core\UserBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;

/**
 * AccountHead
 *
 * @ORM\Table(name="account_head")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\AccountingBundle\Repository\AccountHeadRepository")
 */
class AccountHead
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
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\GlobalOption", inversedBy="accountHeads", cascade={"detach","merge"})
     */
    protected $globalOption;

    /**
     * @ORM\ManyToOne(targetEntity="AccountHead", inversedBy="children", cascade={"detach","merge"})
     * @ORM\JoinColumn(name="parent", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $parent;

    /**
     * @ORM\OneToMany(targetEntity="AccountHead" , mappedBy="parent")
     * @ORM\OrderBy({"name" = "ASC"})
     **/
    private $children;


    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\Expenditure", mappedBy="accountHead" )
     * @ORM\OrderBy({"id" = "DESC"})
     **/
    private  $expendituries;


    /**
     * @ORM\OneToOne(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountBank", inversedBy="accountHead" )
     * @ORM\JoinColumn(name="bank_account_id", referencedColumnName="id", nullable=true, onDelete="cascade")
     **/
    private  $accountBank;

    /**
     * @ORM\OneToOne(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountMobileBank", inversedBy="accountHead" )
     * @ORM\JoinColumn(name="mobile_account_id", referencedColumnName="id", nullable=true, onDelete="cascade")
     **/
    private  $accountMobileBank;


    /**
     * @ORM\OneToOne(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountVendor", inversedBy="accountHead" )
     **/
    private  $accountVendor;


     /**
     * @ORM\OneToOne(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountCondition")
     **/
    private  $accountCondition;

    /**
     * @ORM\OneToOne(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountLoanUser")
     **/
    private  $accountLoanUser;


     /**
     * @ORM\OneToOne(targetEntity="Appstore\Bundle\MedicineBundle\Entity\MedicineVendor", inversedBy="accountHead" )
     **/
    private  $medicineVendor;


    /**
     * @ORM\OneToOne(targetEntity="Appstore\Bundle\InventoryBundle\Entity\Vendor", inversedBy="accountHead" )
     **/
    private  $inventoryVendor;


    /**
     * @ORM\OneToOne(targetEntity="Appstore\Bundle\DomainUserBundle\Entity\Customer", inversedBy="accountHead" )
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private  $customer;

    /**
     * @ORM\OneToOne(targetEntity="Core\UserBundle\Entity\User", inversedBy="accountHead" )
     **/
    private  $employee;


    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountPurchase", mappedBy="accountHead" )
     * @ORM\OrderBy({"id" = "DESC"})
     **/
    private  $accountPurchases;

     /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountSales", mappedBy="accountHead" )
     * @ORM\OrderBy({"id" = "DESC"})
     **/
    private  $accountSales;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\ExpenseCategory", mappedBy="accountHead" )
     * @ORM\OrderBy({"id" = "DESC"})
     **/
    private  $expenseCategory;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountCash", mappedBy="accountHead" )
     * @ORM\OrderBy({"id" = "DESC"})
     **/
    private  $accountCashes;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountJournal", mappedBy="accountHeadDebit" )
     * @ORM\OrderBy({"id" = "DESC"})
     **/
    private  $accountJournalDebits;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountJournal", mappedBy="accountHeadCredit" )
     * @ORM\OrderBy({"id" = "DESC"})
     **/
    private  $accountJournalCredits;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\PaymentSalary", mappedBy="accountHead" )
     * @ORM\OrderBy({"id" = "DESC"})
     **/
    private  $paymentSalaries;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\Transaction", mappedBy="accountHead" )
     * @ORM\OrderBy({"id" = "DESC"})
     **/
    private  $transactions;

	/**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\Transaction", mappedBy="subAccountHead" )
     * @ORM\OrderBy({"id" = "DESC"})
     **/
    private  $subTransactions;

    /**
	 * @var string
	 *
	 * @ORM\Column(name="motherAccount", type="string", length=50, nullable=true)
	 */
	private $motherAccount;

	/**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=20, nullable= true)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="source", type="string", length=30, nullable=true)
     */
    private $source;

    /**
     * @Gedmo\Slug(fields={"name"})
     * @Doctrine\ORM\Mapping\Column(length=255)
     */
    private $slug;


    /**
     * @var string
     *
     * @ORM\Column(name="toIncrease", type="string", length=20, nullable=true)
     */
    private $toIncrease;


	/**
	 * @var integer
	 *
	 * @ORM\Column(name="sorting", type="integer", length=10, nullable=true)
	 */
	private $sorting;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isParent", type="boolean" , nullable=true)
     */
    private $isParent = false;

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
     * Set code
     *
     * @param string $code
     *
     * @return AccountHead
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set status
     *
     * @param boolean $status
     *
     * @return AccountHead
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
     * @return accountHead
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param mixed $parent
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
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
     * @return boolean
     */
    public function getIsParent()
    {
        return $this->isParent;
    }

    /**
     * @param boolean $isParent
     */
    public function setIsParent($isParent)
    {
        $this->isParent = $isParent;
    }

    /**
     * @return string
     */
    public function getToIncrease()
    {
        return $this->toIncrease;
    }

    /**
     * @param string $toIncrease
     */
    public function setToIncrease($toIncrease)
    {
        $this->toIncrease = $toIncrease;
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
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @return mixed
     */
    public function getTransactions()
    {
        return $this->transactions;
    }

    /**
     * @param mixed $transactions
     */
    public function setTransactions($transactions)
    {
        $this->transactions = $transactions;
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
    public function getAccountCashes()
    {
        return $this->accountCashes;
    }

    /**
     * @return mixed
     */
    public function getAccountJournalDebits()
    {
        return $this->accountJournalDebits;
    }

    /**
     * @return mixed
     */
    public function getAccountJournalCredits()
    {
        return $this->accountJournalCredits;
    }

    /**
     * @return mixed
     */
    public function getPaymentSalaries()
    {
        return $this->paymentSalaries;
    }

    /**
     * @return mixed
     */
    public function getExpendituries()
    {
        return $this->expendituries;
    }

    /**
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    /**
     * @return ExpenseCategory
     */
    public function getExpenseCategory()
    {
        return $this->expenseCategory;
    }

	/**
	 * @return int
	 */
	public function getSorting(){
		return $this->sorting;
	}

	/**
	 * @param int $sorting
	 */
	public function setSorting( int $sorting ) {
		$this->sorting = $sorting;
	}

	/**
	 * @return string
	 */
	public function getMotherAccount(){
		return $this->motherAccount;
	}

	/**
	 * @param string $motherAccount
	 */
	public function setMotherAccount( string $motherAccount ) {
		$this->motherAccount = $motherAccount;
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
     * @return Transaction
     */
    public function getSubTransactions()
    {
        return $this->subTransactions;
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
     * @return AccountVendor
     */
    public function getAccountVendor()
    {
        return $this->accountVendor;
    }

    /**
     * @param AccountVendor $accountVendor
     */
    public function setAccountVendor($accountVendor)
    {
        $this->accountVendor = $accountVendor;
    }

    /**
     * @return Customer
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * @param Customer $customer
     */
    public function setCustomer($customer)
    {
        $this->customer = $customer;
    }

    /**
     * @return User
     */
    public function getEmployee()
    {
        return $this->employee;
    }

    /**
     * @param User $employee
     */
    public function setEmployee($employee)
    {
        $this->employee = $employee;
    }

    /**
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @param string $source
     */
    public function setSource($source)
    {
        $this->source = $source;
    }

    /**
     * @return MedicineVendor
     */
    public function getMedicineVendor()
    {
        return $this->medicineVendor;
    }

    /**
     * @param MedicineVendor $medicineVendor
     */
    public function setMedicineVendor($medicineVendor)
    {
        $this->medicineVendor = $medicineVendor;
    }

    /**
     * @return Vendor
     */
    public function getInventoryVendor()
    {
        return $this->inventoryVendor;
    }

    /**
     * @param Vendor $inventoryVendor
     */
    public function setInventoryVendor($inventoryVendor)
    {
        $this->inventoryVendor = $inventoryVendor;
    }


    /**
     * @return AccountCondition
     */
    public function getAccountCondition()
    {
        return $this->accountCondition;
    }

    /**
     * @param AccountCondition $accountCondition
     */
    public function setAccountCondition($accountCondition)
    {
        $this->accountCondition = $accountCondition;
    }

    /**
     * @return mixed
     */
    public function getAccountLoanUser()
    {
        return $this->accountLoanUser;
    }

    /**
     * @param mixed $accountLoanUser
     */
    public function setAccountLoanUser($accountLoanUser)
    {
        $this->accountLoanUser = $accountLoanUser;
    }


}

