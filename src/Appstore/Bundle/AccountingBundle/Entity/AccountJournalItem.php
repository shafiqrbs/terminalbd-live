<?php

namespace Appstore\Bundle\AccountingBundle\Entity;

use Appstore\Bundle\DomainUserBundle\Entity\Branches;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Setting\Bundle\ToolBundle\Entity\TransactionMethod;

/**
 * AccountJournal
 *
 * @ORM\Table(name="account_journal_item")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\AccountingBundle\Repository\AccountJournalItemRepository")
 */
class AccountJournalItem
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
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountJournal", inversedBy="accountJournalItems")
     * @ORM\JoinColumn(onDelete="CASCADE")
     **/
    protected $accountJournal;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountHead")
     **/
    protected $accountHead;

    /**
     * @ORM\ManyToOne(targetEntity="Appstore\Bundle\AccountingBundle\Entity\AccountHead")
     **/
    protected $accountSubHead;


    /**
     * @var float
     *
     * @ORM\Column(name="debit", type="float", nullable=true)
     */
    private $debit = 0;

    /**
     * @var float
     *
     * @ORM\Column(name="credit", type="float", nullable=true)
     */
    private $credit = 0;


    /**
     * @var string
     *
     * @ORM\Column(name="process", type="string", length=50, nullable = true)
     */
    private $process;


    /**
     * @var string
     *
     * @ORM\Column(name="narration", type="text", length=50, nullable = true)
     */
    private $narration;


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
     * @return AccountHead
     */
    public function getAccountSubHead()
    {
        return $this->accountSubHead;
    }

    /**
     * @param AccountHead $accountSubHead
     */
    public function setAccountSubHead($accountSubHead)
    {
        $this->accountSubHead = $accountSubHead;
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
    public function getNarration()
    {
        return $this->narration;
    }

    /**
     * @param string $narration
     */
    public function setNarration($narration)
    {
        $this->narration = $narration;
    }


}

