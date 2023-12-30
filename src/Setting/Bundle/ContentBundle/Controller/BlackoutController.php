<?php

namespace Setting\Bundle\ContentBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Setting\Bundle\ContentBundle\Entity\Blackout;
use Setting\Bundle\ContentBundle\Form\BlackoutType;

/**
 * Blackout controller.
 *
 */
class BlackoutController extends Controller
{

    /**
     * Lists all Blackout entities.
     *
     */
    public function indexAction()
    {

        $user = $this->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingContentBundle:Blackout')->findOneBy(array('user'=>$user));

        if (!empty($entity)) {

            $id = $entity->getId();
            return $this->redirect($this->generateUrl('blackout_edit',array('id'=>$id)));

        }else{

            return $this->redirect($this->generateUrl('blackout_new'));
        }


    }
    /**
     * Creates a new Blackout entity.
     *
     */
    public function createAction(Request $request)
    {
        $user = $this->get('security.context')->getToken()->getUser();
        $entity = new Blackout();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Blackout entity.');
        }

        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $entity->setUser($user);
            $em->persist($entity);
            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'success',"Data has been inserted successfully"
            );
        }
        return $this->redirect($this->generateUrl('blackout'));

    }

    /**
     * Creates a form to create a Blackout entity.
     *
     * @param Blackout $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Blackout $entity)
    {

        $form = $this->createForm(new BlackoutType(), $entity , array(
            'action' => $this->generateUrl('blackout_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'form-horizontal',
                'novalidate' => 'novalidate',
            )
        ));

        $form->add('submit', 'submit', array('label' => 'Submit', 'attr' => array('class' => 'btn btn-primary')));
        return $form;
    }

    /**
     * Displays a form to create a new Blackout entity.
     *
     */
    public function newAction()
    {
        $entity = new Blackout();
        $form   = $this->createCreateForm($entity);

        return $this->render('SettingContentBundle:Blackout:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }


    /**
     * Displays a form to edit an existing Blackout entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->get('security.context')->getToken()->getUser();
        $entity = $em->getRepository('SettingContentBundle:Blackout')->findOneBy(array('id'=> $id ,'user'=>$user));

        if (!$entity) {

             return $this->redirect($this->generateUrl('blackout'));
        }

        $editForm = $this->createEditForm($entity);

        $blackOutDate =  $entity ->getBlackOutDate();
        $blackoutdate = isset( $blackOutDate ) ? $blackOutDate :'';
        $blackoutdate= explode(',' , $blackOutDate);

        $lastdate = end($blackoutdate);


        return $this->render('SettingContentBundle:Blackout:edit.html.twig', array(

            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'blackOutDate' => $blackoutdate,
            'lastdate' => $lastdate

        ));
    }

    /**
    * Creates a form to edit a Blackout entity.
    *
    * @param Blackout $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Blackout $entity)
    {

        $form = $this->createForm(new BlackoutType(), $entity, array(
            'action' => $this->generateUrl('blackout_update', array('id' => $entity->getId())),
            'method' => 'POST',
            'attr' => array(
                'class' => 'form-horizontal',
                'novalidate' => 'novalidate',
            )
        ));

        $form->add('submit', 'submit', array('label' => 'Submit', 'attr' => array('class' => 'btn btn-primary')));
        return $form;
    }
    /**
     * Edits an existing Blackout entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $this->get('security.context')->getToken()->getUser();
        $entity = $em->getRepository('SettingContentBundle:Blackout')->findOneBy(array('id'=> $id ,'user'=>$user));

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Blackout entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {

            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been updated successfully"
            );
        }

        return $this->redirect($this->generateUrl('blackout_edit', array('id' => $id)));

    }

}
