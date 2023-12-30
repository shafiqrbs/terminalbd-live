<?php

namespace Setting\Bundle\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Setting\Bundle\LocationBundle\Entity\Location;

/**
 * ContactPage
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Setting\Bundle\ContentBundle\Repository\ContactPageRepository")
 */
class ContactPage
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
     * @ORM\OneToOne(targetEntity="Core\UserBundle\Entity\User", inversedBy="contactPage")
     **/

    protected $user;

    /**
     * @ORM\OneToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\GlobalOption", inversedBy="contactPage")
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/

    protected $globalOption;

    /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\LocationBundle\Entity\Location", inversedBy="contactPages")
     **/

    protected $location;

    /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\LocationBundle\Entity\Location", inversedBy="contactPageThanas")
     **/

    protected $thana;

    /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\LocationBundle\Entity\Location", inversedBy="contactPageDistricts")
     **/

    protected $district;


    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @var text
     *
     * @ORM\Column(name="content", type="text", nullable=true )
     */
    private $content;

    /**
     * @var text
     *
     * @ORM\Column(name="address1", type="text", nullable=true)
     */
    private $address1;

    /**
     * @var text
     *
     * @ORM\Column(name="address2", type="text", nullable=true)
     */
    private $address2;

    /**
     * @var string
     *
     * @ORM\Column(name="additionalPhone", type="string", length=255, nullable=true)
     */
    private $additionalPhone;

    /**
     * @var string
     *
     * @ORM\Column(name="additionalEmail", type="string", length=255, nullable=true)
     */
    private $additionalEmail;

    /**
     * @var string
     *
     * @ORM\Column(name="postalCode", type="string", length=255, nullable=true)
     */
    private $postalCode;

    /**
     * @var string
     *
     * @ORM\Column(name="fax", type="string", length=255, nullable=true)
     */
    private $fax;

    /**
     * @var string
     *
     * @ORM\Column(name="contactPerson", type="string", length=255 , nullable=true)
     */
    private $contactPerson;

    /**
     * @var string
     *
     * @ORM\Column(name="designation", type="string", length=255, nullable=true)
     */
    private $designation;

    /**
     * @var string
     *
     * @ORM\Column(name="contactMobile", type="string", length=255, nullable=true)
     */
    private $contactMobile;

    /**
     * @var string
     *
     * @ORM\Column(name="contactEmail", type="string", length=255, nullable=true)
     */
    private $contactEmail;

    /**
     * @var string
     *
     * @ORM\Column(name="startHour", type="string", length=10, nullable=true)
     */
    private $startHour;

    /**
     * @var string
     *
     * @ORM\Column(name="endHour", type="string", length=10, nullable=true)
     */
    private $endHour;

    /**
     * @var array
     *
     * @ORM\Column(name="weeklyOffDay", type="array", nullable=true)
     */
    private $weeklyOffDay;


    /**
     * @var boolean
     *
     * @ORM\Column(name="isContactForm", type="boolean", nullable=true)
     */
    private $isContactForm = false;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     */
    private $email;


    /**
     * @var boolean
     *
     * @ORM\Column(name="askForSms", type="boolean", nullable=true)
     */
    private $askForSms = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="askForEmail", type="boolean", nullable=true)
     */
    private $askForEmail = false;

     /**
     * @var boolean
     *
     * @ORM\Column(name="displyPhone", type="boolean", nullable=true)
     */
    private $displayPhone = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="displayEmail", type="boolean", nullable=true)
     */
    private $displayEmail = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isBranch", type="boolean" ,nullable = true)
     */
    private $isBranch = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isMap", type="boolean" ,nullable=true)
     */
    private $isMap;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isBaseInformation", type="boolean" ,nullable=false )
     */
    private $isBaseInformation = true;

    /**
     * @var text
     *
     * @ORM\Column(name="googleMap", type="text", nullable=true )
     */
    private $googleMap;

    /**
     * @var text
     *
     * @ORM\Column(name="latitude",type="string", length=100, type="text", nullable=true )
     */
    private $latitude;

    /**
     * @var text
     *
     * @ORM\Column(name="longitude", type="string", length=100, type="text", nullable=true )
     */
    private $longitude;

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
     * Set name
     *
     * @param string $name
     * @return ContactPage
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set content
     *
     * @param string $content
     * @return ContactPage
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string 
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return boolean
     */
    public function getIsContactForm()
    {
        return $this->isContactForm;
    }

    /**
     * @param boolean $isContactForm
     */
    public function setIsContactForm($isContactForm)
    {
        $this->isContactForm = $isContactForm;
    }

    /**
     * @return boolean
     */
    public function getIsBranch()
    {
        return $this->isBranch;
    }

    /**
     * @param boolean $isBranch
     */
    public function setIsBranch($isBranch)
    {
        $this->isBranch = $isBranch;
    }

    /**
     * @return boolean
     */
    public function getIsMap()
    {
        return $this->isMap;
    }

    /**
     * @param boolean $isMap
     */
    public function setIsMap($isMap)
    {
        $this->isMap = $isMap;
    }

    /**
     * @return boolean
     */
    public function getIsBaseInformation()
    {
        return $this->isBaseInformation;
    }

    /**
     * @param boolean $isBaseInformation
     */
    public function setIsBaseInformation($isBaseInformation)
    {
        $this->isBaseInformation = $isBaseInformation;
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
     * @return text
     */
    public function getAddress1()
    {
        return $this->address1;
    }

    /**
     * @param text $address1
     */
    public function setAddress1($address1)
    {
        $this->address1 = $address1;
    }

    /**
     * @return text
     */
    public function getAddress2()
    {
        return $this->address2;
    }

    /**
     * @param text $address2
     */
    public function setAddress2($address2)
    {
        $this->address2 = $address2;
    }

    /**
     * @return string
     */
    public function getAdditionalPhone()
    {
        return $this->additionalPhone;
    }

    /**
     * @param string $additionalPhone
     */
    public function setAdditionalPhone($additionalPhone)
    {
        $this->additionalPhone = $additionalPhone;
    }

    /**
     * @return string
     */
    public function getAdditionalEmail()
    {
        return $this->additionalEmail;
    }

    /**
     * @param string $additionalEmail
     */
    public function setAdditionalEmail($additionalEmail)
    {
        $this->additionalEmail = $additionalEmail;
    }

    /**
     * @return string
     */
    public function getPostalCode()
    {
        return $this->postalCode;
    }

    /**
     * @param string $postalCode
     */
    public function setPostalCode($postalCode)
    {
        $this->postalCode = $postalCode;
    }

    /**
     * @return string
     */
    public function getFax()
    {
        return $this->fax;
    }

    /**
     * @param string $fax
     */
    public function setFax($fax)
    {
        $this->fax = $fax;
    }

    /**
     * @return string
     */
    public function getContactPerson()
    {
        return $this->contactPerson;
    }

    /**
     * @param string $contactPerson
     */
    public function setContactPerson($contactPerson)
    {
        $this->contactPerson = $contactPerson;
    }

    /**
     * @return string
     */
    public function getDesignation()
    {
        return $this->designation;
    }

    /**
     * @param string $designation
     */
    public function setDesignation($designation)
    {
        $this->designation = $designation;
    }

    /**
     * @return mixed
     */
    public function getThana()
    {
        return $this->thana;
    }

    /**
     * @param mixed $thana
     */
    public function setThana($thana)
    {
        $this->thana = $thana;
    }

    /**
     * @return mixed
     */
    public function getDistrict()
    {
        return $this->district;
    }

    /**
     * @param mixed $district
     */
    public function setDistrict($district)
    {
        $this->district = $district;
    }

    /**
     * @return string
     */
    public function getWeeklyOffDay()
    {
        return $this->weeklyOffDay;
    }

    /**
     * @param string $weeklyOffDay
     */
    public function setWeeklyOffDay($weeklyOffDay)
    {
        $this->weeklyOffDay = $weeklyOffDay;
    }

    /**
     * @return string
     */
    public function getEndHour()
    {
        return $this->endHour;
    }

    /**
     * @param string $endHour
     */
    public function setEndHour($endHour)
    {
        $this->endHour = $endHour;
    }

    /**
     * @return string
     */
    public function getStartHour()
    {
        return $this->startHour;
    }

    /**
     * @param string $startHour
     */
    public function setStartHour($startHour)
    {
        $this->startHour = $startHour;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return boolean
     */
    public function isAskForSms()
    {
        return $this->askForSms;
    }

    /**
     * @param boolean $askForSms
     */
    public function setAskForSms($askForSms)
    {
        $this->askForSms = $askForSms;
    }

    /**
     * @return boolean
     */
    public function isAskForEmail()
    {
        return $this->askForEmail;
    }

    /**
     * @param boolean $askForEmail
     */
    public function setAskForEmail($askForEmail)
    {
        $this->askForEmail = $askForEmail;
    }

    /**
     * @return boolean
     */
    public function isDisplayEmail()
    {
        return $this->displayEmail;
    }

    /**
     * @param boolean $displayEmail
     */
    public function setDisplayEmail($displayEmail)
    {
        $this->displayEmail = $displayEmail;
    }

    /**
     * @return boolean
     */
    public function isDisplayPhone()
    {
        return $this->displayPhone;
    }

    /**
     * @param boolean $displayPhone
     */
    public function setDisplayPhone($displayPhone)
    {
        $this->displayPhone = $displayPhone;
    }

    /**
     * @return text
     */
    public function getGoogleMap()
    {
        return $this->googleMap;
    }

    /**
     * @param text $googleMap
     */
    public function setGoogleMap($googleMap)
    {
        $this->googleMap = $googleMap;
    }

    /**
     * @return text
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * @param text $latitude
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;
    }

    /**
     * @return text
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * @param text $longitude
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;
    }

    /**
     * @return Location
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param Location $location
     */
    public function setLocation($location)
    {
        $this->location = $location;
    }

    /**
     * @return string
     */
    public function getContactEmail()
    {
        return $this->contactEmail;
    }

    /**
     * @param string $contactEmail
     */
    public function setContactEmail($contactEmail)
    {
        $this->contactEmail = $contactEmail;
    }

    /**
     * @return string
     */
    public function getContactMobile()
    {
        return $this->contactMobile;
    }

    /**
     * @param string $contactMobile
     */
    public function setContactMobile($contactMobile)
    {
        $this->contactMobile = $contactMobile;
    }
}
