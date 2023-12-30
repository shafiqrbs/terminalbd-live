<?php

namespace Appstore\Bundle\HumanResourceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;

/**
 * Weekend
 *
 * @ORM\Table(name="hr_weekend")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\HumanResourceBundle\Repository\WeekendRepository")
 */
class Weekend
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
     * @Gedmo\Blameable(on="create")
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\GlobalOption", inversedBy="weekend" )
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    protected $globalOption;

    /**
     * @var array
     *
     * @ORM\Column(name="weekendDate", type="array" , nullable=true)
     */
    private $weekendDate;

    /**
     * @var text
     *
     * @ORM\Column(name="content", type="text", nullable=true)
     */
    private $content;

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
     * Get blackoutDate
     *
     * @return string
     */
    public function getOffWeekendDateDate()
    {
        return explode(',' , $this->weekendDate);
    }

    /**
     * @return text
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param  $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }


    /**
     * @return boolean
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param boolean $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
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
     * @return array
     */
    public function getWeekendDate()
    {
        return $this->weekendDate;
    }

    /**
     * @param array $weekendDate
     */
    public function setWeekendDate($weekendDate)
    {
        $this->weekendDate = $weekendDate;
    }
}
