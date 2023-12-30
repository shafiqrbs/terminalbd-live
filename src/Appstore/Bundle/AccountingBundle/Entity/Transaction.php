<?php

namespace Appstore\Bundle\AccountingBundle\Entity;

use Appstore\Bundle\DomainUserBundle\Entity\Branches;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Transaction
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Appstore\Bundle\AccountingBundle\Repository\TransactionRepository")
 */
class Transaction
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
     * @var string
     *
     * @ORM\Column(name="processHead", type="string", length=50 , nullable = true)
     */
    private $processHead;

     /**
     * @var string
     *
     * @ORM\Column(name="process", type="string", length=50 , nullable = true)
     */
    private $process;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountHead", inversedBy="transactions" )
     **/
     private $accountHead;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountProfit", inversedBy="transactions" )
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
     private $accountProfit;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountJournal", inversedBy="transactions")
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
     private $accountJournal;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountHead", inversedBy="subTransactions" )
     **/
     private $subAccountHead;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\DomainUserBundle\Entity\Branches", inversedBy="transactions" )
     **/
    private  $branches;


    /**
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\GlobalOption", inversedBy="transactions")
     **/
    protected $globalOption;


    /**
     * @var string
     *
     * @ORM\Column(name="toIncrease", type="string", length=50 , nullable = true)
     */
    private $toIncrease;


    /**
     * @var float
     *
     * @ORM\Column(name="amount", type="float" , nullable = true)
     */
    private $amount = 0 ;

    /**
     * @var float
     *
     * @ORM\Column(name="debit", type="float" , nullable = true)
     */
    private $debit = 0 ;

    /**
     * @var float
     *
     * @ORM\Column(name="credit", type="float" , nullable = true)
     */
    private $credit = 0 ;

    /**
     * @var float
     *
     * @ORM\Column(name="balance", type="decimal" , nullable = true)
     */
    private $balance = 0 ;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text" , nullable = true)
     */
    private $content;

    /**
     * @var string
     *
     * @ORM\Column(name="accountRefNo", type="string", length=50, nullable=true)
     */
    private $accountRefNo;


    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created", type="datetime" , nullable=true)
     */
    private $created;

    /**
     * @var \DateTime
     * @ORM\Column(name="updated", type="datetime", nullable=true)
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
     * Set created
     *
     * @param \DateTime $created
     *
     * @return Transaction
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
     * Set updated
     *
     * @param \DateTime $updated
     *
     * @return Transaction
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }



    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content = $content;
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
     * @return float
     */
    public function getDebit()
    {
        return $this->debit;
    }

    /**
     * @param float $debit
     */
    public function setDebit($debit)
    {
        $this->debit = $debit;
    }

    /**
     * @return float
     */
    public function getCredit()
    {
        return $this->credit;
    }

    /**
     * @param float $credit
     */
    public function setCredit($credit)
    {
        $this->credit = $credit;
    }

    /**
     * @return string
     * Assets
     * Dividend
     * Liability
     * Owner's Equity
     * Operating Revenue
     * Operating Expense
     * Non-Operating Revenues and Expenses, Gains, and Losses
     * Marketing Expenses
     * Other
     */


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
    public function getToIncrease()
    {
        return $this->toIncrease;
    }

    /**
     * @param string $toIncrease
     */
    public function setToIncrease($toIncrease)
    {
        $this->toIncrease = $toIncrease;
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
     * @return AccountHead
     */
    public function getSubAccountHead()
    {
        return $this->subAccountHead;
    }

    /**
     * @param AccountHead $subAccountHead
     */
    public function setSubAccountHead($subAccountHead)
    {
        $this->subAccountHead = $subAccountHead;
    }

    /**
     * @return AccountProfit
     */
    public function getAccountProfit()
    {
        return $this->accountProfit;
    }

    /**
     * @param AccountProfit $accountProfit
     */
    public function setAccountProfit($accountProfit)
    {
        $this->accountProfit = $accountProfit;
    }

    /**
     * @return AccountJournal
     */
    public function getAccountJournal()
    {
        return $this->accountJournal;
    }

    /**
     * @param AccountJournal $accountJournal
     */
    public function setAccountJournal($accountJournal)
    {
        $this->accountJournal = $accountJournal;
    }

}

