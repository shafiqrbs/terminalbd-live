<?php

namespace Appstore\Bundle\InventoryBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Product\Bundle\ProductBundle\Entity\Category;
use Setting\Bundle\ToolBundle\Entity\ProductUnit;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * PurchaseVendorItem
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Appstore\Bundle\InventoryBundle\Repository\PurchaseVendorItemRepository")
 */
class PurchaseVendorItem
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
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\InventoryBundle\Entity\InventoryConfig", inversedBy="purchaseVendorItems" , cascade={"detach","merge"} )
     **/
    private  $inventoryConfig;


    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\InventoryBundle\Entity\Purchase", inversedBy="purchaseVendorItems" )
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @ORM\OrderBy({"id" = "DESC"})
     **/
    private  $purchase;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\PurchaseItem", mappedBy="purchaseVendorItem" , cascade={"remove"} )
     **/
    private  $purchaseItems;



    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\GoodsItem", mappedBy="purchaseVendorItem" , cascade={"remove"} )
     * @ORM\OrderBy({"id" = "ASC"})
     **/
    private  $goodsItems;


    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\InventoryBundle\Entity\Product", inversedBy="purchaseVendorItem" )
     **/
    private  $masterItem;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\ItemMetaAttribute", mappedBy="purchaseVendorItem" , cascade={"remove"}  )
     **/
    private  $itemMetaAttributes;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\ItemKeyValue", mappedBy="purchaseVendorItem" , cascade={"remove"}  )
     * @ORM\OrderBy({"sorting" = "ASC"})
     **/
    private  $itemKeyValues;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\ItemGallery", mappedBy="purchaseVendorItem" , cascade={"remove"} )
     */
    protected $itemGalleries;

    /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\LocationBundle\Entity\Country", inversedBy="purchaseVendorItems")
     */
    protected $country;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\InventoryBundle\Entity\ItemBrand", inversedBy="purchaseVendorItems")
     */
    protected $brand;

    /**
     * @ORM\ManyToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\ItemColor", inversedBy="purchaseVendorItems" )
     * @ORM\OrderBy({"id" = "ASC"})
     **/
    private  $itemColors;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\InventoryBundle\Entity\ItemSize", inversedBy="purchaseVendorItems" )
     **/
    private  $size;


    /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\ProductUnit", inversedBy="purchaseVendorItem" )
     **/
    private  $productUnit;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable = true)
     */
    private $name;


    /**
     * @var string
     *
     * @ORM\Column(name="webName", type="string", length=255, nullable = true)
     */
    private $webName;

    /**
     * @Gedmo\Slug(handlers={
     *      @Gedmo\SlugHandler(class="Gedmo\Sluggable\Handler\TreeSlugHandler", options={
     *          @Gedmo\SlugHandlerOption(name="parentRelationField", value="masterItem"),
     *          @Gedmo\SlugHandlerOption(name="separator", value="-")
     *      })
     * }, fields={"webName","code"})
     * @Doctrine\ORM\Mapping\Column(length=255, unique=true, nullable = true)
     */

    private $slug;

    /**
     * @var string
     *
     * @ORM\Column(name="sku", type="string", length=50,nullable = true)
     */
    private $sku;

    /**
     * @var integer
     *
     * @ORM\Column(name="code", type="integer", nullable=true)
     */
    private $code;


    /**
     * @var integer
     *
     * @ORM\Column(name="quantity", type="integer", nullable = true)
     */
    private $quantity;

    /**
     * @var integer
     *
     * @ORM\Column(name="masterQuantity", type="integer", nullable = true)
     */
    private $masterQuantity;

    /**
     * @var string
     *
     * @ORM\Column(name="purchasePrice", type="decimal", nullable = true)
     */
    private $purchasePrice;

    /**
     * @var string
     *
     * @ORM\Column(name="subTotalPurchasePrice", type="decimal", nullable = true)
     */
    private $subTotalPurchasePrice;

    /**
     * @var string
     *
     * @ORM\Column(name="salesPrice", type="decimal", nullable = true)
     */
    private $salesPrice;

    /**
     * @var string
     *
     * @ORM\Column(name="discountPrice", type="decimal", nullable = true)
     */
    private $discountPrice;

    /**
     * @var string
     *
     * @ORM\Column(name="subTotalSalesPrice", type="decimal", nullable = true)
     */
    private $subTotalSalesPrice;


    /**
     * @var string
     *
     * @ORM\Column(name="webPrice", type="decimal", nullable = true)
     */
    private $webPrice;

     /**
     * @var string
     *
     * @ORM\Column(name="overHeadCost", type="decimal", nullable = true)
     */
    private $overHeadCost;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isWeb", type="boolean", nullable = true)
     */
    private $isWeb = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="boolean", nullable = true)
     */
    private $status = true;

    /**
     * @var boolean
     *
     * @ORM\Column(name="preOrder", type="boolean", nullable = true)
     */
    private $preOrder = false;


     /**
     * @var boolean
     *
     * @ORM\Column(name="subProduct", type="boolean", nullable = true)
     */
    private $subProduct = false;


    /**
     * @var string
     *
     * @ORM\Column(name="subTotalWebPrice", type="decimal", nullable = true)
     */
    private $subTotalWebPrice;

    /**
     * @var array()
     *
     * @ORM\Column(name="ageGroup", type="array", nullable = true)
     */
    private $ageGroup;


    /**
     * @var string
     *
     * @ORM\Column(name="gender", type="string", length=10 , nullable = true)
     */
    private $gender;

    /**
     * @var string
     *
     * @ORM\Column(name="warningLabel", type="string", length=10 , nullable = true)
     */
    private $warningLabel;

    /**
     * @var string
     *
     * @ORM\Column(name="warningText", type="string", length=255 , nullable = true)
     */
    private $warningText;


    /**
     * @var string
     *
     * @ORM\Column(name="source", type="string", length=20 , nullable = true)
     */
    private $source;

    /**
     * @var text
     *
     * @ORM\Column(name="content", type="text", nullable=true)
     */
    private $content;


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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $path;

    /**
     * @Assert\File(maxSize="8388608")
     */
    protected $file;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    public function removeGoodsItems($group)
    {
        //optionally add a check here to see that $group exists before removing it.
        return $this->goodsItems->removeElement($group);
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return PurchaseVendorItem
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
     * Set quantity
     *
     * @param integer $quantity
     *
     * @return PurchaseVendorItem
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return integer
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set purchasePrice
     *
     * @param string $purchasePrice
     *
     * @return PurchaseVendorItem
     */
    public function setPurchasePrice($purchasePrice)
    {
        $this->purchasePrice = $purchasePrice;

        return $this;
    }

    /**
     * Get purchasePrice
     *
     * @return string
     */
    public function getPurchasePrice()
    {
        return $this->purchasePrice;
    }

    /**
     * Set salesPrice
     *
     * @param string $salesPrice
     *
     * @return PurchaseVendorItem
     */
    public function setSalesPrice($salesPrice)
    {
        $this->salesPrice = $salesPrice;

        return $this;
    }

    /**
     * Get salesPrice
     *
     * @return string
     */
    public function getSalesPrice()
    {
        return $this->salesPrice;
    }

    /**
     * Set webPrice
     *
     * @param string $webPrice
     *
     * @return PurchaseVendorItem
     */
    public function setWebPrice($webPrice)
    {
        $this->webPrice = $webPrice;

        return $this;
    }

    /**
     * Get webPrice
     *
     * @return string
     */
    public function getWebPrice()
    {
        return $this->webPrice;
    }

    /**
     * @return mixed
     */
    public function getPurchase()
    {
        return $this->purchase;
    }

    /**
     * @param mixed $purchase
     */
    public function setPurchase($purchase)
    {
        $this->purchase = $purchase;
    }

    /**
     * @return PurchaseItem
     */
    public function getPurchaseItems()
    {
        return $this->purchaseItems;

    }

    /**
     * @return string
     */
    public function getSubTotalPurchasePrice()
    {
        return $this->subTotalPurchasePrice;
    }

    /**
     * @param string $subTotalPurchasePrice
     */
    public function setSubTotalPurchasePrice($subTotalPurchasePrice)
    {
        $this->subTotalPurchasePrice = $subTotalPurchasePrice;
    }

    /**
     * @return string
     */
    public function getSubTotalSalesPrice()
    {
        return $this->subTotalSalesPrice;
    }

    /**
     * @param string $subTotalSalesPrice
     */
    public function setSubTotalSalesPrice($subTotalSalesPrice)
    {
        $this->subTotalSalesPrice = $subTotalSalesPrice;
    }

    /**
     * @return string
     */
    public function getSubTotalWebPrice()
    {
        return $this->subTotalWebPrice;
    }

    /**
     * @param string $subTotalWebPrice
     */
    public function setSubTotalWebPrice($subTotalWebPrice)
    {
        $this->subTotalWebPrice = $subTotalWebPrice;
    }

    /**
     * @return boolean
     */
    public function getIsWeb()
    {
        return $this->isWeb;
    }

    /**
     * @param boolean $isWeb
     */
    public function setIsWeb($isWeb)
    {
        $this->isWeb = $isWeb;
    }

    /**
     * @return Product
     */
    public function getMasterItem()
    {
        return $this->masterItem;
    }

    /**
     * @param Product $masterItem
     */
    public function setMasterItem($masterItem)
    {
        $this->masterItem = $masterItem;
    }

    /**
     * @return string
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * @param string $gender
     */
    public function setGender($gender)
    {
        $this->gender = $gender;
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
     * @return mixed
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param mixed $country
     */
    public function setCountry($country)
    {
        $this->country = $country;
    }

    /**
     * @return mixed
     */
    public function getBrand()
    {
        return $this->brand;
    }

    /**
     * @param mixed $brand
     */
    public function setBrand($brand)
    {
        $this->brand = $brand;
    }

    /**
     * @return mixed
     */
    public function getItemMetaAttributes()
    {
        return $this->itemMetaAttributes;
    }

    /**
     * @return ItemGallery
     */
    public function getItemGalleries()
    {
        return $this->itemGalleries;
    }

    /**
     * @return string
     */
    public function getWebName()
    {
        return $this->webName;
    }

    /**
     * @param string $webName
     */
    public function setWebName($webName)
    {
        $this->webName = $webName;
    }

    /**
     * @return mixed
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param mixed $slug
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
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
     * Sets file.
     *
     * @param WebTheme $file
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
    }

    /**
     * Get file.
     *
     * @return WebTheme
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

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        if ($file = $this->getAbsolutePath()) {
            unlink($file);
        }
    }


    protected function getUploadRootDir()
    {
        return __DIR__.'/../../../../../web/'.$this->getUploadDir();
    }

    protected function getUploadDir()
    {
        return 'uploads/domain/'.$this->getInventoryConfig()->getGlobalOption()->getId().'/inventory/item/'.$this->getId().'/';
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
        $filename = date('YmdHmi') . "_" . $this->getFile()->getClientOriginalName();
        $this->getFile()->move(
            $this->getUploadRootDir(),
            $filename
        );

        // set the path property to the filename where you've saved the file
        $this->path = $filename;

        // clean up the file property as you won't need it anymore
        $this->file = null;
    }

    function deleteImageDirectory()
    {

        $dir = $this->getUploadDir();
        if (is_dir($dir) === true)
        {
            $files = array_diff(scandir($dir), array('.','..'));
            foreach ($files as $file) {
                (is_dir("$dir/$file")) ? delTree("$dir/$file") : unlink("$dir/$file");
            }
            return rmdir($dir);
        }
        return false;

    }


    public function  getPurchaseStockItem()
    {
        $quantity = 0;

        if(!$this->purchaseItems->isEmpty()) {

            foreach ($this->purchaseItems AS $purchaseItem) {
                if(!$purchaseItem->getStockItem()->isEmpty()) {
                    foreach ($purchaseItem->getStockItem() AS $item) {
                       $quantity += $item->getQuantity(); //$recipecost now $this->recipecost.

                    }

                }
            }
            return $quantity;
        }
        return false;
    }

    public function getColors()
    {
        if(!$this->purchaseItems->isEmpty()) {
            $color = array();
            foreach ($this->purchaseItems as $purchaseItem) {
                if (!$purchaseItem->getStockItem()->isEmpty()) {
                    foreach ($purchaseItem->getStockItem() AS $item) {
                        $color[] = $item->getItem()->getColor()->getName(); //$recipecost now $this->recipecost.

                    }
                }
            }
            return array_unique($color);
        }
    }
    public function getSizes()
    {
        if(!$this->purchaseItems->isEmpty()) {
            $size = array();
            foreach ($this->purchaseItems as $purchaseItem) {
                if (!$purchaseItem->getStockItem()->isEmpty()) {
                    foreach ($purchaseItem->getStockItem() AS $item) {
                        $size[] = $item->getItem()->getSize()->getName(); //$recipecost now $this->recipecost.

                    }
                }
            }
            return array_unique($size);
        }

    }

    /**
     * @return GoodsItem
     */
    public function getGoodsItems()
    {
        return $this->goodsItems;
    }

    /**
     * @return InventoryConfig
     */
    public function getInventoryConfig()
    {
        return $this->inventoryConfig;
    }

    /**
     * @param InventoryConfig $inventoryConfig
     */
    public function setInventoryConfig($inventoryConfig)
    {
        $this->inventoryConfig = $inventoryConfig;
    }

    /**
     * @return boolean
     */
    public function getSubProduct()
    {
        return $this->subProduct;
    }

    /**
     * @param boolean $subProduct
     */
    public function setSubProduct($subProduct)
    {
        $this->subProduct = $subProduct;
    }

    /**
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @param string $source
     * Inventory
     * FoodProduct
     * VirtualProduct
     */
    public function setSource($source)
    {
        $this->source = $source;
    }


    /**
     * @return mixed
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @param mixed $size
     */
    public function setSize($size)
    {
        $this->size = $size;
    }

    /**
     * @return ItemKeyValue
     */
    public function getItemKeyValues()
    {
        return $this->itemKeyValues;
    }

    public function __clone() {
        $this->id = null;
    }

    /**
     * @return OrderItem
     */
    public function getOrderItems()
    {
        return $this->orderItems;
    }

    /**
     * @return Discount
     */
    public function getDiscount()
    {
        return $this->discount;
    }

    /**
     * @param Discount $discount
     */
    public function setDiscount($discount)
    {
        $this->discount = $discount;
    }

    /**
     * @return mixed
     */
    public function getItemColors()
    {
        return $this->itemColors;
    }

    /**
     * @param mixed $itemColors
     */
    public function setItemColors($itemColors)
    {
        $this->itemColors = $itemColors;
    }

    /**
     * @return mixed
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * @param Promotion $tag
     */
    public function setTag($tag)
    {
        $this->tag = $tag;
    }

    /**
     * @return string
     */
    public function getDiscountPrice()
    {
        return $this->discountPrice;
    }

    /**
     * @param string $discountPrice
     */
    public function setDiscountPrice($discountPrice)
    {
        $this->discountPrice = $discountPrice;
    }

    /**
     * @return mixed
     */
    public function getPromotion()
    {
        return $this->promotion;
    }

    /**
     * @param mixed $promotion
     */
    public function setPromotion($promotion)
    {
        $this->promotion = $promotion;
    }

    /**
     * @return string
     */
    public function getOverHeadCost()
    {
        return $this->overHeadCost;
    }

    /**
     * @param string $overHeadCost
     */
    public function setOverHeadCost($overHeadCost)
    {
        $this->overHeadCost = $overHeadCost;
    }

    /**
     * @return int
     */
    public function getMasterQuantity()
    {
        return $this->masterQuantity;
    }

    /**
     * @param int $masterQuantity
     */
    public function setMasterQuantity($masterQuantity)
    {
        $this->masterQuantity = $masterQuantity;
    }

    /**
     * @return array
     */
    public function getAgeGroup()
    {
        return $this->ageGroup;
    }

    /**
     * @param array $ageGroup
     */
    public function setAgeGroup($ageGroup)
    {
        $this->ageGroup = $ageGroup;
    }


    public function stockReminigQnt(){
        $stockQnt = 0;
        $purchaseItems = $this->purchaseItems;
        foreach($purchaseItems as $item ){
           foreach ($item->getStockItem() as $stock){
               $stockQnt += $stock->getQuantity();
           }
        }

        return $stockQnt;
    }

    public function getSubProductQuantity(){

        $stockQnt = 0;
        $goodsItems = $this->goodsItems;
        foreach($goodsItems as $item ){
            $stockQnt += $item->getQuantity();
        }
        return $stockQnt;
    }

    /**
     * @return string
     */
    public function getSku()
    {
        return $this->sku;
    }

    /**
     * @param string $sku
     */
    public function setSku($sku)
    {
        $this->sku = $sku;
    }

    /**
     * @return int
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param int $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @return mixed
     */
    public function getSTRPadCode()
    {
        $code = str_pad($this->getCode(),6, '0', STR_PAD_LEFT);
        return $code;
    }

    /**
     * @return boolean
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param boolean $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getWarningLabel()
    {
        return $this->warningLabel;
    }

    /**
     * @param string $warningLabel
     */
    public function setWarningLabel($warningLabel)
    {
        $this->warningLabel = $warningLabel;
    }

    /**
     * @return string
     */
    public function getWarningText()
    {
        return $this->warningText;
    }

    /**
     * @param string $warningText
     */
    public function setWarningText($warningText)
    {
        $this->warningText = $warningText;
    }

    /**
     * @return ProductUnit
     */
    public function getProductUnit()
    {
        return $this->productUnit;
    }

    /**
     * @param mixed $productUnit
     */
    public function setProductUnit($productUnit)
    {
        $this->productUnit = $productUnit;
    }

    /**
     * @return bool
     */
    public function getPreOrder()
    {
        return $this->preOrder;
    }

    /**
     * @param bool $preOrder
     */
    public function setPreOrder($preOrder)
    {
        $this->preOrder = $preOrder;
    }


}

