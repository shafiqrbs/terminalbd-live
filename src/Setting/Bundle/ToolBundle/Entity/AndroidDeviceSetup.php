<?php

namespace Setting\Bundle\ToolBundle\Entity;

use Appstore\Bundle\AccountingBundle\Entity\Expenditure;
use Appstore\Bundle\AccountingBundle\Entity\ExpenseAndroidProcess;
use Appstore\Bundle\BusinessBundle\Entity\BusinessAndroidProcess;
use Appstore\Bundle\InventoryBundle\Entity\InventoryAndroidProcess;
use Appstore\Bundle\MedicineBundle\Entity\MedicineAndroidProcess;
use Appstore\Bundle\MedicineBundle\Entity\MedicinePurchase;
use Appstore\Bundle\MedicineBundle\Entity\MedicineSales;
use Appstore\Bundle\RestaurantBundle\Entity\RestaurantAndroidProcess;
use Doctrine\ORM\Mapping as ORM;


/**
 * Icon
 *
 * @ORM\Table("android_device_setup")
 * @ORM\Entity(repositoryClass="Setting\Bundle\ToolBundle\Repository\AndroidDeviceSetupRepository")
 */
class AndroidDeviceSetup
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
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\GlobalOption", inversedBy="androids")
     **/

    private $globalOption;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\ExpenseAndroidProcess", mappedBy="androidDevice" )
     **/
    private  $expenseAndroidProcess;


     /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\MedicineBundle\Entity\MedicineSales", mappedBy="androidDevice" )
     **/
    private  $medicineSales;


    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\MedicineBundle\Entity\MedicinePurchase", mappedBy="androidDevice" )
     **/
    private  $medicinePurchases;


    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\Expenditure", mappedBy="androidDevice" )
     **/
    private  $expenditure;



    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\MedicineBundle\Entity\MedicineAndroidProcess", mappedBy="androidDevice" )
     **/
    private  $medicineAndroidProcess;


     /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\InventoryAndroidProcess", mappedBy="androidDevice" )
     **/
    private  $inventoryAndroidProcess;


     /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\BusinessBundle\Entity\BusinessAndroidProcess", mappedBy="androidDevice" )
     **/
    private  $businessAndroidProcess;


     /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\RestaurantBundle\Entity\RestaurantAndroidProcess", mappedBy="androidDevice" )
     **/
    private  $restaurantAndroidProcess;


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
     * @var string
     *
     * @ORM\Column(name="device", type="string", length=255)
     */
    private $device;

    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="boolean")
     */
    private $status = true;


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
    public function getDevice()
    {
        return $this->device;
    }

    /**
     * @param mixed $device
     */
    public function setDevice($device)
    {
        $this->device = $device;
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
     * @return MedicineSales
     */
    public function getMedicineSales()
    {
        return $this->medicineSales;
    }

    /**
     * @return MedicinePurchase
     */
    public function getMedicinePurchases()
    {
        return $this->medicinePurchases;
    }

    /**
     * @return Expenditure
     */
    public function getExpenditure()
    {
        return $this->expenditure;
    }

    /**
     * @return MedicineAndroidProcess
     */
    public function getMedicineAndroidProcess()
    {
        return $this->medicineAndroidProcess;
    }

    /**
     * @return BusinessAndroidProcess
     */
    public function getBusinessAndroidProcess()
    {
        return $this->businessAndroidProcess;
    }

    /**
     * @return InventoryAndroidProcess
     */
    public function getInventoryAndroidProcess()
    {
        return $this->inventoryAndroidProcess;
    }

    /**
     * @return RestaurantAndroidProcess
     */
    public function getRestaurantAndroidProcess()
    {
        return $this->restaurantAndroidProcess;
    }

    /**
     * @return ExpenseAndroidProcess
     */
    public function getExpenseAndroidProcess()
    {
        return $this->expenseAndroidProcess;
    }


}

