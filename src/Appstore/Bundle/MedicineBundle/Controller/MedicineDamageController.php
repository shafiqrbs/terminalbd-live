<?php

namespace Appstore\Bundle\MedicineBundle\Controller;

use Appstore\Bundle\MedicineBundle\Entity\MedicineDamage;
use Appstore\Bundle\MedicineBundle\Entity\MedicineStock;
use Appstore\Bundle\MedicineBundle\Form\MedicineDamageType;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Damage controller.
 *
 */
class MedicineDamageController extends Controller
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
     * Lists all Damage entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $config = $this->getUser()->getGlobalOption()->getMedicineConfig();
        $entities = $this->getDoctrine()->getRepository('MedicineBundle:MedicineDamage')->findWithSearch($config,$data);
	    $pagination = $this->paginate($entities);
	    return $this->render('MedicineBundle:Damage:index.html.twig', array(
            'entities' => $pagination
        ));
    }

    public function newAction(Request $request){

        $entity = new MedicineDamage();
        $form = $this->createCreateForm($entity);
        return $this->render('MedicineBundle:Damage:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a new Damage entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new MedicineDamage();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        $data = $request->request->all();
        /* @var $stock MedicineStock */
        $stock = $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->find($data['damage']['medicineStock']);
        if(empty($stock)){
            $stock = $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->find($data['medicineStock']);
        }
        if ($form->isValid() and !empty($stock) and $stock->getRemainingQuantity() >= $entity->getQuantity()) {
            $em = $this->getDoctrine()->getManager();
            $config = $this->getUser()->getGlobalOption()->getMedicineConfig();
            $entity->setMedicineConfig($config);
            $entity->setMedicineStock($stock);
            $entity->setPurchasePrice($stock->getPurchasePrice());
            $entity->setSubTotal($stock->getPurchasePrice() * $entity->getQuantity());
            $em->persist($entity);
            $em->flush();
            $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->updateRemovePurchaseQuantity($stock,'damage');
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been inserted successfully"
            );
            return $this->redirect($this->generateUrl('medicine_damage', array('id' => $entity->getId())));
        }
        $this->get('session')->getFlashBag()->add(
            'notice',"Damage quantity must be less or equal current stock quantity"
        );
        return $this->render('MedicineBundle:Damage:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Damage entity.
     *
     * @param Damage $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(MedicineDamage $entity)
    {
        $form = $this->createForm(new MedicineDamageType(), $entity, array(
            'action' => $this->generateUrl('medicine_damage_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    /**
     * Displays a form to edit an existing Damage entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption()->getMedicineConfig();
        $entities = $this->getDoctrine()->getRepository('MedicineBundle:MedicineDamage')->findBy(array('medicineConfig' => $config),array('companyName'=>'ASC'));

        $entity = $em->getRepository('MedicineBundle:MedicineDamage')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Damage entity.');
        }
        $editForm = $this->createEditForm($entity);
        return $this->render('MedicineBundle:Damage:index.html.twig', array(
            'entities'      => $entities,
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Damage entity.
    *
    * @param Damage $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(MedicineDamage $entity)
    {
        $form = $this->createForm(new MedicineDamageType(), $entity, array(
            'action' => $this->generateUrl('medicine_damage_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }
    /**
     * Edits an existing Damage entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption()->getMedicineConfig();
        $entities = $this->getDoctrine()->getRepository('MedicineBundle:MedicineDamage')->findBy(array('medicineConfig' => $config),array('companyName'=>'ASC'));

        $entity = $em->getRepository('MedicineBundle:MedicineDamage')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Damage entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been changed successfully"
            );
            return $this->redirect($this->generateUrl('medicine_damage'));
        }
        return $this->render('MedicineBundle:Damage:index.html.twig', array(
            'entities'      => $entities,
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }
    /**
     * Deletes a Damage entity.
     *
     */
    public function deleteAction($id)
    {

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('MedicineBundle:MedicineDamage')->find($id);
        $stock = $entity->getMedicineStock();
        $em->remove($entity);
        $em->flush();
        $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->updateRemovePurchaseQuantity($stock,'damage');
        $this->get('session')->getFlashBag()->add(
            'error',"Data has been deleted successfully"
        );
        return $this->redirect($this->generateUrl('medicine_damage'));
    }

	public function approvedAction($id)
	{
		$em = $this->getDoctrine()->getManager();
		$config = $this->getUser()->getGlobalOption()->getMedicineConfig();
		$damage = $em->getRepository('MedicineBundle:MedicineDamage')->findOneBy(array('medicineConfig' => $config , 'id' => $id));
		if (!empty($damage) and ($damage->getProcess() == 'Created')) {
			$em = $this->getDoctrine()->getManager();
			$damage->setProcess('Approved');
			$em->flush();
			$this->getDoctrine()->getRepository('AccountingBundle:Transaction')->insertGlobalDamageTransaction($this->getUser()->getGlobalOption(),$damage);
			return new Response('success');
		} else {
			return new Response('failed');
		}
	}


}
