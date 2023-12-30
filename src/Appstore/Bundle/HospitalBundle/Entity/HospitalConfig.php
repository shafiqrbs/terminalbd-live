<?php

namespace Appstore\Bundle\HospitalBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * HospitalConfig
 *
 * @ORM\Table( name ="hms_config")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\HospitalBundle\Repository\HospitalConfigRepository")
 */
class HospitalConfig
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
     * @ORM\OneToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\GlobalOption", inversedBy="hospitalConfig" , cascade={"persist", "remove"})
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private $globalOption;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\HospitalBundle\Entity\Invoice", mappedBy="hospitalConfig")
     **/
    private $hmsInvoices;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\HospitalBundle\Entity\HmsInvoiceReturn", mappedBy="hospitalConfig")
     **/
    private $hmsInvoiceReturns;

     /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\HospitalBundle\Entity\HmsStockOut", mappedBy="hospitalConfig")
     **/
    private $stockOuts;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\HospitalBundle\Entity\HmsCommission", mappedBy="hospitalConfig")
     **/
    private $hmsCommissions;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\HospitalBundle\Entity\HmsServiceGroup", mappedBy="hospitalConfig")
     **/
    private $hmsServiceGroup;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\HospitalBundle\Entity\HmsReverse", mappedBy="hospitalConfig")
     **/
    private $hmsReverses;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\HospitalBundle\Entity\DoctorInvoice", mappedBy="hospitalConfig")
     **/
    private $doctorInvoices;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\HospitalBundle\Entity\PathologicalReport", mappedBy="hospitalConfig")
     **/
    private $pathologicalReport;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\HospitalBundle\Entity\HmsVendor", mappedBy="hospitalConfig")
     **/
    private $vendors;

     /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\HospitalBundle\Entity\HmsPurchase", mappedBy="hospitalConfig")
     **/
    private $purchases;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\HospitalBundle\Entity\Particular", mappedBy="hospitalConfig")
     **/
    private $particulars;

     /**
     * @ORM\ManyToMany(targetEntity="Appstore\Bundle\HospitalBundle\Entity\Service", inversedBy="hospitalConfig", cascade={"remove"})
     **/
    private $services;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\HospitalBundle\Entity\HmsInvoiceTemporaryParticular", mappedBy="hospitalConfig")
     **/
    private $hmsInvoiceTemporaryParticular;


    /**
     * @var string
     *
     * @ORM\Column(name="printer", type="string", length=50,nullable = true)
     */
    private $printer;

    /**
     * @var smallint
     *
     * @ORM\Column(name="vatPercentage", type="smallint",  nullable=true)
     */
    private $vatPercentage;

    /**
     * @var smallint
     *
     * @ORM\Column(name="fontSizeLabel", type="smallint",  nullable=true)
     */
    private $fontSizeLabel;

    /**
     * @var smallint
     *
     * @ORM\Column(name="fontSizeValue", type="smallint",  nullable = true)
     */
    private $fontSizeValue;

    /**
     * @var string
     *
     * @ORM\Column(name="vatRegNo", type="string",  nullable = true)
     */
    private $vatRegNo;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isBranch", type="boolean",  nullable = true)
     */
    private $isBranch = false;

     /**
     * @var boolean
     *
     * @ORM\Column(name="isInventory", type="boolean",  nullable = true)
     */
    private $isInventory = false;

     /**
     * @var boolean
     *
     * @ORM\Column(name="advanceSearchParticular", type="boolean",  nullable = true)
     */
    private $advanceSearchParticular = false;

     /**
     * @var boolean
     *
     * @ORM\Column(name="isMarketingExecutive", type="boolean",  nullable = true)
     */
    private $isMarketingExecutive = false;

     /**
     * @var boolean
     *
     * @ORM\Column(name="appointmentPrescription", type="boolean",  nullable = true)
     */
    private $appointmentPrescription = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="initialDiagnosticShow", type="boolean",  nullable = true)
     */
    private $initialDiagnosticShow = true;

    /**
     * @var boolean
     *
     * @ORM\Column(name="barcodePrint", type="boolean",  nullable = true)
     */
    private $barcodePrint = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="customPrint", type="boolean",  nullable = true)
     */
    private $customPrint = false;


    /**
     * @var boolean
     *
     * @ORM\Column(name="printOff", type="boolean",  nullable = true)
     */
    private $printOff = false;


    /**
     * @var boolean
     *
     * @ORM\Column(name="commissionAutoApproved", type="boolean",  nullable = true)
     */
    private $commissionAutoApproved = false;


    /**
     * @var boolean
     *
     * @ORM\Column(name="isBranchInvoice", type="boolean",  nullable=true)
     */
    private $isBranchInvoice = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="vatEnable", type="boolean",  nullable=true)
     */
    private $vatEnable = false;

    /**
     * @var smallint
     *
     * @ORM\Column(name="printMarginTop", type="smallint",  nullable=true)
     */
    private $printTopMargin = 0;


    /**
     * @var smallint
     *
     * @ORM\Column(name="printMarginBottom", type="smallint",  nullable=true)
     */
    private $printMarginBottom = 0;

    /**
     * @var smallint
     *
     * @ORM\Column(name="printMarginReportTop", type="smallint",  nullable=true)
     */
    private $printMarginReportTop = 0;

    /**
     * @var smallint
     *
     * @ORM\Column(name="printMarginReportLeft", type="smallint",  nullable=true)
     */
    private $printMarginReportLeft = 0;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isPrintHeader", type="boolean",  nullable=true)
     */
    private $isPrintHeader = true;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isInvoiceTitle", type="boolean",  nullable=true)
     */
    private $isInvoiceTitle = true;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isPrintFooter", type="boolean",  nullable=true)
     */
    private $isPrintFooter = true;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isPrintReportHeader", type="boolean",  nullable=true)
     */
    private $isPrintReportHeader = true;


    /**
     * @var string
     *
     * @ORM\Column(name="invoicePrefix", type="string", length=10,nullable = true)
     */
    private $invoicePrefix;

    /**
     * @var array
     *
     * @ORM\Column(name="invoiceProcess", type="array", nullable = true)
     */
    private $invoiceProcess;

    /**
     * @var string
     *
     * @ORM\Column(name="customerPrefix", type="string", length=10,nullable = true)
     */
    private $customerPrefix;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="text",nullable = true)
     */
    private $address;


    /**
     * @var string
     *
     * @ORM\Column(name="messageDiagnostic", type="text",nullable = true)
     */
    private $messageDiagnostic;


     /**
     * @var string
     *
     * @ORM\Column(name="messageAdmission", type="text",nullable = true)
     */
    private $messageAdmission;


     /**
     * @var string
     *
     * @ORM\Column(name="messageVisit", type="text",nullable = true)
     */
    private $messageVisit;


    /**
     * @var smallint
     *
     * @ORM\Column(name="printLeftMargin", type="smallint", nullable = true)
     */
    private $printLeftMargin = 0;


    /**
     * @var integer
     *
     * @ORM\Column(name="invoiceHeight", type="integer", nullable = true)
     */
    private $invoiceHeight = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="reportHeight", type="integer", nullable = true)
     */
    private $reportHeight = 0;



    /**
     * @var boolean
     *
     * @ORM\Column(name="prescriptionBuilder", type="boolean",  nullable=true)
     */
    private $prescriptionBuilder = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="invoicePrintLogo", type="boolean",  nullable=true)
     */
    private $invoicePrintLogo = true;

    /**
     * @var boolean
     *
     * @ORM\Column(name="printInstruction", type="boolean",  nullable=true)
     */
    private $printInstruction = true;
    

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $headerPath;


    /**
     * @Assert\File(maxSize="8388608")
     */
    protected $headerFile;
    

   /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $footerPath;


    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $cssContent;


    /**
     * @Assert\File(maxSize="8388608")
     */
    protected $footerFile;

    

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
     * @return Particular
     */
    public function getParticulars()
    {
        return $this->particulars;
    }

    /**
     * @return ReferredDoctor
     */
    public function getReferredDoctors()
    {
        return $this->referredDoctors;
    }


    /**
     * @return smallint
     */
    public function getPrintLeftMargin()
    {
        return $this->printLeftMargin;
    }

    /**
     * @param smallint $printLeftMargin
     */
    public function setPrintLeftMargin($printLeftMargin)
    {
        $this->printLeftMargin = $printLeftMargin;
    }


    /**
     * @return bool
     */
    public function isIsPrintFooter()
    {
        return $this->isPrintFooter;
    }

    /**
     * @param bool $isPrintFooter
     */
    public function setIsPrintFooter($isPrintFooter)
    {
        $this->isPrintFooter = $isPrintFooter;
    }

    /**
     * @return bool
     */
    public function isIsPrintHeader()
    {
        return $this->isPrintHeader;
    }

    /**
     * @param bool $isPrintHeader
     */
    public function setIsPrintHeader($isPrintHeader)
    {
        $this->isPrintHeader = $isPrintHeader;
    }

    /**
     * @return smallint
     */
    public function getPrintMarginBottom()
    {
        return $this->printMarginBottom;
    }

    /**
     * @param smallint $printMarginBottom
     */
    public function setPrintMarginBottom($printMarginBottom)
    {
        $this->printMarginBottom = $printMarginBottom;
    }

    /**
     * @return string
     */
    public function getPrinter()
    {
        return $this->printer;
    }

    /**
     * @param string $printer
     */
    public function setPrinter($printer)
    {
        $this->printer = $printer;
    }

    /**
     * @return smallint
     */
    public function getVatPercentage()
    {
        return $this->vatPercentage;
    }

    /**
     * @param smallint $vatPercentage
     */
    public function setVatPercentage($vatPercentage)
    {
        $this->vatPercentage = $vatPercentage;
    }

    /**
     * @return string
     */
    public function getVatRegNo()
    {
        return $this->vatRegNo;
    }

    /**
     * @param string $vatRegNo
     */
    public function setVatRegNo($vatRegNo)
    {
        $this->vatRegNo = $vatRegNo;
    }

    /**
     * @return bool
     */
    public function isIsBranch()
    {
        return $this->isBranch;
    }

    /**
     * @param bool $isBranch
     */
    public function setIsBranch($isBranch)
    {
        $this->isBranch = $isBranch;
    }

    /**
     * @return bool
     */
    public function isBarcodePrint()
    {
        return $this->barcodePrint;
    }

    /**
     * @param bool $barcodePrint
     */
    public function setBarcodePrint($barcodePrint)
    {
        $this->barcodePrint = $barcodePrint;
    }

    /**
     * @return bool
     */
    public function getIsBranchInvoice()
    {
        return $this->isBranchInvoice;
    }

    /**
     * @param bool $isBranchInvoice
     */
    public function setIsBranchInvoice($isBranchInvoice)
    {
        $this->isBranchInvoice = $isBranchInvoice;
    }

    /**
     * @return bool
     */
    public function getVatEnable()
    {
        return $this->vatEnable;
    }

    /**
     * @param bool $vatEnable
     */
    public function setVatEnable($vatEnable)
    {
        $this->vatEnable = $vatEnable;
    }

    /**
     * @return smallint
     */
    public function getPrintTopMargin()
    {
        return $this->printTopMargin;
    }

    /**
     * @param smallint $printTopMargin
     */
    public function setPrintTopMargin($printTopMargin)
    {
        $this->printTopMargin = $printTopMargin;
    }

    /**
     * @return HmsPurchase
     */
    public function getPurchases()
    {
        return $this->purchases;
    }

    /**
     * @return HmsVendor
     */
    public function getVendors()
    {
        return $this->vendors;
    }

    /**
     * @return PathologicalReport
     */
    public function getPathologicalReport()
    {
        return $this->pathologicalReport;
    }

    /**
     * @return DoctorInvoice
     */
    public function getDoctorInvoices()
    {
        return $this->doctorInvoices;
    }

    /**
     * @return Invoice
     */
    public function getHmsInvoices()
    {
        return $this->hmsInvoices;
    }

    /**
     * @return HmsReverse
     */
    public function getHmsReverses()
    {
        return $this->hmsReverses;
    }

    /**
     * @return boolean
     */
    public function isInvoicePrintLogo()
    {
        return $this->invoicePrintLogo;
    }

    /**
     * @param boolean $invoicePrintLogo
     */
    public function setInvoicePrintLogo($invoicePrintLogo)
    {
        $this->invoicePrintLogo = $invoicePrintLogo;
    }

    /**
     * @return bool
     */
    public function getPrintInstruction()
    {
        return $this->printInstruction;
    }

    /**
     * @param bool $printInstruction
     */
    public function setPrintInstruction($printInstruction)
    {
        $this->printInstruction = $printInstruction;
    }

    /**
     * @return string
     */
    public function getInvoicePrefix()
    {
        return $this->invoicePrefix;
    }

    /**
     * @param string $invoicePrefix
     */
    public function setInvoicePrefix($invoicePrefix)
    {
        $this->invoicePrefix = $invoicePrefix;
    }

    /**
     * @return string
     */
    public function getCustomerPrefix()
    {
        return $this->customerPrefix;
    }

    /**
     * @param string $customerPrefix
     */
    public function setCustomerPrefix($customerPrefix)
    {
        $this->customerPrefix = $customerPrefix;
    }

    /**
     * @return smallint
     */
    public function getPrintMarginReportTop()
    {
        return $this->printMarginReportTop;
    }

    /**
     * @param smallint $printMarginReportTop
     */
    public function setPrintMarginReportTop($printMarginReportTop)
    {
        $this->printMarginReportTop = $printMarginReportTop;
    }

    /**
     * @return bool
     */
    public function getIsInvoiceTitle()
    {
        return $this->isInvoiceTitle;
    }

    /**
     * @param bool $isInvoiceTitle
     */
    public function setIsInvoiceTitle($isInvoiceTitle)
    {
        $this->isInvoiceTitle = $isInvoiceTitle;
    }


    /**
     * @return array
     */
    public function getInvoiceProcess()
    {
        return $this->invoiceProcess;
    }

    /**
     * @param array $invoiceProcess
     */
    public function setInvoiceProcess($invoiceProcess)
    {
        $this->invoiceProcess = $invoiceProcess;
    }

    /**
     * @return HmsServiceGroup
     */
    public function getHmsServiceGroup()
    {
        return $this->hmsServiceGroup;
    }

    /**
     * @return HmsCommission
     */
    public function getHmsCommissions()
    {
        return $this->hmsCommissions;
    }

    /**
     * @return int
     */
    public function getInvoiceHeight()
    {
        return $this->invoiceHeight;
    }

    /**
     * @param int $invoiceHeight
     */
    public function setInvoiceHeight($invoiceHeight)
    {
        $this->invoiceHeight = $invoiceHeight;
    }

    /**
     * @return int
     */
    public function getReportHeight()
    {
        return $this->reportHeight;
    }

    /**
     * @param int $reportHeight
     */
    public function setReportHeight($reportHeight)
    {
        $this->reportHeight = $reportHeight;
    }

    /**
     * @return smallint
     */
    public function getPrintMarginReportLeft()
    {
        return $this->printMarginReportLeft;
    }

    /**
     * @param smallint $printMarginReportLeft
     */
    public function setPrintMarginReportLeft($printMarginReportLeft)
    {
        $this->printMarginReportLeft = $printMarginReportLeft;
    }

    /**
     * @return smallint
     */
    public function getFontSizeLabel()
    {
        return $this->fontSizeLabel;
    }

    /**
     * @param smallint $fontSizeLabel
     */
    public function setFontSizeLabel($fontSizeLabel)
    {
        $this->fontSizeLabel = $fontSizeLabel;
    }

    /**
     * @return smallint
     */
    public function getFontSizeValue()
    {
        return $this->fontSizeValue;
    }

    /**
     * @param smallint $fontSizeValue
     */
    public function setFontSizeValue($fontSizeValue)
    {
        $this->fontSizeValue = $fontSizeValue;
    }

    /**
     * @return bool
     */
    public function getInitialDiagnosticShow()
    {
        return $this->initialDiagnosticShow;
    }

    /**
     * @param bool $initialDiagnosticShow
     */
    public function setInitialDiagnosticShow($initialDiagnosticShow)
    {
        $this->initialDiagnosticShow = $initialDiagnosticShow;
    }

    /**
     * @return HmsInvoiceTemporaryParticular
     */
    public function getHmsInvoiceTemporaryParticular()
    {
        return $this->hmsInvoiceTemporaryParticular;
    }

	/**
	 * @return HmsStockOut
	 */
	public function getStockOuts() {
		return $this->stockOuts;
	}

    /**
     * @return bool
     */
    public function isCustomPrint()
    {
        return $this->customPrint;
    }

    /**
     * @param bool $customPrint
     */
    public function setCustomPrint(bool $customPrint)
    {
        $this->customPrint = $customPrint;
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
    public function setAddress(string $address)
    {
        $this->address = $address;
    }

    /**
     * @return bool
     */
    public function isPrintReportHeader()
    {
        return $this->isPrintReportHeader;
    }

    /**
     * @param bool $isPrintReportHeader
     */
    public function setIsPrintReportHeader(bool $isPrintReportHeader)
    {
        $this->isPrintReportHeader = $isPrintReportHeader;
    }



    /**
     * @param smallint $printReportTopMargin
     */
    public function setPrintReportTopMargin($printReportTopMargin)
    {
        $this->printReportTopMargin = $printReportTopMargin;
    }

    /**
     * @return HmsInvoiceReturn
     */
    public function getHmsInvoiceReturns()
    {
        return $this->hmsInvoiceReturns;
    }

    /**
     * @return bool
     */
    public function isCommissionAutoApproved()
    {
        return $this->commissionAutoApproved;
    }

    /**
     * @param bool $commissionAutoApproved
     */
    public function setCommissionAutoApproved($commissionAutoApproved)
    {
        $this->commissionAutoApproved = $commissionAutoApproved;
    }

    /**
     * @return string
     */
    public function getMessageDiagnostic()
    {
        return $this->messageDiagnostic;
    }

    /**
     * @param string $messageDiagnostic
     */
    public function setMessageDiagnostic($messageDiagnostic)
    {
        $this->messageDiagnostic = $messageDiagnostic;
    }

    /**
     * @return string
     */
    public function getMessageAdmission()
    {
        return $this->messageAdmission;
    }

    /**
     * @param string $messageAdmission
     */
    public function setMessageAdmission($messageAdmission)
    {
        $this->messageAdmission = $messageAdmission;
    }

    /**
     * @return string
     */
    public function getMessageVisit()
    {
        return $this->messageVisit;
    }

    /**
     * @param string $messageVisit
     */
    public function setMessageVisit($messageVisit)
    {
        $this->messageVisit = $messageVisit;
    }

    /**
     * @return bool
     */
    public function isInventory()
    {
        return $this->isInventory;
    }

    /**
     * @param bool $isInventory
     */
    public function setIsInventory($isInventory)
    {
        $this->isInventory = $isInventory;
    }


    protected function getUploadRootDir()
    {
        return __DIR__.'/../../../../../web/'.$this->getUploadDir();
    }

    protected function getUploadDir()
    {
        return 'uploads/domain/'.$this->getGlobalOption()->getId().'/hms/';
    }

    /**
     * @return mixed
     */
    public function getHeaderPath()
    {
        return $this->headerPath;
    }

    /**
     * @param mixed $HeaderPath
     */
    public function setHeaderPath($headerPath)
    {
        $this->headerPath = $headerPath;
    }

    /**
     * @return mixed
     */
    public function getHeaderFile()
    {
        return $this->headerFile;
    }

    /**
     * @param mixed $headerFile
     */
    public function setHeaderFile(UploadedFile $headerFile = null)
    {
        $this->headerFile = $headerFile;
    }



    public function getAbsoluteHeaderPath()
    {
        return null === $this->headerPath
            ? null
            : $this->getUploadRootDir().'/'.$this->headerPath;
    }

    public function getWebHeaderPath()
    {
        return null === $this->headerPath
            ? null
            : $this->getUploadDir().'/'.$this->headerPath;
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeHeaderUpload()
    {
        if ($file = $this->getAbsoluteHeaderPath()) {
            unlink($file);
        }
    }

    public function headerUpload()
    {

        // the file property can be empty if the field is not required
        if (null === $this->getHeaderFile()) {
            return;
        }

        // use the original file name here but you should
        // sanitize it at least to avoid any security issues

        // move takes the target directory and then the
        // target filename to move to
        $filename = date('YmdHmi') . "_" . $this->getHeaderFile()->getClientOriginalName();

        $this->getHeaderFile()->move(
            $this->getUploadRootDir(),
            $filename
        );

        // set the path property to the filename where you've saved the file
        $this->headerPath = $filename;
        // clean up the file property as you won't need it anymore
        $this->headerFile = null;
    }

    /**
     * @return mixed
     */
    public function getFooterPath()
    {
        return $this->footerPath;
    }

    /**
     * @param mixed $footerPath
     */
    public function setFooterPath($footerPath)
    {
        $this->footerPath = $footerPath;
    }

    /**
     * @return mixed
     */
    public function getFooterFile()
    {
        return $this->footerFile;
    }

    /**
     * @param mixed $footerFile
     */
    public function setFooterFile(UploadedFile $footerFile= null)
    {
        $this->footerFile = $footerFile;
    }



    public function getAbsoluteFooterPath()
    {
        return null === $this->footerPath
            ? null
            : $this->getUploadRootDir().'/'.$this->footerPath;
    }

    public function getWebFooterPath()
    {
        return null === $this->footerPath
            ? null
            : $this->getUploadDir().'/'.$this->footerPath;
    }


    /**
     * @ORM\PostRemove()
     */
    public function removeFooterUpload()
    {
        if ($file = $this->getAbsoluteFooterPath()) {
            unlink($file);
        }
    }

    public function footerUpload()
    {
        // the file property can be empty if the field is not required
        if (null === $this->getFooterFile()) {
            return;
        }

        // use the original file name here but you should
        // sanitize it at least to avoid any security issues

        // move takes the target directory and then the
        // target filename to move to
        $filename = date('YmdHmi') . "_" . $this->getFooterFile()->getClientOriginalName();
        $this->getFooterFile()->move(
            $this->getUploadRootDir(),
            $filename
        );

        // set the path property to the filename where you've saved the file
        $this->footerPath = $filename;

        // clean up the file property as you won't need it anymore
        $this->footerFile = null;
    }

    /**
     * @return bool
     */
    public function isAppointmentPrescription()
    {
        return $this->appointmentPrescription;
    }

    /**
     * @param bool $appointmentPrescription
     */
    public function setAppointmentPrescription($appointmentPrescription)
    {
        $this->appointmentPrescription = $appointmentPrescription;
    }



    /**
     * @return bool
     */
    public function isPrescriptionBuilder()
    {
        return $this->prescriptionBuilder;
    }

    /**
     * @param bool $prescriptionBuilder
     */
    public function setPrescriptionBuilder($prescriptionBuilder)
    {
        $this->prescriptionBuilder = $prescriptionBuilder;
    }

    /**
     * @return mixed
     */
    public function getCssContent()
    {
        return $this->cssContent;
    }

    /**
     * @param mixed $cssContent
     */
    public function setCssContent($cssContent)
    {
        $this->cssContent = $cssContent;
    }

    /**
     * @return bool
     */
    public function isPrintOff()
    {
        return $this->printOff;
    }

    /**
     * @param bool $printOff
     */
    public function setPrintOff($printOff)
    {
        $this->printOff = $printOff;
    }

    /**
     * @return mixed
     */
    public function getServices()
    {
        return $this->services;
    }

    /**
     * @param mixed $services
     */
    public function setServices($services)
    {
        $this->services = $services;
    }

    /**
     * @return bool
     */
    public function isAdvanceSearchParticular()
    {
        return $this->advanceSearchParticular;
    }

    /**
     * @param bool $advanceSearchParticular
     */
    public function setAdvanceSearchParticular($advanceSearchParticular)
    {
        $this->advanceSearchParticular = $advanceSearchParticular;
    }

    /**
     * @return bool
     */
    public function isMarketingExecutive()
    {
        return $this->isMarketingExecutive;
    }

    /**
     * @param bool $isMarketingExecutive
     */
    public function setIsMarketingExecutive($isMarketingExecutive)
    {
        $this->isMarketingExecutive = $isMarketingExecutive;
    }


}

