<?php

namespace Setting\Bundle\MediaBundle\Controller;

use Setting\Bundle\MediaBundle\Entity\PhotoGallery;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Setting\Bundle\MediaBundle\Entity\GalleryImage;
use Setting\Bundle\MediaBundle\Form\GalleryImageType;


/**
 * GalleryImage controller.
 *
 */
class GalleryImageController extends Controller
{

    /**
     * Lists all GalleryImage entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('SettingMediaBundle:GalleryImage')->findAll();

        return $this->render('SettingMediaBundle:GalleryImage:index.html.twig', array(
            'entities' => $entities,
        ));
    }

    public function uploadAction($id)
    {
        $globalOption = $this->getUser()->getGlobalOption()->getId();
        $entity = new GalleryImage();
        $entity ->upload($globalOption,$id);
    }

    /**
     * Creates a new GalleryImage entity.
     *
     */
    public function createAction(Request $request)
    {

        $data = $request->request->all();
        $galleryId = $request->request->get('galleryId');

        $this->getDoctrine()->getRepository('SettingMediaBundle:GalleryImage')->insertGalleryImage($data,$galleryId);
        $this->get('session')->getFlashBag()->add(
            'success',"Data has been inserted successfully"
        );
        return $this->redirect($this->generateUrl('galleryimage_edit', array('id' => $galleryId)));

    }

    /**
     * Creates a form to create a GalleryImage entity.
     *
     * @param GalleryImage $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(GalleryImage $entity)
    {

        $form = $this->createForm(new GalleryImageType(), $entity, array(
            'action' => $this->generateUrl('galleryimage_create', array('id' => $entity->getId())),
            'method' => 'POST',
            'attr' => array(
                'class' => 'form-horizontal',
                'novalidate' => 'novalidate',
            )
        ));

        return $form;

    }

    /**
     * Displays a form to create a new GalleryImage entity.
     *
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $galleryImageId =  $this->get('request')->get('galleryId');

        $entity = new GalleryImage();
        $form   = $this->createCreateForm($entity);
        $globalOption = $this->getUser()->getGlobalOption();
        $entity = $em->getRepository('SettingMediaBundle:PhotoGallery')->findOneBy(array('id'=>$galleryImageId,'globalOption'=>$globalOption));

        if(null === $galleryImageId || empty($entity) ) {

            return $this->redirect($this->generateUrl('gallery'));

        }else{

            return $this->render('SettingMediaBundle:GalleryImage:new.html.twig', array(
                'entity' => $entity,
                'form'   => $form->createView(),
            ));
        }

    }

    /**
     * Displays a form to edit an existing GalleryImage entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();


        $globalOption = $this->getUser()->getGlobalOption();

        $entityGallery = $em->getRepository('SettingMediaBundle:PhotoGallery')->findOneBy(array('id'=>$id ,'globalOption' => $globalOption));
        if(empty($entityGallery) ) {

            return $this->redirect($this->generateUrl('gallery'));
        }

        $entity = $em->getRepository('SettingMediaBundle:GalleryImage')->findBy(array('photoGallery'=>$id));

        if (!$entity) {

            return $this->redirect($this->generateUrl('galleryimage_new', array('galleryId' => $id)));

        }

        $editForm = $this->createEditForm($id);
        return $this->render('SettingMediaBundle:GalleryImage:edit.html.twig', array(
            'entitities'      => $entity,
            'gallery'      => $entityGallery,
            'edit_form'   => $editForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a GalleryImage entity.
     *
     * @param GalleryImage $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm($id)
    {
        $form = $this->createForm(new GalleryImageType(), null, array(
            'action' => $this->generateUrl('galleryimage_update', array('id' => $id)),
            'method' => 'POST',
            'attr' => array(
                'class' => 'form-horizontal',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;

    }
    /**
     * Edits an existing GalleryImage entity.
     *
     */
    public function updateAction(Request $request, $id)
    {

        $data = $request->request->all();
        $this->getDoctrine()->getRepository('SettingMediaBundle:GalleryImage')->updateGalleryImage($data);

        $this->get('session')->getFlashBag()->add(
            'success',"Data has been updated successfully"
        );
        return $this->redirect($this->generateUrl('galleryimage_edit', array('id' => $id)));

    }

    public function deleteImageAction(GalleryImage $entity)
    {

        $em = $this->getDoctrine()->getManager();
        if(!empty($entity)){
            $entity->removeUpload($entity->getPhotoGallery()->getGlobalOption()->getId(),$entity->getPhotoGallery()->getId());
            $em->remove($entity);
            $em->flush();
        }
        exit;

    }


}
