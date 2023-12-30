<?php

namespace Setting\Bundle\ToolBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ApplicationPricing
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Setting\Bundle\ToolBundle\Entity\ApplicationPricingRepository")
 */
class ApplicationPricing
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
     * @ORM\OneToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\AppModule", inversedBy="applicationPricing")
     * @ORM\OrderBy({"name" = "DESC"})
     */
     protected $appModule;

    /**
     * @ORM\OneToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\SyndicateModule", inversedBy="applicationPricing")
     * @ORM\OrderBy({"name" = "DESC"})
     */
     protected $syndicateModule;


    /**
     * @var float
     *
     * @ORM\Column(name="price", type="float")
     */
    private $price;

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
     * Set price
     *
     * @param float $price
     *
     * @return ApplicationPricing
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set status
     *
     * @param boolean $status
     *
     * @return ApplicationPricing
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
     * @return mixed
     */
    public function getAppModule()
    {
        return $this->appModule;
    }

    /**
     * @param mixed $appModule
     */
    public function setAppModule($appModule)
    {
        $this->appModule = $appModule;
    }

    /**
     * @return mixed
     */
    public function getSyndicateModule()
    {
        return $this->syndicateModule;
    }

    /**
     * @param mixed $syndicateModule
     */
    public function setSyndicateModule($syndicateModule)
    {
        $this->syndicateModule = $syndicateModule;
    }
}

