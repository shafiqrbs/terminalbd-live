<?php

namespace Setting\Bundle\ToolBundle\Entity;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * SiteSetting
 * @ORM\Entity
 * @UniqueEntity(fields="user",message="This data is already in use.")
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Setting\Bundle\ToolBundle\Repository\SiteSettingRepository")
 */
class SiteSetting
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
     * @ORM\OneToOne(targetEntity="Core\UserBundle\Entity\User", inversedBy="siteSetting")
     **/

    protected $user;

    /**
     * @ORM\OneToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\GlobalOption", inversedBy="siteSetting")
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/

    protected $globalOption;


    /**
     * @ORM\OneToMany(targetEntity="Setting\Bundle\AppearanceBundle\Entity\Menu", mappedBy="siteSetting")
     **/

    protected $nav;


    /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\WebTheme", inversedBy ="siteSetting")
     **/

    protected $webTheme =null;

    /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\MobileTheme", inversedBy="siteSetting")
     **/

    protected $mobileTheme = null;


    /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\Theme", inversedBy="siteSetting")
     **/

    protected $theme = null;


    /**
     * @ORM\ManyToMany(targetEntity="Setting\Bundle\ToolBundle\Entity\Syndicate", inversedBy="siteSettings")
     * @ORM\JoinTable(name="sitesetting_syndicate",
     *      joinColumns={@ORM\JoinColumn(name="sitesetting_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="syndicate_id", referencedColumnName="id")}
     * )
     */

    protected $syndicates;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="Setting\Bundle\ToolBundle\Entity\Module", inversedBy="siteSettings")
     * @ORM\JoinTable(name="sitesetting_module",
     *      joinColumns={@ORM\JoinColumn(name="sitesetting_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="module_id", referencedColumnName="id")}
     * )
     * @ORM\OrderBy({"name" = "ASC"})
     */

    protected $modules;

    /**
     * @ORM\ManyToMany(targetEntity="Setting\Bundle\ToolBundle\Entity\SyndicateModule", inversedBy="siteSettings")
     * @ORM\JoinTable(name="sitesetting_syndicatemodule",
     *      joinColumns={@ORM\JoinColumn(name="sitesetting_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="syndicate_module_id", referencedColumnName="id")}
     * )
     * @ORM\OrderBy({"name" = "DESC"})
     */

    protected $syndicateModules;

    /**
     * @ORM\ManyToMany(targetEntity="Setting\Bundle\ToolBundle\Entity\AppModule", inversedBy="siteSettings")
     * @ORM\JoinTable(name="sitesetting_appmodule",
     *      joinColumns={@ORM\JoinColumn(name="sitesetting_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="app_module_id", referencedColumnName="id")}
     * )
     * @ORM\OrderBy({"name" = "ASC"})
     */

    protected $appModules;

    /**
     * @var string
     *
     * @ORM\Column(name="uniqueCode", type="string", length=255, nullable=true)
     */
    private $uniqueCode;


    public function _constructor()
    {
        $this->syndicates = new ArrayCollection();
        $this->modules = new ArrayCollection();
        $this->syndicateModules = new ArrayCollection();
        if(!$this->getId()){

            $passcode =substr(str_shuffle(str_repeat('0123456789',5)),0,4);
            $t = microtime(true);
            $micro = ($passcode + floor($t));
            $this->uniqueCode = $micro;
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
     * @param mixed $mobileTheme
     */
    public function setMobileTheme($mobileTheme)
    {
        $this->mobileTheme = $mobileTheme;
    }

    /**
     * @return mixed
     */
    public function getMobileTheme()
    {
        return $this->mobileTheme;
    }

    /**
     * @param mixed $webTheme
     */
    public function setWebTheme($webTheme)
    {
        $this->webTheme = $webTheme;
    }

    /**
     * @return mixed
     */
    public function getWebTheme()
    {
        return $this->webTheme;
    }


    /**
     * @param mixed $syndicates
     */
    public function setSyndicates($syndicates)
    {
        $this->syndicates = $syndicates;
    }

    /**
     * @return mixed
     */
    public function getSyndicates()
    {
        return $this->syndicates;
    }

    /**
     * @param Module $modules
     */
    public function setModules($modules)
    {
        $this->modules = $modules;
    }

    /**
     * @return Module
     */
    public function getModules()
    {
        return $this->modules;
    }

    /**
     * @return mixed
     */
    public function hasModule($module)
    {
        if(empty($this->modules)) {
            return false;
        }
        return $this->modules->contains($module);
    }


    /**
     * @return mixed
     */
    public function getModuleIds()
    {
        $arr =array();
        if(!empty($this->getModules())) {
            foreach ($this->getModules() as $mod ){
                $arr[]= $mod->getId();
            }
        }
        return $arr;

    }



    /**
     * @param mixed $syndicateModules
     */
    public function setSyndicateModules($syndicateModules)
    {
        $this->syndicateModules = $syndicateModules;
    }

    /**
     * @return mixed
     */
    public function getSyndicateModules()
    {
        return $this->syndicateModules;
    }

    /**
     * @return mixed
     */
    public function hasSyndicateModule($syndicateModule)
    {
        if(empty($this->syndicateModules)) {
            return false;
        }

        return $this->syndicateModules->contains($syndicateModule);
    }


    /**
     * @return string
     */
    public function getUniqueCode()
    {
        return $this->uniqueCode;
    }

    /**
     * @param string $uniqueCode
     */
    public function setUniqueCode($uniqueCode)
    {
        $this->uniqueCode = $uniqueCode;
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
     * @return mixed
     */
    public function getAppModules()
    {
        return $this->appModules;
    }

    /**
     * @return mixed
     */
    public function hasAppModule($appModule)
    {
        if(empty($this->appModules)) {
            return false;
        }

        return $this->appModules->contains($appModule);
    }


    /**
     * @param mixed $appModules
     */
    public function setAppModules($appModules)
    {
        $this->appModules = $appModules;
    }

    /**
     * @return mixed
     */
    public function getTheme()
    {
        return $this->theme;
    }

    /**
     * @param mixed $theme
     */
    public function setTheme($theme)
    {
        $this->theme = $theme;
    }


}
