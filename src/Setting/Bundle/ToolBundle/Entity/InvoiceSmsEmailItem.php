<?php

namespace Setting\Bundle\ToolBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * InvoiceSmsEmailItem
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Setting\Bundle\ToolBundle\Repository\InvoiceSmsEmailItemRepository")
 */
class InvoiceSmsEmailItem
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
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\InvoiceSmsEmail", inversedBy="invoiceSmsEmailItems" , cascade={"detach","merge"})
     * @ORM\JoinColumn(name="invoiceSmsEmail_id", referencedColumnName="id", nullable=true, onDelete="cascade")
     **/
    protected $invoiceSmsEmail;

    /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\SmsEmailPricing", inversedBy="invoiceSmsEmailItems",cascade={"detach","merge"})
     * @ORM\JoinColumn(name="smsEmailPricing_id", referencedColumnName="id", nullable=true, onDelete="cascade")
     **/
    protected $smsEmailPricing = 0;

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
     * @return mixed
     */
    public function getInvoiceSmsEmail()
    {
        return $this->invoiceSmsEmail;
    }

    /**
     * @param mixed $invoiceSmsEmail
     */
    public function setInvoiceSmsEmail($invoiceSmsEmail)
    {
        $this->invoiceSmsEmail = $invoiceSmsEmail;
    }

    /**
     * @return mixed
     */
    public function getSmsEmailPricing()
    {
        return $this->smsEmailPricing;
    }

    /**
     * @param mixed $smsEmailPricing
     */
    public function setSmsEmailPricing($smsEmailPricing)
    {
        $this->smsEmailPricing = $smsEmailPricing;
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
}

