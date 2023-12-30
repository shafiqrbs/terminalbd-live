<?php

namespace Setting\Bundle\ContentBundle\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Setting\Bundle\ContentBundle\Entity\ContactPage;
use Setting\Bundle\ContentBundle\Form\ContactPageType;

/**
 * ContactPage controller.
 *
 */
class ContactPageController extends Controller
{

    /**
     * Lists all ContactPage entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $globalOption = $this->getUser()->getGlobalOption();
        $entity = $em->getRepository('SettingContentBundle:ContactPage')->findOneBy(array('globalOption'=>$globalOption));

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ContactPage entity.');
        }

        $editForm = $this->createEditForm($entity);
        return $this->render('SettingContentBundle:ContactPage:edit.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),

        ));
    }

    /**
     * Displays a form to edit an existing ContactPage entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingContentBundle:ContactPage')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ContactPage entity.');
        }

        $editForm = $this->createEditForm($entity);

        return $this->render('SettingContentBundle:ContactPage:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing ContactPage entity.
     *
     */
    public function modifyAction()
    {
        $em = $this->getDoctrine()->getManager();

        $globalOption = $this->getUser()->getGlobalOption();
        $entity = $em->getRepository('SettingContentBundle:ContactPage')->findOneBy(array('globalOption'=>$globalOption));

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ContactPage entity.');
        }

        $editForm = $this->createEditForm($entity);
        return $this->render('SettingContentBundle:ContactPage:edit.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),

        ));
    }

    /**
    * Creates a form to edit a ContactPage entity.
    *
    * @param ContactPage $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(ContactPage $entity)
    {

        $location = $this->getDoctrine()->getRepository('SettingLocationBundle:Location');
        $form = $this->createForm(new ContactPageType($location), $entity, array(
            'action' => $this->generateUrl('contactpage_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;

    }
    /**
     * Edits an existing ContactPage entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingContentBundle:ContactPage')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ContactPage entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'success',"Data has been changed successfully"
            );
            $referer = $request->headers->get('referer');
            return new RedirectResponse($referer);
        }

        return $this->render('SettingContentBundle:ContactPage:edit.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView()

        ));
    }
}
