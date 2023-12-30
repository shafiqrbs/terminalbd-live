<?php

namespace Setting\Bundle\AppearanceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Setting\Bundle\ContentBundle\Entity\ModuleCategory;
use Setting\Bundle\ContentBundle\Entity\PageModule;
use Setting\Bundle\LocationBundle\Entity\GpLocation;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;

/**
 * Menu
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Setting\Bundle\AppearanceBundle\Entity\MenuRepository")
 */
class Menu
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
     * @var string
     *
     * @ORM\Column(name="menu", type="string", length=255)
     */
    private $menu;

    /**
     * @Gedmo\Slug(fields={"slug"}, updatable=false, separator="-")
     * @ORM\Column(length=255)
     */
    private $menuSlug;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=255, nullable = true)
     */
    private $slug;


    /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\GlobalOption", inversedBy="menus")
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    protected $globalOption;

    /**
     * @ORM\OneToMany(targetEntity="Setting\Bundle\AppearanceBundle\Entity\MenuGrouping", mappedBy="menu" , cascade={"persist", "remove"})
     */
    protected $menuGrouping;


    /**
     * @ORM\OneToMany(targetEntity="Setting\Bundle\ContentBundle\Entity\PageModule", mappedBy="menu" , cascade={"persist", "remove"})
     */
    protected $pageModules;

    /**
     * @ORM\OneToMany(targetEntity="Setting\Bundle\AppearanceBundle\Entity\FeatureWidget", mappedBy="menu" , cascade={"persist", "remove"})
     */
    protected $featureWidgets;

    /**
     * @ORM\OneToMany(targetEntity="Setting\Bundle\AppearanceBundle\Entity\Feature", mappedBy="menu" , cascade={"persist", "remove"})
     */
    protected $features;

    /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\Module", inversedBy="nav")
     */
    protected $module;

    /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\AppearanceBundle\Entity\MenuCustom", inversedBy="nav")
     */
    protected $menuCustom;

    /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\SyndicateModule", inversedBy="nav")
     */
    protected $syndicateModule;

    /**
     * @ORM\OneToOne(targetEntity="Setting\Bundle\ContentBundle\Entity\Page", inversedBy="nav")
     */
    protected $page;

    /**
     * @ORM\OneToOne(targetEntity="Setting\Bundle\ContentBundle\Entity\ModuleCategory", inversedBy="nav")
     */
    protected $category;


    /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\Syndicate", inversedBy="nav")
     */
    protected $syndicate;


    /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\SiteSetting", inversedBy="nav")
     */
    protected $siteSetting;

    /**
     * @ORM\OneToMany(targetEntity="Setting\Bundle\ContentBundle\Entity\HomeBlock", mappedBy="menu")
     */
    protected $homeBlocks;

    /**
     * @var boolean
     *
     * @ORM\Column(name="defaultMenu", type="boolean")
     */

    private $defaultMenu = false ;


    /**
     * @var string
     *
     * @ORM\Column(name="mode", type="string", length=255, nullable=true)
     */
    private $mode;


    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="boolean")
     */

    private $status=true;


    public function __construct()
    {
        if(!$this->getId()){
            $this->setStatus(true);
        }

    }

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
     * Set menu
     *
     * @param string $menu
     * @return Menu
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
     * Set menuSlug
     *
     * @param string $menuSlug
     * @return Menu
     */
    public function setMenuSlug($menuSlug)
    {
        $this->menuSlug = $menuSlug;

        return $this;
    }

    /**
     * Get menuSlug
     *
     * @return string
     */
    public function getMenuSlug()
    {
        return $this->menuSlug;
    }


    /**
     * Set status
     *
     * @param boolean $status
     * @return Menu
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }


    /**
     * @param mixed $module
     */
    public function setModule($module)
    {
        $this->module = $module;
    }

    /**
     * @return mixed
     */
    public function getModule()
    {
        return $this->module;
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

    /**
     * @return mixed
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @param mixed $page
     */
    public function setPage($page)
    {
        $this->page = $page;
    }

    /**
     * @return mixed
     */
    public function getSyndicate()
    {
        return $this->syndicate;
    }

    /**
     * @param mixed $syndicate
     */
    public function setSyndicate($syndicate)
    {
        $this->syndicate = $syndicate;
    }

    /**
     * @return mixed
     */
    public function getSiteSetting()
    {
        return $this->siteSetting;
    }

    /**
     * @param mixed $siteSetting
     */
    public function setSiteSetting($siteSetting)
    {
        $this->siteSetting = $siteSetting;
    }


    /**
     * @return mixed
     */
    public function getHomeBlocks()
    {
        return $this->homeBlocks;
    }

    /**
     * @return mixed
     */
    public function getHomeBlock()
    {
        return $this->homeBlock;
    }

    /**
     * @return boolean
     */
    public function getDefaultMenu()
    {
        return $this->defaultMenu;
    }

    /**
     * @param boolean $defaultMenu
     */
    public function setDefaultMenu($defaultMenu)
    {
        $this->defaultMenu = $defaultMenu;
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
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    /**
     * @return MenuCustom
     */
    public function getMenuCustom()
    {
        return $this->menuCustom;
    }

    /**
     * @param MenuCustom $menuCustom
     */
    public function setMenuCustom($menuCustom)
    {
        $this->menuCustom = $menuCustom;
    }

    /**
     * @return boolean
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return PageModule
     */
    public function getPageModules()
    {
        return $this->pageModules;
    }

    /**
     * @return Feature
     */
    public function getFeatures()
    {
        return $this->features;
    }

    /**
     * @return FeatureWidget
     */
    public function getFeatureWidgets()
    {
        return $this->featureWidgets;
    }

    public function getFeatureWidgetExist()
    {

        $data = false;
        if (!empty($this->featureWidgets)){

            foreach ($this->featureWidgets as $feature) {

                /* @var FeatureWidget $feature */
                if ($feature->getPosition() == 'sidebar-top' || $feature->getPosition() == 'sidebar-bottom') {
                    $data = true;
                }
            }
        }
        return $data;

    }

    public function getFeatureWidgetPositionExist($position)
    {

        $data = false;
        if (!empty($this->featureWidgets)){

            foreach ($this->featureWidgets as $feature) {

                /* @var FeatureWidget $feature */
                if ($feature->getPosition() == $position ) {
                    $data = true;
                }
            }
        }
        return $data;

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
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * @param string $mode
     */
    public function setMode($mode)
    {
        $this->mode = $mode;
    }




}
