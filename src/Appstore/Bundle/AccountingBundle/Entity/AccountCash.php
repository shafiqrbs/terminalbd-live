<?php

namespace Appstore\Bundle\AccountingBundle\Entity;

use Appstore\Bundle\DomainUserBundle\Entity\Branches;
use Core\UserBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Setting\Bundle\ToolBundle\Entity\InvoiceModule;

/**
 * AccountCash
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Appstore\Bundle\AccountingBundle\Repository\AccountCashRepository")
 */
class AccountCash
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
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\GlobalOption", inversedBy="accountCashes")
     **/
    protected $globalOption;

    /**
     * @ORM\OneToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\InvoiceModule", inversedBy="accountCash" )
     **/
    private  $invoiceModule;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\DomainUserBundle\Entity\Branches", inversedBy="accountCash" )
     **/
    private  $branches;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountHead", inversedBy="accountCashes" )
     **/
    private  $accountHead;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountBank", inversedBy="accountCashes" )
     **/
    private  $accountBank;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountMobileBank", inversedBy="accountCashes" )
     **/
    private  $accountMobileBank;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountLoan", inversedBy="accountCash" )
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private  $accountLoan;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountLoanLedger")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    protected $loanLedger;


    /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\TransactionMethod", inversedBy="accountCashes" )
     **/
    private  $transactionMethod;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountJournal", inversedBy="accountCash")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    protected $accountJournal;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountBalanceTransfer", inversedBy="accountCashes")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    protected $balanceTransfer;

    /**
     * @ORM\OneToOne(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountPurchase", inversedBy="accountCash")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    protected $accountPurchase;

    /**
     * @ORM\OneToOne(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountPurchaseReturn", inversedBy="accountCash")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    protected $accountPurchaseReturn;

    /**
     * @ORM\OneToOne(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountSales", inversedBy="accountCash")
     * @ORM\JoinColumn(name="accountSales_id", referencedColumnName="id", nullable=true, onDelete="cascade")
     */
    protected $accountSales;

    /**
     * @ORM\OneToOne(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountSalesAdjustment", inversedBy="accountCash")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    protected $accountSalesAdjustment;

    /**
     * @ORM\OneToOne(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountConditionLedger")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    protected $conditionLedger;

    /**
     * @ORM\OneToOne(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountSalesReturn", inversedBy="accountCash")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    protected $accountSalesReturn;


    /**
     * @ORM\OneToOne(targetEntity="Appstore\Bundle\AccountingBundle\Entity\Expenditure", inversedBy="accountCash" )
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    protected $expenditure;

    /**
     * @ORM\OneToOne(targetEntity="Appstore\Bundle\AccountingBundle\Entity\PaymentSalary", inversedBy="accountCash" )
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    protected $paymentSalary;

    /**
     * @ORM\OneToOne(targetEntity="Appstore\Bundle\AccountingBundle\Entity\PettyCash", inversedBy="accountCash" )
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    protected $pettyCash;


	/**
	 * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User", inversedBy="accountCashes" )
	 **/
	private  $toUser;

    /**
     * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User", inversedBy="accountPurchases" )
     **/
    private  $createdBy;


    /**
     * @var string
     *
     * @ORM\Column(name="processHead", type="string", length=50, nullable=true)
     */
    private $processHead;

    /**
     * @var float
     *
     * @ORM\Column(name="debit", type="decimal", nullable=true)
     */
    private $debit;

    /**
     * @var float
     *
     * @ORM\Column(name="credit", type="decimal" , nullable=true)
     */
    private $credit;


    /**
     * @var float
     *
     * @ORM\Column(name="balance", type="decimal" , nullable=true)
     */
    private $balance=0;


    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text", nullable=true)
     */
    private $content;

    /**
     * @var string
     *
     * @ORM\Column(name="accountRefNo", type="string", length=50, nullable=true)
     */
    private $accountRefNo;


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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * Set debit
     *
     * @param string $debit
     *
     * @return AccountCash
     */
    public function setDebit($debit)
    {
        $this->debit = $debit;

        return $this;
    }

    /**
     * Get debit
     *
     * @return string
     */
    public function getDebit()
    {
        return $this->debit;
    }

    /**
     * Set credit
     *
     * @param string $credit
     *
     * @return AccountCash
     */
    public function setCredit($credit)
    {
        $this->credit = $credit;

        return $this;
    }

    /**
     * Get credit
     *
     * @return string
     */
    public function getCredit()
    {
        return $this->credit;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     *
     * @return AccountCash
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
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
     * Set updated
     *
     * @param \DateTime $updated
     *
     * @return AccountCash
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
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
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content = $content;
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
     * @return mixed
     */
    public function getAccountHead()
    {
        return $this->accountHead;
    }

    /**
     * @param mixed $accountHead
     */
    public function setAccountHead($accountHead)
    {
        $this->accountHead = $accountHead;
    }

    /**
     * @return mixed
     */
    public function getTransactionMethod()
    {
        return $this->transactionMethod;
    }

    /**
     * @param mixed $transactionMethod
     */
    public function setTransactionMethod($transactionMethod)
    {
        $this->transactionMethod = $transactionMethod;
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
     * @return AccountJournal
     */
    public function getAccountJournal()
    {
        return $this->accountJournal;
    }

    /**
     * @param AccountJournal $accountJournal
     */
    public function setAccountJournal($accountJournal)
    {
        $this->accountJournal = $accountJournal;
    }

    /**
     * @return AccountPurchase
     */
    public function getAccountPurchase()
    {
        return $this->accountPurchase;
    }

    /**
     * @param AccountPurchase $accountPurchase
     */
    public function setAccountPurchase($accountPurchase)
    {
        $this->accountPurchase = $accountPurchase;
    }

    /**
     * @return Expenditure
     */
    public function getExpenditure()
    {
        return $this->expenditure;
    }

  
    /**
     * @return AccountSales
     */
    public function getAccountSales()
    {
        return $this->accountSales;
    }

    /**
     * @param AccountSales $accountSales
     */
    public function setAccountSales($accountSales)
    {
        $this->accountSales = $accountSales;
    }

    /**
     * @return mixed
     */
    public function getAccountSalesReturn()
    {
        return $this->accountSalesReturn;
    }

    /**
     * @param mixed $accountSalesReturn
     */
    public function setAccountSalesReturn($accountSalesReturn)
    {
        $this->accountSalesReturn = $accountSalesReturn;
    }

    /**
     * @return mixed
     */
    public function getAccountPurchaseReturn()
    {
        return $this->accountPurchaseReturn;
    }

    /**
     * @param mixed $accountPurchaseReturn
     */
    public function setAccountPurchaseReturn($accountPurchaseReturn)
    {
        $this->accountPurchaseReturn = $accountPurchaseReturn;
    }

    /**
     * @param Expenditure $expenditure
     */
    public function setExpenditure($expenditure)
    {
        $this->expenditure = $expenditure;
    }

    /**
     * @return mixed
     */
    public function getPettyCash()
    {
        return $this->pettyCash;
    }

    /**
     * @param mixed $pettyCash
     */
    public function setPettyCash($pettyCash)
    {
        $this->pettyCash = $pettyCash;
    }

    /**
     * @return mixed
     */
    public function getPaymentSalary()
    {
        return $this->paymentSalary;
    }

    /**
     * @param mixed $paymentSalary
     */
    public function setPaymentSalary($paymentSalary)
    {
        $this->paymentSalary = $paymentSalary;
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
     * @return AccountOnlineOrder
     */
    public function getAccountOnlineOrder()
    {
        return $this->accountOnlineOrder;
    }

    /**
     * @param AccountOnlineOrder $accountOnlineOrder
     */
    public function setAccountOnlineOrder($accountOnlineOrder)
    {
        $this->accountOnlineOrder = $accountOnlineOrder;
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
     * @param OrderPayment $orderPayments
     */
    public function setOrderPayments($orderPayments)
    {
        $this->orderPayments = $orderPayments;
    }




    /**
     * @return InvoiceModule
     */
    public function getInvoiceModule()
    {
        return $this->invoiceModule;
    }

    /**
     * @param InvoiceModule $invoiceModule
     */
    public function setInvoiceModule($invoiceModule)
    {
        $this->invoiceModule = $invoiceModule;
    }

	/**
	 * @return AccountBalanceTransfer
	 */
	public function getBalanceTransfer() {
		return $this->balanceTransfer;
	}

	/**
	 * @param AccountBalanceTransfer $balanceTransfer
	 */
	public function setBalanceTransfer( $balanceTransfer ) {
		$this->balanceTransfer = $balanceTransfer;
	}

	/**
	 * @return User
	 */
	public function getToUser() {
		return $this->toUser;
	}

	/**
	 * @param User $toUser
	 */
	public function setToUser( $toUser ) {
		$this->toUser = $toUser;
	}

    /**
     * @return AccountSalesAdjustment
     */
    public function getAccountSalesAdjustment()
    {
        return $this->accountSalesAdjustment;
    }

    /**
     * @return AccountLoan
     */
    public function getAccountLoan()
    {
        return $this->accountLoan;
    }

    /**
     * @param AccountLoan $accountLoan
     */
    public function setAccountLoan($accountLoan)
    {
        $this->accountLoan = $accountLoan;
    }

    /**
     * @return mixed
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * @param mixed $createdBy
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;
    }

    /**
     * @return AccountConditionLedger
     */
    public function getConditionLedger()
    {
        return $this->conditionLedger;
    }

    /**
     * @param AccountConditionLedger $conditionLedger
     */
    public function setConditionLedger($conditionLedger)
    {
        $this->conditionLedger = $conditionLedger;
    }


}

