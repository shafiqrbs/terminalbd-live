<?php

namespace Terminalbd\PosBundle\Entity;
use Appstore\Bundle\AccountingBundle\Entity\AccountBank;
use Appstore\Bundle\AccountingBundle\Entity\AccountMobileBank;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Setting\Bundle\ToolBundle\Entity\Bank;
use Setting\Bundle\ToolBundle\Entity\TransactionMethod;

/**
 * Sales
 *
 * @ORM\Table(name="pos_config")
 * @ORM\Entity(repositoryClass="Terminalbd\PosBundle\Repository\PosRepository")
 */
class PosConfig
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
     * @ORM\OneToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\GlobalOption")
     **/
    private $terminal;

    /**
     * @var boolean
     *
     * @ORM\Column(name="barcodePrint", type="boolean",  nullable=true)
     */
    private $barcodePrint = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="autoPayment", type="boolean",  nullable=true)
     */
    private $autoPayment = false;

     /**
     * @var boolean
     *
     * @ORM\Column(name="isPos", type="boolean",  nullable=true)
     */
    private $isPos = false;

    /**
     * @var string
     *
     * @ORM\Column(type="text",  nullable=true)
     */
    private $templateCss;

    /**
     * @var string
     *
     * @ORM\Column(name="currency", type="string",  nullable=true)
     */
    private $currency;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getTerminal()
    {
        return $this->terminal;
    }

    /**
     * @param mixed $terminal
     */
    public function setTerminal($terminal)
    {
        $this->terminal = $terminal;
    }

    /**
     * @return bool
     */
    public function isBarcodePrint()
    {
        return $this->barcodePrint;
    }

    /**
     * @param bool $barcodePrint
     */
    public function setBarcodePrint($barcodePrint)
    {
        $this->barcodePrint = $barcodePrint;
    }

    /**
     * @return bool
     */
    public function isAutoPayment()
    {
        return $this->autoPayment;
    }

    /**
     * @param bool $autoPayment
     */
    public function setAutoPayment($autoPayment)
    {
        $this->autoPayment = $autoPayment;
    }

    /**
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param string $currency
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }

    /**
     * @return string
     */
    public function getTemplateCss()
    {
        return $this->templateCss;
    }

    /**
     * @param string $templateCss
     */
    public function setTemplateCss($templateCss)
    {
        $this->templateCss = $templateCss;
    }

    /**
     * @return bool
     */
    public function isPos()
    {
        return $this->isPos;
    }

    /**
     * @param bool $isPos
     */
    public function setIsPos($isPos)
    {
        $this->isPos = $isPos;
    }


}

