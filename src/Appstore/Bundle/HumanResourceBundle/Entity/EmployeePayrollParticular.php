<?php

namespace Appstore\Bundle\HumanResourceBundle\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * PayrollParticular
 *
 * @ORM\Table("hr_employee_payroll_particular")
 * @ORM\Entity(repositoryClass="")
 */
class EmployeePayrollParticular
{
    /**
     * @var integer
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;


    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\HumanResourceBundle\Entity\EmployeePayroll", inversedBy="employeePayrollParticulars" )
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private  $employeePayroll;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\HumanResourceBundle\Entity\PayrollSetting", inversedBy="employeePayrollParticulars" )
     **/
    private  $particular;


    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=50 , nullable=true)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="mode", type="string", length=20 , nullable=true)
     */
    private $mode;



    /**
     * @var float
     *
     * @ORM\Column(name="unit", type="float", nullable=true)
     */
    private $unit = 0;


    /**
     * @var float
     *
     * @ORM\Column(name="amount", type="float", nullable=true)
     */
    private $amount = 0;


    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="boolean")
     */
    private $status = true;


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
     * @return mixed
     */
    public function getParticular()
    {
        return $this->particular;
    }

    /**
     * @param mixed $particular
     */
    public function setParticular($particular)
    {
        $this->particular = $particular;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
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
     * @return EmployeePayroll
     */
    public function getEmployeePayroll()
    {
        return $this->employeePayroll;
    }

    /**
     * @param EmployeePayroll $employeePayroll
     */
    public function setEmployeePayroll($employeePayroll)
    {
        $this->employeePayroll = $employeePayroll;
    }

    /**
     * @return string
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * @param string $mode
     */
    public function setMode($mode)
    {
        $this->mode = $mode;
    }

    /**
     * @return float
     */
    public function getUnit()
    {
        return $this->unit;
    }

    /**
     * @param float $unit
     */
    public function setUnit($unit)
    {
        $this->unit = $unit;
    }


}

