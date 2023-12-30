<?php

namespace Appstore\Bundle\MedicineBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * MedicineBrand
 *
 * @ORM\Table("medicine_brand")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\MedicineBundle\Repository\MedicineBrandRepository")
 */
class MedicineBrand
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
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\GlobalOption", inversedBy="medicineBrands")
     **/
    private $globalOption;

     /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\MedicineBundle\Entity\MedicineGeneric", inversedBy="medicineBrands")
     **/
    private $medicineGeneric;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\MedicineBundle\Entity\MedicineCompany", inversedBy="medicineBrands")
     **/
    private $medicineCompany;


    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\MedicineBundle\Entity\MedicineStock", mappedBy="medicineBrand")
     **/
    private $medicineStock;



    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255), nullable=true
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="dar", type="string", length=100, nullable=true)
     */
    private $dar;

     /**
     * @var string
     *
     * @ORM\Column(name="useFor", type="string", length=50, nullable=true)
     */
    private $useFor;

    /**
     * @var string
     *
     * @ORM\Column(name="brand_id", type="string", length=255, nullable=true)
     */
    private $brandId;

/**
     * @var string
     *
     * @ORM\Column(name="generic_id", type="string", length=255, nullable=true)
     */
    private $genericId;

/**
     * @var string
     *
     * @ORM\Column(name="company_id", type="string", length=255, nullable=true)
     */
    private $companyId;


    /**
     * @var string
     *
     * @ORM\Column(name="medicineForm", type="string", length=255, nullable=true)
     */
    private $medicineForm;


    /**
     * @var string
     *
     * @ORM\Column(name="strength", type="string", length=100, nullable=true)
     */
    private $strength;


    /**
     * @var float
     *
     * @ORM\Column(name="price", type="float", length=255, nullable=true)
     */
    private $price;


    /**
     * @var string
     *
     * @ORM\Column(name="packSize", type="string", length=100, nullable=true)
     */
    private $packSize;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="boolean", nullable=true)
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
     * @return MedicineCompany
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
     * @return string
     */
    public function getInteraction()
    {
        return $this->interaction;
    }

    /**
     * @param string $interaction
     */
    public function setInteraction($interaction)
    {
        $this->interaction = $interaction;
    }

    /**
     * @return string
     */
    public function getModeOfAction()
    {
        return $this->modeOfAction;
    }

    /**
     * @param string $modeOfAction
     */
    public function setModeOfAction($modeOfAction)
    {
        $this->modeOfAction = $modeOfAction;
    }

    /**
     * @return string
     */
    public function getSideEffect()
    {
        return $this->sideEffect;
    }

    /**
     * @param string $sideEffect
     */
    public function setSideEffect($sideEffect)
    {
        $this->sideEffect = $sideEffect;
    }

    /**
     * @return string
     */
    public function getMedicineForm()
    {
        return $this->medicineForm;
    }

    /**
     * @param string $medicineForm
     */
    public function setMedicineForm($medicineForm)
    {
        $this->medicineForm = $medicineForm;
    }

    /**
     * @return string
     */
    public function getStrength()
    {
        return $this->strength;
    }

    /**
     * @param string $strength
     */
    public function setStrength($strength)
    {
        $this->strength = $strength;
    }

    /**
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param float $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }

    /**
     * @return string
     */
    public function getPackSize()
    {
        return $this->packSize;
    }

    /**
     * @param string $packSize
     */
    public function setPackSize($packSize)
    {
        $this->packSize = $packSize;
    }

    /**
     * @return MedicineGeneric
     */
    public function getMedicineGeneric()
    {
        return $this->medicineGeneric;
    }

    /**
     * @param MedicineGeneric $medicineGeneric
     */
    public function setMedicineGeneric($medicineGeneric)
    {
        $this->medicineGeneric = $medicineGeneric;
    }

    /**
     * @return string
     */
    public function getBrandId()
    {
        return $this->brandId;
    }

    /**
     * @param string $brandId
     */
    public function setBrandId($brandId)
    {
        $this->brandId = $brandId;
    }

    /**
     * @return string
     */
    public function getGenericId()
    {
        return $this->genericId;
    }

    /**
     * @param string $genericId
     */
    public function setGenericId($genericId)
    {
        $this->genericId = $genericId;
    }

    /**
     * @return string
     */
    public function getCompanyId()
    {
        return $this->companyId;
    }

    /**
     * @param string $companyId
     */
    public function setCompanyId($companyId)
    {
        $this->companyId = $companyId;
    }

    /**
     * @return MedicineCompany
     */
    public function getMedicineCompany()
    {
        return $this->medicineCompany;
    }

    /**
     * @param MedicineCompany $medicineCompany
     */
    public function setMedicineCompany($medicineCompany)
    {
        $this->medicineCompany = $medicineCompany;
    }

    /**
     * @return DmsInvoiceMedicine
     */
    public function getInvoiceMedicine()
    {
        return $this->invoiceMedicine;
    }


    /**
     * @return MedicineStock
     */
    public function getMedicineStock()
    {
        return $this->medicineStock;
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

	/**
	 * @return string
	 */
	public function getDar(){
		return $this->dar;
	}

	/**
	 * @param string $dar
	 */
	public function setDar( string $dar ) {
		$this->dar = $dar;
	}

	/**
	 * @return string
	 */
	public function getUseFor(){
		return $this->useFor;
	}

	/**
	 * @param string $useFor
	 */
	public function setUseFor($useFor ) {
		$this->useFor = $useFor;
	}

    /**
     * Sets file.
     *
     * @param Item $file
     */
    public function setFile($file = null)
    {
        $this->file = $file;
    }

    /**
     * Get file.
     *
     * @return Item
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

    public function getUploadDir()
    {
        return 'uploads/medicine/';
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
     * @param string $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * @return Item
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }


}

