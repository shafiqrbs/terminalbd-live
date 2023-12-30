<?php

namespace Setting\Bundle\ToolBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AdsTool
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Setting\Bundle\ToolBundle\Repository\AdsToolRepository")
 */
class AdsTool
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
     * @ORM\OneToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\GlobalOption", inversedBy="adsTool")
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/

    protected $globalOption;

    /**
     * @var string
     *
     * @ORM\Column(name="googleServiceID", type="string", length=255, nullable=true)
     */
    private $googleServiceID;

    /**
     * @var string
     *
     * @ORM\Column(name="slotID", type="string", length=255, nullable=true)
     */
    private $slotID;

    /**
     * @var string
     *
     * @ORM\Column(name="siteName", type="string", length=255, nullable=true)
     */
    private $siteName;

    /**
     * @var string
     *
     * @ORM\Column(name="keyword", type="string", length=255, nullable=true)
     */
    private $keyword;

    /**
     * @var string
     *
     * @ORM\Column(name="metaDescription", type="text", nullable=true)
     */
    private $metaDescription;

    /**
     * @var string
     *
     * @ORM\Column(name="googleVerificationCode", type="string", length=255, nullable=true)
     */
    private $googleVerificationCode;

    /**
     * @var string
     *
     * @ORM\Column(name="redirectLang", type="string", length=255, nullable=true)
     */
    private $redirectLang;

    /**
     * @var string
     *
     * @ORM\Column(name="redirectCode", type="text", nullable=true)
     */
    private $redirectCode;

    /**
     * @var string
     *
     * @ORM\Column(name="statCounterID", type="string", length=255, nullable=true)
     */
    private $statCounterID;

    /**
     * @var string
     *
     * @ORM\Column(name="statCounterSecurityCode", type="string", length=255, nullable=true)
     */
    private $statCounterSecurityCode;

    /**
     * @var boolean
     *
     * @ORM\Column(name="statCounterVisible", type="boolean", nullable=true)
     */
    private $statCounterVisible;

    /**
     * @var boolean
     *
     * @ORM\Column(name="redirectTablet", type="boolean", nullable=true)
     */
    private $redirectTablet;


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
     * Set googleServiceID
     *
     * @param string $googleServiceID
     *
     * @return AdsTool
     */
    public function setGoogleServiceID($googleServiceID)
    {
        $this->googleServiceID = $googleServiceID;

        return $this;
    }

    /**
     * Get googleServiceID
     *
     * @return string
     */
    public function getGoogleServiceID()
    {
        return $this->googleServiceID;
    }

    /**
     * Set slotID
     *
     * @param string $slotID
     *
     * @return AdsTool
     */
    public function setSlotID($slotID)
    {
        $this->slotID = $slotID;

        return $this;
    }

    /**
     * Get slotID
     *
     * @return string
     */
    public function getSlotID()
    {
        return $this->slotID;
    }

    /**
     * Set siteName
     *
     * @param string $siteName
     *
     * @return AdsTool
     */
    public function setSiteName($siteName)
    {
        $this->siteName = $siteName;

        return $this;
    }

    /**
     * Get siteName
     *
     * @return string
     */
    public function getSiteName()
    {
        return $this->siteName;
    }

    /**
     * Set keyword
     *
     * @param string $keyword
     *
     * @return AdsTool
     */
    public function setKeyword($keyword)
    {
        $this->keyword = $keyword;

        return $this;
    }

    /**
     * Get keyword
     *
     * @return string
     */
    public function getKeyword()
    {
        return $this->keyword;
    }

    /**
     * Set metaDescription
     *
     * @param string $metaDescription
     *
     * @return AdsTool
     */
    public function setMetaDescription($metaDescription)
    {
        $this->metaDescription = $metaDescription;

        return $this;
    }

    /**
     * Get metaDescription
     *
     * @return string
     */
    public function getMetaDescription()
    {
        return $this->metaDescription;
    }

    /**
     * Set googleVerificationCode
     *
     * @param string $googleVerificationCode
     *
     * @return AdsTool
     */
    public function setGoogleVerificationCode($googleVerificationCode)
    {
        $this->googleVerificationCode = $googleVerificationCode;

        return $this;
    }

    /**
     * Get googleVerificationCode
     *
     * @return string
     */
    public function getGoogleVerificationCode()
    {
        return $this->googleVerificationCode;
    }

    /**
     * Set redirectLang
     *
     * @param string $redirectLang
     *
     * @return AdsTool
     */
    public function setRedirectLang($redirectLang)
    {
        $this->redirectLang = $redirectLang;

        return $this;
    }

    /**
     * Get redirectLang
     *
     * @return string
     */
    public function getRedirectLang()
    {
        return $this->redirectLang;
    }

    /**
     * Set redirectCode
     *
     * @param string $redirectCode
     *
     * @return AdsTool
     */
    public function setRedirectCode($redirectCode)
    {
        $this->redirectCode = $redirectCode;

        return $this;
    }

    /**
     * Get redirectCode
     *
     * @return string
     */
    public function getRedirectCode()
    {
        return $this->redirectCode;
    }

    /**
     * Set statCounterID
     *
     * @param string $statCounterID
     *
     * @return AdsTool
     */
    public function setStatCounterID($statCounterID)
    {
        $this->statCounterID = $statCounterID;

        return $this;
    }

    /**
     * Get statCounterID
     *
     * @return string
     */
    public function getStatCounterID()
    {
        return $this->statCounterID;
    }

    /**
     * Set statCounterSecurityCode
     *
     * @param string $statCounterSecurityCode
     *
     * @return AdsTool
     */
    public function setStatCounterSecurityCode($statCounterSecurityCode)
    {
        $this->statCounterSecurityCode = $statCounterSecurityCode;

        return $this;
    }

    /**
     * Get statCounterSecurityCode
     *
     * @return string
     */
    public function getStatCounterSecurityCode()
    {
        return $this->statCounterSecurityCode;
    }

    /**
     * Set statCounterVisible
     *
     * @param boolean $statCounterVisible
     *
     * @return AdsTool
     */
    public function setStatCounterVisible($statCounterVisible)
    {
        $this->statCounterVisible = $statCounterVisible;

        return $this;
    }

    /**
     * Get statCounterVisible
     *
     * @return boolean
     */
    public function getStatCounterVisible()
    {
        return $this->statCounterVisible;
    }

    /**
     * @return mixed
     */
    public function getGlobalOption()
    {
        return $this->globalOption;
    }

    /**
     * @param mixed $globalOption
     */
    public function setGlobalOption($globalOption)
    {
        $this->globalOption = $globalOption;
    }

    /**
     * @return boolean
     */
    public function isRedirectTablet()
    {
        return $this->redirectTablet;
    }

    /**
     * @param boolean $redirectTablet
     */
    public function setRedirectTablet($redirectTablet)
    {
        $this->redirectTablet = $redirectTablet;
    }
}

