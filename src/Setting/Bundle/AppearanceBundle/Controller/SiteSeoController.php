<?php

namespace Setting\Bundle\AppearanceBundle\Controller;

use Doctrine\Entity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Setting\Bundle\AppearanceBundle\Entity\TemplateCustomize;
use Setting\Bundle\AppearanceBundle\Form\TemplateCustomizeType;

/**
 * TemplateCustomize controller.
 *
 */
class SiteSeoController extends Controller
{

    /**
     * Lists all TemplateCustomize entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('SettingAppearanceBundle:TemplateCustomize')->findAll();

        return $this->render('SettingAppearanceBundle:TemplateCustomize:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new TemplateCustomize entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new TemplateCustomize();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            return $this->redirect($this->generateUrl('templatecustomize_show', array('id' => $entity->getId())));
        }

        return $this->render('SettingAppearanceBundle:TemplateCustomize:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a TemplateCustomize entity.
     *
     * @param TemplateCustomize $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(TemplateCustomize $entity)
    {
        $form = $this->createForm(new TemplateCustomizeType(), $entity, array(
            'action' => $this->generateUrl('templatecustomize_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    /**
     * Displays a form to create a new TemplateCustomize entity.
     *
     */
    public function newAction()
    {
        $entity = new TemplateCustomize();
        $form   = $this->createCreateForm($entity);

        return $this->render('SettingAppearanceBundle:TemplateCustomize:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a TemplateCustomize entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingAppearanceBundle:TemplateCustomize')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find TemplateCustomize entity.');
        }

        return $this->render('SettingAppearanceBundle:TemplateCustomize:show.html.twig', array(
            'entity'      => $entity,
        ));
    }

    /**
     * Displays a form to edit an existing TemplateCustomize entity.
     *
     */
    public function editAction($id)
    {

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('SettingAppearanceBundle:TemplateCustomize')->findOneBy(array('globalOption'=>$id));
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find TemplateCustomize entity.');
        }
        $editForm = $this->createEditForm($entity);
        return $this->render('SettingAppearanceBundle:TemplateCustomize:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),

        ));
    }

    /**
    * Creates a form to edit a TemplateCustomize entity.
    *
    * @param TemplateCustomize $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(TemplateCustomize $entity)
    {
        $form = $this->createForm(new TemplateCustomizeType(), $entity, array(
            'action' => $this->generateUrl('templatecustomize_update', array('id' => $entity->getGlobalOption()->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
                'enctype' => 'multipart/form-data',
            )
        ));

        return $form;
    }
    /**
     * Edits an existing TemplateCustomize entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SettingAppearanceBundle:TemplateCustomize')->findOneBy(array('globalOption'=> $id));
        $data = $request->request->all();
        $file = $request->files->all();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find TemplateCustomize entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {

            if(isset($data['removeLogo']) || (isset($file['logo']) && !empty($entity->getLogo()))  ){
                $entity->removeLogo();
                $entity->setLogo(NULL);
            }
            if(isset($data['removeFavicon']) || (isset($file['faviconFile']) && !empty($entity->getFavicon()))  ){
                $entity->removeFavicon();
                $entity->setFavicon(NULL);
            }
            if(isset($data['removeHeaderImage']) || (isset($file['removeHeaderImage']) && !empty($entity->getHeaderBgImage())) ){
                $entity->removeHeaderImage();
                $entity->setHeaderBgImage(NULL);
            }
            if(isset($data['removeBodyImage']) || (isset($file['removeBodyImage']) && !empty($entity->getBgImage())) ){
                $entity->removeBodyImage();
                $entity->setBgImage(NULL);
            }
            $entity->upload();
            $em->flush();
            $this->getDoctrine()->getRepository('SettingAppearanceBundle:TemplateCustomize')->fileUploader($entity,$file);
            return $this->redirect($this->generateUrl('templatecustomize_edit', array('id' => $id)));
        }

        return $this->render('SettingAppearanceBundle:TemplateCustomize:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }

    public function resetAction(TemplateCustomize $entity){
        $em = $this->getDoctrine()->getManager();

        $entity->setAnchorColor(NULL);
        $entity->setAnchorHoverColor(NULL);
        $entity->setBgImage(NULL);
        $entity->setBodyColor(NULL);
        $entity->setBorderColor(NULL);
        $entity->setButtonBgColorHover(NULL);
        $entity->setFooterBgColor(NULL);
        $entity->setButtonBgColor(NULL);
        $entity->setFooterTextColor(NULL);
        $entity->setHeaderBgColor(NULL);
        $entity->setHomeAnchorColor(NULL);
        $entity->setHomeAnchorColorHover(NULL);
        $entity->setHomeBgColor(NULL);
        $entity->setMenuBgColor(NULL);
        $entity->setMenuFontSize(NULL);
        $entity->setMenuLia(NULL);
        $entity->setMenuLiAColor(NULL);
        $entity->setMenuLiAHoverColor(NULL);
        $entity->setMenuLiHovera(NULL);
        $entity->setSiteBgColor(NULL);
        $entity->setSiteNameColor(NULL);
        $entity->setSiteFontSize(NULL);
        $entity->setSiteFontFamily(NULL);
        $entity->setSiteH1TextSize(NULL);
        $entity->setSiteH2TextSize(NULL);
        $entity->setSiteH3TextSize(NULL);
        $entity->setSiteH4TextSize(NULL);
        $entity->setSiteTitleBgColor(NULL);
        $entity->setDividerAfterColor(NULL);
        $entity->setDividerBeforeColor(NULL);
        $entity->setDividerFontColor(NULL);
        $entity->setDividerFontSize(NULL);
        $entity->setSliderPosition('top-right');
        $entity->setSliderTopBottomPosition(NULL);
        $entity->setSliderLeftRightPosition(NULL);
        $em->persist($entity);
        $em->flush();
        return $this->redirect($this->generateUrl('templatecustomize_edit', array('id' => $entity->getGlobalOption()->getId())));

    }


}
