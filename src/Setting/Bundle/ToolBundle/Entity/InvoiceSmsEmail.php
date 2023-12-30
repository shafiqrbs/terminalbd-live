<?php

namespace Setting\Bundle\ToolBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * InvoiceSmsEmail
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Setting\Bundle\ToolBundle\Repository\InvoiceSmsEmailRepository")
 */
class InvoiceSmsEmail
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
     * @ORM\OneToMany(targetEntity="Setting\Bundle\ToolBundle\Entity\InvoiceSmsEmailItem", mappedBy="invoiceSmsEmail")
     **/
    protected $invoiceSmsEmailItems = null;

     /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\GlobalOption", inversedBy="invoiceSmsEmails")
     **/
    protected $globalOption = null;

    /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\PortalBankAccount", inversedBy="invoiceSmsEmails" )
     **/
    private  $portalBankAccount;


    /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\PortalMobileBankAccount", inversedBy="invoiceSmsEmails" )
     **/
    private  $portalMobileBankAccount;

    /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\TransactionMethod", inversedBy="invoiceSmsEmails" )
     **/
    private  $transactionMethod;


    /**
     * @Gedmo\Blameable(on="create")
     * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User", inversedBy="invoiceSmsEmail" )
     **/
    private  $createdBy;

    /**
     * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User", inversedBy="invoiceSmsEmailReceivedBy" )
     **/
    private  $receivedBy;

    /**
     * @var string
     *
     * @ORM\Column(name="paymentMobile", type="string", length=50, nullable=true)
     */
    private $paymentMobile;

    /**
     * @var string
     *
     * @ORM\Column(name="accountNo", type="string", length=100, nullable=true)
     */
    private $accountNo;

    /**
     * @var string
     *
     * @ORM\Column(name="transaction", type="string", length=100, nullable=true)
     */
    private $transaction;

    /**
     * @var integer
     *
     * @ORM\Column(name="code", type="integer", length=255, nullable = true)
     */
    private $code;

     /**
     * @var string
     *
     * @ORM\Column(name="invoice", type="string", length=255, nullable = true)
     */
    private $invoice;

    /**
     * @var string
     *
     * @ORM\Column(name="paymentMethod", type="string", length=255, nullable = true)
     */
    private $paymentMethod;

    /**
     * @var float
     *
     * @ORM\Column(name="totalAmount", type="float", nullable = true)
     */
    private $totalAmount;

    /**
     * @var float
     *
     * @ORM\Column(name="paidAmount", type="float", nullable = true)
     */
    private $paidAmount;

    /**
     * @var integer
     *
     * @ORM\Column(name="smsQuantity", type="integer", nullable = true)
     */
    private $smsQuantity;

    /**
     * @var float
     *
     * @ORM\Column(name="dueAmount", type="float", nullable = true)
     */
    private $dueAmount;

    /**
     * @var string
     *
     * @ORM\Column(name="process", type="string", length=255, nullable = true)
     */
    private $process = 'Pending';

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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set invoice
     *
     * @param string $invoice
     *
     * @return InvoiceSmsEmail
     */
    public function setInvoice($invoice)
    {
        $this->invoice = $invoice;

        return $this;
    }

    /**
     * Get invoice
     *
     * @return string
     */
    public function getInvoice()
    {
        return $this->invoice;
    }

    /**
     * Set paymentMethod
     *
     * @param string $paymentMethod
     *
     * @return InvoiceSmsEmail
     */
    public function setPaymentMethod($paymentMethod)
    {
        $this->paymentMethod = $paymentMethod;

        return $this;
    }

    /**
     * Get paymentMethod
     *
     * @return string
     */
    public function getPaymentMethod()
    {
        return $this->paymentMethod;
    }

    /**
     * Set totalAmount
     *
     * @param float $totalAmount
     *
     * @return InvoiceSmsEmail
     */
    public function setTotalAmount($totalAmount)
    {
        $this->totalAmount = $totalAmount;

        return $this;
    }

    /**
     * Get totalAmount
     *
     * @return float
     */
    public function getTotalAmount()
    {
        return $this->totalAmount;
    }

    /**
     * Set paidAmount
     *
     * @param float $paidAmount
     *
     * @return InvoiceSmsEmail
     */
    public function setPaidAmount($paidAmount)
    {
        $this->paidAmount = $paidAmount;

        return $this;
    }

    /**
     * Get paidAmount
     *
     * @return float
     */
    public function getPaidAmount()
    {
        return $this->paidAmount;
    }

    /**
     * Set dueAmount
     *
     * @param float $dueAmount
     *
     * @return InvoiceSmsEmail
     */
    public function setDueAmount($dueAmount)
    {
        $this->dueAmount = $dueAmount;

        return $this;
    }

    /**
     * Get dueAmount
     *
     * @return float
     */
    public function getDueAmount()
    {
        return $this->dueAmount;
    }

    /**
     * Set process
     *
     * @param string $process
     *
     * @return InvoiceSmsEmail
     */
    public function setProcess($process)
    {
        $this->process = $process;

        return $this;
    }

    /**
     * Get process
     *
     * @return string
     */
    public function getProcess()
    {
        return $this->process;
    }

    /**
     * @return mixed
     */
    public function getGlobalOption()
    {
        return $this->globalOption;
    }

    /**
     * @param mixed $globalOption
     */
    public function setGlobalOption($globalOption)
    {
        $this->globalOption = $globalOption;
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
     * @return PortalBankAccount
     */
    public function getPortalBankAccount()
    {
        return $this->portalBankAccount;
    }

    /**
     * @param PortalBankAccount $portalBankAccount
     */
    public function setPortalBankAccount($portalBankAccount)
    {
        $this->portalBankAccount = $portalBankAccount;
    }

    /**
     * @return mixed
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * @param CreatedBy $createdBy
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;
    }

    /**
     * @return ReceivedBy
     */
    public function getReceivedBy()
    {
        return $this->receivedBy;
    }

    /**
     * @param mixed $receivedBy
     */
    public function setReceivedBy($receivedBy)
    {
        $this->receivedBy = $receivedBy;
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
     * @return mixed
     */
    public function getInvoiceSmsEmailItems()
    {
        return $this->invoiceSmsEmailItems;
    }

    /**
     * @return PortalMobileBankAccount
     */
    public function getPortalMobileBankAccount()
    {
        return $this->portalMobileBankAccount;
    }

    /**
     * @param PortalMobileBankAccount $portalMobileBankAccount
     */
    public function setPortalMobileBankAccount($portalMobileBankAccount)
    {
        $this->portalMobileBankAccount = $portalMobileBankAccount;
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
     * @return string
     */
    public function getPaymentMobile()
    {
        return $this->paymentMobile;
    }

    /**
     * @param string $paymentMobile
     */
    public function setPaymentMobile($paymentMobile)
    {
        $this->paymentMobile = $paymentMobile;
    }

    /**
     * @return string
     */
    public function getAccountNo()
    {
        return $this->accountNo;
    }

    /**
     * @param string $accountNo
     */
    public function setAccountNo($accountNo)
    {
        $this->accountNo = $accountNo;
    }

    /**
     * @return string
     */
    public function getTransaction()
    {
        return $this->transaction;
    }

    /**
     * @param string $transaction
     */
    public function setTransaction($transaction)
    {
        $this->transaction = $transaction;
    }

    /**
     * @return int
     */
    public function getSmsQuantity()
    {
        return $this->smsQuantity;
    }

    /**
     * @param int $smsQuantity
     */
    public function setSmsQuantity($smsQuantity)
    {
        $this->smsQuantity = $smsQuantity;
    }


}

