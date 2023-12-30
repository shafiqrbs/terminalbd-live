<?php

namespace Appstore\Bundle\AccountingBundle\Entity;

use Appstore\Bundle\BusinessBundle\Entity\BusinessPurchase;
use Appstore\Bundle\HospitalBundle\Entity\HmsPurchase;
use Appstore\Bundle\HospitalBundle\Entity\HmsVendor;
use Appstore\Bundle\InventoryBundle\Entity\Purchase;
use Appstore\Bundle\InventoryBundle\Entity\PurchaseReturn;
use Appstore\Bundle\MedicineBundle\Entity\MedicinePurchase;
use Appstore\Bundle\MedicineBundle\Entity\MedicineVendor;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Setting\Bundle\ToolBundle\Entity\TransactionMethod;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * AccountPurchase
 *
 * @ORM\Table(name="account_purchase")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\AccountingBundle\Repository\AccountPurchaseRepository")
 */
    class AccountPurchase
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
         * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\GlobalOption", inversedBy="accountPurchase")
         **/
        protected $globalOption;

        /**
         * @ORM\ManyToOne(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountHead", inversedBy="accountPurchases" )
         **/
        private  $accountHead;


        /**
         * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\ExpenditureItem", mappedBy="purchase"  )
         **/
        private  $expenditureItems;


        /**
         * @ORM\ManyToOne(targetEntity="Appstore\Bundle\InventoryBundle\Entity\Vendor", inversedBy="accountPurchases" , cascade={"detach","merge"} )
         **/
        private  $vendor;

        /**
         * @ORM\ManyToOne(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountVendor", inversedBy="accountPurchases" , cascade={"detach","merge"} )
         **/
        private  $accountVendor;

        /**
         * @ORM\ManyToOne(targetEntity="Appstore\Bundle\InventoryBundle\Entity\Purchase", inversedBy="accountPurchase" , cascade={"detach","merge"} )
         * @ORM\JoinColumn(name="purchase_id", referencedColumnName="id", nullable=true, onDelete="cascade")
         **/
        private  $purchase;


        /**
         * @ORM\OneToOne(targetEntity="Appstore\Bundle\HospitalBundle\Entity\HmsPurchase", inversedBy="accountPurchase" )
         * @ORM\JoinColumn(name="hmsPurchase_id", referencedColumnName="id", nullable=true, onDelete="cascade")
         **/
        private  $hmsPurchase;


        /**
         * @ORM\ManyToOne(targetEntity="Appstore\Bundle\HospitalBundle\Entity\HmsVendor", inversedBy="accountPurchase" )
         **/
        private  $hmsVendor;


        /**
         * @ORM\OneToOne(targetEntity="Appstore\Bundle\RestaurantBundle\Entity\Purchase", inversedBy="accountPurchase" )
         * @ORM\JoinColumn(name="restaurant_id", referencedColumnName="id", nullable=true, onDelete="cascade")
         **/
        private  $restaurantPurchase;


         /**
         * @ORM\OneToOne(targetEntity="Appstore\Bundle\BusinessBundle\Entity\BusinessPurchase", inversedBy="accountPurchase" )
         **/
        private  $businessPurchase;

        /**
         * @ORM\ManyToOne(targetEntity="Appstore\Bundle\MedicineBundle\Entity\MedicineVendor", inversedBy="accountPurchases" )
         **/
        private  $medicineVendor;


        /**
         * @ORM\ManyToOne(targetEntity="Appstore\Bundle\MedicineBundle\Entity\MedicinePurchase", inversedBy="accountPurchases" )
         * @ORM\JoinColumn(name="medicinePurchase_id", referencedColumnName="id", nullable=true, onDelete="cascade")
         **/
        private  $medicinePurchase;

        /**
         * @ORM\ManyToOne(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountBank", inversedBy="accountPurchases" )
         **/
        private  $accountBank;

        /**
         * @ORM\ManyToOne(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountMobileBank", inversedBy="accountPurchases" )
         **/
        private  $accountMobileBank;

        /**
         * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\TransactionMethod", inversedBy="accountPurchases" )
         **/
        private  $transactionMethod;

        /**
         * @ORM\OneToOne(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountCash", mappedBy="accountPurchase" )
         **/
        private  $accountCash;

        /**
         * @Gedmo\Blameable(on="create")
         * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User", inversedBy="accountPurchases" )
         **/
        private  $createdBy;


        /**
         * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User", inversedBy="purchasesToUser" )
         **/
        private  $toUser;

        /**
         * @var float
         *
         * @ORM\Column(name="purchaseAmount", type="float", nullable=true)
         */
        private $purchaseAmount = 0;

        /**
         * @var float
         *
         * @ORM\Column(name="totalAmount", type="float", nullable=true)
         */
        private $totalAmount = 0;

        /**
         * @var float
         *
         * @ORM\Column(name="payment", type="float", nullable=true)
         */
        private $payment = 0;

         /**
         * @var float
         *
         * @ORM\Column(name="commission", type="float", nullable=true)
         */
        private $commission = 0;

        /**
         * @var float
         *
         * @ORM\Column(name="balance", type="float", nullable=true)
         */
        private $balance = 0;

        /**
         * @var string
         *
         * @ORM\Column(name="accountRefNo", type="string", length=50, nullable=true)
         */
        private $accountRefNo;

        /**
         * @var string
         *
         * @ORM\Column(name="voucherType", type="string", length = 60, nullable=true)
         */
        private $voucherType = "bill";

        /**
         * @var string
         *
         * @ORM\Column(name="companyName", type="string", length = 256, nullable=true)
         */
        private $companyName;

        /**
         * @var string
         *
         * @ORM\Column(name="grn", type="string", length = 50, nullable=true)
         */
        private $grn;

        /**
         * @var string
         *
         * @ORM\Column(name="memo", type="string", length = 50, nullable=true)
         */
        private $memo;


        /**
         * @var string
         *
         * @ORM\Column(name="sourceInvoice", type="string", length=50, nullable=true)
         */
        private $sourceInvoice;

        /**
         * @var integer
         *
         * @ORM\Column(name="code", type="integer",  nullable=true)
         */
        private $code;

        /**
         * @var string
         *
         * @ORM\Column(name="remark", type="text", nullable = true)
         */
        private $remark;

        /**
         * @var \DateTime
         * @Gedmo\Timestampable(on="create")
         * @ORM\Column(name="receiveDate", type="datetime")
         */
        private $receiveDate;

        /**
         * @var \DateTime
         * @Gedmo\Timestampable(on="create")
         * @ORM\Column(name="created", type="datetime")
         */
        private $created;

        /**
         * @var \DateTime
         * @ORM\Column(name="updated", type="datetime", nullable = true)
         */
        private $updated;


        /**
         * @var string
         *
         * @ORM\Column(name="process", type="string", length=50, nullable = true)
         */
        private $process;

        /**
         * @var string
         *
         * @ORM\Column(name="processHead", type="string", length=100, nullable = true)
         */
        private $processHead;

        /**
         * @var string
         *
         * @ORM\Column(name="processType", type="string", length=50, nullable = true)
         */
        private $processType;


        /**
         * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User", inversedBy="purchaseApprove" )
         **/
        private  $approvedBy;

        /**
         * @ORM\Column(type="string", length=255, nullable=true)
         */
        protected $path;


        /**
         * @Assert\File(maxSize="8388608")
         */
        protected $file;


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
        public function getVendor()
        {
            return $this->vendor;
        }

        /**
         * @param mixed $vendor
         */
        public function setVendor($vendor)
        {
            $this->vendor = $vendor;
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
         * @return datetime
         */
        public function getReceiveDate()
        {
            return $this->receiveDate;
        }

        /**
         * @param datetime $receiveDate
         */
        public function setReceiveDate($receiveDate)
        {
            $this->receiveDate = $receiveDate;
        }

        /**
         * @return Purchase
         */
        public function getPurchase()
        {
            return $this->purchase;
        }

        /**
         * @param Purchase $purchase
         */
        public function setPurchase($purchase)
        {
            $this->purchase = $purchase;
        }


        /**
         * @return string
         */
        public function getAccountNo()
        {
            return $this->accountNo;
        }

        /**
         * @param string $accountNo
         */
        public function setAccountNo($accountNo)
        {
            $this->accountNo = $accountNo;
        }

        /**
         * @return mixed
         */
        public function getApprovedBy()
        {
            return $this->approvedBy;
        }

        /**
         * @param mixed $approvedBy
         */
        public function setApprovedBy($approvedBy)
        {
            $this->approvedBy = $approvedBy;
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
         * @return mixed
         */
        public function getToUser()
        {
            return $this->toUser;
        }

        /**
         * @param mixed $toUser
         */
        public function setToUser($toUser)
        {
            $this->toUser = $toUser;
        }

        /**
         * @return float
         */
        public function getTotalAmount()
        {
            return $this->totalAmount;
        }

        /**
         * @param float $totalAmount
         */
        public function setTotalAmount($totalAmount)
        {
            $this->totalAmount = $totalAmount;
        }



        /**
         * Purchase
         * Purchase Return
         * @return string
         */
        public function getProcessType()
        {
            return $this->processType;
        }

        /**
         * @param string $processType
         */
        public function setProcessType($processType)
        {
            $this->processType = $processType;
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
         * @return float
         */
        public function getPurchaseAmount()
        {
            return $this->purchaseAmount;
        }

        /**
         * @param float $purchaseAmount
         */
        public function setPurchaseAmount($purchaseAmount)
        {
            $this->purchaseAmount = $purchaseAmount;
        }

        /**
         * @return float
         */
        public function getPayment()
        {
            return $this->payment;
        }

        /**
         * @param float $payment
         */
        public function setPayment($payment)
        {
            $this->payment = $payment;
        }

        /**
         * @return float
         */
        public function getBalance()
        {
            return $this->balance;
        }

        /**
         * @param float $balance
         */
        public function setBalance($balance)
        {
            $this->balance = $balance;
        }

        /**
         * @return string
         */
        public function getAccountRefNo()
        {
            return $this->accountRefNo;
        }

        /**
         * @param string $accountRefNo
         */
        public function setAccountRefNo($accountRefNo)
        {
            $this->accountRefNo = $accountRefNo;
        }

        /**
         * @return int
         */
        public function getCode()
        {
            return $this->code;
        }

        /**
         * @param int $code
         */
        public function setCode($code)
        {
            $this->code = $code;
        }


        /**
         * Set processHead
         * @param string $processHead
         * Purchase
         * Purchase Return
         * Sales
         * Sales Return
         * Journal
         * Bank
         * Expenditure
         * Payroll
         * Petty Cash
         * @return Transaction
         */


        public function getProcessHead()
        {
            return $this->processHead;
        }

        /**
         * @param string $processHead
         */
        public function setProcessHead($processHead)
        {
            $this->processHead = $processHead;
        }

        /**
         * @return TransactionMethod
         */
        public function getTransactionMethod()
        {
            return $this->transactionMethod;
        }

        /**
         * @param TransactionMethod $transactionMethod
         */
        public function setTransactionMethod($transactionMethod)
        {
            $this->transactionMethod = $transactionMethod;
        }

        /**
         * @return AccountBank
         */
        public function getAccountBank()
        {
            return $this->accountBank;
        }

        /**
         * @param AccountBank $accountBank
         */
        public function setAccountBank($accountBank)
        {
            $this->accountBank = $accountBank;
        }


        /**
         * @return string
         */
        public function getRemark()
        {
            return $this->remark;
        }

        /**
         * @param string $remark
         */
        public function setRemark($remark)
        {
            $this->remark = $remark;
        }

        /**
         * @return AccountCash
         */
        public function getAccountCash()
        {
            return $this->accountCash;
        }

        /**
         * @return mixed
         */
        public function getAccountMobileBank()
        {
            return $this->accountMobileBank;
        }

        /**
         * @param mixed $accountMobileBank
         */
        public function setAccountMobileBank($accountMobileBank)
        {
            $this->accountMobileBank = $accountMobileBank;
        }

        /**
         * @return HmsPurchase
         */
        public function getHmsPurchase()
        {
            return $this->hmsPurchase;
        }

        /**
         * @param mixed $hmsPurchase
         */
        public function setHmsPurchase($hmsPurchase)
        {
            $this->hmsPurchase = $hmsPurchase;
        }


        /**
         * @return HmsVendor
         */
        public function getHmsVendor()
        {
            return $this->hmsVendor;
        }

        /**
         * @param HmsVendor $hmsVendor
         */
        public function setHmsVendor($hmsVendor)
        {
            $this->hmsVendor = $hmsVendor;
        }

        /**
         * @return mixed
         */
        public function getRestaurantPurchase()
        {
            return $this->restaurantPurchase;
        }

        /**
         * @param mixed $restaurantPurchase
         */
        public function setRestaurantPurchase($restaurantPurchase)
        {
            $this->restaurantPurchase = $restaurantPurchase;
        }

        /**
         * @return mixed
         */
        public function getRestaurantVendor()
        {
            return $this->restaurantVendor;
        }

        /**
         * @param mixed $restaurantVendor
         */
        public function setRestaurantVendor($restaurantVendor)
        {
            $this->restaurantVendor = $restaurantVendor;
        }

        /**
         * @return DmsPurchase
         */
        public function getDmsPurchase()
        {
            return $this->dmsPurchase;
        }

        /**
         * @param DmsPurchase $dmsPurchase
         */
        public function setDmsPurchase($dmsPurchase)
        {
            $this->dmsPurchase = $dmsPurchase;
        }

        /**
         * @return DmsVendor
         */
        public function getDmsVendor()
        {
            return $this->dmsVendor;
        }

        /**
         * @param DmsVendor $dmsVendor
         */
        public function setDmsVendor($dmsVendor)
        {
            $this->dmsVendor = $dmsVendor;
        }


        /**
         * @return MedicineVendor
         */
        public function getMedicineVendor()
        {
            return $this->medicineVendor;
        }

        /**
         * @param MedicineVendor $medicineVendor
         */
        public function setMedicineVendor($medicineVendor)
        {
            $this->medicineVendor = $medicineVendor;
        }

        /**
         * @return BusinessPurchase
         */
        public function getBusinessPurchase()
        {
            return $this->businessPurchase;
        }

        /**
         * @param BusinessPurchase $businessPurchase
         */
        public function setBusinessPurchase($businessPurchase)
        {
            $this->businessPurchase = $businessPurchase;
        }



        /**
         * @return string
         */
        public function getSourceInvoice()
        {
            return $this->sourceInvoice;
        }

        /**
         * @param string $sourceInvoice
         */
        public function setSourceInvoice($sourceInvoice)
        {
            $this->sourceInvoice = $sourceInvoice;
        }

        /**
         * @return MedicinePurchase
         */
        public function getMedicinePurchase()
        {
            return $this->medicinePurchase;
        }

        /**
         * @param MedicinePurchase $medicinePurchase
         */
        public function setMedicinePurchase($medicinePurchase)
        {
            $this->medicinePurchase = $medicinePurchase;
        }

        /**
         * @return AccountVendor
         */
        public function getAccountVendor()
        {
            return $this->accountVendor;
        }

        /**
         * @param AccountVendor $accountVendor
         */
        public function setAccountVendor($accountVendor)
        {
            $this->accountVendor = $accountVendor;
        }

	    /**
	     * @return HotelPurchase
	     */
	    public function getHotelPurchase() {
		    return $this->hotelPurchase;
	    }

	    /**
	     * @param HotelPurchase $hotelPurchase
	     */
	    public function setHotelPurchase( $hotelPurchase ) {
		    $this->hotelPurchase = $hotelPurchase;
	    }

	    /**
	     * @return string
	     */
	    public function getCompanyName(){
		    return $this->companyName;
	    }

	    /**
	     * @param string $companyName
	     */
	    public function setCompanyName( string $companyName ) {
		    $this->companyName = $companyName;
	    }

	    /**
	     * @return string
	     */
	    public function getGrn(){
		    return $this->grn;
	    }

	    /**
	     * @param string $grn
	     */
	    public function setGrn( string $grn ) {
		    $this->grn = $grn;
	    }

        /**
         * @return ExpenditureItem
         */
        public function getExpenditureItems()
        {
            return $this->expenditureItems;
        }

        /**
         * @return AccountHead
         */
        public function getAccountHead()
        {
            return $this->accountHead;
        }

        /**
         * @param AccountHead $accountHead
         */
        public function setAccountHead($accountHead)
        {
            $this->accountHead = $accountHead;
        }

        /**
         * @return string
         */
        public function getVoucherType()
        {
            return $this->voucherType;
        }

        /**
         * @param string $voucherType
         */
        public function setVoucherType($voucherType)
        {
            $this->voucherType = $voucherType;
        }

        /**
         * @return VoucherItem
         */
        public function getVoucherItems()
        {
            return $this->voucherItems;
        }

        /**
         * @return string
         */
        public function getMemo()
        {
            return $this->memo;
        }

        /**
         * @param string $memo
         */
        public function setMemo($memo)
        {
            $this->memo = $memo;
        }

        /**
         * @return float
         */
        public function getCommission()
        {
            return $this->commission;
        }

        /**
         * @param float $commission
         */
        public function setCommission($commission)
        {
            $this->commission = $commission;
        }

        /**
         * @return \Appstore\Bundle\TallyBundle\Entity\Purchase
         */
        public function getTallyPurchase()
        {
            return $this->tallyPurchase;
        }

        /**
         * @param \Appstore\Bundle\TallyBundle\Entity\Purchase $tallyPurchase
         */
        public function setTallyPurchase($tallyPurchase)
        {
            $this->tallyPurchase = $tallyPurchase;
        }


        /**
         * Sets file.
         *
         * @param Expenditure $file
         */
        public function setFile($file = null)
        {
            $this->file = $file;
        }

        /**
         * Get file.
         *
         * @return Expenditure
         */
        public function getFile()
        {
            return $this->file;
        }

        public function getAbsolutePath()
        {
            return null === $this->path
                ? null
                : $this->getUploadRootDir().'/'.$this->path;
        }

        public function getWebPath()
        {
            return null === $this->path
                ? null
                : $this->getUploadDir().'/'.$this->path;
        }

        public function removeFileImage()
        {
            $path = null === $this->path
                ? null
                : $this->getUploadRootDir().'/'.$this->path;

            if ($file = $path) {
                unlink($file);
            }
        }

        /**
         * @ORM\PostRemove()
         */
        public function removeUpload()
        {
            if ($file = $this->getAbsolutePath()) {
                unlink($file);
            }
        }

        protected function getUploadRootDir()
        {
            return __DIR__.'/../../../../../web/'.$this->getUploadDir();
        }

        public function getUploadDir()
        {
            return 'uploads/domain/'.$this->getGlobalOption()->getId().'/account/';
        }

        public function upload()
        {
            // the file property can be empty if the field is not required
            if (null === $this->getFile()) {
                return;
            }

            // use the original file name here but you should
            // sanitize it at least to avoid any security issues

            // move takes the target directory and then the
            // target filename to move to
            $filename = date('YmdHmi') . "-" . $this->getFile()->getClientOriginalName();
            $this->getFile()->move(
                $this->getUploadRootDir(),
                $filename
            );
            // set the path property to the filename where you've saved the file
            $this->path = $filename;

            // clean up the file property as you won't need it anymore
            $this->file = null;
        }


    }

