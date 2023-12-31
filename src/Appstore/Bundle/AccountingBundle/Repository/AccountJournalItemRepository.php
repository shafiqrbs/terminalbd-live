<?php

namespace Appstore\Bundle\AccountingBundle\Repository;
use Appstore\Bundle\AccountingBundle\Entity\AccountJournal;
use Appstore\Bundle\AccountingBundle\Entity\AccountJournalItem;
use Appstore\Bundle\BusinessBundle\Entity\BusinessPurchase;
use Appstore\Bundle\HospitalBundle\Entity\HmsInvoiceReturn;
use Appstore\Bundle\InventoryBundle\Entity\Purchase;
use Appstore\Bundle\InventoryBundle\Entity\SalesReturn;
use Appstore\Bundle\MedicineBundle\Entity\MedicinePurchase;
use Appstore\Bundle\MedicineBundle\Entity\MedicineSalesReturn;
use Core\UserBundle\Entity\User;
use Doctrine\ORM\EntityRepository;

/**
 * AccountJournalRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class AccountJournalItemRepository extends EntityRepository
{


    /**
     * @param $qb
     * @param $data
     */

    protected function handleSearchBetween($qb,$data)
    {

        if(!empty($data))
        {

            $accountHead = isset($data['accountHead']) ? $data['accountHead'] :'';
            $accountSubHead = isset($data['accountSubHead']) ? $data['accountSubHead'] :'';
            $startDate = isset($data['startDate']) ? $data['startDate'] : '';
            $endDate =   isset($data['endDate']) ? $data['endDate'] : '';
            $user =    isset($data['user'])? $data['user'] :'';

            if (!empty($toUser)) {
                $qb->join('e.toUser','u');
                $qb->andWhere("u.username = :toUser");
                $qb->setParameter('toUser', $toUser);
            }

            if (!empty($user)) {
                $qb->join('j.createdBy','user');
                $qb->andWhere("user.id = :user")->setParameter('user', $user);
            }

            if (!empty($accountHead)) {
                $qb->andWhere("e.accountHead = :accountHead");
                $qb->setParameter('accountHead', $accountHead);
            }

            if (!empty($accountSubHead)) {
                $qb->andWhere("e.accountSubHead = :accountSubHead");
                $qb->setParameter('accountSubHead', $accountSubHead);
            }

            if (!empty($startDate) ) {
                $datetime = new \DateTime($data['startDate']);
                 $startDate = $datetime->format('Y-m-d 00:00:00');
                $qb->andWhere("j.created >= :startDate");
                $qb->setParameter('startDate', $startDate);
            }

            if (!empty($endDate)) {
                $datetime = new \DateTime($data['endDate']);
                $date = $datetime->format('Y-m-d 23:59:59');
                $qb->andWhere("j.created <= :endDate");
                $qb->setParameter('endDate', $date);
            }

        }
    }

    public function insertDoubleEntry(AccountJournal $journal, $data)
    {
        $em = $this->_em;

        foreach ($data['accountHead'] as $key => $value):

           if(($value and $data['debit'][$key]) or ($value and $data['credit'][$key]) ) {

               $metaId = isset($data['journalItem'][$key]) ? $data['journalItem'][$key] : 0 ;
               $journalItem = $this->find($metaId);
               if($journalItem){
                   $item = $journalItem;
               }else{
                   $item = new AccountJournalItem();
               }
               $item->setAccountJournal($journal);
               $accountHead = $em->getRepository('AccountingBundle:AccountHead')->find($value);
               $item->setAccountHead($accountHead);
               $item->setDebit($data['debit'][$key]);
               $item->setCredit($data['credit'][$key]);
               if ($data['subAccountHead'][$key] > 0) {
                   $accountSubHead = $em->getRepository('AccountingBundle:AccountHead')->find($data['subAccountHead'][$key]);
                   $item->setAccountSubHead($accountSubHead);
               }
               $item->setNarration($data['narration'][$key]);
               $em->persist($item);
               $em->flush();;
           }

        endforeach;
    }

    public function findDoubleEntrySearch(User $user,$data = '')
    {
        $globalOption = $user->getGlobalOption()->getId();
        $qb = $this->createQueryBuilder('e');
        $qb->select('e.debit','e.credit','e.narration');
        $qb->addSelect('j.created','j.accountRefNo');
        $qb->addSelect('h.name accountHead');
        $qb->addSelect('sh.name subAccountHead');
        $qb->join('e.accountJournal','j');
        $qb->join('e.accountHead','h');
        $qb->leftJoin('e.accountSubHead','sh');
        $qb->where("j.globalOption = :globalOption");
        $qb->setParameter('globalOption', $globalOption);
        $this->handleSearchBetween($qb,$data);
        $qb->orderBy('j.created','DESC');
        $qb->orderBy('j.accountRefNo','ASC');
        $result = $qb->getQuery();
        return $result;
    }

    public function dailyJournal($user,$data)
    {
        $globalOption = $user->getGlobalOption()->getId();
        $qb = $this->createQueryBuilder('e');
        $qb->select('h.name as name','SUM(e.debit) as debit','SUM(e.credit) as credit','SUM(e.debit) - SUM(e.credit) as amount');
        $qb->join('e.accountJournal','j');
        $qb->join('e.accountHead','h');
        $qb->where("j.globalOption = :globalOption")->setParameter('globalOption', $globalOption);
        $qb->andWhere("h.slug IN (:slug)")->setParameter('slug', array_values(array('cash-in-hand','mobile-account','bank-account')));
        $qb->andWhere("j.process = 'approved'");
        $this->handleSearchBetween($qb,$data);
        $qb->groupBy('h.name');
        $qb->orderBy('h.name','ASC');
        $result = $qb->getQuery()->getArrayResult();
        return $result;
    }
}
