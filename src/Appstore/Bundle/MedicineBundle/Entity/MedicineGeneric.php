<?php

namespace Appstore\Bundle\MedicineBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BrandCompany
 *
 * @ORM\Table("medicine_generic")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\MedicineBundle\Repository\MedicineGenericRepository")
 */
class MedicineGeneric
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
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\MedicineBundle\Entity\MedicineBrand", mappedBy="medicineGeneric" , cascade={"remove"})
     * @ORM\OrderBy({"name" = "ASC"})
     **/
    private $medicineBrands;


    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;


    /**
     * @var string
     *
     * @ORM\Column(name="generic_id", type="string", length=255, nullable=true)
     */
    private $genericId;


    /**
     * @var string
     *
     * @ORM\Column(name="precaution", type="string", length=255, nullable=true)
     */
    private $precaution;


    /**
     * @var string
     *
     * @ORM\Column(name="indication", type="string", length=255, nullable=true)
     */
    private $indication;


    /**
     * @var string
     *
     * @ORM\Column(name="contraIndication", type="string", length=255, nullable=true)
     */
    private $contraIndication;


    /**
     * @var string
     *
     * @ORM\Column(name="dose", type="string", length=255, nullable=true)
     */
    private $dose;


    /**
     * @var string
     *
     * @ORM\Column(name="sideEffect", type="string", length=255, nullable=true)
     */
    private $sideEffect;


    /**
     * @var string
     *
     * @ORM\Column(name="modeOfAction", type="string", length=255, nullable=true)
     */
    private $modeOfAction;

    /**
     * @var string
     *
     * @ORM\Column(name="interaction", type="string", length=255, nullable=true)
     */
    private $interaction;


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
    public function getDose()
    {
        return $this->dose;
    }

    /**
     * @param string $dose
     */
    public function setDose($dose)
    {
        $this->dose = $dose;
    }

    /**
     * @return string
     */
    public function getContraIndication()
    {
        return $this->contraIndication;
    }

    /**
     * @param string $contraIndication
     */
    public function setContraIndication($contraIndication)
    {
        $this->contraIndication = $contraIndication;
    }

    /**
     * @return string
     */
    public function getIndication()
    {
        return $this->indication;
    }

    /**
     * @param string $indication
     */
    public function setIndication($indication)
    {
        $this->indication = $indication;
    }

    /**
     * @return string
     */
    public function getPrecaution()
    {
        return $this->precaution;
    }

    /**
     * @param string $precaution
     */
    public function setPrecaution($precaution)
    {
        $this->precaution = $precaution;
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
     * @return MedicineBrand
     */
    public function getMedicineBrands()
    {
        return $this->medicineBrands;
    }
}

