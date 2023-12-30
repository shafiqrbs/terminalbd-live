<?php

namespace Setting\Bundle\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Setting\Bundle\AppearanceBundle\Entity\FeatureWidget;
use Setting\Bundle\LocationBundle\Entity\Location;
use Setting\Bundle\MediaBundle\Entity\PhotoGallery;
use Setting\Bundle\ToolBundle\Entity\Module;
use Setting\Bundle\ToolBundle\Entity\PricePrefix;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * Page
 *
 * @ORM\Table(name="page")
 * @ORM\Entity(repositoryClass="Setting\Bundle\ContentBundle\Repository\PageRepository")
 */
class Page
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
     * @ORM\ManyToOne(targetEntity="Page", inversedBy="children", cascade={"detach","merge"})
     * @ORM\JoinColumn(name="parent", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $parent;

    /**
     * @ORM\OneToMany(targetEntity="Page" , mappedBy="parent")
     * @ORM\OrderBy({"name" = "ASC"})
     **/
    private $children;


    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable = true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="skuCode", type="string", length=50, nullable = true)
     */
    private $skuCode;

    /**
     * @var string
     *
     * @ORM\Column(name="menu", type="string", length=255, nullable = true)
     */
    private $menu;


    /**
     * @Gedmo\Slug(handlers={
     *      @Gedmo\SlugHandler(class="Gedmo\Sluggable\Handler\TreeSlugHandler", options={
     *          @Gedmo\SlugHandlerOption(name="parentRelationField", value="globalOption"),
     *          @Gedmo\SlugHandlerOption(name="separator", value="-")
     *      })
     * }, fields={"name"})
     * @Doctrine\ORM\Mapping\Column()
     */
    private $slug;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text" , nullable = true)
     */
    private $content;

    /**
     * @var string
     *
     * @ORM\Column(name="shortDescription", type="text" , nullable = true)
     */
    private $shortDescription;

    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="boolean")
     */
    private $status = true;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isSubPage", type="boolean", nullable = true )
     */
    private $isSubPage = false;


    /**
     * @var boolean
     *
     * @ORM\Column(name="removeImage", type="boolean", nullable = true )
     */
    private $removeImage = false;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updated_at", type="datetime")
     */
    private $updated;

    /**
     * @var string
     *
     * @ORM\Column(name="authorName", type="string", length=150, nullable=true)
     */
    private $authorName;

    /**
     * @var string
     *
     * @ORM\Column(name="price", type="string", length = 20, nullable=true)
     */
    private $price = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="designation", type="string", length=150, nullable=true)
     */
    private $designation;

    /**
     * @var string
     *
     * @ORM\Column(name="organization", type="string", length=150, nullable=true)
     */
    private $organization;

    /**
     * @var string
     *
     * @ORM\Column(name="facebook", type="string", length=150, nullable=true)
     */
    private $facebook;


    /**
     * @var string
     *
     * @ORM\Column(name="twitter", type="string", length=150, nullable=true)
     */
    private $twitter;


    /**
     * @var string
     *
     * @ORM\Column(name="linkedIn", type="string", length=150,  nullable=true)
     */
    private $linkedIn;

    /**
     * @var string
     *
     * @ORM\Column(name="gmail", type="string", length=150, nullable=true)
     */
    private $gmail;

    /**
     * @var string
     *
     * @ORM\Column(name="youtube", type="string", length=255, nullable=true)
     */
    private $youtube;

    /**
     * @var string
     *
     * @ORM\Column(name="contactPerson", type="string", length=100, nullable=true)
     */
    private $contactPerson;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=15, nullable=true)
     */
    private $phone;

    /**
     * @var string
     *
     * @ORM\Column(name="mobile", type="string", length=15, nullable=true)
     */
    private $mobile;

    /**
     * @var string
     *
     * @ORM\Column(name="fax", type="string", length=15, nullable=true)
     */
    private $fax;

    /**
     * @var string
     *
     * @ORM\Column(name="additionalPhone", type="string", length=255, nullable=true)
     */
    private $additionalPhone;

    /**
     * @var datetime
     *
     * @ORM\Column(name="startDate", type="datetime", nullable=true)
     */
    private $startDate;

    /**
     * @var datetime
     *
     * @ORM\Column(name="endDate", type="datetime", nullable=true)
     */
    private $endDate;

    /**
     * @var string
     *
     * @ORM\Column(name="startHour", type="string", length=255 , nullable = true)
     */
    private $startHour;

    /**
     * @var string
     *
     * @ORM\Column(name="endHour", type="string", length=255 , nullable = true)
     */
    private $endHour;


    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255 , nullable=true)
     */
    private $email;

    /**
     * @var text
     *
     * @ORM\Column(name="address", type="text",  nullable=true)
     */
    private $address;

    /**
     * @var text
     *
     * @ORM\Column(name="googleMap", type="text", nullable=true )
     */
    private $googleMap;

    /**
     * @var string
     *
     * @ORM\Column(name="website", type="string", length=255, nullable=true)
     */
    private $website;


    /**
     * @Gedmo\Blameable(on="create")
     * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User", inversedBy="pages" )
     **/
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\Module", inversedBy="pages")
     **/
    protected $module;

    /**
     * @Gedmo\Blameable(on="create")
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\GlobalOption", inversedBy="pages" )
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    protected $globalOption;

    /**
     * @ORM\OneToMany(targetEntity="Setting\Bundle\ContentBundle\Entity\PageModule", mappedBy="page" )
     **/
    protected $pageModules;

    /**
     * @ORM\ManyToMany(targetEntity="Setting\Bundle\AppearanceBundle\Entity\SidebarWidgetPanel", mappedBy="page" )
     **/
    protected $sidebarWidgetPanel;

    /**
     * @ORM\ManyToMany(targetEntity="Setting\Bundle\AppearanceBundle\Entity\FeatureWidget", mappedBy="page" )
     **/
    protected $featureWidgets;

    /**
     * @ORM\OneToMany(targetEntity="Setting\Bundle\AppearanceBundle\Entity\Feature", mappedBy="page" , cascade={"persist", "remove"})
     */
    protected $sidebarFeature;

    /**
     * @ORM\OneToMany(targetEntity="Setting\Bundle\ContentBundle\Entity\HomeSlider", mappedBy="page" )
     **/
    protected $homeSliders;

    /**
     * @ORM\ManyToMany(targetEntity="Setting\Bundle\ContentBundle\Entity\ModuleCategory", inversedBy="pages")
     **/
    protected $moduleCategory;

    /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ContentBundle\Entity\ModuleCategory", inversedBy="page")
     **/
    protected $category;

    /**
     * @ORM\OneToMany(targetEntity="Setting\Bundle\ContentBundle\Entity\PageMeta", mappedBy="page" )
     * @ORM\OrderBy({"id" = "DESC"})
     **/
    protected $pageMetas;

    /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\Icon", inversedBy="page")
     **/
    protected $icon;

    /**
     * @ORM\OneToMany(targetEntity="Setting\Bundle\MediaBundle\Entity\PageFile", mappedBy="page")
     * @ORM\OrderBy({"id" = "DESC"})
     */
    protected $pageFiles;

    /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\MediaBundle\Entity\PhotoGallery", inversedBy="pages")
     */
    protected $photoGallery;

    /**
     * @ORM\OneToOne(targetEntity="Setting\Bundle\AppearanceBundle\Entity\Menu", mappedBy="page" , cascade={"persist", "remove"} )
     **/
    protected  $nav;


    /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\LocationBundle\Entity\Location", inversedBy="page")
     **/

    protected $location;


    /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\PricePrefix", inversedBy="page")
     */
    protected $pricePrefix;

    /**
     * @var text
     *
     * @ORM\Column(name="latitude",type="string", length=100, type="text", nullable=true )
     */
    private $latitude;

    /**
     * @var text
     *
     * @ORM\Column(name="longitude", type="string", length=100, nullable=true )
     */
    private $longitude;

     /**
     * @var string
     *
     * @ORM\Column(name="galleryPosition", type="string", length=20, nullable=true )
     */
    private $galleryPosition;

    /**
     * @var string
     *
     * @ORM\Column(name="galleryType", type="string", length=20, nullable=true )
     */
    private $galleryType;


    /**
     * @var int
     *
     * @ORM\Column(name="ordering", type="smallint", length=2,  nullable=true )
     */
    private $ordering = 0;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $path;

    /**
     * @Assert\File(maxSize="8388608")
     */
    protected $file;

    /**
     * @var boolean
     *
     * @ORM\Column(name="feature", type="boolean", nullable=true)
     */
    private $feature = false;

    /**
     * @var text
     *
     * @ORM\Column(name="eventType",type="string", length=50, type="text", nullable=true )
     */
    private $eventType;

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
     * @return Page
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
     * Set menu
     *
     * @param string $menu
     * @return Page
     */
    public function setMenu($menu)
    {
        $this->menu = $menu;

        return $this;
    }

    /**
     * Get menu
     *
     * @return string
     */
    public function getMenu()
    {
        return $this->menu;
    }

    /**
     * Set content
     *
     * @param string $content
     * @return Page
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
     * Set status
     *
     * @param boolean $status
     * @return Page
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
     * Set created
     *
     * @param \DateTime $created
     * @return Page
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
     * @param mixed $user
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
     * @param PhotoGallery $photoGallery
     */
    public function setPhotoGallery($photoGallery)
    {
        $this->photoGallery = $photoGallery;
    }

    /**
     * @return PhotoGallery
     */
    public function getPhotoGallery()
    {
        return $this->photoGallery;
    }

    /**
     * Sets file.
     *
     * @param Page $file
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
    }

    /**
     * Get file.
     *
     * @return Page
     */
    public function getFile()
    {
        return $this->file;
    }

    public function getAbsolutePath()
    {
        return null === $this->path
            ? null
            : $this->getUploadRootDir().'/'.$this->path;
    }

    public function getWebPath()
    {
        return null === $this->path
            ? null
            : $this->getUploadDir().'/' . $this->path;
    }



    protected function getUploadRootDir()
    {
        return __DIR__.'/../../../../../web/'.$this->getUploadDir();
    }

    protected function getUploadDir()
    {
        return 'uploads/domain/'.$this->getGlobalOption()->getId().'/content';
    }

    public function removeUpload()
    {
        if ($file = $this->getAbsolutePath()) {
            unlink($file);
            $this->path = null ;
        }
    }

    public function upload()
    {
        // the file property can be empty if the field is not required
        if (null === $this->getFile()) {
            return;
        }

        // use the original file name here but you should
        // sanitize it at least to avoid any security issues

        // move takes the target directory and then the
        // target filename to move to
        $filename = date('YmdHmi') . "_" . $this->getFile()->getClientOriginalName();
        $this->getFile()->move(
            $this->getUploadRootDir(),
            $filename
        );

        // set the path property to the filename where you've saved the file
        $this->path = $filename ;

        // clean up the file property as you won't need it anymore
        $this->file = null;
    }

    /**
     * @return mixed
     */
    public function getNav()
    {
        return $this->nav;
    }

    /**
     * @param mixed $nav
     */
    public function setNav($nav)
    {
        $this->nav = $nav;
    }

    /**
     * @return mixed
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param mixed $parent
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
    }


    /**
     * @return mixed
     */
    public function getChildren()
    {
        return $this->children;
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
     * @return mixed
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param mixed $slug
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
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
    public function getIsSubPage()
    {
        return $this->isSubPage;
    }

    /**
     * @param boolean $isSubPage
     */
    public function setIsSubPage($isSubPage)
    {
        $this->isSubPage = $isSubPage;
    }

    /**
     * @return PageModule
     */
    public function getPageModules()
    {
        return $this->pageModules;
    }


    /**
     * @return PageMeta
     */
    public function getPageMetas()
    {
        return $this->pageMetas;
    }

    /**
     * @return string
     */
    public function getAuthorName()
    {
        return $this->authorName;
    }

    /**
     * @param string $authorName
     */
    public function setAuthorName($authorName)
    {
        $this->authorName = $authorName;
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
     * @return string
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * @param string $organization
     */
    public function setOrganization($organization)
    {
        $this->organization = $organization;
    }

    /**
     * @return string
     */
    public function getFacebook()
    {
        return $this->facebook;
    }

    /**
     * @param string $facebook
     */
    public function setFacebook($facebook)
    {
        $this->facebook = $facebook;
    }

    /**
     * @return string
     */
    public function getTwitter()
    {
        return $this->twitter;
    }

    /**
     * @param string $twitter
     */
    public function setTwitter($twitter)
    {
        $this->twitter = $twitter;
    }

    /**
     * @return string
     */
    public function getLinkedIn()
    {
        return $this->linkedIn;
    }

    /**
     * @param string $linkedIn
     */
    public function setLinkedIn($linkedIn)
    {
        $this->linkedIn = $linkedIn;
    }

    /**
     * @return string
     */
    public function getGmail()
    {
        return $this->gmail;
    }

    /**
     * @param string $gmail
     */
    public function setGmail($gmail)
    {
        $this->gmail = $gmail;
    }

    /**
     * @return string
     */
    public function getYoutube()
    {
        return $this->youtube;
    }

    /**
     * @param string $youtube
     */
    public function setYoutube($youtube)
    {
        $this->youtube = $youtube;
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
    public function getMobile()
    {
        return $this->mobile;
    }

    /**
     * @param string $mobile
     */
    public function setMobile($mobile)
    {
        $this->mobile = $mobile;
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
     * @return datetime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @param datetime $startDate
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;
    }

    /**
     * @return datetime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @param datetime $endDate
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;
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
     * @return text
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param text $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
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
     * @return string
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * @param string $website
     */
    public function setWebsite($website)
    {
        $this->website = $website;
    }

    /**
     * @return mixed
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * @param mixed $icon
     */
    public function setIcon($icon)
    {
        $this->icon = $icon;
    }

    /**
     * @return mixed
     */
    public function getPageFiles()
    {
        return $this->pageFiles;
    }

    /**
     * @param mixed $pageFiles
     */
    public function setPageFiles($pageFiles)
    {
        $this->pageFiles = $pageFiles;
    }

    /**
     * @return Module
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * @param Module $module
     */
    public function setModule($module)
    {
        $this->module = $module;
    }

    /**
     * @return ModuleCategory
     */
    public function getModuleCategory()
    {
        return $this->moduleCategory;
    }

    /**
     * @param ModuleCategory $moduleCategory
     */
    public function setModuleCategory($moduleCategory)
    {
        $this->moduleCategory = $moduleCategory;
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
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
     * @return HomeSlider
     */
    public function getHomeSliders()
    {
        return $this->homeSliders;
    }

    /**
     * @return FeatureWidget
     */
    public function getFeatureWidgets()
    {
        return $this->featureWidgets;
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
     * @return text
     */
    public function getEventType()
    {
        return $this->eventType;
    }

    /**
     * @param text $eventType
     * Ongoing
     * Upcoming
     */
    public function setEventType($eventType)
    {
        $this->eventType = $eventType;
    }

    /**
     * @return ModuleCategory
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param ModuleCategory $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
    }

    /**
     * @return string
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param string $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }

    /**
     * @return bool
     */
    public function isFeature()
    {
        return $this->feature;
    }

    /**
     * @param bool $feature
     */
    public function setFeature($feature)
    {
        $this->feature = $feature;
    }

    /**
     * @return string
     */
    public function getShortDescription()
    {
        return $this->shortDescription;
    }

    /**
     * @param string $shortDescription
     */
    public function setShortDescription($shortDescription)
    {
        $this->shortDescription = $shortDescription;
    }

    /**
     * @return bool
     */
    public function isRemoveImage()
    {
        return $this->removeImage;
    }

    /**
     * @param bool $removeImage
     */
    public function setRemoveImage($removeImage)
    {
        $this->removeImage = $removeImage;
    }

    /**
     * @return string
     */
    public function getSkuCode()
    {
        return $this->skuCode;
    }

    /**
     * @param string $skuCode
     */
    public function setSkuCode($skuCode)
    {
        $this->skuCode = $skuCode;
    }

    /**
     * @return PricePrefix
     */
    public function getPricePrefix()
    {
        return $this->pricePrefix;
    }

    /**
     * @param PricePrefix $pricePrefix
     */
    public function setPricePrefix($pricePrefix)
    {
        $this->pricePrefix = $pricePrefix;
    }

    /**
     * @return int
     */
    public function getOrdering()
    {
        return $this->ordering;
    }

    /**
     * @param int $ordering
     */
    public function setOrdering($ordering)
    {
        $this->ordering = $ordering;
    }

    /**
     * @return string
     */
    public function getGalleryPosition()
    {
        return $this->galleryPosition;
    }

    /**
     * @param string $galleryPosition
     */
    public function setGalleryPosition($galleryPosition)
    {
        $this->galleryPosition = $galleryPosition;
    }

    /**
     * @return string
     */
    public function getGalleryType()
    {
        return $this->galleryType;
    }

    /**
     * @param string $galleryType
     */
    public function setGalleryType($galleryType)
    {
        $this->galleryType = $galleryType;
    }

    /**
     * @return mixed
     */
    public function getPath()
    {
        return $this->path;
    }

}
