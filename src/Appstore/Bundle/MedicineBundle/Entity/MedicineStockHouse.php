<?php

namespace Appstore\Bundle\MedicineBundle\Entity;

use Core\UserBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Setting\Bundle\ToolBundle\Entity\ProductUnit;

/**
 * MedicineReverse
 *
 * @ORM\Table(name="medicine_stock_house")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\MedicineBundle\Repository\MedicineStockHouseRepository")
 */
class MedicineStockHouse
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
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\MedicineBundle\Entity\MedicineConfig", inversedBy="stockHouses")
     */
    protected $medicineConfig;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\MedicineBundle\Entity\MedicineStock", inversedBy="stockHouses", cascade={"persist"} )
     **/
    private $medicineStock;

    /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\ProductUnit", inversedBy="stockHouses")
     **/
    private $unit;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\MedicineBundle\Entity\MedicineParticular", inversedBy="stockHouses")
     **/
    private $wearHouse;

    /**
     * @Gedmo\Blameable(on="create")
     * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User", inversedBy="stockHouses" )
     **/
    private  $createdBy;

    /**
     * @var string
     *
     * @ORM\Column(name="stockName", type="string", nullable=true)
     */
    private $stockName;


    /**
     * @var integer
     *
     * @ORM\Column(name="stockIn", type="integer", nullable=true)
     */
    private $stockIn = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="stockOut", type="integer", nullable=true)
     */
    private $stockOut = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="remainingStock", type="integer", nullable=true)
     */
    private $remainingStock = 0;


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
     * @return User
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * @param User $createdBy
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;
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
     * @param MedicineSales $medicineSales
     */
    public function setMedicineSales($medicineSales)
    {
        $this->medicineSales = $medicineSales;
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
     * @return mixed
     */
    public function getMedicinePurchase()
    {
        return $this->medicinePurchase;
    }

    /**
     * @param mixed $medicinePurchase
     */
    public function setMedicinePurchase($medicinePurchase)
    {
        $this->medicinePurchase = $medicinePurchase;
    }

    /**
     * @return MedicineStock
     */
    public function getMedicineStock()
    {
        return $this->medicineStock;
    }

    /**
     * @param MedicineStock $medicineStock
     */
    public function setMedicineStock($medicineStock)
    {
        $this->medicineStock = $medicineStock;
    }

    /**
     * @return ProductUnit
     */
    public function getUnit()
    {
        return $this->unit;
    }

    /**
     * @param ProductUnit $unit
     */
    public function setUnit($unit)
    {
        $this->unit = $unit;
    }

    /**
     * @return MedicineParticular
     */
    public function getWearHouse()
    {
        return $this->wearHouse;
    }

    /**
     * @param MedicineParticular $wearHouse
     */
    public function setWearHouse($wearHouse)
    {
        $this->wearHouse = $wearHouse;
    }

    /**
     * @return int
     */
    public function getStockIn()
    {
        return $this->stockIn;
    }

    /**
     * @param int $stockIn
     */
    public function setStockIn(int $stockIn)
    {
        $this->stockIn = $stockIn;
    }

    /**
     * @return int
     */
    public function getStockOut()
    {
        return $this->stockOut;
    }

    /**
     * @param int $stockOut
     */
    public function setStockOut(int $stockOut)
    {
        $this->stockOut = $stockOut;
    }

    /**
     * @return int
     */
    public function getRemainingStock()
    {
        return $this->remainingStock;
    }

    /**
     * @param int $remainingStock
     */
    public function setRemainingStock(int $remainingStock)
    {
        $this->remainingStock = $remainingStock;
    }

    /**
     * @return string
     */
    public function getStockName()
    {
        return $this->stockName;
    }

    /**
     * @param string $stockName
     */
    public function setStockName(string $stockName)
    {
        $this->stockName = $stockName;
    }


}

