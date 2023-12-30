<?php

namespace Appstore\Bundle\AccountingBundle\Entity;

use Appstore\Bundle\DomainUserBundle\Entity\Branches;
use Appstore\Bundle\HospitalBundle\Entity\DoctorInvoice;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Setting\Bundle\ToolBundle\Entity\TransactionMethod;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * ExpenditureItem
 *
 * @ORM\Table("expenditure_item")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\AccountingBundle\Repository\ExpenditureItemRepository")
 */
class ExpenditureItem
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
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\AccountingBundle\Entity\Expenditure", inversedBy="expenditureItems", cascade={"detach","merge"} )
     * @ORM\JoinColumn(name="expenditure_id", referencedColumnName="id", nullable=true, onDelete="cascade")
     **/
    private  $expenditure;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountPurchase", inversedBy="expenditureItems", cascade={"detach","merge"} )
     * @ORM\JoinColumn(name="purchase_id", referencedColumnName="id", nullable=true, onDelete="cascade")
     **/
    private  $purchase;

    /**
     * @var float
     *
     * @ORM\Column(name="price", type="float")
     */
    private $price;

    /**
     * @var float
     *
     * @ORM\Column(name="quantity", type="integer" , nullable=true)
     */
    private $quantity = 1;

    /**
     * @var float
     *
     * @ORM\Column(name="subTotal", type="float")
     */
    private $subTotal;

    /**
     * @var string
     *
     * @ORM\Column(name="particular", type="string", length=255, nullable = true)
     */
    private $particular;


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
     * @return string
     */
    public function getParticular()
    {
        return $this->particular;
    }

    /**
     * @param string $particular
     */
    public function setParticular(string $particular)
    {
        $this->particular = $particular;
    }

    /**
     * @return Expenditure
     */
    public function getExpenditure()
    {
        return $this->expenditure;
    }

    /**
     * @param Expenditure $expenditure
     */
    public function setExpenditure($expenditure)
    {
        $this->expenditure = $expenditure;
    }

    /**
     * @return AccountPurchase
     */
    public function getPurchase()
    {
        return $this->purchase;
    }

    /**
     * @param AccountPurchase $purchase
     */
    public function setPurchase($purchase)
    {
        $this->purchase = $purchase;
    }

    /**
     * @return float
     */
    public function getSubTotal()
    {
        return $this->subTotal;
    }

    /**
     * @param float $subTotal
     */
    public function setSubTotal($subTotal)
    {
        $this->subTotal = $subTotal;
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
     * @return float
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param float $quantity
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }

}

