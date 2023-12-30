<?php

namespace Appstore\Bundle\DomainUserBundle\Importer;

use Appstore\Bundle\DomainUserBundle\Entity\Customer;
use Core\UserBundle\Entity\Profile;
use Core\UserBundle\Entity\User;
use DoctrineExtensions\Query\Sqlite\Date;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Validator\Constraints\DateTime;


class AssociationMemberExcel
{
    use ContainerAwareTrait;

    protected $gpCustomerImport;

    private $data = array();

    public function import($data)
    {
        $this->data = $data;
        foreach($this->data as $key => $item) {

            $user = "0{$item['mobile']}";
            $existUser = $this->getDoctrain()->getRepository('UserBundle:User')->findOneBy(array('username' => $user));
            if(empty($existUser)){

                $option = $this->getDoctrain()->getRepository('SettingToolBundle:GlobalOption')->find(139);
                $entity = new User();
                $entity->setPlainPassword('123456');
                $entity->setGlobalOption($option);
                $entity->setEnabled(true);
                $entity->setUsername(strtolower($user));
                $entity->setEmail($item['email']);
                $entity->setEnabled(true);
                $entity->setRoles(array('ROLE_CUSTOMER'));
                $entity->setUserGroup("member");
                $em = $this->getDoctrain()->getManager();
                $em->persist($entity);
                $em->flush();



                $pro = new Profile();
                $pro->setUser($entity);
                $pro->setAddress($item['address']);
                $pro->setName($item['name']);
                $pro->setMobile('0'.$item['mobile']);
                $em->persist($pro);
                $em->flush();

                $profile = new Customer();
                $profile->setUser($entity->getId());
                $profile->setGlobalOption($option);
                $profile->setAddress($item['address']);
                $profile->setCustomerType('member');
                $profile->setName($item['name']);
                $profile->setProcess("pending");
                $profile->setBatchYear($item['studyDuration']);
                $profile->setHsc($item['hsc']);
                $profile->setSsc($item['ssc']);
                $profile->setGender("male");
                $profile->setBloodGroup($item['blood']);
                $profile->setAdditionalPhone($item['altPhone']);
                if($item['photo']){
                    $profile->setPath($item['photo']);
                }
                if($item['dob']){
                    $stetoTime = strtotime($item['dob']);
                    $date = date('Y-m-d',$stetoTime);
                    $dob = new \DateTime($date);
                    $profile->setDob($dob);
                }
                $at = strtotime($item['created_at']);
                $created = date('Y-m-d',$at);
                $created_at = new \DateTime($created);
                $profile->setCreated($created_at);
                $profile->setProfession($item['memberOccupation']);
                $profile->setMemberDesignation($item['memberDesignation']);
                if($item['guardianType'] == "Father"){
                    $profile->setFatherDesignation($item['guardianOccupation']);
                    $profile->setFatherName($item['guardianName']);
                }else{
                    $profile->setSpouseOccupation($item['guardianOccupation']);
                    $profile->setSpouseName($item['guardianName']);
                    $profile->setSpouseDesignation($item['guardianDesignation']);
                }
                $profile->setMobile($user);
                $em->persist($profile);
                $em->flush();
            }

        }

    }

    public function setGpCustomerImport($gpCustomerImport)
    {
        $this->gpCustomerImport = $gpCustomerImport;
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