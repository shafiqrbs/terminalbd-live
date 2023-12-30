<?php

namespace Setting\Bundle\ToolBundle\Entity;

use Appstore\Bundle\AccountingBundle\Entity\AccountBank;
use Appstore\Bundle\AccountingBundle\Entity\AccountCash;
use Appstore\Bundle\AccountingBundle\Entity\AccountMobileBank;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Invoice
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Setting\Bundle\ToolBundle\Repository\InvoiceModuleRepository")
 */
class InvoiceModule
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
     * @ORM\OneToMany(targetEntity="Setting\Bundle\ToolBundle\Entity\InvoiceModuleItem", mappedBy="invoiceModule",cascade={"remove"})
     **/
    protected $invoiceModuleItems = null;

     /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\GlobalOption", inversedBy="invoiceModules")
     **/
    protected $globalOption = null;


    /**
     * @Gedmo\Blameable(on="create")
     * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User", inversedBy="invoiceModule" )
     **/
    private  $createdBy;

    /**
     * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User", inversedBy="invoiceModulePaymentBy" )
     **/
    private  $paymentBy;

    /**
     * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User", inversedBy="invoiceModuleReceivedBy" )
     **/
    private  $receivedBy;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountBank", inversedBy="invoiceModules" )
     **/
    private  $accountBank;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountMobileBank", inversedBy="invoiceModules" )
     **/
    private  $accountMobileBank;

    /**
     * @ORM\OneToOne(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountCash", mappedBy="invoiceModule" )
     **/
    private  $accountCash;

    /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\TransactionMethod", inversedBy="invoiceModules" )
     **/
    private  $transactionMethod;



    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable = true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="billFor", type="string", length=255, nullable = true)
     */
    private $billFor;

    /**
     * @var string
     *
     * @ORM\Column(name="mobile", type="string", length=255, nullable = true)
     */
    private $mobile;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=255, nullable = true)
     */
    private $address;

    /**
     * @var string
     *
     * @ORM\Column(name="remark", type="string", length=255, nullable = true)
     */
    private $remark;


    /**
     * @var integer
     *
     * @ORM\Column(name="code", type="integer", length=10, nullable = true)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="billMonth", type="string",  nullable = true)
     */
    private $billMonth;

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
     * @var float
     *
     * @ORM\Column(name="dueAmount", type="float", nullable = true)
     */
    private $dueAmount;

    /**
     * @var string
     *
     * @ORM\Column(name="process", type="string",  nullable = true)
     */
    private $process = 'Pending';

    /**
     * @var boolean
     *
     * @ORM\Column(name="customInvoice", type="boolean")
     */
    private $customInvoice = false;



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
     * @return InvoiceModule
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
     * @return InvoiceModule
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
     * @return InvoiceModule
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
     * @return InvoiceModule
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
     * @return InvoiceModule
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
     * @return InvoiceModule
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
     * @param GlobalOption $globalOption
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
     * @return mixed
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
    public function getInvoiceModuleItems()
    {
        return $this->invoiceModuleItems;
    }

    /**
     * @return mixed
     */
    public function getPaymentBy()
    {
        return $this->paymentBy;
    }

    /**
     * @param mixed $paymentBy
     */
    public function setPaymentBy($paymentBy)
    {
        $this->paymentBy = $paymentBy;
    }

    /**
     * @return string
     */
    public function getBillMonth()
    {
        return $this->billMonth;
    }

    /**
     * @param string $billMonth
     */
    public function setBillMonth($billMonth)
    {
        $this->billMonth = $billMonth;
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
    public function getMobile()
    {
        return $this->mobile;
    }

    /**
     * @param string $mobile
     */
    public function setMobile($mobile)
    {
        $this->mobile = $mobile;
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
     * @return string
     */
    public function getBillFor()
    {
        return $this->billFor;
    }

    /**
     * @param string $billFor
     */
    public function setBillFor($billFor)
    {
        $this->billFor = $billFor;
    }

    /**
     * @return bool
     */
    public function isCustomInvoice()
    {
        return $this->customInvoice;
    }

    /**
     * @param bool $customInvoice
     */
    public function setCustomInvoice($customInvoice)
    {
        $this->customInvoice = $customInvoice;
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
}

