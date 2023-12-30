<?php

namespace Setting\Bundle\ToolBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * InvoiceModuleItem
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Setting\Bundle\ToolBundle\Repository\InvoiceModuleItemRepository")
 */
class InvoiceModuleItem
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
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\InvoiceModule", inversedBy="invoiceModuleItems")
     **/
    protected $invoiceModule = null;

    /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\AppModule", inversedBy="invoiceModuleItems")
     **/
    protected $appModule = null;

    /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\SyndicateModule", inversedBy="invoiceModuleItems")
     **/
    protected $syndicateModule = null;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;


    /**
     * @var float
     *
     * @ORM\Column(name="amount", type="float", nullable = true)
     */
    private $amount;


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
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param float $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    /**
     * @return mixed
     */
    public function getAppModule()
    {
        return $this->appModule;
    }

    /**
     * @param mixed $appModule
     */
    public function setAppModule($appModule)
    {
        $this->appModule = $appModule;
    }


    /**
     * @return mixed
     */
    public function getInvoiceModule()
    {
        return $this->invoiceModule;
    }

    /**
     * @param mixed $invoiceModule
     */
    public function setInvoiceModule($invoiceModule)
    {
        $this->invoiceModule = $invoiceModule;
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
}

