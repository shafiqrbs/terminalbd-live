<?php

namespace Setting\Bundle\AppearanceBundle\Entity;

use Appstore\Bundle\EcommerceBundle\Entity\Discount;
use Appstore\Bundle\EcommerceBundle\Entity\ItemBrand;
use Appstore\Bundle\EcommerceBundle\Entity\Promotion;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Product\Bundle\ProductBundle\Entity\Category;
use Setting\Bundle\ContentBundle\Entity\Page;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Feature
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Setting\Bundle\AppearanceBundle\Repository\FeatureRepository")
 */
class Feature
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
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\GlobalOption", inversedBy="features" )
     **/
    private  $globalOption;

    /**
     * @ORM\OneToMany(targetEntity="Setting\Bundle\AppearanceBundle\Entity\FeatureWidgetItem", mappedBy="feature" )
     **/
    private  $featureWidgetItems;

    /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ContentBundle\Entity\Page", inversedBy="sidebarFeature" )
     **/
    private  $page;

    /**
     * @ORM\ManyToMany(targetEntity="Setting\Bundle\AppearanceBundle\Entity\SidebarWidgetPanel", mappedBy="feature" )
     **/
    private  $sidebarWidgetPanel;

    /**
     * @ORM\ManyToOne(targetEntity="Product\Bundle\ProductBundle\Entity\Category", inversedBy="features" )
     **/
    private  $category;


    /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\AppearanceBundle\Entity\Menu", inversedBy="features")
     */
    protected $menu;



    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255 , nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="captionBgColor", type="string", length=30 , nullable=true)
     */
    private $captionBgColor;

     /**
     * @var string
     *
     * @ORM\Column(name="captionFontColor", type="string", length=30 , nullable=true)
     */
    private $captionFontColor;

     /**
     * @var string
     *
     * @ORM\Column(name="buttonName", type="string", length=100 , nullable=true)
     */
    private $buttonName;

     /**
     * @var string
     *
     * @ORM\Column(name="buttonBg", type="string", length=50 , nullable=true)
     */
    private $buttonBg;

    /**
     * @var string
     *
     * @ORM\Column(name="captionPosition", type="string", length=25 , nullable=true)
     */
    private $captionPosition;


    /**
     * @var string
     *
     * @ORM\Column(name="targetTo", type="string", length=50 , nullable=true)
     */
    private $targetTo;

     /**
     * @var string
     *
     * @ORM\Column(name="customUrl", type="string", nullable=true)
     */
    private $customUrl;

    /**
     * @var array
     *
     * @ORM\Column(name="featureFor", type="array",nullable=true)
     */
    private $featureFor;

    /**
     * @var array
     *
     * @ORM\Column(type="boolean",nullable=true)
     */
    private $isSliderTitle = true;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text" , nullable=true)
     */
    private $content;


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
     * @var smallint
     *
     * @ORM\Column(name="sorting", type="smallint" , nullable=true)
     */
    private $sorting;


    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="boolean")
     */
    private $status = true;


    /**
     * @var boolean
     *
     * @ORM\Column(name="isSlider", type="boolean")
     */
    private $isSlider = false;

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
     * @return Feature
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
     * @return Feature
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
     * @return Feature
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
     * Sets file.
     *
     * @param Feature $file
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
    }

    /**
     * Get file.
     *
     * @return Feature
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
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
            : $this->getUploadDir().'/'.$this->path;
    }

    public function removeUpload()
    {
        if ($file = $this->getAbsolutePath()) {
            unlink($file);
        }
    }

    protected function getUploadRootDir()
    {
        return __DIR__.'/../../../../../web/'.$this->getUploadDir();
    }


    protected function getUploadDir()
    {
        return 'uploads/domain/'.$this->getGlobalOption()->getId().'/content';
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
     * @return datetime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param datetime $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }

    /**
     * @return smallint
     */
    public function getSorting()
    {
        return $this->sorting;
    }

    /**
     * @param smallint $sorting
     */
    public function setSorting($sorting)
    {
        $this->sorting = $sorting;
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
     * @return Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param Category $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
    }

    /**
     * @return Promotion
     */
    public function getPromotion()
    {
        return $this->promotion;
    }

    /**
     * @param Promotion $promotion
     */
    public function setPromotion($promotion)
    {
        $this->promotion = $promotion;
    }

    /**
     * @return Promotion
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * @param Promotion $tag
     */
    public function setTag($tag)
    {
        $this->tag = $tag;
    }

    /**
     * @return Discount
     */
    public function getDiscount()
    {
        return $this->discount;
    }

    /**
     * @param Discount $discount
     */
    public function setDiscount($discount)
    {
        $this->discount = $discount;
    }

    /**
     * @return string
     */
    public function getTargetTo()
    {
        return $this->targetTo;
    }

    /**
     * @param string $targetTo
     * Promotion
     * Category
     * Tag
     * Discount
     */
    public function setTargetTo($targetTo)
    {
        $this->targetTo = $targetTo;
    }

    /**
     * @return boolean
     */
    public function getIsSlider()
    {
        return $this->isSlider;
    }

    /**
     * @param boolean $isSlider
     */
    public function setIsSlider($isSlider)
    {
        $this->isSlider = $isSlider;
    }

    /**
     * @return string
     */
    public function getFeatureFor()
    {
        return $this->featureFor;
    }

    /**
     * @param string $featureFor
     * Tab
     * Carousel
     * Feature
     */
    public function setFeatureFor($featureFor)
    {
        $this->featureFor = $featureFor;
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
     * @return FeatureWidgetItem
     */
    public function getFeatureWidgetItems()
    {
        return $this->featureWidgetItems;
    }

    /**
     * @return Page
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @param Page $page
     */
    public function setPage($page)
    {
        $this->page = $page;
    }



    /**
     * @return string
     */
    public function getButtonName()
    {
        return $this->buttonName;
    }

    /**
     * @param string $buttonName
     */
    public function setButtonName($buttonName)
    {
        $this->buttonName = $buttonName;
    }

    /**
     * @return string
     */
    public function getCaptionBgColor()
    {
        return $this->captionBgColor;
    }

    /**
     * @param string $captionBgColor
     */
    public function setCaptionBgColor($captionBgColor)
    {
        $this->captionBgColor = $captionBgColor;
    }

    /**
     * @return string
     */
    public function getCaptionPosition()
    {
        return $this->captionPosition;
    }

    /**
     * @param string $captionPosition
     */
    public function setCaptionPosition($captionPosition)
    {
        $this->captionPosition = $captionPosition;
    }

    /**
     * @return string
     */
    public function getButtonBg()
    {
        return $this->buttonBg;
    }

    /**
     * @param string $buttonBg
     */
    public function setButtonBg($buttonBg)
    {
        $this->buttonBg = $buttonBg;
    }

    /**
     * @return ItemBrand
     */
    public function getBrand()
    {
        return $this->brand;
    }

    /**
     * @param ItemBrand $brand
     */
    public function setBrand($brand)
    {
        $this->brand = $brand;
    }

    /**
     * @return EcommerceMenu
     */
    public function getEcommerceMenu()
    {
        return $this->ecommerceMenu;
    }

    /**
     * @return SidebarWidgetPanel
     */
    public function getSidebarWidgetPanel()
    {
        return $this->sidebarWidgetPanel;
    }

    /**
     * @return Menu
     */
    public function getMenu()
    {
        return $this->menu;
    }

    /**
     * @param Menu $menu
     */
    public function setMenu($menu)
    {
        $this->menu = $menu;
    }

	/**
	 * @return string
	 */
	public function getCaptionFontColor(){
		return $this->captionFontColor;
	}

	/**
	 * @param string $captionFontColor
	 */
	public function setCaptionFontColor( string $captionFontColor ) {
		$this->captionFontColor = $captionFontColor;
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
     * @return array
     */
    public function getisSliderTitle()
    {
        return $this->isSliderTitle;
    }

    /**
     * @param array $isSliderTitle
     */
    public function setIsSliderTitle($isSliderTitle)
    {
        $this->isSliderTitle = $isSliderTitle;
    }

    /**
     * @return string
     */
    public function getCustomUrl()
    {
        return $this->customUrl;
    }

    /**
     * @param string $customUrl
     */
    public function setCustomUrl($customUrl)
    {
        $this->customUrl = $customUrl;
    }



}
