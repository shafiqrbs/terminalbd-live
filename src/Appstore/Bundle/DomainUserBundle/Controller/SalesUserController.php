<?php

namespace Appstore\Bundle\DomainUserBundle\Controller;

use Appstore\Bundle\DomainUserBundle\Entity\DomainUser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


/**
 * WareHouse controller.
 *
 */
class SalesUserController extends Controller
{

    public function indexAction($source ='inventory')
    {
        $em = $this->getDoctrine()->getManager();
        $globalOption = $this->getUser()->getGlobalOption();
        $employees = $em->getRepository('UserBundle:User')->getEmployeeEntities($globalOption);
        return $this->render('DomainUserBundle:SalesUser:index.html.twig', array(
            'globalOption'  => $globalOption,
            'employees'     => $employees
        ));
    }

    public function medicineAction()
    {
        $em = $this->getDoctrine()->getManager();
        $globalOption = $this->getUser()->getGlobalOption();
        $employees = $em->getRepository('UserBundle:User')->getEmployeeEntities($globalOption);
        return $this->render('DomainUserBundle:SalesUser:medicine.html.twig', array(
            'globalOption'  => $globalOption,
            'employees'     => $employees
        ));
    }



    /**
     * Creates a new WareHouse entity.
     *
     */
    public function createAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $globalOption = $this->getUser()->getGlobalOption();
        $data = $request->request->all();
        foreach ($data['user'] as $key => $value){
            $user = $this->getDoctrine()->getRepository('UserBundle:User')->find($value);
            $domainUser = $this->getDoctrine()->getRepository('DomainUserBundle:DomainUser')->findOneBy(array('user' => $user->getId()));
            if(!empty($domainUser)){
                $month = $data['monthlySales'][$key];
                $year = $data['yearlySales'][$key];
                $this->getDoctrine()->getRepository('DomainUserBundle:DomainUser')->updateSalesTarget($user,$month,$year);
            }else{
                $entity = new DomainUser();
                $entity->setGlobalOption($globalOption);
                $entity->setUser($user);
                $entity->setMonthlySales($data['monthlySales'][$key]);
                $entity->setYearlySales($data['yearlySales'][$key]);
                $em->persist($entity);
                $em->flush();
            }

        }
       return $this->redirect($request->headers->get('referer'));

    }
    public function createMedicineAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $globalOption = $this->getUser()->getGlobalOption();
        $data = $request->request->all();


        foreach ($data['user'] as $key => $value){
            $user = $this->getDoctrine()->getRepository('UserBundle:User')->find($value);
            $domainUser = $this->getDoctrine()->getRepository('DomainUserBundle:DomainUser')->findOneBy(array('user' => $user->getId()));
            if(!empty($domainUser)){
                $month = $data['monthlySales'][$key];
                $year = $data['yearlySales'][$key];
                $this->getDoctrine()->getRepository('DomainUserBundle:DomainUser')->updateSalesTarget($user,$month,$year);
            }else{
                $entity = new DomainUser();
                $entity->setGlobalOption($globalOption);
                $entity->setUser($user);
                $entity->setMonthlySales($data['monthlySales'][$key]);
                $entity->setYearlySales($data['yearlySales'][$key]);
                $em->persist($entity);
                $em->flush();
            }

        }
       return $this->redirect($request->headers->get('referer'));

    }



}
