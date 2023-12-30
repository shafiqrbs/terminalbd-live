<?php

namespace Appstore\Bundle\HospitalBundle\Entity;


use Core\UserBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Setting\Bundle\LocationBundle\Entity\Location;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Particular
 *
 * @ORM\Table( name = "hms_particular")
 * @UniqueEntity(fields={"assignOperator"},message="Doctor already existing,Please try again.")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\HospitalBundle\Repository\ParticularRepository")
 */
class Particular
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
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\HospitalBundle\Entity\HospitalConfig", inversedBy="particulars")
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private $hospitalConfig;


    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\HospitalBundle\Entity\Invoice", mappedBy="referredDoctor")
     **/
    private $hmsInvoice;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\HospitalBundle\Entity\Invoice", mappedBy="cabin")
     **/
    private $hmsInvoiceCabin;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\HospitalBundle\Entity\HmsPurchaseItem", mappedBy="particular")
     **/
    private $purchaseItems;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\HospitalBundle\Entity\HmsStockOutItem", mappedBy="particular")
     **/
    private $stockOutItems;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\HospitalBundle\Entity\Service", inversedBy="particulars" )
     * @ORM\OrderBy({"sorting" = "ASC"})
     **/
    private $service;

	/**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountHead")
     * @ORM\OrderBy({"sorting" = "ASC"})
     **/
     private $accountHead;

	/**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\HospitalBundle\Entity\HmsServiceGroup", inversedBy="particulars" )
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @ORM\OrderBy({"sorting" = "ASC"})
     **/
    private $serviceGroup;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\MedicineBundle\Entity\DiagnosticReport", inversedBy="hmsParticulars" )
     * @ORM\OrderBy({"sorting" = "ASC"})
     **/
    private $diagnosticReport;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\HospitalBundle\Entity\PathologicalReport", mappedBy="particular")
     * @ORM\OrderBy({"sorting" = "ASC"})
     **/
    private $pathologicalReports;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\HospitalBundle\Entity\HmsCategory", inversedBy="particulars")
     **/
    private $category;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\HospitalBundle\Entity\HmsCategory", inversedBy="particularDepartments")
     **/
    private $department;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\HospitalBundle\Entity\Invoice", mappedBy="assignDoctor")
     **/
    private $assignDoctorInvoices;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\HospitalBundle\Entity\invoiceParticular", mappedBy="assignDoctor")
     **/
    private $invoiceParticularDoctor;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\HospitalBundle\Entity\InvoiceParticular", mappedBy="particular" )
     * @ORM\OrderBy({"id" = "DESC"})
     **/
    private  $invoiceParticular;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\HospitalBundle\Entity\HmsInvoiceTemporaryParticular", mappedBy="particular" )
     * @ORM\OrderBy({"id" = "DESC"})
     **/
    private  $hmsInvoiceTemporaryParticular;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\HospitalBundle\Entity\AdmissionPatientParticular", mappedBy="particular" )
     * @ORM\OrderBy({"id" = "DESC"})
     **/
    private  $admissionPatientParticular;


    /**
     * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User")
     **/
    private  $assignOperator;

    /**
     * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User")
     **/
    private  $marketingExecutive;

     /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\HospitalBundle\Entity\HmsServiceGroup")
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private  $surgeryDepartment;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\HospitalBundle\Entity\HmsDoctorVisitMode", mappedBy="doctor")
     **/
    private  $visitModes;

    /**
     * @ORM\OneToOne(targetEntity="Core\UserBundle\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="assignDoctor_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     * })
     */
    private  $assignDoctor;

    /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\ProductUnit", inversedBy="particulars" )
     **/
    private  $unit;

    /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\LocationBundle\Entity\Location", inversedBy="particulars")
     **/
    protected $location;


    /**
     * @ORM\ManyToOne(targetEntity="Particular", inversedBy="children")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="parent", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     * })
     */
    private $parent;

    /**
     * @ORM\OneToMany(targetEntity="Particular" , mappedBy="parent")
     * @ORM\OrderBy({"name" = "ASC"})
     **/
    private $children;


    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;


    /**
     * @var string
     *
     * @ORM\Column(name="reportMachineName", type="string", length=255, nullable=true)
     */
    private $reportMachineName;


    /**
     * @var integer
     *
     * @ORM\Column(name="quantity", type="smallint", length=3, nullable=true)
     */
    private $quantity = 1;

    /**
     * @var integer
     *
     * @ORM\Column(name="oldReportId", type="smallint", length=5, nullable=true)
     */
    private $oldReportId;

    /**
     * @var integer
     *
     * @ORM\Column(name="openingQuantity", type="integer", nullable=true)
     */
    private $openingQuantity;

    /**
     * @var integer
     *
     * @ORM\Column(name="minQuantity", type="integer", nullable=true)
     */
    private $minQuantity;


    /**
     * @var integer
     *
     * @ORM\Column(name="purchaseQuantity", type="integer", nullable=true)
     */
    private $purchaseQuantity;

    /**
     * @var integer
     *
     * @ORM\Column(name="salesQuantity", type="integer", nullable=true)
     */
    private $salesQuantity;

    /**
     * @var string
     *
     * @ORM\Column(name="purchaseAverage", type="decimal", nullable=true)
     */
    private $purchaseAverage;

    /**
     * @var string
     *
     * @ORM\Column(name="purchasePrice", type="decimal", nullable=true)
     */
    private $purchasePrice;


     /**
     * @var string
     *
     * @ORM\Column(name="ipdVisitCharge", type="decimal", nullable=true)
     */
    private $ipdVisitCharge;


    /**
     * @var string
     *
     * @ORM\Column(name="room", type="string", length=10, nullable=true)
     */
    private $room;


    /**
     * @var string
     *
     * @ORM\Column(name="sepcimen", type="string", length=255, nullable=true)
     */
    private $sepcimen;


    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text", nullable=true)
     */
    private $content;

    /**
     * @var string
     *
     * @ORM\Column(name="reportContent", type="text", nullable=true)
     */
    private $reportContent;

    /**
     * @var string
     *
     * @ORM\Column(name="instruction", type="text", nullable=true)
     */
    private $instruction;


    /**
     * @var string
     *
     * @ORM\Column(name="overHeadCost", type="decimal", nullable=true)
     */
    private $overHeadCost;

    /**
     * @var string
     *
     * @ORM\Column(name="price", type="decimal", nullable=true)
     */
    private $price;


    /**
     * @var \string
     *
     * @ORM\Column(name="minimumPrice", type="decimal", nullable=true)
     */
    private $minimumPrice;

    /**
     * @var string
     *
     * @ORM\Column(name="commission", type="decimal" , nullable=true)
     */
    private $commission;

    /**
     * @var string
     *
     * @ORM\Column(name="phoneNo", type="string", length=128, nullable=true)
     */
    private $phoneNo;

    /**
     * @var string
     *
     * @ORM\Column(name="startHour", type="string", length=10, nullable=true)
     */
    private $startHour;

    /**
     * @var string
     *
     * @ORM\Column(name="endHour", type="string", length=10, nullable=true)
     */
    private $endHour;

    /**
     * @var array
     *
     * @ORM\Column(name="weeklyOffDay", type="array", nullable=true)
     */
    private $weeklyOffDay;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=100, nullable=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="specialist", type="string", length=255, nullable=true)
     */
    private $specialist;

    /**
     * @var string
     *
     * @ORM\Column(name="educationalDegree", type="string", length=255, nullable=true)
     */
    private $educationalDegree;

    /**
     * @var string
     *
     * @ORM\Column(name="doctorSignature", type="string", length=255, nullable=true)
     */
    private $doctorSignature;

     /**
     * @var string
     *
     * @ORM\Column(name="doctorSignatureBangla", type="string", length=255, nullable=true)
     */
    private $doctorSignatureBangla;

    /**
     * @var string
     *
     * @ORM\Column(name="pathologistSignature", type="string", length=255, nullable=true)
     */
    private $pathologistSignature;

    /**
     * @var string
     *
     * @ORM\Column(name="currentJob", type="string", length=256, nullable=true)
     */
    private $currentJob;

     /**
     * @var string
     *
     * @ORM\Column(name="remark", type="string", length=256, nullable=true)
     */
    private $remark;

     /**
     * @var string
     *
     * @ORM\Column(name="visitTime", type="string", length=256, nullable=true)
     */
    private $visitTime;

    /**
     * @var string
     *
     * @ORM\Column(name="designation", type="string", length=256, nullable=true)
     */
    private $designation;


    /**
     * @var integer
     *
     * @ORM\Column(name="code", type="integer",  nullable=true)
     */
    private $code;

    /**
     * @var integer
     *
     * @ORM\Column(name="reportHeight", type="integer",  nullable=true)
     */
    private $reportHeight = 8;

    /**
     * @var string
     *
     * @ORM\Column(name="particularCode", type="string", length=10, nullable=true)
     */
    private $particularCode;


    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=255, nullable=true)
     */
    private $address;

    /**
     * @var string
     *
     * @ORM\Column(name="mobile", type="string", length=15, nullable=true)
     */
    private $mobile;

    /**
     * @var boolean
     *
     * @ORM\Column(name="testDuration", type="boolean" )
     */
    private $testDuration = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="reportFormat", type="boolean" )
     */
    private $reportFormat = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="discountValid", type="boolean", nullable=true)
     */
    private $discountValid = false;


    /**
     * @var boolean
     *
     * @ORM\Column(name="isDoctor", type="boolean", nullable=true)
     */
    private $isDoctor = false;



    /**
     * @var boolean
     *
     * @ORM\Column(name="admissionDefault", type="boolean", nullable=true)
     */
    private $admissionDefault = false;


    /**
     * @var boolean
     *
     * @ORM\Column(name="reportUnitHide", type="boolean", nullable=true)
     */
    private $reportUnitHide = false;


    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="boolean" )
     */
    private $status= true;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isDelete", type="boolean", nullable=true)
     */
    private $isDelete = false;


    /**
     * @var boolean
     *
     * @ORM\Column(name="isReportContent", type="boolean", nullable=true)
     */
    private $isReportContent = false;

     /**
     * @var boolean
     *
     * @ORM\Column(name="isMachineFormat", type="boolean", nullable=true)
     */
    private $isMachineFormat = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isAttachment", type="boolean", nullable=true)
     */
    private $isAttachment = false;


    /**
     * @var boolean
     *
     * @ORM\Column(name="additionalField", type="boolean", nullable=true)
     */
    private $additionalField = false;


    /**
     * @var boolean
     *
     * @ORM\Column(name="sendToAccount", type="boolean", nullable=true)
     */
    private $sendToAccount = true;


    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $path;


    /**
     * @Assert\File(maxSize="8388608")
     */
    protected $file;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $signaturePath;


    /**
     * @Assert\File(maxSize="8388608")
     */
    protected $signatureFile;


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
     * Set name
     *
     * @param string $name
     *
     * @return Particular
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    public function particularNameCode(){

        return $this->particularCode.' - '.$this->name;
    }

     public function getReferred(){

        return $this->particularCode.' - '.$this->name .' ('. $this->mobile .')';
    }

    public function getDoctor(){
       $designation = empty($this->designation) ? '' : " (".$this->designation.")";
        return $this->name.$designation;
    }

    public function getReferredName(){

        if($this->getService()->getId() == 6 ){
            return $this->particularCode." - {$this->name} ( {$this->mobile} )";
        }else{
            $designation = empty($this->designation) ? '' : " (".$this->designation.")";
            return $this->particularCode.' - '.$this->name.$designation;
        }

    }

    public function getVisitDoctor(){

        $weeklyDay = empty($this->weeklyOffDay) ? '' : $this->weeklyOffDay;
        $days = array();
        if($weeklyDay){
            foreach ($weeklyDay as $day){
                $days[] = $day;
            }
        }
        $present =  " [".implode( ', ', $days)."]";
        $designation = empty($this->doctorSignature) ? '' : " (".$this->doctorSignature.")";
        return $this->name.$designation.$present;
    }

    public function getReportDoctor(){
        return $this->name;
    }

    /**
     * Set content
     *
     * @param string $content
     *
     * @return Particular
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set price
     *
     * @param string $price
     *
     * @return Particular
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return string
     */
    public function getPrice()
    {
        return $this->price;
    }



    /**
     * Set commission
     *
     * @param string $commission
     *
     * @return Particular
     */
    public function setCommission($commission)
    {
        $this->commission = $commission;

        return $this;
    }

    /**
     * Get commission
     *
     * @return string
     */
    public function getCommission()
    {
        return $this->commission;
    }

    /**
     * @return string
     */
    public function getMinimumPrice()
    {
        return $this->minimumPrice;
    }

    /**
     * @param string $minimumPrice
     */
    public function setMinimumPrice($minimumPrice)
    {
        $this->minimumPrice = $minimumPrice;
    }

    /**
     * @return HospitalConfig
     */
    public function getHospitalConfig()
    {
        return $this->hospitalConfig;
    }

    /**
     * @param HospitalConfig $hospitalConfig
     */
    public function setHospitalConfig($hospitalConfig)
    {
        $this->hospitalConfig = $hospitalConfig;
    }

    /**
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }

    /**
     * @return bool
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param bool $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
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
    public function getAssignOperator()
    {
        return $this->assignOperator;
    }

    /**
     * @param User $assignOperator
     */
    public function setAssignOperator($assignOperator)
    {
        $this->assignOperator = $assignOperator;
    }

    /**
     * @return string
     */
    public function getParticularCode()
    {
        return $this->particularCode;
    }

    /**
     * @param string $particularCode
     */
    public function setParticularCode($particularCode)
    {
        $this->particularCode = $particularCode;
    }

    /**
     * @return Service
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * @param Service $service
     */
    public function setService($service)
    {
        $this->service = $service;
    }


    /**
     * @return HmsCategory
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param HmsCategory $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
    }


    /**
     * @return HmsCategory
     */
    public function getDepartment()
    {
        return $this->department;
    }

    /**
     * @param HmsCategory $department
     */
    public function setDepartment($department)
    {
        $this->department = $department;
    }

    /**
     * @return string
     */
    public function getRoom()
    {
        return $this->room;
    }

    /**
     * @param string $room
     */
    public function setRoom($room)
    {
        $this->room = $room;
    }

    /**
     * @return string
     */
    public function getStartHour()
    {
        return $this->startHour;
    }

    /**
     * @param string $startHour
     */
    public function setStartHour($startHour)
    {
        $this->startHour = $startHour;
    }

    /**
     * @return string
     */
    public function getEndHour()
    {
        return $this->endHour;
    }

    /**
     * @param string $endHour
     */
    public function setEndHour($endHour)
    {
        $this->endHour = $endHour;
    }

    /**
     * @return array
     */
    public function getWeeklyOffDay()
    {
        return $this->weeklyOffDay;
    }

    /**
     * @param array $weeklyOffDay
     */
    public function setWeeklyOffDay($weeklyOffDay)
    {
        $this->weeklyOffDay = $weeklyOffDay;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getSpecialist()
    {
        return $this->specialist;
    }

    /**
     * @param string $specialist
     */
    public function setSpecialist($specialist)
    {
        $this->specialist = $specialist;
    }

    /**
     * @return string
     */
    public function getEducationalDegree()
    {
        return $this->educationalDegree;
    }

    /**
     * @param string $educationalDegree
     */
    public function setEducationalDegree($educationalDegree)
    {
        $this->educationalDegree = $educationalDegree;
    }

    /**
     * @return string
     */
    public function getCurrentJob()
    {
        return $this->currentJob;
    }

    /**
     * @param string $currentJob
     */
    public function setCurrentJob($currentJob)
    {
        $this->currentJob = $currentJob;
    }

    /**
     * @return string
     */
    public function getPhoneNo()
    {
        return $this->phoneNo;
    }

    /**
     * @param string $phoneNo
     */
    public function setPhoneNo($phoneNo)
    {
        $this->phoneNo = $phoneNo;
    }

    /**
     * @return ProductUnit
     */
    public function getUnit()
    {
        return $this->unit;
    }

    /**
     * @param ProductUnit $unit
     */
    public function setUnit($unit)
    {
        $this->unit = $unit;
    }

    /**
     * @return InvoiceParticular
     */
    public function getInvoiceParticular()
    {
        return $this->invoiceParticular;
    }

    /**
     * @return Location
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param Location $location
     */
    public function setLocation($location)
    {
        $this->location = $location;
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
     * Sets file.
     *
     * @param Particular $file
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
    }

    /**
     * Get file.
     *
     * @return Particular
     */
    public function getFile()
    {
        return $this->file;
    }

    public function getAbsolutePath()
    {
        return null === $this->path
            ? null
            : $this->getUploadRootDir().'/'.$this->path;
    }

    public function getWebPath()
    {
        return null === $this->path
            ? null
            : $this->getUploadDir().'/'.$this->path;
    }

    protected function getUploadRootDir()
    {
        return __DIR__.'/../../../../../web/'.$this->getUploadDir();
    }

    protected function getUploadDir()
    {
        return 'uploads/domain/'.$this->getHospitalConfig()->getGlobalOption()->getId().'/hms/';
    }

    public function upload()
    {
        // the file property can be empty if the field is not required
        if (null === $this->getFile()) {
            return;
        }

        // use the original file name here but you should
        // sanitize it at least to avoid any security issues

        // move takes the target directory and then the
        // target filename to move to
        $filename = date('YmdHmi') . "_" . $this->getFile()->getClientOriginalName();
        $this->getFile()->move(
            $this->getUploadRootDir(),
            $filename
        );

        // set the path property to the filename where you've saved the file
        $this->path = $filename ;

        // clean up the file property as you won't need it anymore
        $this->file = null;
    }

    /**
     * @return Invoice
     */
    public function getHmsInvoice()
    {
        return $this->hmsInvoice;
    }

    /**
     * @return string
     */
    public function getInstruction()
    {
        return $this->instruction;
    }

    /**
     * @param string $instruction
     */
    public function setInstruction($instruction)
    {
        $this->instruction = $instruction;
    }

    /**
     * @return Invoice
     */
    public function getHmsInvoiceCabin()
    {
        return $this->hmsInvoiceCabin;
    }


    /**
     * @return int
     */
    public function getPurchaseQuantity()
    {
        return $this->purchaseQuantity;
    }

    /**
     * @param int $purchaseQuantity
     */
    public function setPurchaseQuantity($purchaseQuantity)
    {
        $this->purchaseQuantity = $purchaseQuantity;
    }

    /**
     * @return int
     */
    public function getSalesQuantity()
    {
        return $this->salesQuantity;
    }

    /**
     * @param int $salesQuantity
     */
    public function setSalesQuantity($salesQuantity)
    {
        $this->salesQuantity = $salesQuantity;
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
    public function getAssignDoctorInvoices()
    {
        return $this->assignDoctorInvoices;
    }

    /**
     * @return HmsPurchaseItem
     */
    public function getPurchaseItems()
    {
        return $this->purchaseItems;
    }

    /**
     * @return PathologicalReport
     */
    public function getPathologicalReports()
    {
        return $this->pathologicalReports;
    }

    /**
     * @return User
     */
    public function getAssignDoctor()
    {
        return $this->assignDoctor;
    }

    /**
     * @param User $assignDoctor
     */
    public function setAssignDoctor($assignDoctor)
    {
        $this->assignDoctor = $assignDoctor;
    }

    /**
     * @return InvoiceParticular
     */
    public function getInvoiceParticularDoctor()
    {
        return $this->invoiceParticularDoctor;
    }

    /**
     * @return string
     */
    public function getSepcimen()
    {
        return $this->sepcimen;
    }

    /**
     * @param string $sepcimen
     */
    public function setSepcimen($sepcimen)
    {
        $this->sepcimen = $sepcimen;
    }

    /**
     * @return string
     */
    public function getDesignation()
    {
        return $this->designation;
    }

    /**
     * @param string $designation
     */
    public function setDesignation($designation)
    {
        $this->designation = $designation;
    }

    /**
     * @return string
     */
    public function getOverHeadCost()
    {
        return $this->overHeadCost;
    }

    /**
     * @param string $overHeadCost
     */
    public function setOverHeadCost($overHeadCost)
    {
        $this->overHeadCost = $overHeadCost;
    }

    /**
     * @return bool
     */
    public function getTestDuration()
    {
        return $this->testDuration;
    }

    /**
     * @param bool $testDuration
     */
    public function setTestDuration($testDuration)
    {
        $this->testDuration = $testDuration;
    }

    /**
     * @return AdmissionPatientParticular
     */
    public function getAdmissionPatientParticular()
    {
        return $this->admissionPatientParticular;
    }

    /**
     * @return mixed
     */
    public function getServiceGroup()
    {
        return $this->serviceGroup;
    }

    /**
     * @param mixed $serviceGroup
     */
    public function setServiceGroup($serviceGroup)
    {
        $this->serviceGroup = $serviceGroup;
    }

    /**
     * @return string
     */
    public function getPurchaseAverage()
    {
        return $this->purchaseAverage;
    }

    /**
     * @param string $purchaseAverage
     */
    public function setPurchaseAverage($purchaseAverage)
    {
        $this->purchaseAverage = $purchaseAverage;
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
    public function getOpeningQuantity()
    {
        return $this->openingQuantity;
    }

    /**
     * @param int $openingQuantity
     */
    public function setOpeningQuantity($openingQuantity)
    {
        $this->openingQuantity = $openingQuantity;
    }

    /**
     * @return int
     */
    public function getMinQuantity()
    {
        return $this->minQuantity;
    }

    /**
     * @param int $minQuantity
     */
    public function setMinQuantity($minQuantity)
    {
        $this->minQuantity = $minQuantity;
    }

    /**
     * @return bool
     */
    public function getReportFormat()
    {
        return $this->reportFormat;
    }

    /**
     * @param bool $reportFormat
     */
    public function setReportFormat($reportFormat)
    {
        $this->reportFormat = $reportFormat;
    }

    /**
     * @return string
     */
    public function getPurchasePrice()
    {
        return $this->purchasePrice;
    }

    /**
     * @param string $purchasePrice
     */
    public function setPurchasePrice($purchasePrice)
    {
        $this->purchasePrice = $purchasePrice;
    }



    /**
     * @return mixed
     */
    public function isDelete()
    {
        return $this->isDelete;
    }

    /**
     * @param mixed $isDelete
     */
    public function setIsDelete($isDelete)
    {
        $this->isDelete = $isDelete;
    }

    /**
     * @return DiagnosticReport
     */
    public function getDiagnosticReport()
    {
        return $this->diagnosticReport;
    }

    /**
     * @param DiagnosticReport $diagnosticReport
     */
    public function setDiagnosticReport($diagnosticReport)
    {
        $this->diagnosticReport = $diagnosticReport;
    }

    /**
     * @return HmsInvoiceTemporaryParticular
     */
    public function getHmsInvoiceTemporaryParticular()
    {
        return $this->hmsInvoiceTemporaryParticular;
    }

    /**
     * @return bool
     */
    public function getDiscountValid()
    {
        return $this->discountValid;
    }

    /**
     * @param bool $discountValid
     */
    public function setDiscountValid($discountValid)
    {
        $this->discountValid = $discountValid;
    }

    /**
     * @param int $oldReportId
     */
    public function setOldReportId($oldReportId)
    {
        $this->oldReportId = $oldReportId;
    }

    /**
     * @return string
     */
    public function getPathologistSignature()
    {
        return $this->pathologistSignature;
    }

    /**
     * @param string $pathologistSignature
     */
    public function setPathologistSignature($pathologistSignature)
    {
        $this->pathologistSignature = $pathologistSignature;
    }

    /**
     * @return string
     */
    public function getDoctorSignature()
    {
        return $this->doctorSignature;
    }

    /**
     * @param string $doctorSignature
     */
    public function setDoctorSignature($doctorSignature)
    {
        $this->doctorSignature = $doctorSignature;
    }

    /**
     * @return mixed
     */
    public function getSurgeryDepartment()
    {
        return $this->surgeryDepartment;
    }

    /**
     * @param mixed $surgeryDepartment
     */
    public function setSurgeryDepartment($surgeryDepartment)
    {
        $this->surgeryDepartment = $surgeryDepartment;
    }

    /**
     * @return bool
     */
    public function isReportUnitHide()
    {
        return $this->reportUnitHide;
    }

    /**
     * @param bool $reportUnitHide
     */
    public function setReportUnitHide($reportUnitHide)
    {
        $this->reportUnitHide = $reportUnitHide;
    }

    /**
     * @return string
     */
    public function getReportContent()
    {
        return $this->reportContent;
    }

    /**
     * @param string $reportContent
     */
    public function setReportContent($reportContent)
    {
        $this->reportContent = $reportContent;
    }


    /**
     * @return bool
     */
    public function isAttachment()
    {
        return $this->isAttachment;
    }

    /**
     * @param bool $isAttachment
     */
    public function setIsAttachment($isAttachment)
    {
        $this->isAttachment = $isAttachment;
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
     * @return bool
     */
    public function isAdditionalField()
    {
        return $this->additionalField;
    }

    /**
     * @param bool $additionalField
     */
    public function setAdditionalField($additionalField)
    {
        $this->additionalField = $additionalField;
    }

    /**
     * @return bool
     */
    public function isReportContent()
    {
        return $this->isReportContent;
    }

    /**
     * @param bool $isReportContent
     */
    public function setIsReportContent($isReportContent)
    {
        $this->isReportContent = $isReportContent;
    }

    /**
     * @return mixed
     */
    public function getSignaturePath()
    {
        return $this->signaturePath;
    }

    /**
     * @param mixed $signaturePath
     */
    public function setSignaturePath($signaturePath)
    {
        $this->signaturePath = $signaturePath;
    }

    /**
     * @return mixed
     */
    public function getSignatureFile()
    {
        return $this->signatureFile;
    }

    /**
     * @param mixed $signatureFile
     */
    public function setSignatureFile(UploadedFile $signatureFile = null)
    {
        $this->signatureFile = $signatureFile;
    }



    public function getAbsoluteSignaturePath()
    {
        return null === $this->signaturePath
            ? null
            : $this->getUploadRootDir().'/'.$this->signaturePath;
    }

    public function getWebSignaturePath()
    {
        return null === $this->signaturePath
            ? null
            : $this->getUploadDir().'/'.$this->signaturePath;
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        if ($file = $this->getAbsolutePath()) {
            unlink($file);
        }
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeSignatureUpload()
    {
        if ($file = $this->getAbsoluteSignaturePath()) {
            unlink($file);
        }
    }

    public function signatureUpload()
    {
        // the file property can be empty if the field is not required
        if (null === $this->getSignatureFile()) {
            return;
        }

        // use the original file name here but you should
        // sanitize it at least to avoid any security issues

        // move takes the target directory and then the
        // target filename to move to
        $filename = date('YmdHmi') . "_" . $this->getSignatureFile()->getClientOriginalName();
        $this->getSignatureFile()->move(
            $this->getUploadRootDir(),
            $filename
        );

        // set the path property to the filename where you've saved the file
        $this->signaturePath = $filename;

        // clean up the file property as you won't need it anymore
        $this->signatureFile = null;
    }

    /**
     * @return string
     */
    public function getReportMachineName()
    {
        return $this->reportMachineName;
    }

    /**
     * @param string $reportMachineName
     */
    public function setReportMachineName($reportMachineName)
    {
        $this->reportMachineName = $reportMachineName;
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
    public function getVisitTime()
    {
        return $this->visitTime;
    }

    /**
     * @param string $visitTime
     */
    public function setVisitTime($visitTime)
    {
        $this->visitTime = $visitTime;
    }

    /**
     * @return string
     */
    public function getDoctorSignatureBangla()
    {
        return $this->doctorSignatureBangla;
    }

    /**
     * @param string $doctorSignatureBangla
     */
    public function setDoctorSignatureBangla($doctorSignatureBangla)
    {
        $this->doctorSignatureBangla = $doctorSignatureBangla;
    }

    /**
     * @return mixed
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
     * @return mixed
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @param mixed $children
     */
    public function setChildren($children)
    {
        $this->children = $children;
    }

    /**
     * @return mixed
     */
    public function getVisitModes()
    {
        return $this->visitModes;
    }

    /**
     * @return bool
     */
    public function isDoctor()
    {
        return $this->isDoctor;
    }

    /**
     * @param bool $isDoctor
     */
    public function setIsDoctor($isDoctor)
    {
        $this->isDoctor = $isDoctor;
    }

    /**
     * @return string
     */
    public function getIpdVisitCharge()
    {
        return $this->ipdVisitCharge;
    }

    /**
     * @param string $ipdVisitCharge
     */
    public function setIpdVisitCharge($ipdVisitCharge)
    {
        $this->ipdVisitCharge = $ipdVisitCharge;
    }

    /**
     * @return bool
     */
    public function isMachineFormat()
    {
        return $this->isMachineFormat;
    }

    /**
     * @param bool $isMachineFormat
     */
    public function setIsMachineFormat($isMachineFormat)
    {
        $this->isMachineFormat = $isMachineFormat;
    }

    /**
     * @return bool
     */
    public function isSendToAccount()
    {
        return $this->sendToAccount;
    }

    /**
     * @param bool $sendToAccount
     */
    public function setSendToAccount($sendToAccount)
    {
        $this->sendToAccount = $sendToAccount;
    }

    /**
     * @return bool
     */
    public function isAdmissionDefault()
    {
        return $this->admissionDefault;
    }

    /**
     * @param bool $admissionDefault
     */
    public function setAdmissionDefault($admissionDefault)
    {
        $this->admissionDefault = $admissionDefault;
    }

    /**
     * @return mixed
     */
    public function getMarketingExecutive()
    {
        return $this->marketingExecutive;
    }

     /**
     * @return mixed
     */
    public function marketingExecutiveEmployee()
    {
        return "{$this->particularCode} - {$this->marketingExecutive->getProfile()->getName()}";
    }

    /**
     * @param mixed $marketingExecutive
     */
    public function setMarketingExecutive($marketingExecutive)
    {
        $this->marketingExecutive = $marketingExecutive;
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

}

