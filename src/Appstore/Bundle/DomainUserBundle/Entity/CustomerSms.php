<?php

namespace Appstore\Bundle\DomainUserBundle\Entity;

use Core\UserBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;

/**
 * ElectionSms
 *
 * @ORM\Table( name ="customer_sms")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\DomainUserBundle\Repository\CustomerSmsRepository")
 */
class CustomerSms
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
	 * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\GlobalOption", inversedBy="sms" , cascade={"detach","merge"} )
	 **/
	private  $globalOption;


	/**
	 * @var string
	 *
	 * @ORM\Column(name="name", type="string",  length = 255, nullable=true)
	 */
	private $name;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="content", type="string",  length = 255, nullable=true)
	 */
	private $content;


	/**
	 * @var int
	 *
	 * @ORM\Column(name="total", type="smallint",  length = 6, nullable=true)
	 */
	private $total;

	/**
	 * @var int
	 *
	 * @ORM\Column(name="success", type="smallint",  length = 6, nullable=true)
	 */
	private $success;

	/**
	 * @var int
	 *
	 * @ORM\Column(name="failed", type="smallint",  length = 6, nullable=true)
	 */
	private $failed;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="process", type="string",  length = 20, nullable=true)
	 */
	private $process;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="smsStatus", type="string",  length = 20, nullable=true)
	 */
	private $smsStatus = 'Created';


	/**
	 * @var boolean
	 *
	 * @ORM\Column(name="status", type="boolean" )
	 */
	private $status= true;


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
	 * Get id
	 *
	 * @return integer
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @return bool
	 */
	public function getStatus()
	{
		return $this->status;
	}

	/**
	 * @param bool $status
	 */
	public function setStatus($status)
	{
		$this->status = $status;
	}



	/**
	 * @return \DateTime
	 */
	public function getCreated(): \DateTime {
		return $this->created;
	}

	/**
	 * @param \DateTime $created
	 */
	public function setCreated( \DateTime $created ) {
		$this->created = $created;
	}

	/**
	 * @return \DateTime
	 */
	public function getUpdated(){
		return $this->updated;
	}

	/**
	 * @param \DateTime $updated
	 */
	public function setUpdated( \DateTime $updated ) {
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
	 * @return string
	 */
	public function getContent(){
		return $this->content;
	}

	/**
	 * @param string $content
	 */
	public function setContent( string $content ) {
		$this->content = $content;
	}

	/**
	 * @return int
	 */
	public function getSuccess(){
		return $this->success;
	}

	/**
	 * @param int $success
	 */
	public function setSuccess( int $success ) {
		$this->success = $success;
	}

	/**
	 * @return int
	 */
	public function getFailed(){
		return $this->failed;
	}

	/**
	 * @param int $failed
	 */
	public function setFailed( int $failed ) {
		$this->failed = $failed;
	}



	/**
	 * @return int
	 */
	public function getTotal(){
		return $this->total;
	}

	/**
	 * @param int $total
	 */
	public function setTotal( int $total ) {
		$this->total = $total;
	}

	/**
	 * @return string
	 */
	public function getProcess(){
		return $this->process;
	}

	/**
	 * @param string $process
	 * Member
	 * Voter
	 * Committee
	 * VoteCenter
	 * Event
	 */
	public function setProcess( string $process ) {
		$this->process = $process;
	}

	/**
	 * @return string
	 */
	public function getSmsStatus() {
		return $this->smsStatus;
	}

	/**
	 * @param string $smsStatus
	 */
	public function setSmsStatus( string $smsStatus ) {
		$this->smsStatus = $smsStatus;
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


}

