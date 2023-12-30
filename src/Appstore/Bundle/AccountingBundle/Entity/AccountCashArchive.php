<?php

namespace Appstore\Bundle\AccountingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * AccountCash
 *
 * @ORM\Table(name="account_cash_archive")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\AccountingBundle\Repository\AccountCashRepository")
 */
class AccountCashArchive
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
     * @var integer
     *
     * @ORM\Column(name="globalOption", type="integer",nullable=true)
     */
    protected $globalOption;

    /**
     * @var integer
     *
     * @ORM\Column(name="invoiceModule", type="integer",nullable=true)
     */
    private  $invoiceModule;


    /**
     * @var integer
     *
     * @ORM\Column(name="accountHead", type="integer",nullable=true)
     */
    private  $accountHead;


    /**
     * @var integer
     *
     * @ORM\Column(name="accountBank", type="integer",nullable=true)
     */
    private  $accountBank;


    /**
     * @var integer
     *
     * @ORM\Column(name="accountMobileBank", type="integer",nullable=true)
     */
    private  $accountMobileBank;


    /**
     * @var integer
     *
     * @ORM\Column(name="accountLoan", type="integer",nullable=true)
     */
    private  $accountLoan;


    /**
     * @var integer
     *
     * @ORM\Column(name="loanLedger", type="integer",nullable=true)
     */
    protected $loanLedger;


    /**
     * @var integer
     *
     * @ORM\Column(name="transactionMethod", type="integer",nullable=true)
     */
    private  $transactionMethod;


    /**
     * @var integer
     *
     * @ORM\Column(name="accountJournal", type="integer",nullable=true)
     */
    protected $accountJournal;


    /**
     * @var integer
     *
     * @ORM\Column(name="balanceTransfer", type="integer",nullable=true)
     */
    protected $balanceTransfer;


    /**
     * @var integer
     *
     * @ORM\Column(name="accountPurchase", type="integer",nullable=true)
     */
    protected $accountPurchase;


    /**
     * @var integer
     *
     * @ORM\Column(name="accountPurchaseReturn", type="integer",nullable=true)
     */
    protected $accountPurchaseReturn;


    /**
     * @var integer
     *
     * @ORM\Column(name="accountSales", type="integer",nullable=true)
     */
    protected $accountSales;


    /**
     * @var integer
     *
     * @ORM\Column(name="accountSalesAdjustment", type="integer",nullable=true)
     */
    protected $accountSalesAdjustment;


    /**
     * @var integer
     *
     * @ORM\Column(name="conditionLedge", type="integer",nullable=true)
     */
    protected $conditionLedger;


    /**
     * @var integer
     *
     * @ORM\Column(name="accountSalesReturn", type="integer",nullable=true)
     */
    protected $accountSalesReturn;


    /**
     * @var integer
     *
     * @ORM\Column(name="expenditur", type="integer",nullable=true)
     */
    protected $expenditure;


    /**
     * @var integer
     *
     * @ORM\Column(name="paymentSalary", type="integer",nullable=true)
     */
    protected $paymentSalary;


    /**
     * @var integer
     *
     * @ORM\Column(name="pettyCash", type="integer",nullable=true)
     */
    protected $pettyCash;


    /**
     * @var integer
     *
     * @ORM\Column(name="toUser", type="integer",nullable=true)
     */
	private  $toUser;


    /**
     * @var integer
     *
     * @ORM\Column(name="createdBy", type="integer",nullable=true)
     */
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
     * @return int
     */
    public function getGlobalOption()
    {
        return $this->globalOption;
    }

    /**
     * @param int $globalOption
     */
    public function setGlobalOption($globalOption)
    {
        $this->globalOption = $globalOption;
    }

    /**
     * @return int
     */
    public function getInvoiceModule()
    {
        return $this->invoiceModule;
    }

    /**
     * @param int $invoiceModule
     */
    public function setInvoiceModule($invoiceModule)
    {
        $this->invoiceModule = $invoiceModule;
    }

    /**
     * @return int
     */
    public function getAccountHead()
    {
        return $this->accountHead;
    }

    /**
     * @param int $accountHead
     */
    public function setAccountHead($accountHead)
    {
        $this->accountHead = $accountHead;
    }

    /**
     * @return int
     */
    public function getAccountBank()
    {
        return $this->accountBank;
    }

    /**
     * @param int $accountBank
     */
    public function setAccountBank($accountBank)
    {
        $this->accountBank = $accountBank;
    }

    /**
     * @return int
     */
    public function getAccountMobileBank()
    {
        return $this->accountMobileBank;
    }

    /**
     * @param int $accountMobileBank
     */
    public function setAccountMobileBank($accountMobileBank)
    {
        $this->accountMobileBank = $accountMobileBank;
    }

    /**
     * @return int
     */
    public function getAccountLoan()
    {
        return $this->accountLoan;
    }

    /**
     * @param int $accountLoan
     */
    public function setAccountLoan($accountLoan)
    {
        $this->accountLoan = $accountLoan;
    }

    /**
     * @return int
     */
    public function getLoanLedger()
    {
        return $this->loanLedger;
    }

    /**
     * @param int $loanLedger
     */
    public function setLoanLedger($loanLedger)
    {
        $this->loanLedger = $loanLedger;
    }

    /**
     * @return int
     */
    public function getTransactionMethod()
    {
        return $this->transactionMethod;
    }

    /**
     * @param int $transactionMethod
     */
    public function setTransactionMethod($transactionMethod)
    {
        $this->transactionMethod = $transactionMethod;
    }

    /**
     * @return int
     */
    public function getAccountJournal()
    {
        return $this->accountJournal;
    }

    /**
     * @param int $accountJournal
     */
    public function setAccountJournal($accountJournal)
    {
        $this->accountJournal = $accountJournal;
    }

    /**
     * @return int
     */
    public function getBalanceTransfer()
    {
        return $this->balanceTransfer;
    }

    /**
     * @param int $balanceTransfer
     */
    public function setBalanceTransfer($balanceTransfer)
    {
        $this->balanceTransfer = $balanceTransfer;
    }

    /**
     * @return int
     */
    public function getAccountPurchase()
    {
        return $this->accountPurchase;
    }

    /**
     * @param int $accountPurchase
     */
    public function setAccountPurchase($accountPurchase)
    {
        $this->accountPurchase = $accountPurchase;
    }

    /**
     * @return int
     */
    public function getAccountPurchaseReturn()
    {
        return $this->accountPurchaseReturn;
    }

    /**
     * @param int $accountPurchaseReturn
     */
    public function setAccountPurchaseReturn($accountPurchaseReturn)
    {
        $this->accountPurchaseReturn = $accountPurchaseReturn;
    }

    /**
     * @return int
     */
    public function getAccountSales()
    {
        return $this->accountSales;
    }

    /**
     * @param int $accountSales
     */
    public function setAccountSales($accountSales)
    {
        $this->accountSales = $accountSales;
    }

    /**
     * @return int
     */
    public function getAccountSalesAdjustment()
    {
        return $this->accountSalesAdjustment;
    }

    /**
     * @param int $accountSalesAdjustment
     */
    public function setAccountSalesAdjustment($accountSalesAdjustment)
    {
        $this->accountSalesAdjustment = $accountSalesAdjustment;
    }

    /**
     * @return int
     */
    public function getConditionLedger()
    {
        return $this->conditionLedger;
    }

    /**
     * @param int $conditionLedger
     */
    public function setConditionLedger($conditionLedger)
    {
        $this->conditionLedger = $conditionLedger;
    }

    /**
     * @return int
     */
    public function getAccountSalesReturn()
    {
        return $this->accountSalesReturn;
    }

    /**
     * @param int $accountSalesReturn
     */
    public function setAccountSalesReturn($accountSalesReturn)
    {
        $this->accountSalesReturn = $accountSalesReturn;
    }

    /**
     * @return int
     */
    public function getExpenditure()
    {
        return $this->expenditure;
    }

    /**
     * @param int $expenditure
     */
    public function setExpenditure($expenditure)
    {
        $this->expenditure = $expenditure;
    }

    /**
     * @return int
     */
    public function getPaymentSalary()
    {
        return $this->paymentSalary;
    }

    /**
     * @param int $paymentSalary
     */
    public function setPaymentSalary($paymentSalary)
    {
        $this->paymentSalary = $paymentSalary;
    }

    /**
     * @return int
     */
    public function getPettyCash()
    {
        return $this->pettyCash;
    }

    /**
     * @param int $pettyCash
     */
    public function setPettyCash($pettyCash)
    {
        $this->pettyCash = $pettyCash;
    }

    /**
     * @return int
     */
    public function getToUser()
    {
        return $this->toUser;
    }

    /**
     * @param int $toUser
     */
    public function setToUser($toUser)
    {
        $this->toUser = $toUser;
    }

    /**
     * @return int
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * @param int $createdBy
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;
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
     * @return float
     */
    public function getDebit()
    {
        return $this->debit;
    }

    /**
     * @param float $debit
     */
    public function setDebit($debit)
    {
        $this->debit = $debit;
    }

    /**
     * @return float
     */
    public function getCredit()
    {
        return $this->credit;
    }

    /**
     * @param float $credit
     */
    public function setCredit($credit)
    {
        $this->credit = $credit;
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
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content = $content;
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




}

