<?php

namespace Appstore\Bundle\AccountingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * AccountSales
 *
 * @ORM\Table(name="account_sales_archive")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\AccountingBundle\Repository\AccountSalesRepository")
 */
class AccountSalesArchive
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
     * @ORM\Column(name="accountHead", type="integer",nullable=true)
     */
    private  $accountHead;


    /**
     * @var integer
     *
     * @ORM\Column(name="customer", type="integer",nullable=true)
     */
    private  $customer;

    /**
     * @var integer
     *
     * @ORM\Column(name="invoice", type="integer",nullable=true)
     */
    private  $invoice;


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
     * @ORM\Column(name="transactionMethod", type="integer",nullable=true)
     */
    private  $transactionMethod;

    /**
     * @var integer
     *
     * @ORM\Column(name="accountCash", type="integer",nullable=true)
     */
    private  $accountCash;

    /**
     * @var integer
     *
     * @ORM\Column(name="createdBy", type="integer",nullable=true)
     */
    private  $createdBy;


    /**
     * @var integer
     *
     * @ORM\Column(name="approvedBy", type="integer",nullable=true)
     */
    private  $approvedBy;


    /**
     * @var integer
     *
     * @ORM\Column(name="toUser", type="integer",nullable=true)
     */
    private  $toUser;


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
    public function getExpenditureItems()
    {
        return $this->expenditureItems;
    }

    /**
     * @param int $expenditureItems
     */
    public function setExpenditureItems($expenditureItems)
    {
        $this->expenditureItems = $expenditureItems;
    }

    /**
     * @return int
     */
    public function getVendor()
    {
        return $this->vendor;
    }

    /**
     * @param int $vendor
     */
    public function setVendor($vendor)
    {
        $this->vendor = $vendor;
    }

    /**
     * @return int
     */
    public function getInvoice()
    {
        return $this->invoice;
    }

    /**
     * @param int $invoice
     */
    public function setInvoice($invoice)
    {
        $this->invoice = $invoice;
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
    public function getAccountCash()
    {
        return $this->accountCash;
    }

    /**
     * @param int $accountCash
     */
    public function setAccountCash($accountCash)
    {
        $this->accountCash = $accountCash;
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
     * @return float
     */
    public function getTotalAmount()
    {
        return $this->totalAmount;
    }

    /**
     * @param float $totalAmount
     */
    public function setTotalAmount($totalAmount)
    {
        $this->totalAmount = $totalAmount;
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
     * @return int
     */
    public function getApprovedBy()
    {
        return $this->approvedBy;
    }

    /**
     * @param int $approvedBy
     */
    public function setApprovedBy($approvedBy)
    {
        $this->approvedBy = $approvedBy;
    }

    /**
     * @return int
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * @param int $customer
     */
    public function setCustomer($customer)
    {
        $this->customer = $customer;
    }


}

