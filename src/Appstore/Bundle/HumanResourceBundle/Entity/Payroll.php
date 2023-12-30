<?php

namespace Appstore\Bundle\HumanResourceBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;

/**
 * Payroll
 * @ORM\Table("hr_payroll")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\HumanResourceBundle\Repository\PayrollRepository")
 */
class Payroll
{
    /**
     * @var integer
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\GlobalOption", inversedBy="payroll")
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    protected $globalOption;


    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\DomainUserBundle\Entity\Branches", inversedBy="payrolls")
     **/
    protected $branch;


    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\HumanResourceBundle\Entity\PayrollSheet", mappedBy="payroll" )
     **/
    private  $payrollSheets;


    /**
     * @var string
     *
     * @ORM\Column(name="salaryType", type="string", length = 50 , nullable=true)
     */
    private $salaryType;


     /**
     * @var string
     *
     * @ORM\Column(name="employeeType", type="string", length = 50 , nullable=true)
     */
    private $employeeType;


    /**
     * @var string
     *
     * @ORM\Column(name="process", type="string", length = 30 , nullable=true)
     */
    private $process;


    /**
     * @var string
     *
     * @ORM\Column(name="effectedMonth", type="string" , nullable=true)
     */
    private $effectedMonth;


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
     * @ORM\Column(name="loanReceive", type="float", nullable=true)
     */
    private $loanReceive;


    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="boolean")
     */
    private $status = false;


   /**
     * @var boolean
     *
     * @ORM\Column(name="bonusApplicable", type="boolean")
     */
    private $bonusApplicable = false;


    /**
     * @var boolean
     *
     * @ORM\Column(name="arearApplicable", type="boolean")
     */
    private $arearApplicable = false;


    /**
     * @var string
     *
     * @ORM\Column(name="remark", type="string", length=255 , nullable=true)
     */
    private $remark;


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
     * @return PayrollSheet
     */
    public function getPayrollSheets()
    {
        return $this->payrollSheets;
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
     * @return Payroll
     */
    public function getBranch()
    {
        return $this->branch;
    }

    /**
     * @param Payroll $branch
     */
    public function setBranch($branch)
    {
        $this->branch = $branch;
    }

    /**
     * @return bool
     */
    public function isArearApplicable()
    {
        return $this->arearApplicable;
    }

    /**
     * @param bool $arearApplicable
     */
    public function setArearApplicable($arearApplicable)
    {
        $this->arearApplicable = $arearApplicable;
    }

    /**
     * @return string
     */
    public function getEmployeeType()
    {
        return $this->employeeType;
    }

    /**
     * @param string $employeeType
     */
    public function setEmployeeType($employeeType)
    {
        $this->employeeType = $employeeType;
    }

}

