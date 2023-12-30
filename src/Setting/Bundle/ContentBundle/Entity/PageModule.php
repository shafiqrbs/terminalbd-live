<?php

namespace Setting\Bundle\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Setting\Bundle\AppearanceBundle\Entity\Menu;
use Setting\Bundle\ToolBundle\Entity\Module;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * Page
 *
 * @ORM\Table(name="PageModule")
 * @ORM\Entity(repositoryClass="Setting\Bundle\ContentBundle\Repository\PageModuleRepository")
 */
class PageModule
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
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\Module", inversedBy="pageModules" )
     **/
    protected $module;

    /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ContentBundle\Entity\Page", inversedBy="pageModules" )
     **/
    protected $page;

    /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\AppearanceBundle\Entity\Menu", inversedBy="pageModules")
     **/
    protected  $menu;


    /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ContentBundle\Entity\HomePage", inversedBy="pageModules" )
     **/
    protected $homePage;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", nullable= true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="contentPosition", type="string", nullable= true)
     */
    private $contentPosition = 'Body';

    /**
     * @var integer
     *
     * @ORM\Column(name="showLimit", type="smallint", nullable= true)
     */
    private $showLimit = 4;

    /**
     * @var integer
     *
     * @ORM\Column(name="ordering", type="smallint", nullable= true)
     */
     private $ordering = 0;


    /**
     * @var integer
     *
     * @ORM\Column(name="showColumn", type="smallint", nullable= true)
     */
    private $showColumn = 3;

    /**
     * @var string
     *
     * @ORM\Column(name="showingType", type="string", nullable= true)
     */
    private $showingType = 'list';


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
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getShowLimit()
    {
        return $this->showLimit;
    }

    /**
     * @param int $showLimit
     */
    public function setShowLimit($showLimit)
    {
        $this->showLimit = $showLimit;
    }

    /**
     * @return mixed
     */
    public function getHomePage()
    {
        return $this->homePage;
    }

    /**
     * @param mixed $homePage
     */
    public function setHomePage($homePage)
    {
        $this->homePage = $homePage;
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
     * @return int
     */
    public function getShowColumn()
    {
        return $this->showColumn;
    }

    /**
     * @param int $showColumn
     */
    public function setShowColumn($showColumn)
    {
        $this->showColumn = $showColumn;
    }

    /**
     * @return string
     */
    public function getContentPosition()
    {
        return $this->contentPosition;
    }

    /**
     * @param string $contentPosition
     * SideBar
     * Footer
     * Top
     * Bottom
     */

    public function setContentPosition($contentPosition)
    {
        $this->contentPosition = $contentPosition;
    }

    /**
     * @return string
     */
    public function getShowingType()
    {
        return $this->showingType;
    }

    /**
     * @param string $showingType
     */
    public function setShowingType($showingType)
    {
        $this->showingType = $showingType;
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


}
