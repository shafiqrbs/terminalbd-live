<?php

namespace Setting\Bundle\ToolBundle\Importer;

use Setting\Bundle\ToolBundle\Entity\TaxTariff;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class TariffExcel
{
    use ContainerAwareTrait;

    private $data = array();

    public function import($data)
    {
        $this->data = $data;

        foreach($this->data as $key => $item) {

            $hsCode = $item['HSCode'];
            $productOld = $this->getDoctrain()->getRepository('SettingToolBundle:TaxTariff')->findOneBy(array('hsCode' => $hsCode));
            if(empty($productOld) and !empty($item['Name'])){
                $product = new TaxTariff();
                $product->setHsCode($item['HSCode']);
                $product->setName( $item['Name']);
                $product->setCustomsDuty( $item['CD']);
                $product->setSupplementaryDuty( $item['SD']);
                $product->setValueAddedTax( $item['VAT']);
                $product->setAdvanceIncomeTax( $item['AIT']);
                $product->setRegularityDuty( $item['RD']);
                $product->setAdvanceTax( $item['ATV']);
                $this->persist($product);
                $this->flush($product);
            }

        }

    }


    /**
     * @return \Doctrine\Common\Persistence\ObjectManager|object
     */
    private function getEntityManager()
    {
        return $this->getDoctrain()->getManager();
    }


    private function persist($entity){
        $this->getEntityManager()->persist($entity);
    }

    private function flush()
    {
        $this->getEntityManager()->flush();
    }

    /**
     * @return \Doctrine\Bundle\DoctrineBundle\Registry
     */
    private function getDoctrain()
    {
        return $this->container->get('doctrine');
    }


}