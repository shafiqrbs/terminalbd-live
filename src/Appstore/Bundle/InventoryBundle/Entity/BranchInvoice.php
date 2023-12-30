<?php

namespace Appstore\Bundle\InventoryBundle\Entity;

use Appstore\Bundle\AccountingBundle\Entity\AccountBank;
use Appstore\Bundle\AccountingBundle\Entity\AccountBkash;
use Appstore\Bundle\AccountingBundle\Entity\AccountMobileBank;
use Appstore\Bundle\DomainUserBundle\Entity\Branches;
use Core\UserBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Setting\Bundle\ToolBundle\Entity\Bank;
use Setting\Bundle\ToolBundle\Entity\TransactionMethod;

/**
 * BranchInvoice
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Appstore\Bundle\InventoryBundle\Repository\BranchInvoiceRepository")
 */
class BranchInvoice
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
         * @ORM\ManyToOne(targetEntity="Appstore\Bundle\InventoryBundle\Entity\InventoryConfig", inversedBy="branchInvoices" )
         **/
        private  $inventoryConfig;

        /**
         * @ORM\ManyToOne(targetEntity="Appstore\Bundle\DomainUserBundle\Entity\Branches", inversedBy="branchInvoice" )
         **/

        private  $branches;

        /**
         * @ORM\OneToOne(targetEntity="Appstore\Bundle\InventoryBundle\Entity\SalesImport", inversedBy="branchInvoice" )
         **/
        private  $branchInvoiceImport;

        /**
         * @ORM\OneToMany(targetEntity="Appstore\Bundle\InventoryBundle\Entity\BranchInvoiceItem", mappedBy="branchInvoice"  , cascade={"remove"} )
         * @ORM\OrderBy({"id" = "DESC"})
         **/
        private  $branchInvoiceItems;

        /**
         * @Gedmo\Blameable(on="create")
         * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User", inversedBy="branchInvoice" )
         **/
        private  $createdBy;

        /**
         * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User", inversedBy="branchInvoiceApprovedBy" )
         **/
        private  $approvedBy;

        /**
         * @var string
         *
         * @ORM\Column(name="invoice", type="string", length=50, nullable=true)
         */
        private $invoice;

        /**
         * @var integer
         *
         * @ORM\Column(name="code", type="integer",  nullable=true)
         */
        private $code;

        /**
         * @var integer
         *
         * @ORM\Column(name="totalItem", type="smallint", length=3, nullable=true)
         */
        private $totalItem;

        /**
         * @var float
         *
         * @ORM\Column(name="totalQuantity", type="float", nullable=true)
         */
        private $totalQuantity;


        /**
         * @var string
         *
         * @ORM\Column(name="process", type="string", length=50, nullable=true)
         */
        private $process = "pending";


        /**
         * @var string
         *
         * @ORM\Column(name="total", type="decimal", nullable=true)
         */
        private $total;


        /**
         * @var string
         *
         * @ORM\Column(name="comment", type="text", nullable=true)
         */
        private $comment;


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
         * @return string
         */
        public function getTotal()
        {
         return $this->total;
        }

        /**
         * @param string $total
         */
        public function setTotal($total)
        {
         $this->total = $total;
        }

        /**
         * @return string
         */
        public function getComment()
        {
         return $this->comment;
        }

        /**
         * @param string $comment
         */
        public function setComment($comment)
        {
         $this->comment = $comment;
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
         * @return User
         */
        public function getCreatedBy()
        {
         return $this->createdBy;
        }

        /**
         * @param User $createdBy
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
         * @return BranchInvoiceItem
         */
        public function getBranchInvoiceItems()
        {
                return $this->branchInvoiceItems;
        }

        /**
         * @return User
         */
        public function getApprovedBy()
        {
                return $this->approvedBy;
        }

        /**
         * @param User $approvedBy
         */
        public function setApprovedBy($approvedBy)
        {
                $this->approvedBy = $approvedBy;
        }

        /**
         * @return SalesImport
         */
        public function getBranchInvoiceImport()
        {
                return $this->branchInvoiceImport;
        }

        /**
         * @param SalesImport $branchInvoiceImport
         */
        public function setBranchInvoiceImport($branchInvoiceImport)
        {
                $this->branchInvoiceImport = $branchInvoiceImport;
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
         * @return float
         */
        public function getTotalQuantity()
        {
                return $this->totalQuantity;
        }

        /**
         * @param float $totalQuantity
         */
        public function setTotalQuantity($totalQuantity)
        {
                $this->totalQuantity = $totalQuantity;
        }
}

