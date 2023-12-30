<?php

namespace Appstore\Bundle\DomainUserBundle\Importer;

use Appstore\Bundle\DomainUserBundle\Entity\Customer;
use Core\UserBundle\Entity\Profile;
use Core\UserBundle\Entity\User;
use DoctrineExtensions\Query\Sqlite\Date;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Validator\Constraints\DateTime;


class CustomerExcel
{
    use ContainerAwareTrait;

    protected $globalOption;

    private $data = array();

    public function isValid($data) {

        if($this->globalOption == null) {
            throw new \Exception("You must set config first");
        }
        return true;
    }


    public function import($data)
    {
        $this->data = $data;
        foreach($this->data as $key => $item) {
            $em = $this->getDoctrain()->getManager();
            $mobile = "0{$item['mobile']}";
            $name = $item['name'];
            $existUser = $this->getDoctrain()->getRepository('DomainUserBundle:Customer')->findOneBy(array('globalOption' => $this->globalOption, 'name' => $name, 'mobile' => $mobile));
            if(empty($existUser)){
                $customer = new Customer();
                $customer->setGlobalOption($this->globalOption);
                $customer->setAddress($item['address']);
                $customer->setName($item['name']);
                $customer->setMobile($mobile);
                $em->persist($customer);
                $em->flush();
            }

        }

    }

    public function setGlobalOption($globalOption)
    {
        $this->globalOption = $globalOption;
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


    function sentence_case($string) {

        $sentences = preg_split('/([.?!]+)/', $string, -1, PREG_SPLIT_NO_EMPTY|PREG_SPLIT_DELIM_CAPTURE);
        $new_string = '';
        foreach ($sentences as $key => $sentence) {
            $new_string .= ($key & 1) == 0?
                ucfirst(strtolower(trim($sentence))) :
                $sentence.' ';
        }
        return trim($new_string);
    }



}