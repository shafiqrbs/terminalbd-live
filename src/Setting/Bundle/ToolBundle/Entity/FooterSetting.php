<?php

namespace Setting\Bundle\ToolBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FooterSetting
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Setting\Bundle\ToolBundle\Repository\FooterSettingRepository")
 */
class FooterSetting
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
     * @ORM\OneToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\GlobalOption", inversedBy="footerSetting")
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/

    protected $globalOption;


    /**
     * @var string
     *
     * @ORM\Column(name="copyRight", type="string", length=255, nullable=true)
     */
    private $copyRight;

    /**
     * @var boolean
     *
     * @ORM\Column(name="displayWebsite", type="boolean")
     */
    private $displayWebsite = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="socialMedia", type="boolean")
     */
    private $socialMedia = false;

 /**
     * @var boolean
     *
     * @ORM\Column(name="turnOffBranding", type="boolean")
     */
    private $turnOffBranding = false;

 /**
     * @var boolean
     *
     * @ORM\Column(name="addressHomePage", type="boolean")
     */
    private $addressHomePage = false;

 /**
     * @var boolean
     *
     * @ORM\Column(name="addressSubPage", type="boolean")
     */
    private $addressSubPage = false;

 /**
     * @var boolean
     *
     * @ORM\Column(name="addressIconPage", type="boolean")
     */
    private $addressIconPage = false;

 /**
     * @var boolean
     *
     * @ORM\Column(name="phoneHomePage", type="boolean")
     */
    private $phoneHomePage = false;

 /**
     * @var boolean
     *
     * @ORM\Column(name="phoneSubPage", type="boolean")
     */
    private $phoneSubPage = false;

 /**
     * @var boolean
     *
     * @ORM\Column(name="phoneDisplay", type="boolean")
     */
    private $phoneDisplay = false;



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
     * Set copyRight
     *
     * @param string $copyRight
     *
     * @return FooterSetting
     */
    public function setCopyRight($copyRight)
    {
        $this->copyRight = $copyRight;

        return $this;
    }

    /**
     * Get copyRight
     *
     * @return string
     */
    public function getCopyRight()
    {
        return $this->copyRight;
    }

    /**
     * @return boolean
     */
    public function isPhoneDisplay()
    {
        return $this->phoneDisplay;
    }

    /**
     * @param boolean $phoneDisplay
     */
    public function setPhoneDisplay($phoneDisplay)
    {
        $this->phoneDisplay = $phoneDisplay;
    }

    /**
     * @return boolean
     */
    public function isDisplayWebsite()
    {
        return $this->displayWebsite;
    }

    /**
     * @param boolean $displayWebsite
     */
    public function setDisplayWebsite($displayWebsite)
    {
        $this->displayWebsite = $displayWebsite;
    }

    /**
     * @return boolean
     */
    public function isTurnOffBranding()
    {
        return $this->turnOffBranding;
    }

    /**
     * @param boolean $turnOffBranding
     */
    public function setTurnOffBranding($turnOffBranding)
    {
        $this->turnOffBranding = $turnOffBranding;
    }

    /**
     * @return boolean
     */
    public function isAddressHomePage()
    {
        return $this->addressHomePage;
    }

    /**
     * @param boolean $addressHomePage
     */
    public function setAddressHomePage($addressHomePage)
    {
        $this->addressHomePage = $addressHomePage;
    }

    /**
     * @return boolean
     */
    public function isAddressSubPage()
    {
        return $this->addressSubPage;
    }

    /**
     * @param boolean $addressSubPage
     */
    public function setAddressSubPage($addressSubPage)
    {
        $this->addressSubPage = $addressSubPage;
    }

    /**
     * @return boolean
     */
    public function isAddressIconPage()
    {
        return $this->addressIconPage;
    }

    /**
     * @param boolean $addressIconPage
     */
    public function setAddressIconPage($addressIconPage)
    {
        $this->addressIconPage = $addressIconPage;
    }

    /**
     * @return boolean
     */
    public function isPhoneHomePage()
    {
        return $this->phoneHomePage;
    }

    /**
     * @param boolean $phoneHomePage
     */
    public function setPhoneHomePage($phoneHomePage)
    {
        $this->phoneHomePage = $phoneHomePage;
    }

    /**
     * @return boolean
     */
    public function isPhoneSubPage()
    {
        return $this->phoneSubPage;
    }

    /**
     * @param boolean $phoneSubPage
     */
    public function setPhoneSubPage($phoneSubPage)
    {
        $this->phoneSubPage = $phoneSubPage;
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
    public function isSocialMedia()
    {
        return $this->socialMedia;
    }

    /**
     * @param boolean $socialMedia
     */
    public function setSocialMedia($socialMedia)
    {
        $this->socialMedia = $socialMedia;
    }


}

