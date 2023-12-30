<?php

namespace Appstore\Bundle\MedicineBundle\Entity;

use Appstore\Bundle\HospitalBundle\Entity\Particular;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Setting\Bundle\ToolBundle\Entity\ProductUnit;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * MedicineBrand
 *
 * @ORM\Table("medicine_stock")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\MedicineBundle\Repository\MedicineStockRepository")
 */
class MedicineStock
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
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\MedicineBundle\Entity\MedicineBrand", inversedBy="medicineStock")
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private $medicineBrand;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\MedicineBundle\Entity\MedicinePurchaseItem", mappedBy="medicineStock")
     * @ORM\OrderBy({"expirationDate" = "ASC"})
     **/
    private $medicinePurchaseItems;

	/**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\MedicineBundle\Entity\MedicinePrepurchaseItem", mappedBy="medicineStock")
     **/
    private $medicinePrepurchaseItems;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\MedicineBundle\Entity\MedicineSalesItem", mappedBy="medicineStock")
     **/
    private $medicineSalesItems;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\MedicineBundle\Entity\MedicineSalesReturn", mappedBy="medicineStock")
     **/
    private $medicineSalesReturns;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\MedicineBundle\Entity\MedicineDamage", mappedBy="medicineStock" )
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private  $medicineDamages;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\MedicineBundle\Entity\MedicinePurchaseReturnItem", mappedBy="medicineStock" )
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private  $medicinePurchaseReturnItems;

     /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\MedicineBundle\Entity\MedicineSalesTemporary", mappedBy="medicineStock")
     **/
    private $medicineSalesTemporary;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\MedicineBundle\Entity\MedicineConfig", inversedBy="medicineStock")
     **/
    private $medicineConfig;



    /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\ProductUnit", inversedBy="medicineStocks")
     **/
    private $unit;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\MedicineBundle\Entity\MedicineParticular", inversedBy="medicineStockRacks")
     **/
    private $rackNo;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\MedicineBundle\Entity\MedicineStockHouse", mappedBy="medicineStock")
     **/
    private $stockHouses;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\MedicineBundle\Entity\MedicineParticular", inversedBy="accessoriesStockBrand")
     **/
    private $accessoriesBrand;

     /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\MedicineBundle\Entity\MedicineParticular")
     **/
    private $category;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255,nullable = true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=255,nullable = true)
     */
    private $slug;

    /**
     * @var string
     *
     * @ORM\Column(name="brandName", type="string", length=255,nullable = true)
     */
    private $brandName;

    /**
     * @var string
     *
     * @ORM\Column(name="mode", type="string", length = 20,nullable = true)
     */
    private $mode = 'medicine';

    /**
     * @var integer
     *
     * @ORM\Column(name="code", type="integer", nullable=true)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="sku", type="string", length=50, nullable = true)
     */
    private $sku;


    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", nullable = true)
     */
    private $description;


    /**
     * @var integer
     *
     * @ORM\Column(name="openingQuantity", type="integer", nullable=true)
     */
    private $openingQuantity;

    /**
     * @var integer
     *
     * @ORM\Column(name="minQuantity", type="integer", nullable=true)
     */
    private $minQuantity = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="maxQuantity", type="integer", nullable=true)
     */
    private $maxQuantity;

    /**
     * @var integer
     *
     * @ORM\Column(name="bonusQuantity", type="integer", nullable=true)
     */
    private $bonusQuantity;

    /**
     * @var integer
     *
     * @ORM\Column(name="bonusAdjustment", type="integer", nullable=true)
     */
    private $bonusAdjustment;

     /**
     * @var integer
     *
     * @ORM\Column(name="adjustmentQuantity", type="integer", nullable=true)
     */
    private $adjustmentQuantity;

    /**
     * @var integer
     *
     * @ORM\Column(name="reorderQuantity", type="integer", nullable=true)
     */
    private $reorderQuantity;

    /**
     * @var integer
     *
     * @ORM\Column(name="remainingQuantity", type="integer", nullable=true)
     */
    private $remainingQuantity = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="purchaseQuantity", type="integer", nullable=true)
     */
    private $purchaseQuantity;

    /**
     * @var integer
     *
     * @ORM\Column(name="purchaseReturnQuantity", type="integer", nullable=true)
     */
    private $purchaseReturnQuantity=0;

    /**
     * @var integer
     *
     * @ORM\Column(name="salesQuantity", type="integer", nullable=true)
     */
    private $salesQuantity = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="salesReturnQuantity", type="integer", nullable=true)
     */
    private $salesReturnQuantity = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="pack", type="integer", nullable = true)
     */
    private $pack = 1;

    /**
     * @var string
     *
     * @ORM\Column(name="barcode", type="string", nullable = true)
     */
    private $barcode;


    /**
     * @var integer
     *
     * @ORM\Column(name="damageQuantity", type="integer", nullable=true)
     */
    private $damageQuantity = 0;

    /**
     * @var float
     *
     * @ORM\Column(name="purchasePrice", type="float", nullable=true)
     */
    private $purchasePrice =0;

    /**
     * @var float
     *
     * @ORM\Column(name="tradePrice", type="float", nullable=true)
     */
    private $tradePrice =0;

    /**
     * @var float
     *
     * @ORM\Column(name="averagePurchasePrice", type="float", nullable=true)
     */
    private $averagePurchasePrice = 0;


    /**
     * @var float
     *
     * @ORM\Column(name="salesPrice", type="float",  nullable=true)
     */
    private $salesPrice = 0;

    /**
     * @var float
     *
     * @ORM\Column(name="averageSalesPrice", type="float", nullable=true)
     */
    private $averageSalesPrice = 0;


    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="boolean",  nullable=true)
     */
    private $status = true;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isDelete", type="boolean" )
     */
    private $isDelete = false;


    /**
     * @var boolean
     *
     * @ORM\Column(name="printHide", type="boolean",  nullable=true)
     */
    private $printHide;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isWeb", type="boolean",  nullable=true)
     */
    private $isWeb = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="openingApprove", type="boolean",  nullable=true)
     */
    private $openingApprove = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isAndroid", type="boolean",  nullable=true)
     */
    private $isAndroid = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isImport", type="boolean",  nullable=true)
     */
    private $isImport = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="noDiscount", type="boolean",  nullable=true)
     */
    private $noDiscount = false;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $path;


    /**
     * @Assert\File(maxSize="8388608")
     */
    protected $file;

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
     * @return MedicineBrand
     */
    public function getMedicineBrand()
    {
        return $this->medicineBrand;
    }

    /**
     * @param MedicineBrand $medicineBrand
     */
    public function setMedicineBrand($medicineBrand)
    {
        $this->medicineBrand = $medicineBrand;
    }


    /**
     * @return float
     */
    public function getPurchasePrice()
    {
        return $this->purchasePrice;
    }

    /**
     * @param float $purchasePrice
     */
    public function setPurchasePrice($purchasePrice)
    {
        $this->purchasePrice = $purchasePrice;
    }

    /**
     * @return float
     */
    public function getSalesPrice()
    {
        return $this->salesPrice;
    }

    /**
     * @param float $salesPrice
     */
    public function setSalesPrice($salesPrice)
    {
        $this->salesPrice = $salesPrice;
    }

    /**
     * @return int
     */
    public function getOpeningQuantity()
    {
        return $this->openingQuantity;
    }

    /**
     * @param int $openingQuantity
     */
    public function setOpeningQuantity($openingQuantity)
    {
        $this->openingQuantity = $openingQuantity;
    }

    /**
     * @return int
     */
    public function getMinQuantity()
    {
        return $this->minQuantity;
    }

    /**
     * @param int $minQuantity
     */
    public function setMinQuantity($minQuantity)
    {
        $this->minQuantity = $minQuantity;
    }

    /**
     * @return int
     */
    public function getMaxQuantity()
    {
        return $this->maxQuantity;
    }

    /**
     * @param int $maxQuantity
     */
    public function setMaxQuantity($maxQuantity)
    {
        $this->maxQuantity = $maxQuantity;
    }

    /**
     * @return int
     */
    public function getRemainingQuantity()
    {
        return $this->remainingQuantity;
    }

    /**
     * @param int $remainingQuantity
     */
    public function setRemainingQuantity($remainingQuantity)
    {
        $this->remainingQuantity = $remainingQuantity;
    }

    /**
     * @return int
     */
    public function getPurchaseQuantity()
    {
        return $this->purchaseQuantity;
    }

    /**
     * @param int $purchaseQuantity
     */
    public function setPurchaseQuantity($purchaseQuantity)
    {
        $this->purchaseQuantity = $purchaseQuantity;
    }

    /**
     * @return int
     */
    public function getPurchaseReturnQuantity()
    {
        return $this->purchaseReturnQuantity;
    }

    /**
     * @param int $purchaseReturnQuantity
     */
    public function setPurchaseReturnQuantity($purchaseReturnQuantity)
    {
        $this->purchaseReturnQuantity = $purchaseReturnQuantity;
    }

    /**
     * @return int
     */
    public function getSalesQuantity()
    {
        return $this->salesQuantity;
    }

    /**
     * @param int $salesQuantity
     */
    public function setSalesQuantity($salesQuantity)
    {
        $this->salesQuantity = $salesQuantity;
    }

    /**
     * @return int
     */
    public function getSalesReturnQuantity()
    {
        return $this->salesReturnQuantity;
    }

    /**
     * @param int $salesReturnQuantity
     */
    public function setSalesReturnQuantity($salesReturnQuantity)
    {
        $this->salesReturnQuantity = $salesReturnQuantity;
    }

    /**
     * @return int
     */
    public function getDamageQuantity()
    {
        return $this->damageQuantity;
    }

    /**
     * @param int $damageQuantity
     */
    public function setDamageQuantity($damageQuantity)
    {
        $this->damageQuantity = $damageQuantity;
    }

    /**
     * @return mixed
     */
    public function getSku()
    {
        return $this->sku;
    }

    /**
     * @param mixed $sku
     */
    public function setSku($sku)
    {
        $this->sku = $sku;
    }

    /**
     * @return mixed
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param mixed $code
     */
    public function setCode($code)
    {
        $this->code = $code;
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
     * @return bool
     */
    public function isNoDiscount()
    {
        return $this->noDiscount;
    }

    /**
     * @param bool $noDiscount
     */
    public function setNoDiscount($noDiscount)
    {
        $this->noDiscount = $noDiscount;
    }

    /**
     * @return MedicinePurchaseItem
     */
    public function getMedicinePurchaseItems()
    {
        return $this->medicinePurchaseItems;
    }

    public function getMedicineStockSkuQuantity(){

        $medicineStockSkuQuantity = $this->getSku().'-'.$this->getName().'-'.$this->getRackNo()->getName().'('.$this->getRemainingQuantity().')';
            return $medicineStockSkuQuantity;

    }

    /**
     * @return MedicineParticular
     */
    public function getRackNo()
    {
        return $this->rackNo;
    }

    /**
     * @param MedicineParticular $rackNo
     */
    public function setRackNo($rackNo)
    {
        $this->rackNo = $rackNo;
    }

    /**
     * @return MedicineSalesItem
     */
    public function getMedicineSalesItems()
    {
        return $this->medicineSalesItems;
    }

    /**
     * @return string
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * @param string $mode
     */
    public function setMode($mode)
    {
        $this->mode = $mode;
    }

    /**
     * @return MedicineParticular
     */
    public function getAccessoriesBrand()
    {
        return $this->accessoriesBrand;
    }

    /**
     * @param MedicineParticular $accessoriesBrand
     */
    public function setAccessoriesBrand($accessoriesBrand)
    {
        $this->accessoriesBrand = $accessoriesBrand;
    }

    /**
     * @return string
     */
    public function getBrandName()
    {
        return $this->brandName;
    }

    /**
     * @param string $brandName
     */
    public function setBrandName($brandName)
    {
        $this->brandName = $brandName;
    }

    /**
     * @return MedicineSalesTemporary
     */
    public function getMedicineSalesTemporary()
    {
        return $this->medicineSalesTemporary;
    }

    /**
     * @return MedicineDamage
     */
    public function getMedicineDamages()
    {
        return $this->medicineDamages;
    }

    /**
     * @param mixed $medicineDamages
     */
    public function setMedicineDamages($medicineDamages)
    {
        $this->medicineDamages = $medicineDamages;
    }

    /**
     * @return MedicinePurchaseReturnItem
     */
    public function getMedicinePurchaseReturnItems()
    {
        return $this->medicinePurchaseReturnItems;
    }

    /**
     * @return MedicineSalesReturn
     */
    public function getMedicineSalesReturns()
    {
        return $this->medicineSalesReturns;
    }

    /**
     * @return int
     */
    public function getPack()
    {
        return $this->pack;
    }

    /**
     * @param int $pack
     */
    public function setPack($pack)
    {
        $this->pack = $pack;
    }

    /**
     * @return float
     */
    public function getAverageSalesPrice()
    {
        return $this->averageSalesPrice;
    }

    /**
     * @param float $averageSalesPrice
     */
    public function setAverageSalesPrice($averageSalesPrice)
    {
        $this->averageSalesPrice = $averageSalesPrice;
    }

    /**
     * @return float
     */
    public function getAveragePurchasePrice()
    {
        return $this->averagePurchasePrice;
    }

    /**
     * @param float $averagePurchasePrice
     */
    public function setAveragePurchasePrice($averagePurchasePrice)
    {
        $this->averagePurchasePrice = $averagePurchasePrice;
    }

    /**
     * @return bool
     */
    public function isWeb()
    {
        return $this->isWeb;
    }

    /**
     * @param bool $isWeb
     */
    public function setIsWeb($isWeb)
    {
        $this->isWeb = $isWeb;
    }

    /**
     * @return bool
     */
    public function isAndroid()
    {
        return $this->isAndroid;
    }

    /**
     * @param bool $isAndroid
     */
    public function setIsAndroid($isAndroid)
    {
        $this->isAndroid = $isAndroid;
    }

    /**
     * @return bool
     */
    public function isPrintHide()
    {
        return $this->printHide;
    }

    /**
     * @param bool $printHide
     */
    public function setPrintHide($printHide)
    {
        $this->printHide = $printHide;
    }

    /**
     * Sets file.
     *
     * @param Particular $file
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
    }

    /**
     * Get file.
     *
     * @return Particular
     */
    public function getFile()
    {
        return $this->file;
    }

    public function getAbsolutePath()
    {
        return null === $this->path
            ? null
            : $this->getUploadRootDir().'/'.$this->path;
    }

    public function getWebPath()
    {
        return null === $this->path
            ? null
            : $this->getUploadDir().'/'.$this->path;
    }

    protected function getUploadRootDir()
    {
        return __DIR__.'/../../../../../web/'.$this->getUploadDir();
    }

    protected function getUploadDir()
    {
        return 'uploads/domain/'.$this->getMedicineConfig()->getGlobalOption()->getId().'/product/';
    }

    public function upload()
    {
        // the file property can be empty if the field is not required
        if (null === $this->getFile()) {
            return;
        }

        // use the original file name here but you should
        // sanitize it at least to avoid any security issues

        // move takes the target directory and then the
        // target filename to move to
        $this->getFile()->move(
            $this->getUploadRootDir(),
            $this->getFile()->getClientOriginalName()
        );

        // set the path property to the filename where you've saved the file
        $this->path = $this->getFile()->getClientOriginalName();

        // clean up the file property as you won't need it anymore
        $this->file = null;
    }

    /**
     * @return bool
     */
    public function isImport()
    {
        return $this->isImport;
    }


    /**
     * @param bool $isImport
     */
    public function setIsImport($isImport)
    {
        $this->isImport = $isImport;
    }

    /**
     * @return mixed
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param mixed $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return bool
     */
    public function isOpeningApprove()
    {
        return $this->openingApprove;
    }

    /**
     * @param bool $openingApprove
     */
    public function setOpeningApprove($openingApprove)
    {
        $this->openingApprove = $openingApprove;
    }

    /**
     * @return int
     */
    public function getReorderQuantity()
    {
        return $this->reorderQuantity;
    }

    /**
     * @param int $reorderQuantity
     */
    public function setReorderQuantity($reorderQuantity)
    {
        $this->reorderQuantity = $reorderQuantity;
    }

    /**
     * @return string
     */
    public function getBarcode()
    {
        return $this->barcode;
    }

    /**
     * @param string $barcode
     */
    public function setBarcode($barcode)
    {
        $this->barcode = $barcode;
    }

    /**
     * @return int
     */
    public function getBonusQuantity()
    {
        return $this->bonusQuantity;
    }

    /**
     * @param int $bonusQuantity
     */
    public function setBonusQuantity($bonusQuantity)
    {
        $this->bonusQuantity = $bonusQuantity;
    }

    /**
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    /**
     * @return int
     */
    public function getAdjustmentQuantity()
    {
        return $this->adjustmentQuantity;
    }

    /**
     * @param int $adjustmentQuantity
     */
    public function setAdjustmentQuantity($adjustmentQuantity)
    {
        $this->adjustmentQuantity = $adjustmentQuantity;
    }

    /**
     * @return int
     */
    public function getBonusAdjustment()
    {
        return $this->bonusAdjustment;
    }

    /**
     * @param int $bonusAdjustment
     */
    public function setBonusAdjustment($bonusAdjustment)
    {
        $this->bonusAdjustment = $bonusAdjustment;
    }

    /**
     * @return bool
     */
    public function isDelete()
    {
        return $this->isDelete;
    }

    /**
     * @param bool $isDelete
     */
    public function setIsDelete($isDelete)
    {
        $this->isDelete = $isDelete;
    }

    /**
     * @return float
     */
    public function getTradePrice()
    {
        return $this->tradePrice;
    }

    /**
     * @param float $tradePrice
     */
    public function setTradePrice($tradePrice)
    {
        $this->tradePrice = $tradePrice;
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
     * @return mixed
     */
    public function getEcommerceItem()
    {
        return $this->ecommerceItem;
    }

}

