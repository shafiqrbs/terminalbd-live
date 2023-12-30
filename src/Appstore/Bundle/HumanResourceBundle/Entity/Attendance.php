<?php

namespace Appstore\Bundle\HumanResourceBundle\Entity;

use Core\UserBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Gedmo\Mapping\Annotation as Gedmo;
/**
 * Attendance
 *
 * @ORM\Table(name="hr_attendance")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\HumanResourceBundle\Repository\AttendanceRepository")
 */
class Attendance
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
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\GlobalOption", inversedBy="attendance")
     **/
    protected $globalOption;

    /**
     * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User", inversedBy="attendance")
     **/
    private  $employee;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\HumanResourceBundle\Entity\DailyAttendance", mappedBy="attendance")
     **/
    protected $dailyAttendance;

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
     * @var string
     *
     * @ORM\Column(name="totalDay", type="smallint", length=3, nullable =true)
     */
    private $totalDay;

    /**
     * @var string
     *
     * @ORM\Column(name="weekend", type="smallint", length=3, nullable =true)
     */
    private $weekend;

    /**
     * @var integer
     *
     * @ORM\Column(name="present", type="smallint", length=3, nullable =true)
     */
    private $present;

    /**
     * @var integer
     *
     * @ORM\Column(name="absence", type="smallint", length=3, nullable =true)
     */
     private $absence;

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
     * @return int
     */
    public function getAbsence()
    {
        return $this->absence;
    }

    /**
     * @param int $absence
     */
    public function setAbsence($absence)
    {
        $this->absence = $absence;
    }

    /**
     * @return int
     */
    public function getPresent()
    {
        return $this->present;
    }

    /**
     * @param int $present
     */
    public function setPresent($present)
    {
        $this->present = $present;
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
    public function getWeekend()
    {
        return $this->weekend;
    }

    /**
     * @param string $weekend
     */
    public function setWeekend($weekend)
    {
        $this->weekend = $weekend;
    }

    /**
     * @return string
     */
    public function getTotalDay()
    {
        return $this->totalDay;
    }

    /**
     * @param string $totalDay
     */
    public function setTotalDay($totalDay)
    {
        $this->totalDay = $totalDay;
    }

    /**
     * @return DailyAttendance
     */
    public function getDailyAttendance()
    {
        return $this->dailyAttendance;
    }

    public function monthlyPresentDays()
    {
        $presents = array();

        /* @var $row DailyAttendance */

        foreach ($this->getDailyAttendance() as $row ){
            $presents[] = $row->getPresentDay();
        }
        return $presents;
    }
}
