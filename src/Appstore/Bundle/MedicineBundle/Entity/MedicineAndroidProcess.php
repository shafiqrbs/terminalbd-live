<?php

namespace Appstore\Bundle\MedicineBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Setting\Bundle\ToolBundle\Entity\AndroidDeviceSetup;

/**
 * MedicineAndroidProcess
 *
 * @ORM\Table(name="medicine_android_process")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\MedicineBundle\Repository\MedicineAndroidProcessRepository")
 */
class MedicineAndroidProcess
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
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\MedicineBundle\Entity\MedicineConfig", inversedBy="androidProcesses")
     */
    protected $medicineConfig;

    /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\AndroidDeviceSetup", inversedBy="medicineAndroidProcess" )
     **/
    private  $androidDevice;


    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\MedicineBundle\Entity\MedicineSales", mappedBy="androidProcess" )
     **/
    private  $medicineSales;


   /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\MedicineBundle\Entity\MedicinePurchase", mappedBy="androidProcess" )
     **/
    private  $medicinePurchase;


   /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\MedicineBundle\Entity\MedicinePurchaseItem", mappedBy="androidProcess" )
     **/
    private  $medicinePurchaseItem;


    /**
     * @var string
     *
     * @ORM\Column(name="jsonItem", type="text",  nullable=true)
     */
    private $jsonItem;


     /**
     * @var string
     *
     * @ORM\Column(name="jsonSubItem", type="text",  nullable=true)
     */
    private $jsonSubItem;


    /**
     * @var string
     *
     * @ORM\Column(name="process", type="string", length = 30, nullable=true)
     */
    private $process = 'sales';

    /**
     * @var string
     *
     * @ORM\Column(name="ipAddress", type="string", length = 100, nullable=true)
     */
    private $ipAddress;

    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="boolean", nullable=true)
     */
    private $status = false;

    /**
     * @var integer
     *
     * @ORM\Column(name="itemCount", type="integer", nullable=true)
     */
    private $itemCount = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="subItemCount", type="integer", nullable=true)
     */
    private $subItemCount = 0;

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
     * @return MedicineConfig
     */
    public function getMedicineConfig()
    {
        return $this->medicineConfig;
    }

    /**
     * @param MedicineConfig $medicineConfig
     */
    public function setMedicineConfig($medicineConfig)
    {
        $this->medicineConfig = $medicineConfig;
    }

    /**
     * @return AndroidDeviceSetup
     */
    public function getAndroidDevice()
    {
        return $this->androidDevice;
    }

    /**
     * @param AndroidDeviceSetup $androidDevice
     */
    public function setAndroidDevice($androidDevice)
    {
        $this->androidDevice = $androidDevice;
    }

    /**
     * @return string
     */
    public function getJsonItem()
    {
        return $this->jsonItem;
    }

    /**
     * @param string $jsonItem
     */
    public function setJsonItem($jsonItem)
    {
        $this->jsonItem = $jsonItem;
    }

    /**
     * @return string
     */
    public function getJsonSubItem()
    {
        return $this->jsonSubItem;
    }

    /**
     * @param string $jsonSubItem
     */
    public function setJsonSubItem($jsonSubItem)
    {
        $this->jsonSubItem = $jsonSubItem;
    }

    /**
     * @return string
     */
    public function getProcess()
    {
        return $this->process;
    }

    /**
     * @param string $process
     */
    public function setProcess($process)
    {
        $this->process = $process;
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
     * @return MedicineSales
     */
    public function getMedicineSales()
    {
        return $this->medicineSales;
    }

    /**
     * @return MedicineSalesItem
     */
    public function getMedicineSalesItem()
    {
        return $this->medicineSalesItem;
    }

    /**
     * @return MedicinePurchase
     */
    public function getMedicinePurchase()
    {
        return $this->medicinePurchase;
    }

    /**
     * @return MedicinePurchaseItem
     */
    public function getMedicinePurchaseItem()
    {
        return $this->medicinePurchaseItem;
    }

    /**
     * @return int
     */
    public function getItemCount()
    {
        return $this->itemCount;
    }

    /**
     * @param int $itemCount
     */
    public function setItemCount($itemCount)
    {
        $this->itemCount = $itemCount;
    }

    /**
     * @return int
     */
    public function getSubItemCount()
    {
        return $this->subItemCount;
    }

    /**
     * @param int $subItemCount
     */
    public function setSubItemCount($subItemCount)
    {
        $this->subItemCount = $subItemCount;
    }

    /**
     * @return string
     */
    public function getIpAddress()
    {
        return $this->ipAddress;
    }

    /**
     * @param string $ipAddress
     */
    public function setIpAddress($ipAddress)
    {
        $this->ipAddress = $ipAddress;
    }


}

