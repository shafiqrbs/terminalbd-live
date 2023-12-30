<?php
namespace Appstore\Bundle\RestaurantBundle\Importer;


use Appstore\Bundle\RestaurantBundle\Entity\ItemImport;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;


class ProductExcel
{
    use ContainerAwareTrait;

    /* @var $itemImport  ItemImport */
    protected $itemImport;


    private $data = array();

    public function isValid($data) {

        return true;

    }

    public function import($data)
    {
        $this->data = $data;
        $inventory = $this->itemImport->getRestaurantConfig();
        foreach($this->data as $key => $item) {

            $name = ucfirst(strtolower($item['Name']));
            $category = ucfirst(strtolower($item['Category']));
            $productOld = $this->getDoctrain()->getRepository('RestaurantBundle:Particular')->findOneBy(array('restaurantConfig' => $inventory,'name' => $name));
            if(empty($productOld)){
                $salesPrice = empty($item['SalesPrice']) ? 0 : $item['SalesPrice'];
                $unit = empty($item['Unit']) ? 'Pcs' : $item['Unit'];
                $service = empty($item['service']) ? 'Product' : $item['service'];
                $barcode = empty($item['Barcode']) ? time() : $item['Barcode'];
                $product = new \Appstore\Bundle\RestaurantBundle\Entity\Particular();
                $product->setRestaurantConfig($inventory);
                $product->setName($name);
                $unit = $this->getDoctrain()->getRepository('SettingToolBundle:ProductUnit')->findOneBy(array('name' => $unit));
                if(!empty($unit)){
                    $product->setUnit($unit);
                }
                $service = $this->getDoctrain()->getRepository('RestaurantBundle:Service')->findOneBy(array('name' => $service));
                if(!empty($service)){
                    $product->setService($service);
                }
                $product->setPrice($salesPrice);
                if ($category) {
                    $cat = $this->getCategory($category);
                    $product->setCategory($cat);
                }
                $this->save($product);
            }

        }

    }

    public function setItemImport($itemImport)
    {
        $this->itemImport = $itemImport;
    }


    private function getCategory($item)
    {
        $config = $this->itemImport->getRestaurantConfig();
        $categoryRepository = $this->getCategoryRepository();

        $category = $categoryRepository->findOneBy(array(
            'restaurantConfig'   => $config,
            'name'              => $item
        ));
        if($category){
            return $category;
        }else{
            $category = new \Appstore\Bundle\RestaurantBundle\Entity\Category();
            $category->setName($item);
            $category->setRestaurantConfig($config);
            $category = $this->save($category);
            return $category;
        }
    }

    private function save($entity){
        $this->persist($entity);
        $this->getEntityManager()->flush();
        return $entity;
    }

    public function setExcelImport($excelImport)
    {
        $this->excelImport = $excelImport;
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

    /**
     * @return \Appstore\Bundle\RestaurantBundle\Repository\CategoryRepository
     */
    private function getCategoryRepository()
    {
        return $this->getDoctrain()->getRepository('RestaurantBundle:Category');
    }


}