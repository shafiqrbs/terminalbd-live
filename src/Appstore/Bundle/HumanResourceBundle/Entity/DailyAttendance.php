<?php

namespace Appstore\Bundle\HumanResourceBundle\Entity;

use Core\UserBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Gedmo\Mapping\Annotation as Gedmo;
/**
 * DailyAttendance
 *
 * @ORM\Table(name="hr_attendance_daily")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\HumanResourceBundle\Repository\DailyAttendanceRepository")
 */
class DailyAttendance
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
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\GlobalOption", inversedBy="dailyAttendance")
     **/
    protected $globalOption;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\HumanResourceBundle\Entity\EmployeeLeave", inversedBy="dailyAttendance")
     **/
    protected $employeeLeave;


    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\HumanResourceBundle\Entity\Attendance", inversedBy="dailyAttendance")
     **/
    protected $attendance;

    /**
     * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User", inversedBy="userAttendance")
     **/
    private  $user;


    /**
     * @var string
     *
     * @ORM\Column(name="year", type="string", length=20, nullable =true)
     */
    private $year;

    /**
     * @var string
     *
     * @ORM\Column(name="month", type="string", length=30, nullable =true)
     */
    private $month;

    /**
     * @var integer
     *
     * @ORM\Column(name="PresentDay", type="smallint", length=3, nullable =true)
     */
    private $presentDay;


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
     * @var boolean
     *
     * @ORM\Column(name="present", type="boolean", nullable =true)
     */
    private $present = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="presentIn", type="boolean", nullable =true)
     */
    private $presentIn = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="presentOut", type="boolean", nullable =true)
     */
    private $presentOut = false;

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
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
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
     * @return string
     */
    public function getMonth()
    {
        return $this->month;
    }

    /**
     * @param string $month
     */
    public function setMonth($month)
    {
        $this->month = $month;
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
     * @return bool
     */
    public function getPresent()
    {
        return $this->present;
    }

    /**
     * @param bool $present
     */
    public function setPresent($present)
    {
        $this->present = $present;
    }

    /**
     * @return integer
     */
    public function getPresentDay()
    {
        return $this->presentDay;
    }

    /**
     * @param integer $presentDay
     */
    public function setPresentDay($presentDay)
    {
        $this->presentDay = $presentDay;
    }

    /**
     * @return bool
     */
    public function isPresentIn()
    {
        return $this->presentIn;
    }

    /**
     * @param bool $presentIn
     */
    public function setPresentIn($presentIn)
    {
        $this->presentIn = $presentIn;
    }

    /**
     * @return bool
     */
    public function isPresentOut()
    {
        return $this->presentOut;
    }

    /**
     * @param bool $presentOut
     */
    public function setPresentOut($presentOut)
    {
        $this->presentOut = $presentOut;
    }

    /**
     * @return Attendance
     */
    public function getAttendance()
    {
        return $this->attendance;
    }

    /**
     * @param Attendance $attendance
     */
    public function setAttendance($attendance)
    {
        $this->attendance = $attendance;
    }


    /**
     * @return EmployeeLeave
     */
    public function getEmployeeLeave()
    {
        return $this->employeeLeave;
    }

    /**
     * @param EmployeeLeave $employeeLeave
     */
    public function setEmployeeLeave($employeeLeave)
    {
        $this->employeeLeave = $employeeLeave;
    }
}
