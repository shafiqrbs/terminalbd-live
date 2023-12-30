<?php

namespace Appstore\Bundle\DomainUserBundle\Entity;

use Core\UserBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;

/**
 * DomainUser
 *
 * @ORM\Table("domain_user")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\DomainUserBundle\Repository\DomainUserRepository")
 */
class DomainUser
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
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\GlobalOption", inversedBy="domainUser")
     **/
    protected $globalOption;

    /**
     * @ORM\OneToOne(targetEntity="Core\UserBundle\Entity\User", inversedBy="domainUser")
     **/
    protected $user;

    /**
     * @var float
     *
     * @ORM\Column(name="sales", type="float", nullable = true)
     */
    private $sales;

    /**
     * @var float
     *
     * @ORM\Column(name="monthlySales", type="float", nullable = true)
     */
    private $monthlySales;

    /**
     * @var float
     *
     * @ORM\Column(name="yearlySales", type="float", nullable = true)
     */
    private $yearlySales;


    /**
     * @var int
     *
     * @ORM\Column(name="discountPercent", type="integer", nullable = true)
     */
    private $discountPercent =0;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;


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
     * Set created
     *
     * @param \DateTime $created
     *
     * @return DomainUser
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set status
     *
     * @param boolean $status
     *
     * @return DomainUser
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return boolean
     */
    public function getStatus()
    {
        return $this->status;
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
     * @param User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return float
     */
    public function getSales()
    {
        return $this->sales;
    }

    /**
     * @param float $sales
     */
    public function setSales($sales)
    {
        $this->sales = $sales;
    }

    /**
     * @return float
     */
    public function getMonthlySales()
    {
        return $this->monthlySales;
    }

    /**
     * @param float $monthlySales
     */
    public function setMonthlySales($monthlySales)
    {
        $this->monthlySales = $monthlySales;
    }

    /**
     * @return float
     */
    public function getYearlySales()
    {
        return $this->yearlySales;
    }

    /**
     * @param float $yearlySales
     */
    public function setYearlySales($yearlySales)
    {
        $this->yearlySales = $yearlySales;
    }

    /**
     * @return int
     */
    public function getDiscountPercent()
    {
        return $this->discountPercent;
    }

    /**
     * @param int $discountPercent
     */
    public function setDiscountPercent($discountPercent)
    {
        $this->discountPercent = $discountPercent;
    }




}

