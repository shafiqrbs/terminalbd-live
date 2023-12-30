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
 * @ORM\Table("hr_payroll_sheet")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\HumanResourceBundle\Repository\PayrollSheetRepository")
 */
class PayrollSheet
{
    /**
     * @var integer
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\HumanResourceBundle\Entity\Payroll", inversedBy="payrollSheets")
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    protected $payroll;


    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\HumanResourceBundle\Entity\EmployeePayroll", inversedBy="payrollSheets" )
     **/
    private  $employee;


    /**
     * @var string
     *
     * @ORM\Column(name="salaryType", type="string", length = 50 , nullable=true)
     */
    private $salaryType;


    /**
     * @var float
     *
     * @ORM\Column(name="monthlyHour", type="float" , nullable=true)
     */
    private $monthlyHour = 0;


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
     * @ORM\Column(name="loanInstallment", type="float", nullable=true)
     */
    private $loanInstallment;


    /**
     * @var string
     *
     * @ORM\Column(name="paymentMethod", type="string", length=50 , nullable=true)
     */
    private $paymentMethod;


     /**
     * @var string
     *
     * @ORM\Column(name="particularAllowance", type="text", nullable = true)
     */
    private $particularAllowance;


     /**
     * @var string
     *
     * @ORM\Column(name="particularDeduction", type="text", nullable = true)
     */
    private $particularDeduction;


    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="boolean")
     */
    private $status = false;


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
     * @return EmployeePayroll
     */
    public function getEmployee()
    {
        return $this->employee;
    }

    /**
     * @param EmployeePayroll $employee
     */
    public function setEmployee($employee)
    {
        $this->employee = $employee;
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
     * @return bool
     */
    public function isStatus()
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
     * @return Payroll
     */
    public function getPayroll()
    {
        return $this->payroll;
    }

    /**
     * @param Payroll $payroll
     */
    public function setPayroll($payroll)
    {
        $this->payroll = $payroll;
    }

    /**
     * @return string
     */
    public function getParticularAllowance()
    {
        return $this->particularAllowance;
    }

    /**
     * @param string $particularAllowance
     */
    public function setParticularAllowance($particularAllowance)
    {
        $this->particularAllowance = $particularAllowance;
    }

    /**
     * @return string
     */
    public function getParticularDeduction()
    {
        return $this->particularDeduction;
    }

    /**
     * @param string $particularDeduction
     */
    public function setParticularDeduction($particularDeduction)
    {
        $this->particularDeduction = $particularDeduction;
    }

}

