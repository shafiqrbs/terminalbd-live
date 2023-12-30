<?php

namespace Setting\Bundle\ToolBundle\Importer;

use Appstore\Bundle\InventoryBundle\Entity\Product;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;


class ProductExcel
{
    use ContainerAwareTrait;

    protected $productImport;

    private $data = array();

    public function import($data)
    {
        $this->data = $data;

        foreach($this->data as $key => $item) {

            $inventory = $this->productImport->getInventoryConfig();

            $productName = $item['ProductName'];
            $productOld = $this->getDoctrain()->getRepository('InventoryBundle:Product')->findOneBy(array('inventoryConfig' => $inventory , 'name' => $productName));

            $category = $item['Category'];
            if(!empty($category)){
                $category = $this->getDoctrain()->getRepository('ProductProductBundle:Category')->findOneBy(array('name' => $productName));
            }

            $unit = $item['Unit'];
            if($unit){
                $unit = $this->getDoctrain()->getRepository('InventoryBundle:Product')->findOneBy(array('name' => $unit));
            }

            if(empty($productOld)){
                $product = new Product();
                $product->setInventoryConfig($inventory);
                if(!empty($category)){
                    $product->setCategory($category);
                }
                if(!empty($unit)){
                    $product->setProductUnit($unit);
                }
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