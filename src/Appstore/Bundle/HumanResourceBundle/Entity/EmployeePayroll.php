<?php

namespace Appstore\Bundle\HumanResourceBundle\Entity;


use Core\UserBundle\Entity\Profile;
use Core\UserBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Setting\Bundle\ToolBundle\Entity\Bank;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;

/**
 * Payroll
 *
 * @ORM\Table("hr_employee_payroll")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\HumanResourceBundle\Repository\EmployeePayrollRepository")
 */
class EmployeePayroll
{
    /**
     * @var integer
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\GlobalOption", inversedBy="employeePayroll")
     **/
    protected $globalOption;



    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\HumanResourceBundle\Entity\PayrollSheet", mappedBy="employee" )
     **/
    private  $payrollSheets;

    /**
     * @ORM\OneToOne(targetEntity="Core\UserBundle\Entity\Profile", inversedBy="employeePayroll")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="profile_id", referencedColumnName="id", unique=true, onDelete="CASCADE")
     * })
     */
    private  $profile;


    /**
     * @ORM\OneToOne(targetEntity="Core\UserBundle\Entity\User", inversedBy="employeePayroll")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="employee_id", referencedColumnName="id", unique=true, onDelete="CASCADE")
     * })
     */

    protected $employee;



    /**
     * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User", inversedBy="payrollApproved" )
     **/
    private  $approvedBy;

    /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\Bank", inversedBy="employeePayroll" )
     **/
    private  $bank;


    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\HumanResourceBundle\Entity\EmployeePayrollParticular", mappedBy="employeePayroll" )
     **/
    private  $employeePayrollParticulars;


    /**
     * @var string
     *
     * @ORM\Column(name="employeeName", type="string" , length = 150 , nullable=true)
     */
    private $employeeName;


    /**
     * @var string
     *
     * @ORM\Column(name="salaryType", type="string", length = 50 , nullable=true)
     */
    private $salaryType;


    /**
     * @var string
     *
     * @ORM\Column(name="effectedMonth", type="string" , nullable=true)
     */
    private $effectedMonth;

    /**
     * @var float
     *
     * @ORM\Column(name="monthlyHour", type="float" , nullable=true)
     */
    private $monthlyHour = 0;


    /**
     * @var float
     *
     * @ORM\Column(name="taxNumber", type="float" , nullable=true)
     */
    private $taxNumber = 0;

    /**
     * @var float
     *
     * @ORM\Column(name="basicAmount", type="float", nullable=true)
     */
    private $basicAmount = 0;

    /**
     * @var float
     *
     * @ORM\Column(name="allowanceAmount", type="float", nullable=true)
     */
    private $allowanceAmount = 0;

    /**
     * @var float
     *
     * @ORM\Column(name="deductionAmount", type="float", nullable=true)
     */
    private $deductionAmount = 0;

    /**
     * @var float
     *
     * @ORM\Column(name="advanceAmount", type="float", nullable=true)
     */
    private $advanceAmount = 0;

    /**
     * @var float
     *
     * @ORM\Column(name="arearAmount", type="float", nullable=true)
     */
    private $arearAmount = 0;


    /**
     * @var float
     *
     * @ORM\Column(name="bonusPercentage", type="float", nullable=true)
     */
    private $bonusPercentage = 60;


    /**
     * @var float
     *
     * @ORM\Column(name="bonusAmount", type="float", nullable=true)
     */
    private $bonusAmount = 0;


    /**
     * @var float
     *
     * @ORM\Column(name="totalAmount", type="float", nullable=true)
     */
    private $totalAmount = 0;

    /**
     * @var float
     *
     * @ORM\Column(name="payableAmount", type="float", nullable=true)
     */
    private $payableAmount = 0;

    /**
     * @var float
     *
     * @ORM\Column(name="loanAmount", type="float", nullable=true)
     */
    private $loanAmount;

    /**
     * @var float
     *
     * @ORM\Column(name="loanReceive", type="float", nullable=true)
     */
    private $loanReceive;


     /**
     * @var float
     *
     * @ORM\Column(name="loanDue", type="float", nullable=true)
     */
    private $loanDue;


     /**
     * @var float
     *
     * @ORM\Column(name="loanInstallment", type="float", nullable=true)
     */
    private $loanInstallment;

