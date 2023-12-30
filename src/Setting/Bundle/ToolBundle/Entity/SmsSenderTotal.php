<?php

namespace Setting\Bundle\ToolBundle\Entity;
use Doctrine\ORM\Mapping as ORM;


/**
 * SmsSenderTotal
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Setting\Bundle\ToolBundle\Repository\SmsSenderTotalRepository")
 */
class SmsSenderTotal
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
     * @ORM\OneToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\GlobalOption", inversedBy="smsSenderTotal")
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    protected $globalOption;

    /**
     * @var integer
     *
     * @ORM\Column(name="purchase", type="integer", nullable= true)
     */
    private $purchase;

    /**
     * @var integer
     *
     * @ORM\Column(name="sending", type="integer", nullable= true)
     */
    private $sending;

    /**
     * @var integer
     *
     * @ORM\Column(name="remaining", type="integer", nullable= true)
     */
    private $remaining;


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
     * @return int
     */
    public function getPurchase()
    {
        return $this->purchase;
    }

    /**
     * @param int $purchase
     */
    public function setPurchase($purchase)
    {
        $this->purchase = $purchase;
    }

    /**
     * @return int
     */
    public function getSending()
    {
        return $this->sending;
    }

    /**
     * @param int $sending
     */
    public function setSending($sending)
    {
        $this->sending = $sending;
    }

    /**
     * @return int
     */
    public function getRemaining()
    {
        return $this->remaining;
    }

    /**
     * @param int $remaining
     */
    public function setRemaining($remaining)
    {
        $this->remaining = $remaining;
    }


}

