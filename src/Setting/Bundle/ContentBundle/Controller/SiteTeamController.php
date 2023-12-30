<?php

namespace Setting\Bundle\ContentBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Setting\Bundle\ContentBundle\Entity\SiteTeam;
use Setting\Bundle\ContentBundle\Form\SiteTeamType;

/**
 * SiteTeam controller.
 *
 */
class SiteTeamController extends Controller
{

    /**
     * Lists all SiteTeam entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('SettingContentBundle:SiteTeam')->findAll();

        return $this->render('SettingContentBundle:SiteTeam:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new SiteTeam entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new SiteTeam();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $slug = $this->get('setting.menuSettingRepo')-> urlSlug($entity->getName());
            $entity ->setSlug($slug);
            $entity ->upload();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('siteteam'));
        }

        return $this->render('SettingContentBundle:SiteTeam:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a SiteTeam entity.
     *
     * @param SiteTeam $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(SiteTeam $entity)
    {
	    $form = $this->createForm(new SiteTeamType(), $entity, array(
            'action' => $this->generateUrl('siteteam_create', array('id' => $entity->getId())),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
	    return $form;
    }

    /**
     * Displays a form to create a new SiteTeam entity.
     *
     */
    public function newAction()
    {
        $entity = new SiteTeam();
        $form   = $this->createCreateForm($entity);

        return $this->render('SettingContentBundle:SiteTeam:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a SiteTeam entity.
     *
     */
    public function showAction($id)
    {
    }

    /**
     * Displays a form to edit an existing SiteTeam entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingContentBundle:SiteTeam')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find SiteTeam entity.');
        }

        $editForm = $this->createEditForm($entity);
        return $this->render('SettingContentBundle:SiteTeam:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a SiteTeam entity.
    *
    * @param SiteTeam $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(SiteTeam $entity)
    {
        $form = $this->createForm(new SiteTeamType(), $entity, array(
            'action' => $this->generateUrl('siteteam_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }
    /**
     * Edits an existing SiteTeam entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingContentBundle:SiteTeam')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find SiteTeam entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {

            $slug = $this->get('setting.menuSettingRepo')->urlSlug($entity->getName());
            $entity ->setSlug($slug);
            $entity->upload();
            $em->flush();

            return $this->redirect($this->generateUrl('siteteam'));
        }

        return $this->render('SettingContentBundle:SiteTeam:new.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a SiteTeam entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('SettingContentBundle:SiteTeam')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find SiteTeam entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('siteteam'));
    }

    /**
     * Creates a form to delete a SiteTeam entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('siteteam_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
