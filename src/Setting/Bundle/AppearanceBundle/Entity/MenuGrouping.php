<?php

namespace Setting\Bundle\AppearanceBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * MenuGrouping
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Setting\Bundle\AppearanceBundle\Entity\MenuGroupingRepository")
 */
class MenuGrouping
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
     * @ORM\ManyToOne(targetEntity="MenuGrouping", inversedBy="children",  cascade={"remove"})
     * @ORM\JoinColumn(name="parent", referencedColumnName="id", onDelete="SET NULL" , nullable = true)
     */
    protected $parent;

    /**
     * @ORM\OneToMany(targetEntity="MenuGrouping" , mappedBy="parent")
     * @ORM\OrderBy({"sorting" = "ASC"})
     **/
    private $children;

    /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\GlobalOption", inversedBy="menuGroupings")
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    protected $globalOption;


    /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\AppearanceBundle\Entity\Menu", inversedBy="menuGrouping")
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/

    protected $menu;

    /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\AppearanceBundle\Entity\MenuGroup", inversedBy="menuGrouping")
     **/

    protected $menuGroup;


    /**
     * @var integer
     *
     * @ORM\Column(name="sorting", type="smallint" , nullable = true)
     */
    private $sorting = 0 ;



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
     * @param mixed $parent
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    /**
     * @return mixed
     */
    public function getParent()
    {
        return $this->parent;
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
     * @param mixed $menuGroup
     */
    public function setMenuGroup($menuGroup)
    {
        $this->menuGroup = $menuGroup;
    }

    /**
     * @return mixed
     */
    public function getMenuGroup()
    {
        return $this->menuGroup;
    }

    /**
     * @param mixed $menu
     */
    public function setMenu($menu)
    {
        $this->menu = $menu;
    }

    /**
     * @return mixed
     */
    public function getMenu()
    {
        return $this->menu;
    }



    /**
     * Set sorting
     *
     * @param integer $sorting
     * @return MenuGrouping
     */
    public function setSorting($sorting)
    {
        $this->sorting = $sorting;

        return $this;
    }

    /**
     * Get sorting
     *
     * @return integer 
     */
    public function getSorting()
    {
        return $this->sorting;
    }

    /**
     * @return mixed
     */
    public function getChildren()
    {
        return $this->children;
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
}
