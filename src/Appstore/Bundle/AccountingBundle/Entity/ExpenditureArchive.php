<?php

namespace Appstore\Bundle\AccountingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Expenditure
 *
 * @ORM\Table(name="account_expenditure_archive")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\AccountingBundle\Repository\ExpenditureRepository")
 */
class ExpenditureArchive
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
     * @ORM\Column(name="expenseCategory", type="integer",nullable=true)
     */
    private  $expenseCategory;

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
     * @ORM\Column(name="doctorInvoice", type="integer",nullable=true)
     */
    private  $doctorInvoice;

    /**
     * @var integer
     *
     * @ORM\Column(name="deviceSalesId", type="integer",nullable=true)
     */
    private $deviceSalesId;

    /**
     * @var float
     *
     * @ORM\Column(name="amount", type="float")
     */
    private $amount;

    /**
     * @var float
     *
     * @ORM\Column(name="balance", type="float", nullable=true)
     */
    private $balance = 0;


    /**
     * @var string
     *
     * @ORM\Column(name="accountRefNo", type="string", length=50, nullable=true)
     */
    private $accountRefNo;

    /**
     * @var integer
     *
     * @ORM\Column(name="code", type="integer",  nullable=true)
     */
    private $code;


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
     * @ORM\Column(name="remark", type="string", length=255, nullable = true)
     */
    private $remark;

    /**
     * @var string
     *
     * @ORM\Column(name="process", type="string", length=255, nullable = true)
     */
    private $process;



    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="generatedDate", type="datetime", nullable=true)
     */
    private $generatedDate;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updated", type="datetime")
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
     * @return mixed
     */
    public function getExpenseCategory()
    {
        return $this->expenseCategory;
    }

    /**
     * @param mixed $expenseCategory
     */
    public function setExpenseCategory($expenseCategory)
    {
        $this->expenseCategory = $expenseCategory;
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
    public function getDoctorInvoice()
    {
        return $this->doctorInvoice;
    }

    /**
     * @param int $doctorInvoice
     */
    public function setDoctorInvoice($doctorInvoice)
    {
        $this->doctorInvoice = $doctorInvoice;
    }

    /**
     * @return int
     */
    public function getDeviceSalesId()
    {
        return $this->deviceSalesId;
    }

    /**
     * @param int $deviceSalesId
     */
    public function setDeviceSalesId($deviceSalesId)
    {
        $this->deviceSalesId = $deviceSalesId;
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
    public function getGeneratedDate()
    {
        return $this->generatedDate;
    }

    /**
     * @param \DateTime $generatedDate
     */
    public function setGeneratedDate($generatedDate)
    {
        $this->generatedDate = $generatedDate;
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

