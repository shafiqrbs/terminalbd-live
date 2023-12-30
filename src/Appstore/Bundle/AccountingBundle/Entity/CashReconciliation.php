<?php

namespace Appstore\Bundle\AccountingBundle\Entity;

use Appstore\Bundle\InventoryBundle\Entity\Sales;
use Doctrine\ORM\Mapping as ORM;
use Setting\Bundle\ToolBundle\Entity\Bank;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;

/**
 * AccountBkash
 *
 *
 * @ORM\Table(name="account_cash_reconciliation")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\AccountingBundle\Repository\CashReconciliationRepository")
 */
class CashReconciliation
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
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\GlobalOption", inversedBy="cashReconciliation")
     **/
    protected $globalOption;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\CashReconciliationMeta", mappedBy="cashReconciliation")
     **/
    protected $cashReconciliationMetas;

    /**
     * @var float
     *
     * @ORM\Column(name="cash", type="float", nullable=true)
     */
    private $cash = 0;

    /**
     * @var float
     *
     * @ORM\Column(name="bank", type="float", nullable=true)
     */
    private $bank = 0;

    /**
     * @var float
     *
     * @ORM\Column(name="mobile", type="float", nullable=true)
     */
    private $mobile = 0;

    /**
     * @var float
     *
     * @ORM\Column(name="systemCash", type="float", nullable=true)
     */
    private $systemCash = 0;

    /**
     * @var float
     *
     * @ORM\Column(name="systemBank", type="float", nullable=true)
     */
    private $systemBank = 0;

    /**
     * @var float
     *
     * @ORM\Column(name="systemMobile", type="float", nullable=true)
     */
    private $systemMobile = 0;

    /**
     * @var float
     *
     * @ORM\Column(name="withdraw", type="float", nullable=true)
     */
    private $withdraw = 0;

    /**
     * @var float
     *
     * @ORM\Column(name="total", type="float", nullable=true)
     */
    private $total = 0;



    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="boolean")
     */
    private $status = true;

    /**
     * @var \DateTime
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;


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
    public function getAccountType()
    {
        return $this->accountType;
    }

    /**
     * @param string $accountType
     */
    public function setAccountType($accountType)
    {
        $this->accountType = $accountType;
    }

    /**
     * @return boolean
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param boolean $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return float
     */
    public function getBank()
    {
        return $this->bank;
    }

    /**
     * @param float $bank
     */
    public function setBank(float $bank)
    {
        $this->bank = $bank;
    }

    /**
     * @return float
     */
    public function getMobile()
    {
        return $this->mobile;
    }

    /**
     * @param float $mobile
     */
    public function setMobile(float $mobile)
    {
        $this->mobile = $mobile;
    }

    /**
     * @return float
     */
    public function getWithdraw()
    {
        return $this->withdraw;
    }

    /**
     * @param float $withdraw
     */
    public function setWithdraw(float $withdraw)
    {
        $this->withdraw = $withdraw;
    }

    /**
     * @return float
     */
    public function getCash()
    {
        return $this->cash;
    }

    /**
     * @param float $cash
     */
    public function setCash(float $cash)
    {
        $this->cash = $cash;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     *
     * @return CashReconciliation
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @return mixed
     */
    public function getGlobalOption()
    {
        return $this->globalOption;
    }

    /**
     * @param mixed $globalOption
     */
    public function setGlobalOption($globalOption)
    {
        $this->globalOption = $globalOption;
    }

    /**
     * @return CashReconciliationMeta
     */
    public function getCashReconciliationMetas()
    {
        return $this->cashReconciliationMetas;
    }

    /**
     * @return float
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @param float $total
     */
    public function setTotal(float $total)
    {
        $this->total = $total;
    }

    /**
     * @return float
     */
    public function getSystemCash()
    {
        return $this->systemCash;
    }

    /**
     * @param float $systemCash
     */
    public function setSystemCash(float $systemCash)
    {
        $this->systemCash = $systemCash;
    }

    /**
     * @return float
     */
    public function getSystemBank()
    {
        return $this->systemBank;
    }

    /**
     * @param float $systemBank
     */
    public function setSystemBank(float $systemBank)
    {
        $this->systemBank = $systemBank;
    }

    /**
     * @return float
     */
    public function getSystemMobile()
    {
        return $this->systemMobile;
    }

    /**
     * @param float $systemMobile
     */
    public function setSystemMobile(float $systemMobile)
    {
        $this->systemMobile = $systemMobile;
    }


}

