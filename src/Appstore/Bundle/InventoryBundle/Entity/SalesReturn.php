<?php

namespace Appstore\Bundle\InventoryBundle\Entity;

use Appstore\Bundle\AccountingBundle\Entity\AccountSalesReturn;
use Appstore\Bundle\DomainUserBundle\Entity\Branches;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * SalesReturn
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Appstore\Bundle\InventoryBundle\Repository\SalesReturnRepository")
 */
class SalesReturn implements CodeAwareEntity
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
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\InventoryBundle\Entity\InventoryConfig", inversedBy="salesReturn" )
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private  $inventoryConfig;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\DomainUserBundle\Entity\Branches", inversedBy="salesReturns" )
     **/
    private  $branches;

    /**
     * @var integer
     *
     * @ORM\Column(name="code", type="integer", nullable=true)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="invoice", type="string", length=100, nullable=true)
     */
    private $invoice = '';

    /**
     * @var string
     *
     * @ORM\Column(name="journal", type="string", length=50, nullable=true)
     */
    private $journal;

    /**
     * @var string
     *
     * @ORM\Column(name="salesAccount", type="string", length=50, nullable=true)
     */
    private $salesAccount;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\InventoryBundle\Entity\Sales", inversedBy="salesReturn" )
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private  $sales;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\InventoryBundle\Entity\Sales", inversedBy="salesReturnAdjustment" )
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    private  $salesAdjustmentInvoice;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountSalesReturn", mappedBy="salesReturn", cascade={"remove"} )
     * @ORM\OrderBy({"id" = "DESC"})
     **/
    private  $accountSalesReturn;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\SalesReturnItem", mappedBy="salesReturn" , cascade={"remove"} )
     **/
    private  $salesReturnItems;

    /**
     * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User", inversedBy="salesReturn" )
     **/
    private  $createdBy;

    /**
     * @var integer
     *
     * @ORM\Column(name="totalQuantity", type="integer", nullable=true)
     */
    private $totalQuantity;

    /**
     * @var integer
     *
     * @ORM\Column(name="totalItem", type="integer", nullable=true)
     */
    private $totalItem;

    /**
     * @var float
     *
     * @ORM\Column(name="total", type="float", nullable=true)
     */
    private $total;

    /**
     * @var string
     *
     * @ORM\Column(name="process", type="string", length=50, nullable=true)
     */
    private $process = 'created';

	/**
     * @var string
     *
     * @ORM\Column(name="mode", type="string", length=20, nullable=true)
     */
    private $mode = 'adjustment';


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
     * @return Sales
     */
    public function getSales()
    {
        return $this->sales;
    }

    /**
     * @param Sales $sales
     */
    public function setSales($sales)
    {
        $this->sales = $sales;
    }

    /**
     * @return mixed
     */
    public function getInventoryConfig()
    {
        return $this->inventoryConfig;
    }

    /**
     * @param mixed $inventoryConfig
     */
    public function setInventoryConfig($inventoryConfig)
    {
        $this->inventoryConfig = $inventoryConfig;
    }

    /**
     * @return mixed
     */
    public function getSalesReturnItems()
    {
        return $this->salesReturnItems;
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
     * @return mixed
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * @param mixed $createdBy
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;
    }

    /**
     * @return integer
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param integer $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @return int
     */
    public function getTotalQuantity()
    {
        return $this->totalQuantity;
    }

    /**
     * @param int $totalQuantity
     */
    public function setTotalQuantity($totalQuantity)
    {
        $this->totalQuantity = $totalQuantity;
    }

    /**
     * @return int
     */
    public function getTotalItem()
    {
        return $this->totalItem;
    }

    /**
     * @param int $totalItem
     */
    public function setTotalItem($totalItem)
    {
        $this->totalItem = $totalItem;
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
    public function setTotal($total)
    {
        $this->total = $total;
    }

    /**
     * @return string
     */
    public function getProcess()
    {
        return $this->process;
    }

    /**
     * @param string $process
     */
    public function setProcess($process)
    {
        $this->process = $process;
    }

    /**
     * @return string
     */
    public function getInvoice()
    {
        return $this->invoice;
    }

    /**
     * @param string $invoice
     */
    public function setInvoice($invoice)
    {
        $this->invoice = $invoice;
    }

    /**
     * @return AccountSalesReturn
     */
    public function getAccountSalesReturn()
    {
        return $this->accountSalesReturn;
    }


    /**
     * @return Sales
     */
    public function getSalesAdjustmentInvoice()
    {
        return $this->salesAdjustmentInvoice;
    }

    /**
     * @param Sales $salesAdjustmentInvoice
     */
    public function setSalesAdjustmentInvoice($salesAdjustmentInvoice)
    {
        $this->salesAdjustmentInvoice = $salesAdjustmentInvoice;
    }

    /**
     * @return Branches
     */
    public function getBranches()
    {
        return $this->branches;
    }

    /**
     * @param Branches $branches
     */
    public function setBranches($branches)
    {
        $this->branches = $branches;
    }

	/**
	 * @return string
	 */
	public function getMode(){
		return $this->mode;
	}

	/**
	 * @param string $mode
	 */
	public function setMode( string $mode ) {
		$this->mode = $mode;
	}

	/**
	 * @return string
	 */
	public function getJournal(){
		return $this->journal;
	}

	/**
	 * @param string $journal
	 */
	public function setJournal( string $journal ) {
		$this->journal = $journal;
	}

	/**
	 * @return string
	 */
	public function getSalesAccount() {
		return $this->salesAccount;
	}

	/**
	 * @param string $salesAccount
	 */
	public function setSalesAccount( $salesAccount ) {
		$this->salesAccount = $salesAccount;
	}


}

