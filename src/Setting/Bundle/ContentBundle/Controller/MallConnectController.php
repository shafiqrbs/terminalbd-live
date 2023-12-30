<?php

namespace Setting\Bundle\ContentBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Setting\Bundle\ContentBundle\Entity\MallConnect;
use Setting\Bundle\ContentBundle\Form\MallConnectType;

/**
 * MallConnect controller.
 *
 */
class MallConnectController extends Controller
{

    /**
     * Lists all MallConnect entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $globalOption = $this->getUser()->getGlobalOption();
        $entities = $em->getRepository('SettingContentBundle:MallConnect')->findBy(array('globalOption'=>$globalOption));
        return $this->render('SettingContentBundle:MallConnect:index.html.twig', array(
            'entities' => $entities,
        ));


    }

    /**
     * Creates a new MallConnect entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new MallConnect();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if($entity->getShopNo())

        if ($form->isValid() and $this->checkDuplicateEntry($entity) == true) {

                $em = $this->getDoctrine()->getManager();
                $globalOption = $this->getUser()->getGlobalOption();
                $entity->setGlobalOption($globalOption);
                $entity->setName($globalOption->getName());
                $em->persist($entity);
                $em->flush();
                return $this->redirect($this->generateUrl('mallconnect'));
        }
        $this->get('session')->getFlashBag()->add(
            'error',"This data is already in use"
        );
        return $this->render('SettingContentBundle:MallConnect:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    public function checkDuplicateEntry(MallConnect $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $true = $em->getRepository('SettingContentBundle:MallConnect')->findBy(array('shopNo'=>$entity->getShopNo(),'mall'=>$entity->getMall()));
        if(!empty($true))
        {
            return false;
        }
        return true;
    }

    /**
     * Creates a form to create a MallConnect entity.
     *
     * @param MallConnect $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(MallConnect $entity)
    {
        $form = $this->createForm(new MallConnectType(), $entity, array(
            'action' => $this->generateUrl('mallconnect_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    /**
     * Displays a form to create a new MallConnect entity.
     *
     */
    public function newAction()
    {
        $entity = new MallConnect();
        $form   = $this->createCreateForm($entity);

        return $this->render('SettingContentBundle:MallConnect:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a MallConnect entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingContentBundle:MallConnect')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find MallConnect entity.');
        }

        return $this->render('SettingContentBundle:MallConnect:show.html.twig', array(
            'entity'      => $entity,
        ));
    }

    /**
     * Displays a form to edit an existing MallConnect entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingContentBundle:MallConnect')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find MallConnect entity.');
        }

        $editForm = $this->createEditForm($entity);

        return $this->render('SettingContentBundle:MallConnect:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a MallConnect entity.
    *
    * @param MallConnect $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(MallConnect $entity)
    {
        $form = $this->createForm(new MallConnectType(), $entity, array(
            'action' => $this->generateUrl('mallconnect_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
         return $form;
    }
    /**
     * Edits an existing MallConnect entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingContentBundle:MallConnect')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find MallConnect entity.');
        }
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('mallconnect_edit', array('id' => $id)));
        }

        return $this->render('SettingContentBundle:MallConnect:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }
    /**
     * Deletes a MallConnect entity.
     *
     */
    public function deleteAction(MallConnect $entity)
    {

            $em = $this->getDoctrine()->getManager();
            if (!$entity) {
                throw $this->createNotFoundException('Unable to find MallConnect entity.');
            }
            $em->remove($entity);
            $em->flush();
            return $this->redirect($this->generateUrl('mallconnect'));
    }

    /**
     * Status a news entity.
     *
     */
    public function statusAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('SettingContentBundle:MallConnect')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find District entity.');
        }

        $status = $entity->getStatus();
        if($status == 1){
            $entity->setStatus(0);
        } else{
            $entity->setStatus(1);
        }
        $em->flush();
        $this->get('session')->getFlashBag()->add(
            'error',"Status has been changed successfully"
        );
        return $this->redirect($this->generateUrl('mallconnect'));
    }

}
