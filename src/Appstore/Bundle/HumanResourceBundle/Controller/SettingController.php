<?php


namespace Appstore\Bundle\HumanResourceBundle\Controller;

use Appstore\Bundle\HumanResourceBundle\Entity\PayrollSetting;
use Appstore\Bundle\HumanResourceBundle\Form\PayrollSettingType;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Particular controller.
 *
 */
class SettingController extends Controller
{

    /**
     * Lists all Particular entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new PayrollSetting();
        $form = $this->createCreateForm($entity);
        $config = $this->getUser()->getGlobalOption();
        $entities = $this->getDoctrine()->getRepository('HumanResourceBundle:PayrollSetting')->findBy(array('globalOption' => $config),array('mode'=>'ASC'));
        return $this->render('HumanResourceBundle:Setting:index.html.twig', array(
            'entities' => $entities,
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }
    /**
     * Creates a new Particular entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new PayrollSetting();
        $config = $this->getUser()->getGlobalOption();
       $entities = $this->getDoctrine()->getRepository('HumanResourceBundle:PayrollSetting')->findBy(array('globalOption' => $config),array('mode'=>'ASC'));
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $config = $this->getUser()->getGlobalOption();
            $entity->setGlobalOption($config);
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been inserted successfully"
            );
            return $this->redirect($this->generateUrl('payrollsetting', array('id' => $entity->getId())));
        }

        return $this->render('HumanResourceBundle:Setting:index.html.twig', array(
            'entities' => $entities,
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Particular entity.
     *
     * @param PayrollSetting $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(PayrollSetting $entity)
    {
        $form = $this->createForm(new PayrollSettingType(), $entity, array(
            'action' => $this->generateUrl('payrollsetting_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    /**
     * Displays a form to edit an existing Particular entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption();
       $entities = $this->getDoctrine()->getRepository('HumanResourceBundle:PayrollSetting')->findBy(array('globalOption' => $config),array('mode'=>'ASC'));
        $entity = $em->getRepository('HumanResourceBundle:PayrollSetting')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Particular entity.');
        }

        $editForm = $this->createEditForm($entity);
        return $this->render('HumanResourceBundle:Setting:index.html.twig', array(
            'entities'      => $entities,
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Particular entity.
    *
    * @param PayrollSetting $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(PayrollSetting $entity)
    {
        $form = $this->createForm(new PayrollSettingType(), $entity, array(
            'action' => $this->generateUrl('payrollsetting_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }
    /**
     * Edits an existing Particular entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $config = $this->getUser()->getGlobalOption();
       $entities = $this->getDoctrine()->getRepository('HumanResourceBundle:PayrollSetting')->findBy(array('globalOption' => $config),array('mode'=>'ASC'));

        $entity = $em->getRepository('HumanResourceBundle:PayrollSetting')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Particular entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been changed successfully"
            );
            return $this->redirect($this->generateUrl('payrollsetting'));
        }

        return $this->render('HumanResourceBundle:Setting:index.html.twig', array(
            'entities'      => $entities,
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }
    /**
     * Deletes a Particular entity.
     *
     */
    public function deleteAction($id)
    {

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('HumanResourceBundle:PayrollSetting')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Particular entity.');
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

        return $this->redirect($this->generateUrl('payrollsetting'));
    }


    /**
     * Status a Page entity.
     *
     */
    public function statusAction(Request $request, $id)
    {

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('HumanResourceBundle:PayrollSetting')->find($id);

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
        return $this->redirect($this->generateUrl('payrollsetting'));
    }


}
