<?php

namespace Appstore\Bundle\DomainUserBundle\Entity;

use Appstore\Bundle\AccountingBundle\Entity\AccountBalanceTransfer;
use Appstore\Bundle\AccountingBundle\Entity\AccountCash;
use Appstore\Bundle\AccountingBundle\Entity\AccountJournal;
use Appstore\Bundle\AccountingBundle\Entity\AccountPurchaseCommission;
use Appstore\Bundle\AccountingBundle\Entity\AccountSales;
use Appstore\Bundle\AccountingBundle\Entity\AccountSalesAdjustment;
use Appstore\Bundle\AccountingBundle\Entity\Expenditure;
use Appstore\Bundle\AccountingBundle\Entity\Transaction;
use Appstore\Bundle\HumanResourceBundle\Entity\Payroll;
use Appstore\Bundle\InventoryBundle\Entity\BranchInvoice;
use Appstore\Bundle\InventoryBundle\Entity\Delivery;
use Appstore\Bundle\InventoryBundle\Entity\DeliveryReturn;
use Appstore\Bundle\InventoryBundle\Entity\Sales;
use Appstore\Bundle\InventoryBundle\Entity\SalesReturn;
use Appstore\Bundle\MedicineBundle\Entity\MedicineSales;
use Appstore\Bundle\TallyBundle\Entity\StockItem;
use Core\UserBundle\Entity\Profile;
use Core\UserBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Setting\Bundle\LocationBundle\Entity\Location;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 * Branches
 * @UniqueEntity(fields="branchManager",message="This data is already in use.")
 * @ORM\Table("branches")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\DomainUserBundle\Repository\BranchesRepository")
 */
class Branches
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
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\GlobalOption", inversedBy="branches" )
     **/
    private $globalOption;

     /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\HumanResourceBundle\Entity\Payroll", mappedBy="branch" )
     **/
    private $payrolls;


    /**
     * @ORM\OneToOne(targetEntity="Core\UserBundle\Entity\User", inversedBy="branches" )
     **/
    private  $branchManager;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\Delivery", mappedBy="branch" )
     **/
    private  $deliveries;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\DeliveryReturn", mappedBy="branch" )
     **/
    private  $deliveryReturns;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\MedicineBundle\Entity\MedicineSales", mappedBy="branch" )
     **/
    private  $medicineSales;

    /**
     * @ORM\OneToMany(targetEntity="Core\UserBundle\Entity\profile", mappedBy="branches" )
     **/
    private  $profiles;

     /**
     * @ORM\OneToMany(targetEntity="Core\UserBundle\Entity\profile", mappedBy="employeeBranch" )
     **/
    private  $employeeProfiles;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\Sales", mappedBy="branches")
     * @ORM\OrderBy({"id" = "DESC"})
     **/
    private  $sales;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\SalesReturn", mappedBy="branches")
     * @ORM\OrderBy({"id" = "DESC"})
     **/
    private  $salesReturns;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\BranchInvoice", mappedBy="branches" )
     **/
    private  $branchInvoice;

    /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\LocationBundle\Entity\Location", inversedBy="branches")
     **/
    protected $location;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountSales", mappedBy="branches" )
     **/
    private  $accountSales;

     /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountSalesAdjustment", mappedBy="branches" )
     **/
    private  $accountSalesAdjustment;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountPurchaseCommission", mappedBy="branches" )
     **/
    private  $accountPurchaseCommission;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountSalesReturn", mappedBy="branches" )
     **/
    private  $accountSalesReturn;


    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountCash", mappedBy="branches" )
     **/
    private  $accountCash;


    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\Expenditure", mappedBy="branches" )
     **/
    private  $expenditure;


    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\Transaction", mappedBy="branches" )
     **/
    private  $transactions;

     /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountJournal", mappedBy="branches" )
     **/
    private  $accountJournal;

     /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountBalanceTransfer", mappedBy="branches" )
     **/
    private  $balanceTransfer;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
     private $name;

    /**
     * @var strings
     *
     * @ORM\Column(name="address", type="text", nullable = true)
     */
     private $address;

    /**
     * @var string
     *
     * @ORM\Column(name="mobile", type="text", nullable = true)
     */
     private $mobile;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="text", nullable = true)
     */
     private $email;


    /**
     * @var integer
     *
     * @ORM\Column(name="code", type="integer", nullable = true)
     */
     private $code;


    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="boolean")
     */
    private $status = true;


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
     * @return Branches
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
     * Set code
     *
     * @param integer $code
     *
     * @return Branches
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return integer
     */
    public function getCode()
    {
        return $this->code;
    }


    /**
     * Set status
     *
     * @param boolean $status
     *
     * @return Branches
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return boolean
     */
    public function getStatus()
    {
        return $this->status;
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
     * @return strings
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param strings $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * @return string
     */
    public function getMobile()
    {
        return $this->mobile;
    }

    /**
     * @param string $mobile
     */
    public function setMobile($mobile)
    {
        $this->mobile = $mobile;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return Sales
     */
    public function getSales()
    {
        return $this->sales;
    }

    /**
     * @return User
     */
    public function getBranchManager()
    {
        return $this->branchManager;
    }

    /**
     * @param User $branchManager
     */
    public function setBranchManager($branchManager)
    {
        $this->branchManager = $branchManager;
    }

    /**
     * @return AccountSales
     */
    public function getAccountSales()
    {
        return $this->accountSales;
    }

    /**
     * @return BranchInvoice
     */
    public function getBranchInvoice()
    {
        return $this->branchInvoice;
    }

    /**
     * @return Profile
     */
    public function getProfiles()
    {
        return $this->profiles;
    }

    /**
     * @return Location
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param Location $location
     */
    public function setLocation($location)
    {
        $this->location = $location;
    }

    /**
     * @return Delivery
     */
    public function getDeliveries()
    {
        return $this->deliveries;
    }

    /**
     * @return DeliveryReturn
     */
    public function getDeliveryReturns()
    {
        return $this->deliveryReturns;
    }

    /**
     * @return SalesReturn
     */
    public function getSalesReturns()
    {
        return $this->salesReturns;
    }

    /**
     * @return Expenditure
     */
    public function getExpenditure()
    {
        return $this->expenditure;
    }

    /**
     * @return AccountCash
     */
    public function getAccountCash()
    {
        return $this->accountCash;
    }

    /**
     * @return Transaction
     */
    public function getTransactions()
    {
        return $this->transactions;
    }

    /**
     * @return AccountJournal
     */
    public function getAccountJournal()
    {
        return $this->accountJournal;
    }

    /**
     * @return mixed
     */
    public function getAccountSalesReturn()
    {
        return $this->accountSalesReturn;
    }

    /**
     * @return MedicineSales
     */
    public function getMedicineSales()
    {
        return $this->medicineSales;
    }

	/**
	 * @return AccountBalanceTransfer
	 */
	public function getBalanceTransfer() {
		return $this->balanceTransfer;
	}

    /**
     * @return AccountSalesAdjustment
     */
    public function getAccountSalesAdjustment()
    {
        return $this->accountSalesAdjustment;
    }

    /**
     * @return AccountPurchaseCommission
     */
    public function getAccountPurchaseCommission()
    {
        return $this->accountPurchaseCommission;
    }

    /**
     * @return Payroll
     */
    public function getPayrolls()
    {
        return $this->payrolls;
    }

    /**
     * @return StockItem
     */
    public function getStockItems()
    {
        return $this->stockItems;
    }


}

