<?php

namespace Appstore\Bundle\AccountingBundle\Entity;

use Appstore\Bundle\BusinessBundle\Entity\BusinessVendor;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * AccountPurchase
 *
 * @ORM\Table(name="account_purchase_archive")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\AccountingBundle\Repository\AccountPurchaseRepository")
 */
    class AccountPurchaseArchive
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
         * @ORM\Column(name="vendor", type="integer",nullable=true)
         */
        private  $vendor;

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
         * @ORM\Column(name="toUser", type="integer",nullable=true)
         */
        private  $toUser;


        /**
         * @var float
         *
         * @ORM\Column(name="purchaseAmount", type="float", nullable=true)
         */
        private $purchaseAmount = 0;

        /**
         * @var float
         *
         * @ORM\Column(name="totalAmount", type="float", nullable=true)
         */
        private $totalAmount = 0;

        /**
         * @var float
         *
         * @ORM\Column(name="payment", type="float", nullable=true)
         */
        private $payment = 0;

         /**
         * @var float
         *
         * @ORM\Column(name="commission", type="float", nullable=true)
         */
        private $commission = 0;

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
         * @var string
         *
         * @ORM\Column(name="voucherType", type="string", length = 60, nullable=true)
         */
        private $voucherType = "bill";

        /**
         * @var string
         *
         * @ORM\Column(name="companyName", type="string", length = 256, nullable=true)
         */
        private $companyName;

        /**
         * @var string
         *
         * @ORM\Column(name="grn", type="string", length = 50, nullable=true)
         */
        private $grn;

        /**
         * @var string
         *
         * @ORM\Column(name="memo", type="string", length = 50, nullable=true)
         */
        private $memo;


        /**
         * @var string
         *
         * @ORM\Column(name="sourceInvoice", type="string", length=50, nullable=true)
         */
        private $sourceInvoice;

        /**
         * @var integer
         *
         * @ORM\Column(name="code", type="integer",  nullable=true)
         */
        private $code;

        /**
         * @var string
         *
         * @ORM\Column(name="remark", type="text", nullable = true)
         */
        private $remark;

        /**
         * @var \DateTime
         * @Gedmo\Timestampable(on="create")
         * @ORM\Column(name="receiveDate", type="datetime")
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
         * @ORM\Column(name="process", type="string", length=50, nullable = true)
         */
        private $process;

        /**
         * @var string
         *
         * @ORM\Column(name="processHead", type="string", length=100, nullable = true)
         */
        private $processHead;

        /**
         * @var string
         *
         * @ORM\Column(name="processType", type="string", length=50, nullable = true)
         */
        private $processType;

        /**
         * @var integer
         *
         * @ORM\Column(name="approvedBy", type="integer",  nullable=true)
         */
        private $approvedBy;



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
         * @return float
         */
        public function getPurchaseAmount()
        {
            return $this->purchaseAmount;
        }

        /**
         * @param float $purchaseAmount
         */
        public function setPurchaseAmount($purchaseAmount)
        {
            $this->purchaseAmount = $purchaseAmount;
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
        public function getCommission()
        {
            return $this->commission;
        }

        /**
         * @param float $commission
         */
        public function setCommission($commission)
        {
            $this->commission = $commission;
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
         * @return string
         */
        public function getVoucherType()
        {
            return $this->voucherType;
        }

        /**
         * @param string $voucherType
         */
        public function setVoucherType($voucherType)
        {
            $this->voucherType = $voucherType;
        }

        /**
         * @return string
         */
        public function getCompanyName()
        {
            return $this->companyName;
        }

        /**
         * @param string $companyName
         */
        public function setCompanyName($companyName)
        {
            $this->companyName = $companyName;
        }

        /**
         * @return string
         */
        public function getGrn()
        {
            return $this->grn;
        }

        /**
         * @param string $grn
         */
        public function setGrn($grn)
        {
            $this->grn = $grn;
        }

        /**
         * @return string
         */
        public function getMemo()
        {
            return $this->memo;
        }

        /**
         * @param string $memo
         */
        public function setMemo($memo)
        {
            $this->memo = $memo;
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
         * @return \DateTime
         */
        public function getReceiveDate()
        {
            return $this->receiveDate;
        }

        /**
         * @param \DateTime $receiveDate
         */
        public function setReceiveDate($receiveDate)
        {
            $this->receiveDate = $receiveDate;
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




    }

