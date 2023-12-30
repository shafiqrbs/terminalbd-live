<?php

namespace Setting\Bundle\AppearanceBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Setting\Bundle\AppearanceBundle\Entity\MegaMenu;
use Setting\Bundle\AppearanceBundle\Form\MegaMenuType;

/**
 * MegaMenu controller.
 *
 */
class MegaMenuController extends Controller
{

    /**
     * Lists all MegaMenu entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('SettingAppearanceBundle:MegaMenu')->findBy(array(),array('sorting' => 'asc'));
        return $this->render('SettingAppearanceBundle:MegaMenu:index.html.twig', array(
            'entities' => $entities,
        ));
    }


    /**
     * Creates a new MegaMenu entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new MegaMenu();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'success',"Data has been inserted successfully"
            );

            return $this->redirect($this->generateUrl('megamenu_show', array('id' => $entity->getId())));
        }

        return $this->render('SettingAppearanceBundle:MegaMenu:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a MegaMenu entity.
     *
     * @param MegaMenu $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(MegaMenu $entity)
    {

        $em = $this->getDoctrine()->getRepository('ProductProductBundle:Category');
        $cr = $this->getDoctrine()->getRepository('ProductProductBundle:Collection');
        $sr = $this->getDoctrine()->getRepository('SettingToolBundle:Syndicate');
        $go = $this->getDoctrine()->getRepository('SettingToolBundle:GlobalOption');

        $form = $this->createForm(new MegaMenuType($em, $cr , $sr, $go), $entity, array(
            'action' => $this->generateUrl('megamenu_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    /**
     * Displays a form to create a new MegaMenu entity.
     *
     */
    public function ecommerceMenuAction()
    {
        $entity = new MegaMenu();
        $form   = $this->createCreateForm($entity);
        return $this->render('SettingAppearanceBundle:MegaMenu:ecommerce.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }


    /**
     * Displays a form to create a new MegaMenu entity.
     *
     */
    public function newAction()
    {
        $entity = new MegaMenu();
        $form   = $this->createCreateForm($entity);
        return $this->render('SettingAppearanceBundle:MegaMenu:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a MegaMenu entity.
     *
     */

    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingAppearanceBundle:MegaMenu')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find MegaMenu entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('SettingAppearanceBundle:MegaMenu:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing MegaMenu entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingAppearanceBundle:MegaMenu')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find MegaMenu entity.');
        }

        $editForm = $this->createEditForm($entity);

        return $this->render('SettingAppearanceBundle:MegaMenu:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a MegaMenu entity.
     *
     * @param MegaMenu $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(MegaMenu $entity)
    {
        $em = $this->getDoctrine()->getRepository('ProductProductBundle:Category');
        $cr = $this->getDoctrine()->getRepository('ProductProductBundle:Collection');
        $sr = $this->getDoctrine()->getRepository('SettingToolBundle:Syndicate');
        $form = $this->createForm(new MegaMenuType($em, $cr,$sr), $entity, array(
            'action' => $this->generateUrl('megamenu_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));


        return $form;
    }
    /**
     * Edits an existing MegaMenu entity.
     *
     */
    public function updateAction(Request $request, MegaMenu $entity)
    {
        $em = $this->getDoctrine()->getManager();



        $deleteForm = $this->createDeleteForm($entity->getId());
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'success',"Data has been updated successfully"
            );


            return $this->redirect($this->generateUrl('megamenu_edit', array('id' => $entity->getId())));
        }

        return $this->render('SettingAppearanceBundle:MegaMenu:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a MegaMenu entity.
     *
     */
    public function deleteAction(MegaMenu $megamenu )
    {

            $em = $this->getDoctrine()->getManager();
            if (!$megamenu) {
                throw $this->createNotFoundException('Unable to find MegaMenu entity.');
            }

            $em->remove($megamenu);
            $em->flush();
            return $this->redirect($this->generateUrl('megamenu'));
    }

    /**
     * Creates a form to delete a Admission entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('megamenu_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
            ;
    }




    public function sortedAction(Request $request){

        $data = $request->request->get('menuItem');
        $this->getDoctrine()->getRepository('SettingAppearanceBundle:MegaMenu')->setOrdering($data);
        exit;

    }

    /**
     * Status a Page entity.
     *
     */
    public function statusAction(Request $request, Page $entity)
    {
        $form = $this->createDeleteForm($entity->getId());
        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();
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
        return $this->redirect($this->generateUrl('megamenu'));
    }


}
