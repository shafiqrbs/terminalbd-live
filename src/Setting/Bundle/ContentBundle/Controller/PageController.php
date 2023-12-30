<?php

namespace Setting\Bundle\ContentBundle\Controller;

use Knp\Snappy\Pdf;
use Setting\Bundle\ContentBundle\Entity\PageMeta;
use Setting\Bundle\ContentBundle\Entity\PageModule;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Setting\Bundle\ContentBundle\Entity\Page;
use Setting\Bundle\ContentBundle\Form\PageType;
use Symfony\Component\HttpFoundation\Response;

/**
 * Page controller.
 *
 */
class PageController extends Controller
{

    public function paginate($entities)
    {

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $this->get('request')->query->get('page', 1)/*page number*/,
            20  /*limit per page*/
        );
        return $pagination;
    }


    /**
     * Lists Spacific user  Page entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('SettingContentBundle:Page')->getPagesFor($this->getUser()->getGlobalOption(),'page');
        $entities = $this->paginate($entities);

        return $this->render('SettingContentBundle:Page:index.html.twig', array(
            'pagination' => $entities,
        ));
    }

    /**
     * Creates a new Page entity.
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */

    public function createAction(Request $request)
    {
        $entity = new Page();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        $data = $request->request->all();
        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $entity->setUser($this->getUser());
            $entity->setGlobalOption($this->getUser()->getGlobalOption());
            $entity ->setModule($this->getDoctrine()->getRepository('SettingToolBundle:Module')->findOneBy(array('slug' => 'page')));
            $entity ->upload();
            $em->persist($entity);
            $em->flush();
            $this->getDoctrine()->getRepository('SettingContentBundle:PageMeta')->pageMeta($entity,$data);
            $this->getDoctrine()->getRepository('SettingAppearanceBundle:Menu')->createMenuForPage($entity);

            $this->get('session')->getFlashBag()->add(
                'success',"Data has been inserted successfully"
            );
            return $this->redirect($this->generateUrl('page'));

        }

        return $this->render('SettingContentBundle:Page:new.html.twig', array(
            'entity' => $entity,
            'globalOption' => $this->getUser()->getGlobalOption(),
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

        $form = $this->createForm(new PageType($this->getUser()->getGlobalOption()), $entity, array(
            'action' => $this->generateUrl('page_create', array('id' => $entity->getId())),
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

        return $this->render('SettingContentBundle:Page:new.html.twig', array(
            'entity' => $entity,
            'globalOption' => $this->getUser()->getGlobalOption(),
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Page entity.
     *
     */
    public function showAction(Page $entity)
    {
        return $this->render('SettingContentBundle:Page:show.html.twig', array(
            'entity'      => $entity

        ));
    }

    /**
     * Displays a form to edit an existing Page entity.
     *
     */
    public function editAction(Page $entity)
    {
        $editForm = $this->createEditForm($entity);

        /** @var  $pageArr */
        $pageArr = array();

        /** @var PageModule $row */
         foreach ($entity->getPageModules() as $row):
             $pageArr[$row->getModule()->getId()] =  $row;
         endforeach;


        return $this->render('SettingContentBundle:Page:new.html.twig', array(
            'entity'      => $entity,
            'globalOption' => $this->getUser()->getGlobalOption(),
            'form'   => $editForm->createView(),
            'pageFeature' => $pageArr
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

        $form = $this->createForm(new PageType($entity->getUser()->getGlobalOption()), $entity, array(
            'action' => $this->generateUrl('page_update', array('id' => $entity->getId())),
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
    public function updateAction(Request $request, Page $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);
        $data = $request->request->all();
        if ($editForm->isValid()) {

            if($entity->isRemoveImage() == 1 ){
                $entity->removeUpload();
            }elseif($entity->isRemoveImage() != 1) {
                $entity->upload();
            }
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been updated successfully"
            );
            $this->getDoctrine()->getRepository('SettingContentBundle:PageMeta')->pageMeta($entity,$data);
            $this->getDoctrine()->getRepository('SettingContentBundle:PageModule')->createFeatureModule($entity,$data);
            $this->getDoctrine()->getRepository('SettingContentBundle:Page')->updatePageMenu($entity);

            return $this->redirect($this->generateUrl('page_edit', array('id' => $entity->getId())));
        }

        return $this->render('SettingContentBundle:Page:new.html.twig', array(
            'entity'      => $entity,
            'globalOption' => $this->getUser()->getGlobalOption(),
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
        $this->get('session')->getFlashBag()->add('success',"Data has been updated successfully");
        return $this->redirect($this->generateUrl('page'));
}

    /**
     * Status a Page entity.
     *
     */
    public function statusAction(Request $request, Page $entity)
    {

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
        return $this->redirect($this->generateUrl('page'));
    }

    public function printDetailsAction(Request $request, Page $page)
    {

        return $this->render('SettingContentBundle:Page:print.html.twig', array(
            'entity'      => $page

        ));

    }
    public function pdfDetailsAction(Request $request, Page $page)
    {

        $html = $this->renderView(
            'SettingContentBundle:Page:print.html.twig', array(
                'entity' => $page
            )
        );


        $wkhtmltopdfPath = '/usr/local/bin/wkhtmltopdf'; /* check mac pdf */
        //$wkhtmltopdfPath = 'xvfb-run --server-args="-screen 0, 1280x1024x24" /usr/bin/wkhtmltopdf --use-xserver'; /* check server pdf */
        $snappy          = new Pdf($wkhtmltopdfPath);
        $pdf             = $snappy->getOutputFromHtml($html);

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="education.pdf"');
        echo $pdf;

        return new Response('');

    }

    public function metaDeleteAction(PageMeta $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($entity);
        $em->flush();
        return new Response('success');

    }

}
