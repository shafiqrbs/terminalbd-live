<?php

namespace Appstore\Bundle\HumanResourceBundle\Entity;

use Core\UserBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Gedmo\Mapping\Annotation as Gedmo;
/**
 * LeaveSetup
 *
 * @ORM\Table(name="hr_employee_leave")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\HumanResourceBundle\Repository\EmployeeLeaveRepository")
 */
class EmployeeLeave
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
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\GlobalOption", inversedBy="employeeLeave")
     **/
    protected $globalOption;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\HumanResourceBundle\Entity\LeaveSetup", inversedBy="employeeLeave")
     **/
    protected $leaveSetup;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\HumanResourceBundle\Entity\DailyAttendance", mappedBy="employeeLeave")
     **/
    protected $dailyAttendance;

    /**
     * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User", inversedBy="employeeLeave")
     **/
    private  $employee;

    /**
     * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User", inversedBy="employeeLeaveApprove")
     **/
    private  $approvedBy;

    /**
     * @var string
     *
     * @ORM\Column(name="year", type="string", length=20, nullable =true)
     */
    private $year;

    /**
     * @var integer
     *
     * @ORM\Column(name="noOffDay", type="smallint", length=2, nullable=true)
     */
    private $noOffDay;

    /**
     * @var string
     *
     * @ORM\Column(name="noOfDate", type="text", nullable=true)
     */
    private $noOfDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="startDate", type="datetime", nullable=true)
     */
    private $startDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="endDate", type="datetime", nullable=true)
     */
    private $endDate;

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
     * @return LeaveSetup
     */
    public function getLeaveSetup()
    {
        return $this->leaveSetup;
    }

    /**
     * @param LeaveSetup $leaveSetup
     */
    public function setLeaveSetup($leaveSetup)
    {
        $this->leaveSetup = $leaveSetup;
    }

    /**
     * @return int
     */
    public function getNoOffDay()
    {
        return $this->noOffDay;
    }

    /**
     * @param int $noOffDay
     */
    public function setNoOffDay($noOffDay)
    {
        $this->noOffDay = $noOffDay;
    }

    /**
     * @return \DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @param \DateTime $startDate
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;
    }

    /**
     * @return \DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @param \DateTime $endDate
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;
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
     * @return string
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * @param string $year
     */
    public function setYear($year)
    {
        $this->year = $year;
    }

    /**
     * @return DailyAttendance
     */
    public function getDailyAttendance()
    {
        return $this->dailyAttendance;
    }

    /**
     * @return string
     */
    public function getNoOfDate()
    {
        return $this->noOfDate;
    }

    /**
     * @param string $noOfDate
     */
    public function setNoOfDate($noOfDate)
    {
        $this->noOfDate = $noOfDate;
    }

}