    /**
     * @var string
     *
     * @ORM\Column(name="loanAdjustMonth", type="datetime" , nullable=true)
     */
    private $loanAdjustMonth;

    /**
     * @var string
     *
     * @ORM\Column(name="paymentMethod", type="string", length=50 , nullable=true)
     */
    private $paymentMethod;


    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="boolean")
     */
    private $status = true;


   /**
     * @var boolean
     *
     * @ORM\Column(name="bonusApplicable", type="boolean")
     */
    private $bonusApplicable = false;


   /**
     * @var string
     *
     * @ORM\Column(name="accountNo", type="string", length=100, nullable = true)
     */
    private $accountNo;

    /**
     * @var string
     *
     * @ORM\Column(name="bankAccountName", type="string", length=100, nullable = true)
     */
    private $bankAccountName;

    /**
     * @var string
     *
     * @ORM\Column(name="branch", type="string", length=255, nullable = true)
     */
    private $branch;

    /**
     * @var string
     *
     * @ORM\Column(name="mobileBanking", type="string", length=255, nullable = true)
     */
    private $mobileBanking;

    /**
     * @var string
     *
     * @ORM\Column(name="mobileAccount", type="string", length=255, nullable = true)
     */
    private $mobileAccount;

    /**
     * @var string
     *
     * @ORM\Column(name="remark", type="string", length=255 , nullable=true)
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
     * @return User
     */
    public function getEmployee()
    {
        return $this->employee;
    }

    /**
     * @param User $employee
     */
    public function setEmployee($employee)
    {
        $this->employee = $employee;
    }


    /**
     * @return string
     */
    public function getEmployeeName()
    {
        return $this->employeeName;
    }

    /**
     * @param string $employeeName
     */
    public function setEmployeeName($employeeName)
    {
        $this->employeeName = $employeeName;
    }

    /**
     * @return string
     */
    public function getSalaryType()
    {
        return $this->salaryType;
    }

    /**
     * @param string $salaryType
     */
    public function setSalaryType($salaryType)
    {
        $this->salaryType = $salaryType;
    }

    /**
     * @return string
     */
    public function getEffectedMonth()
    {
        return $this->effectedMonth;
    }

    /**
     * @param string $effectedMonth
     */
    public function setEffectedMonth($effectedMonth)
    {
        $this->effectedMonth = $effectedMonth;
    }

    /**
     * @return float
     */
    public function getMonthlyHour()
    {
        return $this->monthlyHour;
    }

    /**
     * @param float $monthlyHour
     */
    public function setMonthlyHour($monthlyHour)
    {
        $this->monthlyHour = $monthlyHour;
    }

    /**
     * @return float
     */
    public function getTaxNumber()
    {
        return $this->taxNumber;
    }

    /**
     * @param float $taxNumber
     */
    public function setTaxNumber($taxNumber)
    {
        $this->taxNumber = $taxNumber;
    }

    /**
     * @return float
     */
    public function getBasicAmount()
    {
        return $this->basicAmount;
    }

    /**
     * @param float $basicAmount
     */
    public function setBasicAmount($basicAmount)
    {
        $this->basicAmount = $basicAmount;
    }

    /**
     * @return float
     */
    public function getArearAmount()
    {
        return $this->arearAmount;
    }

    /**
     * @param float $arearAmount
     */
    public function setArearAmount($arearAmount)
    {
        $this->arearAmount = $arearAmount;
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
    public function getLoanAmount()
    {
        return $this->loanAmount;
    }

    /**
     * @param float $loanAmount
     */
    public function setLoanAmount($loanAmount)
    {
        $this->loanAmount = $loanAmount;
    }

    /**
     * @return float
     */
    public function getLoanReceive()
    {
        return $this->loanReceive;
    }

    /**
     * @param float $loanReceive
     */
    public function setLoanReceive($loanReceive)
    {
        $this->loanReceive = $loanReceive;
    }

    /**
     * @return float
     */
    public function getLoanDue()
    {
        return $this->loanDue;
    }

    /**
     * @param float $loanDue
     */
    public function setLoanDue($loanDue)
    {
        $this->loanDue = $loanDue;
    }

    /**
     * @return float
     */
    public function getLoanInstallment()
    {
        return $this->loanInstallment;
    }

    /**
     * @param float $loanInstallment
     */
    public function setLoanInstallment($loanInstallment)
    {
        $this->loanInstallment = $loanInstallment;
    }

    /**
     * @return string
     */
    public function getLoanAdjustMonth()
    {
        return $this->loanAdjustMonth;
    }

    /**
     * @param string $loanAdjustMonth
     */
    public function setLoanAdjustMonth($loanAdjustMonth)
    {
        $this->loanAdjustMonth = $loanAdjustMonth;
    }

    /**
     * @return string
     */
    public function getPaymentMethod()
    {
        return $this->paymentMethod;
    }

    /**
     * @param string $paymentMethod
     */
    public function setPaymentMethod($paymentMethod)
    {
        $this->paymentMethod = $paymentMethod;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return User
     */
    public function getApprovedBy()
    {
        return $this->approvedBy;
    }

    /**
     * @param User $approvedBy
     */
    public function setApprovedBy($approvedBy)
    {
        $this->approvedBy = $approvedBy;
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
    public function getBankAccountName()
    {
        return $this->bankAccountName;
    }

    /**
     * @param string $bankAccountName
     */
    public function setBankAccountName($bankAccountName)
    {
        $this->bankAccountName = $bankAccountName;
    }

    /**
     * @return string
     */
    public function getBranch()
    {
        return $this->branch;
    }

    /**
     * @param string $branch
     */
    public function setBranch($branch)
    {
        $this->branch = $branch;
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
     * @return EmployeePayrollParticular
     */
    public function getEmployeePayrollParticulars()
    {
        return $this->employeePayrollParticulars;
    }

    /**
     * @return float
     */
    public function getBonusPercentage()
    {
        return $this->bonusPercentage;
    }

    /**
     * @param float $bonusPercentage
     */
    public function setBonusPercentage($bonusPercentage)
    {
        $this->bonusPercentage = $bonusPercentage;
    }

    /**
     * @return bool
     */
    public function isBonusApplicable()
    {
        return $this->bonusApplicable;
    }

    /**
     * @param bool $bonusApplicable
     */
    public function setBonusApplicable($bonusApplicable)
    {
        $this->bonusApplicable = $bonusApplicable;
    }

    /**
     * @return string
     */
    public function getMobileBanking()
    {
        return $this->mobileBanking;
    }

    /**
     * @param string $mobileBanking
     */
    public function setMobileBanking($mobileBanking)
    {
        $this->mobileBanking = $mobileBanking;
    }

    /**
     * @return string
     */
    public function getMobileAccount()
    {
        return $this->mobileAccount;
    }

    /**
     * @param string $mobileAccount
     */
    public function setMobileAccount($mobileAccount)
    {
        $this->mobileAccount = $mobileAccount;
    }

    /**
     * @return Profile
     */
    public function getProfile()
    {
        return $this->profile;
    }

    /**
     * @param Profile $profile
     */
    public function setProfile($profile)
    {
        $this->profile = $profile;
    }

    /**
     * @return float
     */
    public function getAllowanceAmount()
    {
        return $this->allowanceAmount;
    }

    /**
     * @param float $allowanceAmount
     */
    public function setAllowanceAmount($allowanceAmount)
    {
        $this->allowanceAmount = $allowanceAmount;
    }

    /**
     * @return float
     */
    public function getDeductionAmount()
    {
        return $this->deductionAmount;
    }

    /**
     * @param float $deductionAmount
     */
    public function setDeductionAmount($deductionAmount)
    {
        $this->deductionAmount = $deductionAmount;
    }

    /**
     * @return float
     */
    public function getAdvanceAmount()
    {
        return $this->advanceAmount;
    }

    /**
     * @param float $advanceAmount
     */
    public function setAdvanceAmount($advanceAmount)
    {
        $this->advanceAmount = $advanceAmount;
    }

    /**
     * @return float
     */
    public function getPayableAmount()
    {
        return $this->payableAmount;
    }

    /**
     * @param float $payableAmount
     */
    public function setPayableAmount($payableAmount)
    {
        $this->payableAmount = $payableAmount;
    }

    /**
     * @return float
     */
    public function getBonusAmount()
    {
        return $this->bonusAmount;
    }

    /**
     * @param float $bonusAmount
     */
    public function setBonusAmount($bonusAmount)
    {
        $this->bonusAmount = $bonusAmount;
    }

    /**
     * @return PayrollSheet
     */
    public function getPayrollSheets()
    {
        return $this->payrollSheets;
    }


}

