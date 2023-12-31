<?php

namespace Appstore\Bundle\HospitalBundle\Repository;
use Appstore\Bundle\HospitalBundle\Entity\InvoiceParticular;
use Appstore\Bundle\HospitalBundle\Entity\InvoicePathologicalReport;
use Doctrine\ORM\EntityRepository;


/**
 * InvoicePathologicalReportRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class InvoicePathologicalReportRepository extends EntityRepository
{

    public function insert(InvoiceParticular $invoiceParticular , $data)
    {
        $this->insertPathologicalReportValue($invoiceParticular , $data);
        $this->insertMetaValue($invoiceParticular , $data);
    }

    public function insertMetaValue($invoiceParticular , $data){

        $em = $this->_em;
        if ($data and isset($data['metaValue']) and !empty($data['metaValue'])) {

            $i = 0;
            foreach ($data['metaValue'] as $row):

                if (isset($data['metaValue'][$i]) and !empty($data['metaValue'][$i])) {

                    if (!empty($data['metaValue'][$i]) and !empty($data['metaId'][$i])) {
                        $entity = $em->getRepository('HospitalBundle:InvoicePathologicalReport')->find($data['metaId'][$i]);
                    } elseif (!empty($data['metaValue'][$i])) {
                        $entity = new InvoicePathologicalReport();
                        $entity->setInvoiceParticular($invoiceParticular);
                    }
                    $entity->setMetaValue($data['metaValue'][$i]);
                    $entity->setMetaKey($data['metaKey'][$i]);
                    $em->persist($entity);
                }
                $i++;
            endforeach;
            $em->flush();

        }
    }

    public function insertPathologicalReportValue($invoiceParticular , $data)
    {
        $em = $this->_em;

        if ($data and isset($data['pathologicalReportId']) and !empty($data['pathologicalReportId'])) {

            $i = 0;
            foreach ($data['pathologicalReportId'] as $key => $row):
                $pathologicalReportId = $data['pathologicalReportId'][$key];
                $entity = $em->getRepository('HospitalBundle:InvoicePathologicalReport')->findOneBy(array('invoiceParticular' => $invoiceParticular ,'pathologicalReport' => $pathologicalReportId ));
                if(empty($entity)){
                    $pathologicalReport = $em->getRepository('HospitalBundle:PathologicalReport')->find($pathologicalReportId);
                    $entity = new InvoicePathologicalReport();
                    $entity->setPathologicalReport($pathologicalReport);
                }
                $entity->setInvoiceParticular($invoiceParticular);
                $entity->setResult($data['result'][$key]);
                $em->persist($entity);
                $em->flush($entity);

            endforeach;

        }

    }

}
