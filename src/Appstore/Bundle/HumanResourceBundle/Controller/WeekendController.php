<?php

namespace Appstore\Bundle\HumanResourceBundle\Controller;
use Appstore\Bundle\DomainUserBundle\Form\HrBlackoutType;
use Appstore\Bundle\DomainUserBundle\Entity\HrBlackout;
use Appstore\Bundle\HumanResourceBundle\Entity\Weekend;
use Appstore\Bundle\HumanResourceBundle\Form\WeekendType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * DomainUser controller.
 *
 */
class WeekendController extends Controller
{
    /**
     * Lists all Blackout entities.
     *
     */
    public function indexAction()
    {

        $option = $this->getUser()->getGlobalOption();
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('HumanResourceBundle:Weekend')->findOneBy(array('globalOption' => $option));
        if (empty($entity)) {
            $entity = new  Weekend();
            $em = $this->getDoctrine()->getManager();
            $entity->setGlobalOption($option);
            $em->persist($entity);
            $em->flush();
        }

        $blackout ='';
        $editForm = $this->createEditForm($entity);
        $blackOutDate =  $entity ->getWeekendDate();
        $blackoutdate = isset( $blackOutDate ) ? $blackOutDate :'';
        if($blackoutdate){
            $blackoutdate = (array_map('trim',array_filter(explode(',',$blackoutdate))));
            $blackout=implode("','",$blackoutdate);
            $blackout="'".$blackout."'";
        }
        return $this->render('HumanResourceBundle:Weekend:index.html.twig', array(
            'entity'        => $entity,
            'form'          => $editForm->createView(),
            'blackout'      => $blackout,
        ));


    }

    /**
     * Creates a form to edit a Blackout entity.
     *
     * @param Weekend $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Weekend $entity)
    {

        $form = $this->createForm(new WeekendType(), $entity, array(
            'action' => $this->generateUrl('weekend_update', array('id' => $entity->getId())),
            'method' => 'POST',
            'attr' => array(
                'class' => 'form-horizontal',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }
    /**
     * Edits an existing Blackout entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('HumanResourceBundle:Weekend')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Weekend entity.');
        }
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);
        if ($editForm->isValid()) {
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been updated successfully"
            );
        }
        return $this->redirect($this->generateUrl('weekend'));

    }

}
