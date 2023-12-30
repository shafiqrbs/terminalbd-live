<?php

namespace Setting\Bundle\MediaBundle\Controller;

use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Setting\Bundle\MediaBundle\Entity\PhotoGallery;
use Setting\Bundle\MediaBundle\Form\PhotoGalleryType;

/**
 * PhotoGallery controller.
 *
 */
class PhotoGalleryController extends Controller
{

    /**
     * Lists all PhotoGallery entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $globalOption = $this->getUser()->getGlobalOption();
        $entities = $em->getRepository('SettingMediaBundle:PhotoGallery')->findBy(array('globalOption' => $globalOption),array('created'=>'desc'));

        $pagination = $this->paginate($entities);

        return $this->render('SettingMediaBundle:PhotoGallery:index.html.twig', array(
            'pagination' => $pagination
        ));

    }


    public function galleriesDeleteList()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('SettingMediaBundle:PhotoGallery')->findAll();

        $pagination = $this->paginate($entities);

        return $this->render('SettingMediaBundle:PhotoGallery:index.html.twig', array(
            'pagination' => $pagination
        ));

    }



    public function paginate($entities)
    {

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $this->get('request')->query->get('page', 1)/*page number*/,
            30  /*limit per page*/
        );
        return $pagination;
    }

    /**
     * Lists all PhotoGallery entities.
     *
     */
    public function galleryAction()
    {
        $em = $this->getDoctrine()->getManager();

        $globalOption = $this->getUser()->getGlobalOption()->getId();
        $entities = $em->getRepository('SettingMediaBundle:PhotoGallery')->findBy(array('globalOption' => $globalOption ),array('created'=>'desc'));

        return $this->render('SettingMediaBundle:PhotoGallery:index.html.twig', array(
            'pagination' => $entities,
        ));
    }

    /**
     * Lists all PhotoGallery entities.
     *
     */
    public function galleryDeleteAction()
    {
        $em = $this->getDoctrine()->getManager();

        $globalOption = $this->getUser()->getGlobalOption();
        $entities = $em->getRepository('SettingMediaBundle:PhotoGallery')->findBy(array('globalOption' => $globalOption ),array('created'=>'desc'));
        return $this->render('SettingMediaBundle:PhotoGallery:index.html.twig', array(
            'pagination' => $entities,
        ));
    }


    /**
     * Creates a new PhotoGallery entity.
     *
     */
    public function createAction(Request $request)
    {

        $entity = new PhotoGallery();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        $globalOption = $this->getUser()->getGlobalOption();

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $entity->setGlobalOption($globalOption);
            $entity->upload();
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been inserted successfully"
            );
            $this->createDirectory($entity->getId());
            return $this->redirect($this->generateUrl('gallery'));
        }

        return $this->render('SettingMediaBundle:PhotoGallery:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    public function createDirectory($gallery, $dir = null)
    {
        $globalOption = $this->getUser()->getGlobalOption()->getId();
        $assets_dir = __DIR__.'/../../../../../web/uploads/domain/';
        if(file_exists($assets_dir.$globalOption)){
            mkdir($assets_dir.$globalOption.'/media/'.$gallery, 0777);
        }else{
            return false;
        }
    }


    /**
     * Creates a form to create a PhotoGallery entity.
     *
     * @param PhotoGallery $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(PhotoGallery $entity)
    {

        $form = $this->createForm(new PhotoGalleryType($this->getUser()->getGlobalOption()), $entity, array(
            'action' => $this->generateUrl('gallery_create', array('id' => $entity->getId())),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));

        return $form;
    }

    /**
     * Displays a form to create a new PhotoGallery entity.
     *
     */
    public function newAction()
    {
        $entity = new PhotoGallery();
        $form   = $this->createCreateForm($entity);

        return $this->render('SettingMediaBundle:PhotoGallery:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a PhotoGallery entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingMediaBundle:PhotoGallery')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PhotoGallery entity.');
        }
        return $this->render('SettingMediaBundle:PhotoGallery:show.html.twig', array(
            'entity'      => $entity,

        ));
    }

    /**
     * Displays a form to edit an existing PhotoGallery entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingMediaBundle:PhotoGallery')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PhotoGallery entity.');
        }

        $editForm = $this->createEditForm($entity);

        return $this->render('SettingMediaBundle:PhotoGallery:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),

        ));
    }

    /**
    * Creates a form to edit a PhotoGallery entity.
    *
    * @param PhotoGallery $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(PhotoGallery $entity)
    {

        $form = $this->createForm(new PhotoGalleryType($this->getUser()->getGlobalOption()), $entity, array(
            'action' => $this->generateUrl('gallery_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;

    }
    /**
     * Edits an existing PhotoGallery entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingMediaBundle:PhotoGallery')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PhotoGallery entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $entity->upload();
            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'success',"Data has been updated successfully"
            );
            return $this->redirect($this->generateUrl('gallery_edit', array('id' => $id)));
        }

        return $this->render('SettingMediaBundle:PhotoGallery:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),

        ));
    }
    /**
     * Deletes a PhotoGallery entity.
     *
     */
    public function deleteAction(PhotoGallery $entity)
    {

	    $em = $this->getDoctrine()->getManager();
	    if (!$entity) {
		    throw $this->createNotFoundException('Unable to find Product entity.');
	    }

	    try {
		    $entity->deleteImageDirectory();
		    $em->remove($entity);
		    $em->flush();
		    $this->get('session')->getFlashBag()->add(
			    'error',"Data has been deleted successfully"
		    );

	    } catch (ForeignKeyConstraintViolationException $e) {
		    $this->get('session')->getFlashBag()->add(
			    'notice',"Data has been relation another Table"
		    );
	    }
	    return $this->redirect($this->generateUrl('gallery'));
    }




    /**
     * Status a PhotoGallery entity.
     *
     */
    public function statusAction(Request $request, $id)
    {

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('SettingMediaBundle:PhotoGallery')->find($id);

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
        return $this->redirect($this->generateUrl('gallery'));
    }


}
