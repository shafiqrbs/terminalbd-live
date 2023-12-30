<?php

namespace Appstore\Bundle\AccountingBundle\Entity;

use Core\UserBundle\Entity\User;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;


/**
 * AccountBank
 *
 *
 * @ORM\Table(name="account_profit")
 * @ORM\Entity(repositoryClass="Appstore\Bundle\AccountingBundle\Repository\AccountProfitRepository")
 */
class AccountProfit
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
     * @ORM\ManyToOne(targetEntity="Setting\Bundle\ToolBundle\Entity\GlobalOption", inversedBy="accountProfit")
     **/
    protected $globalOption;


    /**
     * @ORM\OneToMany(targetEntity="Appstore\Bundle\AccountingBundle\Entity\Transaction", mappedBy="accountProfit" )
     */
    protected $transactions;

    /**
     * @Gedmo\Blameable(on="create")
     * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User")
     **/
    private  $createdBy;


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
     * @var \DateTime
     * @ORM\Column(name="generateMonth", type="datetime")
     */
    private $generateMonth;


    /**
     * @var integer
     *
     * @ORM\Column(name="month", type="integer",  nullable = true)
     */
    private $month = 0;


    /**
     * @var integer
     *
     * @ORM\Column(name="year", type="integer",  nullable = true)
     */
    private $year = 0;


    /**
     * @var float
     *
     * @ORM\Column(name="sales", type="float",  nullable = true)
     */
    private $sales = 0;


     /**
     * @var float
     *
     * @ORM\Column(name="salesReturn", type="float",  nullable = true)
     */
    private $salesReturn = 0;


    /**
     * @var float
     *
     * @ORM\Column(name="purchase", type="float",  nullable = true)
     */
    private $purchase = 0;

    /**
     * @var float
     *
     * @ORM\Column(name="expenditure", type="float",  nullable = true)
     */
    private $expenditure = 0;

    /**
     * @var float
     *
     * @ORM\Column(name="revenue", type="float",  nullable = true)
     */
    private $revenue = 0;


    /**
     * @var float
     *
     * @ORM\Column(name="profit", type="float",  nullable = true)
     */
    private $profit = 0;

    /**
     * @var float
     *
     * @ORM\Column(name="loss", type="float",  nullable = true)
     */
    private $loss = 0;


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
     * @return Transaction
     */
    public function getTransactions()
    {
        return $this->transactions;
    }

    /**
     * @return int
     */
    public function getMonth()
    {
        return $this->month;
    }

    /**
     * @param int $month
     */
    public function setMonth($month)
    {
        $this->month = $month;
    }

    /**
     * @return int
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * @param int $year
     */
    public function setYear($year)
    {
        $this->year = $year;
    }

    /**
     * @return float
     */
    public function getLoss()
    {
        return $this->loss;
    }

    /**
     * @param float $loss
     */
    public function setLoss($loss)
    {
        $this->loss = $loss;
    }

    /**
     * @return float
     */
    public function getProfit()
    {
        return $this->profit;
    }

    /**
     * @param float $profit
     */
    public function setProfit($profit)
    {
        $this->profit = $profit;
    }

    /**
     * @return float
     */
    public function getSales()
    {
        return $this->sales;
    }

    /**
     * @param float $sales
     */
    public function setSales($sales)
    {
        $this->sales = $sales;
    }


    /**
     * @return float
     */
    public function getPurchase()
    {
        return $this->purchase;
    }

    /**
     * @param float $purchase
     */
    public function setPurchase($purchase)
    {
        $this->purchase = $purchase;
    }

    /**
     * @return float
     */
    public function getExpenditure()
    {
        return $this->expenditure;
    }

    /**
     * @param float $expenditure
     */
    public function setExpenditure($expenditure)
    {
        $this->expenditure = $expenditure;
    }

    /**
     * @return float
     */
    public function getRevenue()
    {
        return $this->revenue;
    }

    /**
     * @param float $revenue
     */
    public function setRevenue($revenue)
    {
        $this->revenue = $revenue;
    }

    /**
     * @return \DateTime
     */
    public function getGenerateMonth()
    {
        return $this->generateMonth;
    }

    /**
     * @param \DateTime $generateMonth
     */
    public function setGenerateMonth($generateMonth)
    {
        $this->generateMonth = $generateMonth;
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
     * @return float
     */
    public function getSalesReturn()
    {
        return $this->salesReturn;
    }

    /**
     * @param float $salesReturn
     */
    public function setSalesReturn($salesReturn)
    {
        $this->salesReturn = $salesReturn;
    }

}

