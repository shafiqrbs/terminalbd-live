<?php

namespace Appstore\Bundle\DomainUserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * UserInbox
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Appstore\Bundle\DomainUserBundle\Repository\UserInboxRepository")
 */
class UserInbox
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
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\DomainUserBundle\Entity\Customer", inversedBy="userInbox")
     **/
    protected $customer;

    /**
     * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User", inversedBy="userInbox")
     **/
    private  $user;


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
     * @ORM\Column(name="type", type="string", length=255,nullable=true)
     *
     * 'contact','callback','leave','internal'
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="subject", type="string" , nullable=true)
     *
     */
    private $subject;

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
     * @ORM\Column(name="reply", type="text" , nullable=true)
     *
     */
     private $reply;

    /**
     * @var boolean
     *
     * @ORM\Column(name="archive", type="boolean" )
     */
    private $archive = 0;


    /**
     * @var \DateTime
     *
     * @ORM\Column(name="replyDate", type="datetime", nullable=true)
     */
    private $replyDate;


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
     * @return string
     */
    public function getReply()
    {
        return $this->reply;
    }

    /**
     * @param string $reply
     */
    public function setReply($reply)
    {
        $this->reply = $reply;
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
    public function getReplyDate()
    {
        return $this->replyDate;
    }

    /**
     * @param \DateTime $replyDate
     */
    public function setReplyDate($replyDate)
    {
        $this->replyDate = $replyDate;
    }



    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * @param mixed $customer
     */
    public function setCustomer($customer)
    {
        $this->customer = $customer;
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
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @param string $subject
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }
}
