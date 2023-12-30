<?php

namespace Appstore\Bundle\DoctorPrescriptionBundle\Entity;

use Appstore\Bundle\HospitalBundle\Entity\InvoiceParticular;
use Core\UserBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * DpsInvoiceParticular
 *
 * @ORM\Table( name = "dps_invoice_particular")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\DoctorPrescriptionBundle\Repository\DpsInvoiceParticularRepository")
 */
class DpsInvoiceParticular
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;


    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\DoctorPrescriptionBundle\Entity\DpsInvoice", inversedBy="invoiceParticulars")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @ORM\OrderBy({"id" = "ASC"})
     **/
    private $dpsInvoice;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\DoctorPrescriptionBundle\Entity\DpsParticular", inversedBy="invoiceParticular", cascade={"persist"} )
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private $dpsParticular;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\DoctorPrescriptionBundle\Entity\DpsService", inversedBy="invoiceParticular", cascade={"persist"} )
     **/
    private $dpsService;

    /**
     * @var string
     *
     * @ORM\Column(name="metaKey", type="string", length=225, nullable=true)
     */
    private $metaKey;

    /**
     * @var string
     *
     * @ORM\Column(name="metaValue", type="text", nullable=true)
     */
    private $metaValue;

    /**
     * @var string
     *
     * @ORM\Column(name="diseases", type="text", nullable=true)
     */
    private $diseases;

    /**
     * @var string
     *
     * @ORM\Column(name="teethPosition", type="string", length=25, nullable=true)
     */
    private $teethPosition;

    /**
     * @var array
     *
     * @ORM\Column(name="teethNo", type="array", nullable=true)
     */
    private $teethNo;


    /**
     * @var integer
     *
     * @ORM\Column(name="metaCheck", type="integer", nullable=true)
     */
    private $metaCheck;


    /**
     * @var boolean
     *
     * @ORM\Column(name="metaStatus", type="boolean", nullable=true)
     */
    private $metaStatus;


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
     * @return DpsInvoice
     */
    public function getDpsInvoice()
    {
        return $this->dpsInvoice;
    }

    /**
     * @param DpsInvoice $dpsInvoice
     */
    public function setDpsInvoice($dpsInvoice)
    {
        $this->dpsInvoice = $dpsInvoice;
    }

    /**
     * @return string
     */
    public function getMetaKey()
    {
        return $this->metaKey;
    }

    /**
     * @param string $metaKey
     */
    public function setMetaKey($metaKey)
    {
        $this->metaKey = $metaKey;
    }

    /**
     * @return string
     */
    public function getMetaValue()
    {
        return $this->metaValue;
    }

    /**
     * @param string $metaValue
     */
    public function setMetaValue($metaValue)
    {
        $this->metaValue = $metaValue;
    }

    /**
     * @return bool
     */
    public function getMetaStatus()
    {
        return $this->metaStatus;
    }

    /**
     * @param bool $metaStatus
     */
    public function setMetaStatus($metaStatus)
    {
        $this->metaStatus = $metaStatus;
    }

    /**
     * @return DpsParticular
     */
    public function getDpsParticular()
    {
        return $this->dpsParticular;
    }

    /**
     * @param DpsParticular $dpsParticular
     */
    public function setDpsParticular($dpsParticular)
    {
        $this->dpsParticular = $dpsParticular;
    }

    /**
     * @return string
     */
    public function getTeethPosition()
    {
        return $this->teethPosition;
    }

    /**
     * @param string $teethPosition
     */
    public function setTeethPosition($teethPosition)
    {
        $this->teethPosition = $teethPosition;
    }

    /**
     * @return array
     */
    public function getTeethNo()
    {
        return $this->teethNo;
    }

    /**
     * @param array $teethNo
     */
    public function setTeethNo($teethNo)
    {
        $this->teethNo = $teethNo;
    }

    /**
     * @return DpsService
     */
    public function getDpsService()
    {
        return $this->dpsService;
    }

    /**
     * @param DpsService $dpsService
     */
    public function setDpsService($dpsService)
    {
        $this->dpsService = $dpsService;
    }

    /**
     * @return int
     */
    public function getMetaCheck()
    {
        return $this->metaCheck;
    }

    /**
     * @param int $metaCheck
     */
    public function setMetaCheck($metaCheck)
    {
        $this->metaCheck = $metaCheck;
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
     * @param InvoiceParticular $file
     */
    public function setFile($file = null)
    {
        $this->file = $file;
    }

    /**
     * Get file.
     *
     * @return InvoiceParticular
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

    public function removeFileImage()
    {
        $path = null === $this->path
            ? null
            : $this->getUploadRootDir().'/'.$this->path;

        if ($file = $path) {
            unlink($file);
        }
    }


    protected function getUploadRootDir()
    {
        return __DIR__.'/../../../../../web/'.$this->getUploadDir();
    }

    public function getUploadDir()
    {
        return 'uploads/domain/dps/'.$this->getDpsInvoice()->getDpsConfig()->getId().'/';
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
        $filename = date('YmdHmi') . "-" . $this->getFile()->getClientOriginalName();
        $this->getFile()->move(
            $this->getUploadRootDir(),
            $filename
        );
        // set the path property to the filename where you've saved the file
        $this->path = $this->getFile()->getClientOriginalName();

        // clean up the file property as you won't need it anymore
        $this->file = null;
    }

    /**
     * @return mixed
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param mixed $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * @return string
     */
    public function getDiseases()
    {
        return $this->diseases;
    }

    /**
     * @param string $diseases
     */
    public function setDiseases($diseases)
    {
        $this->diseases = $diseases;
    }


}

