<?php

namespace Appstore\Bundle\MedicineBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * MedicineImport
 *
 * @ORM\Table("medicine_import")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\MedicineBundle\Repository\MedicineImportRepository")
 */
class MedicineImport
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
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\MedicineBundle\Entity\MedicineConfig")
     */
    protected $medicineConfig;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="name", type="string", length=255, nullable=true)
	 */
	private $name;

    /**
	 * @var string
	 *
	 * @ORM\Column(name="progress", type="string", length=20, nullable=true)
	 */
	private $progress='created';

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
	 * @Assert\File(maxSize="10M")
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
			: $this->getUploadRootDir(). $this->path;
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
        return 'uploads/domain/medicine/';
    }

	public function upload()
	{
		// the file property can be empty if the field is not required
		if (null === $this->getFile()) {
			return;
		}

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
	 * @return \DateTime
	 */
	public function getCreated(){
		return $this->created;
	}

	/**
	 * @param \DateTime $created
	 */
	public function setCreated( \DateTime $created ) {
		$this->created = $created;
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
	public function getProgress(): string {
		return $this->progress;
	}

	/**
	 * @param string $progress
	 */
	public function setProgress( string $progress ) {
		$this->progress = $progress;
	}

	/**
	 * @param mixed $path
	 */
	public function setPath( $path ) {
		$this->path = $path;
	}

    /**
     * @return mixed
     */
    public function getMedicineConfig()
    {
        return $this->medicineConfig;
    }

    /**
     * @param mixed $medicineConfig
     */
    public function setMedicineConfig($medicineConfig)
    {
        $this->medicineConfig = $medicineConfig;
    }




}

