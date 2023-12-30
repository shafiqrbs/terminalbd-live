<?php

namespace Setting\Bundle\AppearanceBundle\Entity;

use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * SidebarWidget
 *
 * @ORM\Table("app_social_icon")
 * @ORM\Entity(repositoryClass="Setting\Bundle\AppearanceBundle\Repository\SidebarWidgetRepository")
 */
class SocialIcon
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
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\AppearanceBundle\Entity\TemplateCustomize", inversedBy="socialIcons")
     **/
    private $templateCustomize;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=100,nullable = true)
     */
    private $name;


	/**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255,nullable = true)
     */
    private $url;


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
     * Set name
     *
     * @param string $name
     *
     * @return SidebarWidget
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
     * Set status
     *
     * @param boolean $status
     *
     * @return SidebarWidget
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
	 * @return TemplateCustomize
	 */
	public function getTemplateCustomize() {
		return $this->templateCustomize;
	}

	/**
	 * @param TemplateCustomize $templateCustomize
	 */
	public function setTemplateCustomize( $templateCustomize ) {
		$this->templateCustomize = $templateCustomize;
	}

	/**
	 * @return mixed
	 */
	public function getUrl() {
		return $this->url;
	}

	/**
	 * @param mixed $url
	 */
	public function setUrl( $url ) {
		$this->url = $url;
	}


}

