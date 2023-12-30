<?php

namespace Appstore\Bundle\HospitalBundle\Repository;
use Appstore\Bundle\HospitalBundle\Controller\InvoiceController;
use Appstore\Bundle\HospitalBundle\Entity\Invoice;
use Appstore\Bundle\HospitalBundle\Entity\InvoiceParticular;
use Doctrine\ORM\EntityRepository;


/**
 * DoctorInvoiceParticularRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class DoctorInvoiceParticularRepository extends EntityRepository
{

    public function insertInvoiceItems($invoice, $data)
    {
        $particular = $this->_em->getRepository('HospitalBundle:Particular')->find($data['particularId']);
        $em = $this->_em;
        $entity = new InvoiceParticular();
        $entity->setHmsInvoice($invoice);
        $entity->setParticular($particular);
        $entity->setEstimatePrice($particular->getPrice());
        $entity->setSalesPrice($data['price']);
        $entity->setQuantity($data['quantity']);
        $entity->setSubTotal($data['price'] * $data['quantity']);
        $em->persist($entity);
        $em->flush();

    }

    public function getSalesItems(Invoice $sales)
    {
        $entities = $sales->getInvoiceParticulars();
        $data = '';
        $i = 1;
        foreach ($entities as $entity) {
            $data .= '<tr id="remove-'. $entity->getId() . '">';
            $data .= '<td class="span1"><span class="badge badge-warning toggle badge-custom" id=' . $i . '" ><span>[+]</span></span></td>';
            $data .= '<td class="span1" >' . $i . '</td>';
            $data .= '<td class="span1" >' . $entity->getParticular()->getParticularCode() . '</td>';
            $data .= '<td class="span4" >' . $entity->getParticular()->getName() . '</td>';
            $data .= '<td class="span2" >' . $entity->getParticular()->getService()->getName() . '</td>';
            $data .= '<td class="span1" >' . $entity->getQuantity() . '</td>';
            $data .= '<td class="span2" >' . $entity->getSubTotal() . '</td>';
            $data .= '<td class="span1" >
                     <a id="'.$entity->getId(). '" title="Are you sure went to delete ?" data-url="/hms/invoice/' . $sales->getId() . '/' . $entity->getId() . '/particular-delete" href="javascript:" class="btn red mini delete" ><i class="icon-trash"></i></a>
                     </td>';
            $data .= '</tr>';
            if ($entity->getParticular()->getService()->getId() == 1 ){
                $data .= '<tr id="show-'. $entity->getId() . '" class="showing-overview">';
                $data .= '<td colspan="7">';
                $data .= '<table class="table table-bordered table-striped table-condensed flip-content ">';
                $data .= '<tr class="">';
                $data .= '<th class="span3" >Instruction</th>';
                $data .= '<td class="span9" >' . $entity->getParticular()->getInstruction() . '</td>';
                $data .= '</tr>';
                $data .= '</table>';
                $data .= '</td>';
                $data .= '</tr>';
            }


            $i++;
        }
        return $data;
    }

    public function invoiceParticularLists($user){

            $invoice = isset($data['invoice'])? $data['invoice'] :'';
            $particular = isset($data['particular'])? $data['particular'] :'';
            $category = isset($data['category'])? $data['category'] :'';

            $hospital = $user->getGlobalOption()->getHospitalConfig()->getId();

            $qb = $this->createQueryBuilder('e');
            $qb->select('e');
            $qb->join('e.invoice','invoice');
            $qb->join('e.particular','particular');
            $qb->join('particular.category','category');
            $qb->where('particular.service = :service')->setParameter('service',1) ;
            $qb->andWhere('invoice.hospitalConfig = :hospital')->setParameter('hospital', $hospital) ;
            /*$qb->andWhere('particular.process IN(:process)');
            $qb->setParameter('process',array_values(array('In-progress','Damage','Impossible')));
            if (!empty($invoice)) {
                $qb->andWhere($qb->expr()->like("invoice.invoice", "'%$invoice%'"  ));
            }
            if (!empty($particular)) {
                $qb->andWhere('particular.name = :partName')->setParameter('partName', $particular) ;
            }
            if (!empty($category)) {
                $qb->andWhere('category.name = :catName')->setParameter('catName', $category) ;
            }*/
            $qb->orderBy('e.updated','DESC');
            $qb->getQuery();
            return  $qb;

    }
}
