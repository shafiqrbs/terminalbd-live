<?php

namespace Appstore\Bundle\DoctorPrescriptionBundle\Entity;

use Appstore\Bundle\AccountingBundle\Entity\AccountBank;
use Appstore\Bundle\AccountingBundle\Entity\AccountMobileBank;
use Appstore\Bundle\AccountingBundle\Entity\AccountSales;
use Appstore\Bundle\DomainUserBundle\Entity\Customer;
use Appstore\Bundle\MedicineBundle\Entity\DiagnosticReport;
use Appstore\Bundle\MedicineBundle\Entity\MedicineDoctorPrescribe;
use Core\UserBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Setting\Bundle\ToolBundle\Entity\Bank;
use Setting\Bundle\ToolBundle\Entity\PaymentCard;
use Setting\Bundle\ToolBundle\Entity\TransactionMethod;

/**
 * Invoice
 *
 * @ORM\Table( name ="dps_invoice")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\DoctorPrescriptionBundle\Repository\DpsInvoiceRepository")
 */
class DpsInvoice
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
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\DoctorPrescriptionBundle\Entity\DpsConfig", inversedBy="dpsInvoices")
     **/
    private $dpsConfig;


    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\HospitalBundle\Entity\Invoice", inversedBy="dpsInvoice", cascade={"persist"} )
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private $hmsInvoice;


     /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\DoctorPrescriptionBundle\Entity\DpsService", inversedBy="dpsInvoices")
     **/
    private $service;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\DoctorPrescriptionBundle\Entity\DpsInvoiceParticular", mappedBy="dpsInvoice" , cascade={"remove"} )
     **/
    private  $invoiceParticulars;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\MedicineBundle\Entity\MedicineDoctorPrescribe", mappedBy="dpsInvoice" , cascade={"remove"} )
     * @ORM\OrderBy({"updated" = "DESC"})
     **/
    private  $medicineDoctorPrescribes;


    /**
     * @ORM\ManyToMany(targetEntity="Appstore\Bundle\MedicineBundle\Entity\DiagnosticReport", inversedBy="dpsInvoice" , cascade={"remove"} )
     **/
    private  $investigations;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\DoctorPrescriptionBundle\Entity\DpsTreatmentPlan", mappedBy="dpsInvoice" , cascade={"remove"} )
     * @ORM\OrderBy({"created" = "DESC"})
     **/
    private  $dpsTreatmentPlans;


   /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountSales", mappedBy="dpsInvoice" )
     * @ORM\OrderBy({"id" = "DESC"})
     **/
    private  $accountSales;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\DomainUserBundle\Entity\Customer", inversedBy="dpsInvoices" ,cascade={"persist"} )
     **/
    private  $customer;


    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\DoctorPrescriptionBundle\Entity\DpsParticular", inversedBy="assignDoctorInvoices", cascade={"persist"}  )
     **/
    private  $assignDoctor;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\HospitalBundle\Entity\Particular")
     **/
    private  $hmsAssignDoctor;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\DoctorPrescriptionBundle\Entity\DpsParticular")
     **/
    private  $diseasesProfile;

     /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\HospitalBundle\Entity\HmsServiceGroup")
     **/
    private  $hmsDiseasesProfile;

    /**
     * @Gedmo\Blameable(on="create")
     * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User", inversedBy="dpsInvoiceCreatedBy" )
     **/
    private  $createdBy;


    /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\TransactionMethod", inversedBy="dpsInvoices" )
     **/
    private  $transactionMethod;

    /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\Bank", inversedBy="dpsInvoices" )
     **/
    private  $bank;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountBank", inversedBy="dpsInvoices" )
     **/
    private  $accountBank;


    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountMobileBank", inversedBy="dpsInvoices" )
     **/
    private  $accountMobileBank;


    /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\PaymentCard", inversedBy="dpsInvoices" )
     **/
    private  $paymentCard;

    /**
     * @var string
     *
     * @ORM\Column(name="cardNo", type="string", length=100, nullable=true)
     */
    private $cardNo;

    /**
     * @var string
     *
     * @ORM\Column(name="paymentMobile", type="string", length=50, nullable=true)
     */
    private $paymentMobile;

    /**
     * @var string
     *
     * @ORM\Column(name="paymentInWord", type="string", length=255, nullable=true)
     */
    private $paymentInWord;

    /**
     * @var string
     *
     * @ORM\Column(name="process", type="string", length=50, nullable=true)
     */
    private $process ='In-progress';

    /**
     * @var string
     *
     * @ORM\Column(name="transactionId", type="string", length=100, nullable=true)
     */
    private $transactionId;

    /**
     * @var string
     *
     * @ORM\Column(name="invoice", type="string", length=50, nullable=true)
     */
    private $invoice;

     /**
     * @var string
     *
     * @ORM\Column(name="doctorName", type="string", length=255, nullable=true)
     */
    private $doctorName;

     /**
     * @var string
     *
     * @ORM\Column(name="visitType", type="string", length=255, nullable=true)
     */
    private $visitType;

    /**
     * @var integer
     *
     * @ORM\Column(name="code", type="integer",  nullable=true)
     */
    private $code;


    /**
     * @var string
     *
     * @ORM\Column(name="paymentStatus", type="string", length=50, nullable=true)
     */
    private $paymentStatus = "Pending";

    /**
     * @var float
     *
     * @ORM\Column(name="subTotal", type="float", nullable=true)
     */
    private $subTotal;


    /**
     * @var float
     *
     * @ORM\Column(name="estimateTotal", type="float", nullable=true)
     */
    private $estimateTotal;


    /**
     * @var float
     *
     * @ORM\Column(name="discount", type="float", nullable=true)
     */
    private $discount;

    /**
     * @var integer
     *
     * @ORM\Column(name="percentage", type="smallint" , length=3 , nullable=true)
     */
    private $percentage;


    /**
     * @var float
     *
     * @ORM\Column(name="vat", type="float", nullable=true)
     */
    private $vat;

    /**
     * @var float
     *
     * @ORM\Column(name="total", type="float", nullable=true)
     */
    private $total;

    /**
     * @var float
     *
     * @ORM\Column(name="payment", type="float", nullable=true)
     */
    private $payment;

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="text", nullable=true)
     */
    private $comment;

    /**
     * @var float
     *
     * @ORM\Column(name="due", type="float", nullable=true)
     */
    private $due;

    /**
     * @var boolean
     *
     * @ORM\Column(name="sendSms", type="boolean" )
     */
    private $sendSms = false;


    /**
     * @var string
     *
     * @ORM\Column(name="mobile", type="text", nullable=true)
     */
    private $mobile;

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
     * @return DpsInvoiceParticular
     */
    public function getInvoiceParticulars()
    {
        return $this->invoiceParticulars;
    }

    /**
     * @param DpsInvoiceParticular $invoiceParticulars
     */
    public function setInvoiceParticulars($invoiceParticulars)
    {
        $this->invoiceParticulars = $invoiceParticulars;
    }


    /**
     * @return string
     */
    public function getSubTotal()
    {
        return $this->subTotal;
    }

    /**
     * @param string $subTotal
     */
    public function setSubTotal($subTotal)
    {
        $this->subTotal = $subTotal;
    }

    /**
     * @return string
     */
    public function getDiscount()
    {
        return $this->discount;
    }

    /**
     * @param string $discount
     */
    public function setDiscount($discount)
    {
        $this->discount = $discount;
    }

    /**
     * @return string
     */
    public function getVat()
    {
        return $this->vat;
    }

    /**
     * @param string $vat
     */
    public function setVat($vat)
    {
        $this->vat = $vat;
    }

    /**
     * @return string
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @param string $total
     */
    public function setTotal($total)
    {
        $this->total = $total;
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
     * @return mixed
     */
    public function getPaymentCard()
    {
        return $this->paymentCard;
    }

    /**
     * @param PaymentCard $paymentCard
     */
    public function setPaymentCard($paymentCard)
    {
        $this->paymentCard = $paymentCard;
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
    public function getPaymentStatus()
    {
        return $this->paymentStatus;
    }

    /**
     * @param string $paymentStatus
     * Paid
     * Pending
     * Partial
     * Due
     * Other
     */
    public function setPaymentStatus($paymentStatus)
    {
        $this->paymentStatus = $paymentStatus;
    }


    /**
     * @return integer
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param integer $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @return string
     */
    public function getInvoice()
    {
        return $this->invoice;
    }

    /**
     * @param string $invoice
     */
    public function setInvoice($invoice)
    {
        $this->invoice = $invoice;
    }


    /**
     * @return string
     */
    public function getPayment()
    {
        return $this->payment;
    }

    /**
     * @param string $payment
     */
    public function setPayment($payment)
    {
        $this->payment = $payment;
    }


    /**
     * @return AccountSales
     */
    public function getAccountSales()
    {
        return $this->accountSales;
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
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * @param User $createdBy
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;
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
    public function getTransactionId()
    {
        return $this->transactionId;
    }

    /**
     * @param string $transactionId
     */
    public function setTransactionId($transactionId)
    {
        $this->transactionId = $transactionId;
    }

    /**
     * @return string
     */
    public function getCardNo()
    {
        return $this->cardNo;
    }

    /**
     * @param string $cardNo
     */
    public function setCardNo($cardNo)
    {
        $this->cardNo = $cardNo;
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
    public function getPaymentInWord()
    {
        return $this->paymentInWord;
    }

    /**
     * @param string $paymentInWord
     */
    public function setPaymentInWord($paymentInWord)
    {
        $this->paymentInWord = $paymentInWord;
    }

    /**
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param string $comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    /**
     * @return string
     */
    public function getDue()
    {
        return $this->due;
    }

    /**
     * @param string $due
     */
    public function setDue($due)
    {
        $this->due = $due;
    }

    /**
     * @return DpsService
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * @param DpsService $service
     */
    public function setService($service)
    {
        $this->service = $service;
    }


    /**
     * @return int
     */
    public function getPercentage()
    {
        return $this->percentage;
    }

    /**
     * @param int $percentage
     */
    public function setPercentage($percentage)
    {
        $this->percentage = $percentage;
    }



    /**
     * @return DpsParticular
     */
    public function getAssignDoctor()
    {
        return $this->assignDoctor;
    }

    /**
     * @param DpsParticular $assignDoctor
     */
    public function setAssignDoctor($assignDoctor)
    {
        $this->assignDoctor = $assignDoctor;
    }




    /**
     * @return DpsConfig
     */
    public function getDpsConfig()
    {
        return $this->dpsConfig;
    }

    /**
     * @param DpsConfig $dpsConfig
     */
    public function setDpsConfig($dpsConfig)
    {
        $this->dpsConfig = $dpsConfig;
    }



    /**
     * @return DpsTreatmentPlan
     */
    public function getDpsTreatmentPlans()
    {
        return $this->dpsTreatmentPlans;
    }

    /**
     * @return bool
     */
    public function isSendSms()
    {
        return $this->sendSms;
    }

    /**
     * @param bool $sendSms
     */
    public function setSendSms($sendSms)
    {
        $this->sendSms = $sendSms;
    }


    /**
     * @return float
     */
    public function getEstimateTotal()
    {
        return $this->estimateTotal;
    }

    /**
     * @param float $estimateTotal
     */
    public function setEstimateTotal($estimateTotal)
    {
        $this->estimateTotal = $estimateTotal;
    }

    /**
     * @return DiagnosticReport
     */
    public function getInvestigations()
    {
        return $this->investigations;
    }

    /**
     * @param DiagnosticReport $investigations
     */
    public function setInvestigations($investigations)
    {
        $this->investigations = $investigations;
    }

    /**
     * @return MedicineDoctorPrescribe
     */
    public function getMedicineDoctorPrescribes()
    {
        return $this->medicineDoctorPrescribes;
    }

    /**
     * @return mixed
     */
    public function getHmsAssignDoctor()
    {
        return $this->hmsAssignDoctor;
    }

    /**
     * @param mixed $hmsAssignDoctor
     */
    public function setHmsAssignDoctor($hmsAssignDoctor)
    {
        $this->hmsAssignDoctor = $hmsAssignDoctor;
    }

    /**
     * @return mixed
     */
    public function getHmsDiseasesProfile()
    {
        return $this->hmsDiseasesProfile;
    }

    /**
     * @param mixed $hmsDiseasesProfile
     */
    public function setHmsDiseasesProfile($hmsDiseasesProfile)
    {
        $this->hmsDiseasesProfile = $hmsDiseasesProfile;
    }

    /**
     * @return mixed
     */
    public function getDiseasesProfile()
    {
        return $this->diseasesProfile;
    }

    /**
     * @param mixed $diseasesProfile
     */
    public function setDiseasesProfile($diseasesProfile)
    {
        $this->diseasesProfile = $diseasesProfile;
    }

    /**
     * @return mixed
     */
    public function getHmsInvoice()
    {
        return $this->hmsInvoice;
    }

    /**
     * @param mixed $hmsInvoice
     */
    public function setHmsInvoice($hmsInvoice)
    {
        $this->hmsInvoice = $hmsInvoice;
    }

    /**
     * @return string
     */
    public function getDoctorName()
    {
        return $this->doctorName;
    }

    /**
     * @param string $doctorName
     */
    public function setDoctorName($doctorName)
    {
        $this->doctorName = $doctorName;
    }

    /**
     * @return string
     */
    public function getVisitType()
    {
        return $this->visitType;
    }

    /**
     * @param string $visitType
     */
    public function setVisitType($visitType)
    {
        $this->visitType = $visitType;
    }


}

