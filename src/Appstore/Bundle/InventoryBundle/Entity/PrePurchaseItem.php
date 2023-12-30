<?php

namespace Appstore\Bundle\InventoryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Product\Bundle\ProductBundle\Entity\Category;
use Setting\Bundle\ToolBundle\Entity\ProductUnit;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * Product
 *
 * @ORM\Table(name = "item_pre_purchase")
 * @ORM\Entity(repositoryClass="")
 */
class PrePurchaseItem
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
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\InventoryBundle\Entity\InventoryConfig", inversedBy="prePurchaseItem" )
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private  $inventoryConfig;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\InventoryBundle\Entity\Product", inversedBy="prePurchaseItems" )
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private  $masterItem;


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
     * @ORM\Column(name="requestBy", type="string", length=255)
     */
    private $requestBy;

    /**
     * @var integer
     *
     * @ORM\Column(name="requestQnt", type="integer",  nullable=true)
     */
    private $requestQnt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="availableDate", type="date",  nullable=true)
     */
    private $availableDate;


    /**
     * @var text
     *
     * @ORM\Column(name="content", type="text", nullable=true)
     */
    private $content;


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
     * @return mixed
     */
    public function getMasterItem()
    {
        return $this->masterItem;
    }

    /**
     * @param mixed $masterItem
     */
    public function setMasterItem($masterItem)
    {
        $this->masterItem = $masterItem;
    }

    /**
     * @return mixed
     */
    public function getInventoryConfig()
    {
        return $this->inventoryConfig;
    }

    /**
     * @param mixed $inventoryConfig
     */
    public function setInventoryConfig($inventoryConfig)
    {
        $this->inventoryConfig = $inventoryConfig;
    }

    /**
     * @return bool
     */
    public function isStatus()
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
     * @return text
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param text $content
     */
    public function setContent($content)
    {
        $this->content = $content;
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
    public function getRequestBy()
    {
        return $this->requestBy;
    }

    /**
     * @param string $requestBy
     */
    public function setRequestBy($requestBy)
    {
        $this->requestBy = $requestBy;
    }

    /**
     * @return int
     */
    public function getRequestQnt()
    {
        return $this->requestQnt;
    }

    /**
     * @param int $requestQnt
     */
    public function setRequestQnt($requestQnt)
    {
        $this->requestQnt = $requestQnt;
    }

    /**
     * @return \DateTime
     */
    public function getAvailableDate()
    {
        return $this->availableDate;
    }

    /**
     * @param \DateTime $availableDate
     */
    public function setAvailableDate($availableDate)
    {
        $this->availableDate = $availableDate;
    }

}

