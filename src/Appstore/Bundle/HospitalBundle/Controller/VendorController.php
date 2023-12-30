<?php

namespace Appstore\Bundle\HospitalBundle\Controller;

use Appstore\Bundle\MedicineBundle\Entity\MedicineVendor;
use Appstore\Bundle\HospitalBundle\Form\VendorType;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Vendor controller.
 *
 */
class VendorController extends Controller
{

    public function paginate($entities)
    {
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $this->get('request')->query->get('page', 1)/*page number*/,
            25  /*limit per page*/
        );
        $pagination->setTemplate('SettingToolBundle:Widget:pagination.html.twig');
        return $pagination;
    }

    /**
     * @Secure(roles="ROLE_DOMAIN_HOSPITAL_PURCHASE")
     */
    public function indexAction()
    {

        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $config = $this->getUser()->getGlobalOption()->getMedicineConfig();
        $entities = $this->getDoctrine()->getRepository('MedicineBundle:MedicineVendor')->findWithSearch($this->getUser(),$data);
        $pagination = $this->paginate($entities);
        return $this->render('HospitalBundle:Vendor:index.html.twig', array(
            'entities' => $pagination,
            'searchForm'   => $data,
        ));
    }

    /**
     * @Secure(roles="ROLE_DOMAIN_HOSPITAL_PURCHASE")
     */
    public function newAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new MedicineVendor();
        $form = $this->createCreateForm($entity);
        $config = $this->getUser()->getGlobalOption()->getMedicineConfig();
        return $this->render('HospitalBundle:Vendor:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a new Vendor entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new MedicineVendor();
        $config = $this->getUser()->getGlobalOption()->getMedicineConfig();
        $entities = $this->getDoctrine()->getRepository('MedicineBundle:MedicineVendor')->findWithSearch($this->getUser());
        $pagination = $this->paginate($entities);
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        $data = $request->request->all();
        $company = $data['vendor']['companyName'];
        $exitVendor = $this->getDoctrine()->getRepository('MedicineBundle:MedicineVendor')->findOneBy(array('medicineConfig'=>$config,'companyName'=>$company));
        if ($form->isValid() and empty($exitVendor)) {
            $em = $this->getDoctrine()->getManager();
            $config = $this->getUser()->getGlobalOption()->getMedicineConfig();
            $entity->setMedicineConfig($config);
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been inserted successfully"
            );
            return $this->redirect($this->generateUrl('hms_vendor'));
        }
        $this->getDoctrine()->getRepository('AccountingBundle:AccountVendor')->insertVendor($entity);
        $this->get('session')->getFlashBag()->add(
            'success',"User mobile no already exist,Please try again."
        );
        return $this->render('HospitalBundle:Vendor:index.html.twig', array(
            'entities' => $pagination,
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Vendor entity.
     *
     * @param Vendor $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(MedicineVendor $entity)
    {
        $form = $this->createForm(new VendorType(), $entity, array(
            'action' => $this->generateUrl('hms_vendor_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    /**
     * @Secure(roles="ROLE_DOMAIN_HOSPITAL_PURCHASE")
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption()->getMedicineConfig();
        $entity = $em->getRepository('MedicineBundle:MedicineVendor')->findOneBy(array('medicineConfig'=> $config,'id'=>$id));
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Vendor entity.');
        }
        $editForm = $this->createEditForm($entity);
        return $this->render('HospitalBundle:Vendor:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a Vendor entity.
     *
     * @param Vendor $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(MedicineVendor $entity)
    {
        $form = $this->createForm(new VendorType(), $entity, array(
            'action' => $this->generateUrl('hms_vendor_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }
    /**
     * Edits an existing Vendor entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $global = $this->getUser()->getGlobalOption();
        $config = $global->getMedicineConfig();
        $entities = $this->getDoctrine()->getRepository('MedicineBundle:MedicineVendor')->findWithSearch($this->getUser());
        $pagination = $this->paginate($entities);

        $entity = $em->getRepository('MedicineBundle:MedicineVendor')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Vendor entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);
        $data = $request->request->all();
        if ($editForm->isValid()) {
            $em->flush();
            $this->getDoctrine()->getRepository('MedicineBundle:MedicineVendor')->updateVendorCompanyName($entity);
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been changed successfully"
            );
            return $this->redirect($this->generateUrl('hms_vendor'));
        }

        return $this->render('HospitalBundle:Vendor:index.html.twig', array(
            'entities'      => $pagination,
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }
    /**
     * @Secure(roles="ROLE_DOMAIN_HOSPITAL_PURCHASE")
     */
    public function deleteAction($id)
    {

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('MedicineBundle:MedicineVendor')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Vendor entity.');
        }
        try {

            $em->remove($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'error',"Data has been deleted successfully"
            );

        } catch (ForeignKeyConstraintViolationException $e) {
            $this->get('session')->getFlashBag()->add(
                'notice',"Data has been relation another Table"
            );
        }catch (\Exception $e) {
            $this->get('session')->getFlashBag()->add(
                'notice', 'Please contact system administrator further notification.'
            );
        }

        return $this->redirect($this->generateUrl('hms_vendor'));
    }


    /**
     * Status a Page entity.
     *
     */
    public function statusAction(Request $request, $id)
    {

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('MedicineBundle:MedicineVendor')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find District entity.');
        }

        $status = $entity->isStatus();
        if($status == 1){
            $entity->setStatus(false);
        } else{
            $entity->setStatus(true);
        }
        $em->flush();
        $this->get('session')->getFlashBag()->add(
            'success',"Status has been changed successfully"
        );
        return $this->redirect($this->generateUrl('hms_vendor'));
    }

    public function autoSearchAutoCompleteAction(Request $request)
    {
        $item = $_REQUEST['term'];
        $items = array();
        if ($item) {
            $inventory = $this->getUser()->getGlobalOption()->getMedicineConfig();
            $entities = $this->getDoctrine()->getRepository('MedicineBundle:MedicineVendor')->searchAutoComplete($inventory,$item);
            foreach ($entities as $entity):
                $items[] = array('id' => $entity['id'], 'value' => $entity['text']);
            endforeach;
        }
        return new JsonResponse($items);
    }

    public function autoSearchAction(Request $request)
    {
        $item = $_REQUEST['q'];
        if ($item) {
            $inventory = $this->getUser()->getGlobalOption()->getMedicineConfig();
            $item = $this->getDoctrine()->getRepository('MedicineBundle:MedicineVendor')->searchVendor($inventory,$item);
        }
        return new JsonResponse($item);
    }

    public function searchVendorNameAction($vendor)
    {
        return new JsonResponse(array(
            'id'=>$vendor,
            'text'=>$vendor
        ));
    }


}
