<?php

namespace Appstore\Bundle\AccountingBundle\Entity;

use Appstore\Bundle\DomainUserBundle\Entity\Branches;
use Core\UserBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Setting\Bundle\ToolBundle\Entity\TransactionMethod;

/**
 * AccountJournal
 *
 * @ORM\Table(name="account_journal")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\AccountingBundle\Repository\AccountJournalRepository")
 */
class AccountJournal
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
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\GlobalOption", inversedBy="accountJournal")
     **/

    protected $globalOption;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\DomainUserBundle\Entity\Branches", inversedBy="accountJournal" )
     **/
    private  $branches;

    /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\TransactionMethod", inversedBy="accountJournals" )
     **/
    private  $transactionMethod;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\Transaction", mappedBy="accountJournal" , cascade={"detach","merge"} )
     **/
    private  $transactions;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountCash", mappedBy="accountJournal" , cascade={"detach","merge"} )
     **/
    private  $accountCash;

    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountJournalItem", mappedBy="accountJournal" , cascade={"detach","merge"} )
     **/
    private  $accountJournalItems;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountHead", inversedBy="accountJournalDebits" )
     **/
    private  $accountHeadDebit;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountHead", inversedBy="accountJournalCredits" )
     **/
    private  $accountHeadCredit;

     /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\AccountingBundle\Entity\VoucherType")
     **/
    private  $voucherType;

    /**
     * @Gedmo\Blameable(on="create")
     * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User", inversedBy="accountJournal" )
     **/
    private  $createdBy;


    /**
     * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User", inversedBy="accountJournalToUser" )
     **/
    private  $toUser;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountBank", inversedBy="accountJournals" )
     **/
    private  $accountBank;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountMobileBank", inversedBy="accountJournals" )
     **/
    private  $accountMobileBank;

    /**
     * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User", inversedBy="accountJournalApprove" )
     **/
    private  $approvedBy;


    /**
     * @var float
     *
     * @ORM\Column(name="amount", type="float",  nullable = true)
     */
    private $amount;

    /**
     * @var string
     *
     * @ORM\Column(name="remark", type="text",  nullable = true)
     */
    private $remark;

    /**
     * @var string
     *
     * @ORM\Column(name="processHead", type="string", length=100, nullable = true)
     */
    private $processHead;

    /**
     * @var string
     *
     * @ORM\Column(name="journalSource", type="string", length=100, nullable = true)
     */
    private $journalSource;

    /**
     * @var string
     *
     * @ORM\Column(name="process", type="string", length=50, nullable = true)
     */
    private $process;

     /**
     * @var string
     *
     * @ORM\Column(name="mode", type="string", length=50, nullable = true)
     */
    private $mode = "journal";


    /**
     * @var string
     *
     * @ORM\Column(name="transactionType", type="string", length=25, nullable = true)
     */
    private $transactionType = 'Debit';

    /**
     * @var string
     *
     * @ORM\Column(name="accountRefNo", type="string", length=50, nullable=true)
     */
    private $accountRefNo;

    /**
     * @var integer
     *
     * @ORM\Column(name="code", type="integer",  nullable=true)
     */
    private $code;


     /**
     * @var string
     *
     * @ORM\Column(name="voucherNo", type="string",  nullable=true)
     */
    private $voucherNo;


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
     * Set amount
     *
     * @param float $amount
     *
     * @return AccountJournal
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
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
     * @return mixed
     */
    public function getToUser()
    {
        return $this->toUser;
    }

    /**
     * @param User $toUser
     */
    public function setToUser($toUser)
    {
        $this->toUser = $toUser;
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
     * @return mixed
     */
    public function getAccountHeadDebit()
    {
        return $this->accountHeadDebit;
    }

    /**
     * @param mixed $accountHeadDebit
     */
    public function setAccountHeadDebit($accountHeadDebit)
    {
        $this->accountHeadDebit = $accountHeadDebit;
    }

    /**
     * @return mixed
     */
    public function getAccountHeadCredit()
    {
        return $this->accountHeadCredit;
    }

    /**
     * @param mixed $accountHeadCredit
     */
    public function setAccountHeadCredit($accountHeadCredit)
    {
        $this->accountHeadCredit = $accountHeadCredit;
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
     * @return string
     */
    public function getTransactionType()
    {
        return $this->transactionType;
    }

    /**
     * @param string $transactionType
     * Debit
     * Credit
     */
    public function setTransactionType($transactionType)
    {
        $this->transactionType = $transactionType;
    }

    /**
     * Set process
     * @param string $process

     * @return Transaction
     */

    public function setProcess($process)
    {
        $this->process = $process;

        return $this;
    }

    /**
     * Get process
     *
     * @return string
     */
    public function getProcess()
    {
        return $this->process;
    }

    /**
     * @return string
     */
    public function getProcessHead()
    {
        return $this->processHead;
    }

    /**
     * @param string $processHead
     *
     * Purchase
     * Purchase Return
     * Sales
     * Sales Return
     * Journal
     * Bank
     * Expenditure
     * Payroll
     * Petty Cash
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
    public function getJournalSource()
    {
        return $this->journalSource;
    }

    /**
     * @param string $journalSource
     */
    public function setJournalSource($journalSource)
    {
        $this->journalSource = $journalSource;
    }

    /**
     * @return string
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * @param string $mode
     */
    public function setMode($mode)
    {
        $this->mode = $mode;
    }

    /**
     * @return AccountJournalItem
     */
    public function getAccountJournalItems()
    {
        return $this->accountJournalItems;
    }

    /**
     * @return Transaction
     */
    public function getTransactions()
    {
        return $this->transactions;
    }

    /**
     * @return mixed
     */
    public function getVoucherType()
    {
        return $this->voucherType;
    }

    /**
     * @param mixed $voucherType
     */
    public function setVoucherType($voucherType)
    {
        $this->voucherType = $voucherType;
    }

    /**
     * @return string
     */
    public function getVoucherNo()
    {
        return $this->voucherNo;
    }

    /**
     * @param string $voucherNo
     */
    public function setVoucherNo($voucherNo)
    {
        $this->voucherNo = $voucherNo;
    }




}

