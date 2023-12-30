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
 * @ORM\Table(name = "item_master")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\InventoryBundle\Repository\ProductRepository")
 */
class Product implements CodeAwareEntity
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
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\InventoryBundle\Entity\InventoryConfig", inversedBy="products" )
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private  $inventoryConfig;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\Item", mappedBy="masterItem")
     */
    protected $items;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\PurchaseVendorItem", mappedBy="masterItem")
     */
    protected $purchaseVendorItem;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\PrePurchaseItem", mappedBy="masterItem")
     */
    protected $prePurchaseItems;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\StockItem", mappedBy="product")
     */
    protected $stockItems;

    /**
     * @ORM\ManyToOne(targetEntity="Product\Bundle\ProductBundle\Entity\Category", inversedBy="masterProducts" )
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private  $category;

    /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\ProductUnit", inversedBy="masterProducts" )
     **/
    private  $productUnit;

	/**
	 * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\ItemMetaAttribute", mappedBy="product" , cascade={"remove"}  )
	 **/
	private  $itemMetaAttributes;

	/**
	 * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\ItemKeyValue", mappedBy="product" , cascade={"remove"}  )
	 * @ORM\OrderBy({"sorting" = "ASC"})
	 **/
	private  $itemKeyValues;

	/**
	 * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\ItemGallery", mappedBy="product" , cascade={"remove"} )
	 */
	protected $itemGalleries;

	/**
	 * @ORM\ManyToOne(targetEntity="Setting\Bundle\LocationBundle\Entity\Country", inversedBy="products")
	 */
	protected $country;

	/**
	 * @ORM\ManyToOne(targetEntity="Appstore\Bundle\InventoryBundle\Entity\ItemBrand", inversedBy="products")
	 */
	protected $brand;

	/**
	 * @ORM\ManyToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\ItemColor", inversedBy="products" )
	 * @ORM\OrderBy({"id" = "ASC"})
	 **/
	private  $colors;

	/**
	 * @ORM\ManyToOne(targetEntity="Appstore\Bundle\InventoryBundle\Entity\ItemSize", inversedBy="products" )
	 **/
	private  $size;



	/**
     * @Gedmo\Slug(fields={"name"}, updatable=false, separator="-")
     * @ORM\Column(length=255, unique=true)
     */
    private $slug;


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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="code", type="integer", length=50)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="unit", type="string", length=50, nullable = true)
     */
    private $unit;

    /**
     * @var array()
     *
     * @ORM\Column(name="ageGroup", type="array", nullable = true)
     */
    private $ageGroup;


    /**
     * @var string
     *
     * @ORM\Column(name="gender", type="string", length=255 , nullable = true)
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
	 * @ORM\Column(name="webName", type="string", length=255, nullable = true)
	 */
	private $webName;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="source", type="string", length=20 , nullable = true)
	 */
	private $source;

	/**
	 * @var float
	 *
	 * @ORM\Column(name="purchasePrice", type="float", nullable = true)
	 */
	private $purchasePrice;

	/**
	 * @var float
	 *
	 * @ORM\Column(name="salesPrice", type="float", nullable = true)
	 */
	private $salesPrice;

	/**
	 * @var float
	 *
	 * @ORM\Column(name="webPrice", type="float", nullable = true)
	 */
	private $webPrice;

	/**
	 * @var float
	 *
	 * @ORM\Column(name="wholeSalesPrice", type="float", nullable = true)
	 */
	private $wholeSalesPrice;

	/**
	 * @var float
	 *
	 * @ORM\Column(name="overHeadCost", type="float", nullable = true)
	 */
	private $overHeadCost;


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

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Product
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
     * Set code
     *
     * @param integer $code
     *
     * @return Product
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return integer
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set unit
     *
     * @param string $unit
     *
     * @return Product
     */
    public function setUnit($unit)
    {
        $this->unit = $unit;

        return $this;
    }

    /**
     * Get unit
     *
     * @return string
     */
    public function getUnit()
    {
        return $this->unit;
    }

    /**
     * Set status
     *
     * @param boolean $status
     *
     * @return Product
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
     * @return Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param Category $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
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
     * @return mixed
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @return mixed
     */
    public function getSTRPadCode()
    {
        $code = str_pad($this->getCode(),4, '0', STR_PAD_LEFT);
        return $code;
    }

    /**
     * @return PurchaseVendorItem
     */
    public function getPurchaseVendorItem()
    {
        return $this->product;
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
        return 'uploads/domain/'.$this->getInventoryConfig()->getGlobalOption()->getId().'/inventory/masterItem/';
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

    /**
     * @return ProductUnit
     */
    public function getProductUnit()
    {
        return $this->productUnit;
    }

    /**
     * @param ProductUnit $productUnit
     */
    public function setProductUnit($productUnit)
    {
        $this->productUnit = $productUnit;
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
     * @return StockItem
     */
    public function getStockItems()
    {
        return $this->stockItems;
    }

    public function getNameUnit()
    {
        $unit='';
        if($this->getProductUnit()){
            $unit =' - '.$this->getProductUnit()->getName();
        }
        return $this->name.$unit;
    }

    /**
     * @return PrePurchaseItem
     */
    public function getPrePurchaseItems()
    {
        return $this->prePurchaseItems;
    }

	/**
	 * @return ItemMetaAttribute
	 */
	public function getItemMetaAttributes() {
		return $this->itemMetaAttributes;
	}

	/**
	 * @param ItemMetaAttribute $itemMetaAttributes
	 */
	public function setItemMetaAttributes( $itemMetaAttributes ) {
		$this->itemMetaAttributes = $itemMetaAttributes;
	}

	/**
	 * @return ItemKeyValue
	 */
	public function getItemKeyValues() {
		return $this->itemKeyValues;
	}

	/**
	 * @param ItemKeyValue $itemKeyValues
	 */
	public function setItemKeyValues( $itemKeyValues ) {
		$this->itemKeyValues = $itemKeyValues;
	}

	/**
	 * @return ItemColor
	 */
	public function getColors() {
		return $this->colors;
	}

	/**
	 * @param ItemColor $colors
	 */
	public function setColors( $colors ) {
		$this->colors = $colors;
	}

	/**
	 * @return ItemSize
	 */
	public function getSize() {
		return $this->size;
	}

	/**
	 * @param ItemSize $size
	 */
	public function setSize( $size ) {
		$this->size = $size;
	}

	/**
	 * @return Discount
	 */
	public function getDiscount() {
		return $this->discount;
	}

	/**
	 * @param Discount $discount
	 */
	public function setDiscount( $discount ) {
		$this->discount = $discount;
	}

	/**
	 * @return Promotion
	 */
	public function getTag() {
		return $this->tag;
	}

	/**
	 * @param Promotion $tag
	 */
	public function setTag( $tag ) {
		$this->tag = $tag;
	}

	/**
	 * @return Promotion
	 */
	public function getPromotion() {
		return $this->promotion;
	}

	/**
	 * @param Promotion $promotion
	 */
	public function setPromotion( $promotion ) {
		$this->promotion = $promotion;
	}

	/**
	 * @return string
	 */
	public function getWarningLabel(): string {
		return $this->warningLabel;
	}

	/**
	 * @param string $warningLabel
	 */
	public function setWarningLabel( string $warningLabel ) {
		$this->warningLabel = $warningLabel;
	}

	/**
	 * @return string
	 */
	public function getWarningText(): string {
		return $this->warningText;
	}

	/**
	 * @param string $warningText
	 */
	public function setWarningText( string $warningText ) {
		$this->warningText = $warningText;
	}

	/**
	 * @return string
	 */
	public function getWebName(): string {
		return $this->webName;
	}

	/**
	 * @param string $webName
	 */
	public function setWebName( string $webName ) {
		$this->webName = $webName;
	}

	/**
	 * @return mixed
	 */
	public function getSource() {
		return $this->source;
	}

	/**
	 * @param mixed $source
	 * Inventory
	 * Non-inventory
	 */
	public function setSource( $source ) {
		$this->source = $source;
	}

	/**
	 * @return float
	 */
	public function getPurchasePrice(){
		return $this->purchasePrice;
	}

	/**
	 * @param float $purchasePrice
	 */
	public function setPurchasePrice( float $purchasePrice ) {
		$this->purchasePrice = $purchasePrice;
	}

	/**
	 * @return float
	 */
	public function getSalesPrice() {
		return $this->salesPrice;
	}

	/**
	 * @param float $salesPrice
	 */
	public function setSalesPrice( $salesPrice ) {
		$this->salesPrice = $salesPrice;
	}

	/**
	 * @return float
	 */
	public function getWebPrice() {
		return $this->webPrice;
	}

	/**
	 * @param float $webPrice
	 */
	public function setWebPrice( $webPrice ) {
		$this->webPrice = $webPrice;
	}

	/**
	 * @return float
	 */
	public function getWholeSalesPrice() {
		return $this->wholeSalesPrice;
	}

	/**
	 * @param float $wholeSalesPrice
	 */
	public function setWholeSalesPrice( $wholeSalesPrice ) {
		$this->wholeSalesPrice = $wholeSalesPrice;
	}

	/**
	 * @return float
	 */
	public function getOverHeadCost() {
		return $this->overHeadCost;
	}

	/**
	 * @param float $overHeadCost
	 */
	public function setOverHeadCost( $overHeadCost ) {
		$this->overHeadCost = $overHeadCost;
	}


}

