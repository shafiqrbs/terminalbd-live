<?php

namespace Setting\Bundle\ContentBundle\Controller;

use Setting\Bundle\ContentBundle\Entity\Page;
use Setting\Bundle\ContentBundle\Form\ProductType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Setting\Bundle\ContentBundle\Form\ServiceType;

/**
 * Page controller.
 *
 */
class ProductController extends Controller
{

    /**
     * Lists all Page entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $globalOption = $this->getUser()->getGlobalOption();
        $entities = $em->getRepository('SettingContentBundle:Page')->getPagesFor($globalOption,'products');
        return $this->render('SettingContentBundle:Product:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new Page entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Page();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        $data = $request->request->all();
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $user = $this->getUser();
            $entity->setGlobalOption($user->getGlobalOption());
            $entity ->setModule($this->getDoctrine()->getRepository('SettingToolBundle:Module')->findOneBy(array('slug' => 'products')));
            $entity->upload();
            $em->persist($entity);
            $em->flush();
            $this->getDoctrine()->getRepository('SettingContentBundle:PageMeta')->pageMeta($entity,$data);
            return $this->redirect($this->generateUrl('product_new'));
        }

        return $this->render('SettingContentBundle:Product:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Page entity.
     *
     * @param Page $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Page $entity)
    {

        $form = $this->createForm(new ProductType($this->getUser()->getGlobalOption()), $entity, array(
            'action' => $this->generateUrl('product_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    /**
     * Displays a form to create a new Page entity.
     *
     */
    public function newAction()
    {
        $entity = new Page();
        $form   = $this->createCreateForm($entity);

        return $this->render('SettingContentBundle:Product:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Page entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingContentBundle:Service')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Service entity.');
        }
        return $this->render('SettingContentBundle:Product:show.html.twig', array(
            'entity'      => $entity,
        ));
    }

    /**
     * Displays a form to edit an existing Page entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingContentBundle:Page')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Service entity.');
        }

        $editForm = $this->createEditForm($entity);

        return $this->render('SettingContentBundle:Product:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }


    /**
     * Creates a form to edit a Page entity.
     *
     * @param Page $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Page $entity)
    {

        $form = $this->createForm(new ProductType($this->getUser()->getGlobalOption()), $entity, array(
            'action' => $this->generateUrl('product_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }
    /**
     * Edits an existing Page entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('SettingContentBundle:Page')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Service entity.');
        }
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $data = $request->request->all();
            if( $entity->upload() and !empty($entity->getFile())){
                $entity->removeUpload();
            }
            $entity->upload();
            $em->flush();
            $this->getDoctrine()->getRepository('SettingContentBundle:PageMeta')->pageMeta($entity,$data);
            return $this->redirect($this->generateUrl('product_edit', array('id' => $id)));
        }

        return $this->render('SettingContentBundle:Product:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }


    /**
     * Deletes a Page entity.
     *
     */
    public function deleteAction(Page $entity)
    {
        $em = $this->getDoctrine()->getManager();
        if(!empty($entity->getFile())){
            $entity->removeUpload();
        }
        $em->remove($entity);
        $em->flush();
        $this->get('session')->getFlashBag()->add('success',"Data has been deleted successfully");
        return $this->redirect($this->generateUrl('product'));
    }



    /**
     * Status a news entity.
     *
     */
    public function statusAction($id)
    {

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('SettingContentBundle:Page')->find($id);

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
        return $this->redirect($this->generateUrl('product'));
    }


    public function copyAction(Page $page)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = New Page();

        $entity->setGlobalOption($page->getGlobalOption());
        $entity->setName($page->getName());
        $entity->setModule($page->getModule());
        $entity->setModuleCategory($page->getModuleCategory());
        $entity->setContent($page->getContent());
        $entity->setSkuCode($page->getSkuCode());
        $entity->setPrice($page->getPrice());
        $entity->setPhotoGallery($page->getPhotoGallery());
        $em->persist($entity);
        $em->flush();
        $this->getDoctrine()->getRepository('SettingContentBundle:PageMeta')->insertMetaKeyValue($entity,$page);
        $this->get('session')->getFlashBag()->add(
            'success',"Data has been added successfully"
        );
        return $this->redirect($this->generateUrl('product_edit',array('id'=>$entity->getId())));
    }


}
