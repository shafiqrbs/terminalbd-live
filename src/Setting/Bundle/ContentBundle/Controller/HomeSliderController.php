<?php

namespace Setting\Bundle\ContentBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Setting\Bundle\ContentBundle\Entity\HomeSlider;
use Setting\Bundle\ContentBundle\Form\HomeSliderType;

/**
 * HomeSlider controller.
 *
 */
class HomeSliderController extends Controller
{

    /**
     * Lists all HomeSlider entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $globalOption = $this->getUser()->getGlobalOption();
        $entities = $em->getRepository('SettingContentBundle:HomeSlider')->findBy(array('globalOption'=>$globalOption));

        return $this->render('SettingContentBundle:HomeSlider:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new HomeSlider entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new HomeSlider();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $user = $this->getUser();
            $entity->setGlobalOption($user->getGlobalOption());
            $entity->upload();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('homeslider'));
        }

        return $this->render('SettingContentBundle:HomeSlider:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a HomeSlider entity.
     *
     * @param HomeSlider $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(HomeSlider $entity)
    {

        $form = $this->createForm(new HomeSliderType($this->getUser()->getGlobalOption()->getId()), $entity, array(
            'action' => $this->generateUrl('homeslider_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
       return $form;
    }

    /**
     * Displays a form to create a new HomeSlider entity.
     *
     */
    public function newAction()
    {
        $entity = new HomeSlider();
        $form   = $this->createCreateForm($entity);

        return $this->render('SettingContentBundle:HomeSlider:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a HomeSlider entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingContentBundle:HomeSlider')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find HomeSlider entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('SettingContentBundle:HomeSlider:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing HomeSlider entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingContentBundle:HomeSlider')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find HomeSlider entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('SettingContentBundle:HomeSlider:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }


    /**
    * Creates a form to edit a HomeSlider entity.
    *
    * @param HomeSlider $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(HomeSlider $entity)
    {

        $form = $this->createForm(new HomeSliderType($this->getUser()->getGlobalOption()->getId()), $entity, array(
            'action' => $this->generateUrl('homeslider_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }
    /**
     * Edits an existing HomeSlider entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('SettingContentBundle:HomeSlider')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find HomeSlider entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            if($entity->upload()){
                $entity->removeUpload();
            }

            $entity->upload();
            $em->flush();

            return $this->redirect($this->generateUrl('homeslider_edit', array('id' => $id)));
        }

        return $this->render('SettingContentBundle:HomeSlider:edit.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }


    /**
     * Deletes a HomeSlider entity.
     *
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $globalOption = $this->getUser()->getGlobalOption();
        $entity = $em->getRepository('SettingContentBundle:HomeSlider')->findOneBy(array('globalOption'=>$globalOption,'id'=>$id));
        if (!empty($entity)) {

            $entity->removeUpload();
            $em->remove($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Status has been deleted successfully"
            );
        }else{
            $this->get('session')->getFlashBag()->add(
                'error',"Sorry! Data not deleted"
            );
        }
        return $this->redirect($this->generateUrl('homeslider'));
    }

    /**
     * Creates a form to delete a HomeSlider entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('homeslider_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }

    /**
     * Status a news entity.
     *
     */
    public function statusAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('SettingContentBundle:HomeSlider')->find($id);

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
        return $this->redirect($this->generateUrl('homeslider'));
    }

    public function sortingAction()
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->get('security.context')->getToken()->getUser();

        $entities = $em->getRepository('SettingContentBundle:HomeSlider')->findBy(array('user'=>$user),array('sorting'=>'asc'));

        return $this->render('SettingContentBundle:HomeSlider:sorting.html.twig', array(
            'entities' => $entities,

        ));

    }

    public function sortedAction(Request $request)
    {
        $data = $request ->request->get('menuItem');
        $this->getDoctrine()->getRepository('SettingContentBundle:HomeSlider')->setMenuOrdering($data);
        exit;

    }
}
