<?php

namespace Appstore\Bundle\DomainUserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User;
use Gedmo\Mapping\Annotation as Gedmo;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;

/**
 * CustomerInbox
 *
 * @ORM\Table("domain_notepad")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\DomainUserBundle\Repository\NotepadRepository")
 */
class Notepad
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
	 * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\GlobalOption", inversedBy="notepads")
	 * @ORM\JoinColumn(onDelete="CASCADE")
	 **/
	protected $globalOption;

	/**
     * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User", inversedBy="notepad")
     **/
    protected $user;


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
     * @var string
     *
     * @ORM\Column(name="content", type="text" , nullable=true)
     *
     */
    private $content;

	/**
     * @var string
     *
     * @ORM\Column(name="name", type="string" , length=255, nullable=true)
     *
     */
    private $name;


    /**
     * @var boolean
     *
     * @ORM\Column(name="archive", type="boolean" )
     */
    private $archive = 0;

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
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param \DateTime $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }


    /**
     * @return boolean
     */
    public function getArchive()
    {
        return $this->archive;
    }

    /**
     * @param boolean $archive
     */
    public function setArchive($archive)
    {
        $this->archive = $archive;
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
	 * @return string
	 */
	public function getName(){
		return $this->name;
	}

	/**
	 * @param string $name
	 */
	public function setName( string $name ) {
		$this->name = $name;
	}

	/**
	 * @return GlobalOption
	 */
	public function getGlobalOption() {
		return $this->globalOption;
	}

	/**
	 * @param GlobalOption $globalOption
	 */
	public function setGlobalOption( $globalOption ) {
		$this->globalOption = $globalOption;
	}


}
