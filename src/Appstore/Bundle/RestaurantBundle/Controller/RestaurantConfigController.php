<?php

namespace Appstore\Bundle\RestaurantBundle\Controller;

use Appstore\Bundle\RestaurantBundle\Entity\RestaurantConfig;
use Appstore\Bundle\RestaurantBundle\Form\ConfigType;
use Frontend\FrontentBundle\Service\MobileDetect;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\SecurityExtraBundle\Annotation\RunAs;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * HospitalConfigController.
 *
 */
class RestaurantConfigController extends Controller
{

    /**
     * Lists all Particular entities.
     * @Secure(roles="ROLE_DOMAIN_RESTAURANT_MANAGER,ROLE_DOMAIN")
     */

    public function manageAction()
    {

        $entity = $this->getUser()->getGlobalOption()->getRestaurantConfig();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Invoice entity preparation.');
        }
        $form = $this->createEditForm($entity);
        return $this->render('RestaurantBundle:Config:manage.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
        ));
    }

    /**
     * Creates a form to edit a Invoice entity.wq
     *
     * @param Invoice $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(RestaurantConfig $entity)
    {

        $config = $this->getUser()->getGlobalOption()->getRestaurantConfig();
        $form = $this->createForm(new ConfigType($config), $entity, array(
            'action' => $this->generateUrl('restaurant_config_update'),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    /**
     * Lists all Particular entities.
     * @Secure(roles="ROLE_DOMAIN_RESTAURANT_MANAGER,ROLE_DOMAIN")
     */

    public function updateAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $this->getUser()->getGlobalOption()->getRestaurantConfig();

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Particular entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {

            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Report has been created successfully"
            );

            return $this->redirect($this->generateUrl('restaurant_config_manage'));
        }

        return $this->render('RestaurantBundle:Config:manage.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }

}

