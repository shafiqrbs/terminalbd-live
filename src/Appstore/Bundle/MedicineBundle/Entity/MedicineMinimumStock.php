<?php

namespace Appstore\Bundle\MedicineBundle\Entity;

use Appstore\Bundle\HospitalBundle\Entity\Particular;
use Doctrine\ORM\Mapping as ORM;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Setting\Bundle\ToolBundle\Entity\ProductUnit;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * MedicineMinimumStock
 *
 * @ORM\Table("medicine_minimum_stock")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\MedicineBundle\Repository\MedicineMinimumStockRepository")
 */
class MedicineMinimumStock
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
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\MedicineBundle\Entity\MedicineConfig", inversedBy="medicineMinimumStock")
     **/
    private $medicineConfig;

    /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\ProductUnit", inversedBy="medicineMinimumStock")
     **/
    private $unit;

    /**
     * @var integer
     *
     * @ORM\Column(name="minQuantity", type="integer", nullable=true)
     */
    private $minQuantity;


    /**
     * @var integer
     *
     * @ORM\Column(name="reorderQuantity", type="integer", nullable=true)
     */
    private $reorderQuantity;


     /**
     * @var integer
     *
     * @ORM\Column(name="company", type="integer", nullable=true)
     */
    private $company;

    /**
     * @var integer
     *
     * @ORM\Column(name="medicineForm", type="integer", nullable=true)
     */
    private $medicineForm;


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
     * @return int
     */
    public function getMedicineForm()
    {
        return $this->medicineForm;
    }

    /**
     * @param int $medicineForm
     */
    public function setMedicineForm($medicineForm)
    {
        $this->medicineForm = $medicineForm;
    }

    /**
     * @return int
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @param int $company
     */
    public function setCompany($company)
    {
        $this->company = $company;
    }


}

