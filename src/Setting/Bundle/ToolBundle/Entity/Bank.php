<?php

namespace Setting\Bundle\ToolBundle\Entity;

use Appstore\Bundle\AccountingBundle\Entity\PaymentSalary;
use Appstore\Bundle\BusinessBundle\Entity\BusinessInvoice;
use Appstore\Bundle\DoctorPrescriptionBundle\Entity\DpsTreatmentPlan;
use Appstore\Bundle\HospitalBundle\Entity\InvoiceTransaction;
use Appstore\Bundle\HumanResourceBundle\Entity\EmployeePayroll;
use Appstore\Bundle\InventoryBundle\Entity\Sales;
use Appstore\Bundle\MedicineBundle\Entity\MedicineSales;
use Doctrine\ORM\Mapping as ORM;

/**
 * Bank
 *
 * @ORM\Table(name="banks")
 * @ORM\Entity
 */
class Bank
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
	 * @ORM\OneToMany(targetEntity="Core\UserBundle\Entity\Profile", mappedBy="bank")
	 */
	protected $profile;

	/**
	 * @ORM\OneToMany(targetEntity="Appstore\Bundle\HumanResourceBundle\Entity\EmployeePayroll", mappedBy="bank")
	 */
	protected $employeePayroll;

	/**
	 * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountBank", mappedBy="bank")
	 */
	protected $accountBanks;

	/**
	 * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\PaymentSalary", mappedBy="bank")
	 */
	protected $paymentSalaries;

	/**
	 * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\Sales", mappedBy="bank")
	 */
	protected $sales;

	/**
	 * @ORM\OneToMany(targetEntity="Appstore\Bundle\HospitalBundle\Entity\Invoice", mappedBy="bank")
	 */
	protected $hmsInvoices;

	/**
	 * @ORM\OneToMany(targetEntity="Appstore\Bundle\HospitalBundle\Entity\DoctorInvoice", mappedBy="bank")
	 */
	protected $doctorInvoices;

	/**
	 * @ORM\OneToMany(targetEntity="Appstore\Bundle\HospitalBundle\Entity\InvoiceTransaction", mappedBy="bank" )
	 */
	protected $invoiceTransactions;

	/**
	 * @ORM\OneToMany(targetEntity="Appstore\Bundle\DoctorPrescriptionBundle\Entity\DpsInvoice", mappedBy="bank" )
	 */
	protected $dpsInvoices;

	/**
	 * @ORM\OneToMany(targetEntity="Appstore\Bundle\RestaurantBundle\Entity\Invoice", mappedBy="bank" )
	 */
	protected $restaurantInvoices;

	/**
	 * @ORM\OneToMany(targetEntity="Appstore\Bundle\MedicineBundle\Entity\MedicineSales", mappedBy="bank" )
	 */
	protected $medicineSales;

	/**
	 * @ORM\OneToMany(targetEntity="Appstore\Bundle\BusinessBundle\Entity\BusinessInvoice", mappedBy="bank" )
	 */
	protected $businessInvoice;

	/**
	 * @ORM\OneToMany(targetEntity="Appstore\Bundle\DoctorPrescriptionBundle\Entity\DpsTreatmentPlan", mappedBy="bank" )
	 */
	protected $dpsTreatmentPlans;



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
	 * @var string
	 *
	 * @ORM\Column(name="name", type="string", length=255)
	 */
	private $name;



	/**
	 * Set name
	 *
	 * @param string $name
	 *
	 * @return Bank
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
	 * @return mixed
	 */
	public function getAccountBanks()
	{
		return $this->accountBanks;
	}


	/**
	 * @return mixed
	 */
	public function getPortalBankAccount()
	{
		return $this->portalBankAccount;
	}

	/**
	 * @return Sales
	 */
	public function getSales()
	{
		return $this->sales;
	}

	/**
	 * @return PaymentSalary
	 */
	public function getPaymentSalaries()
	{
		return $this->paymentSalaries;
	}

	/**
	 * @return InvoiceTransaction
	 */
	public function getInvoiceTransactions()
	{
		return $this->invoiceTransactions;
	}

	/**
	 * @return DmsInvoice
	 */
	public function getDmsInvoices()
	{
		return $this->dmsInvoices;
	}

	/**
	 * @return MedicineSales
	 */
	public function getMedicineSales()
	{
		return $this->medicineSales;
	}

	/**
	 * @return DpsTreatmentPlan
	 */
	public function getDpsTreatmentPlans()
	{
		return $this->dpsTreatmentPlans;
	}

	/**
	 * @return BusinessInvoice
	 */
	public function getBusinessInvoice()
	{
		return $this->businessInvoice;
	}

	/**
	 * @return HotelInvoice
	 */
	public function getHotelInvoice() {
		return $this->hotelInvoice;
	}

    /**
     * @return EmployeePayroll
     */
    public function getEmployeePayroll()
    {
        return $this->employeePayroll;
    }
}

