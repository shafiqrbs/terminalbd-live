<?php

namespace Setting\Bundle\ToolBundle\Controller;

use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Setting\Bundle\ToolBundle\Entity\SmsBulk;
use Setting\Bundle\ToolBundle\Form\SmsBulkType;

/**
 * SmsBulk controller.
 *
 */
class SmsBulkController extends Controller
{

    /**
     * Lists all SmsBulk entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('SettingToolBundle:SmsBulk')->findAll();

        return $this->render('SettingToolBundle:SmsBulk:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new SmsBulk entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new SmsBulk();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->setGlobalOption($this->getUser()->getGlobalOption());
            $entity->upload();
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been inserted successfully"
            );
            return $this->redirect($this->generateUrl('smsbulk_show', array('id' => $entity->getId())));
        }

        return $this->render('SettingToolBundle:SmsBulk:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    public function createSmsBulkFolder($folderName)
    {
        $mobile_dir = __DIR__.'/../../../../../src/Frontend/FrontendBundle/Resources/views/Template/Desktop/';
        $desktop_dir = __DIR__.'/../../../../../src/Frontend/FrontendBundle/Resources/views/Template/Mobile/';
        if(!file_exists($mobile_dir)){
                mkdir($mobile_dir.'/'.$folderName, 0777);
                mkdir($desktop_dir.'/'.$folderName, 0777);
            }else{
                return false;

        }


    }

    /**
     * Creates a form to create a SmsBulk entity.
     *
     * @param SmsBulk $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(SmsBulk $entity)
    {

        $location = $this->getDoctrine()->getRepository('SettingLocationBundle:Location');
        $form = $this->createForm(new SmsBulkType($location), $entity, array(
            'action' => $this->generateUrl('smsbulk_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    /**
     * Displays a form to create a new SmsBulk entity.
     *
     */
    public function newAction()
    {
        $entity = new SmsBulk();
        $form   = $this->createCreateForm($entity);

        return $this->render('SettingToolBundle:SmsBulk:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a SmsBulk entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingToolBundle:SmsBulk')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find SmsBulk entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('SettingToolBundle:SmsBulk:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing SmsBulk entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingToolBundle:SmsBulk')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find SmsBulk entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('SettingToolBundle:SmsBulk:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a SmsBulk entity.
    *
    * @param SmsBulk $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(SmsBulk $entity)
    {
        $location = $this->getDoctrine()->getRepository('SettingLocationBundle:Location');
        $form = $this->createForm(new SmsBulkType($location), $entity, array(
            'action' => $this->generateUrl('smsbulk_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
       return $form;
    }
    /**
     * Edits an existing SmsBulk entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingToolBundle:SmsBulk')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find SmsBulk entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {

            $entity->upload();
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been updated successfully"
            );
            return $this->redirect($this->generateUrl('smsbulk_edit', array('id' => $id)));
        }

        return $this->render('SettingToolBundle:SmsBulk:new.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a SmsBulk entity.
     *
     */
    public function deleteAction(SmsBulk $entity)
    {
            $em = $this->getDoctrine()->getManager();
            if (!$entity) {
                throw $this->createNotFoundException('Unable to find SmsBulk entity.');
            }
            $entity->removeUpload();
            $em->remove($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'error',"Data has been deleted successfully"
            );
            return $this->redirect($this->generateUrl('smsbulk'));
    }

    /**
     * Creates a form to delete a SmsBulk entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('smsbulk_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }

    /**
     * Status a Page entity.
     *
     */
    public function statusAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        //$data = $request->request->all();


        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('SettingToolBundle:SmsBulk')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find District entity.');
        }

        $status = $entity->getStatus();
        if($status == 1){
            $entity->setStatus(false);
        } else{
            $entity->setStatus(true);
        }
        $em->flush();
        $this->get('session')->getFlashBag()->add(
            'success',"Status has been changed successfully"
        );
        return $this->redirect($this->generateUrl('smsbulk'));
    }

    public function sendingAction(SmsBulk $entity)
    {

        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        /* @var $global GlobalOption */
        $global = $this->getUser()->getGlobalOption();
        if ($global->getSmsSenderTotal() and $global->getSmsSenderTotal()->getRemaining() > 0 and $global->getNotificationConfig()->getSmsActive() == 1) {
            if ($entity->getProcess() != "Complete") {
                $dispatcher = $this->container->get('event_dispatcher');
                $dispatcher->dispatch('setting_tool.post.bulk_sms', new \Setting\Bundle\ToolBundle\Event\SmsBulkEvent($entity));
                $entity->setProcess('Complete');
                $em->flush();
            }
        }
        return $this->redirect($this->generateUrl('smsbulk_show',array('id'=>$entity->getId())));
    }

}
