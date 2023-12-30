<?php

namespace Appstore\Bundle\AccountingBundle\Controller;

use Appstore\Bundle\AccountingBundle\Entity\AccountingConfig;
use Appstore\Bundle\AccountingBundle\Entity\BusinessConfig;
use Appstore\Bundle\AccountingBundle\Form\AccountConfigType;
use Appstore\Bundle\AccountingBundle\Form\ConfigType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * BusinessConfigController.
 *
 */
class AccountConfigController extends Controller
{


    public function manageAction()
    {


        $em = $this->getDoctrine()->getManager();
        $entity = $this->getUser()->getGlobalOption()->getAccountingConfig();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Invoice entity preparation.');
        }
        $form = $this->createEditForm($entity);
        return $this->render('AccountingBundle:Config:manage.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
        ));
    }

    public function resetAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $this->getUser()->getGlobalOption()->getAccountingConfig();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Invoice entity preparation.');
        }
        $globalOption = $this->getUser()->getGlobalOption();
        $this->getDoctrine()->getRepository(AccountingConfig::class)->accountingReset($globalOption);
        return $this->redirect($this->generateUrl('account_config_manage'));
    }

    /**
     * Creates a form to edit a Invoice entity.wq
     *
     * @param AccountingConfig $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(AccountingConfig $entity)
    {
        $config = $this->getUser()->getGlobalOption();
        $form = $this->createForm(new AccountConfigType($config), $entity, array(
            'action' => $this->generateUrl('account_config_update'),
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
    public function updateAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $this->getUser()->getGlobalOption()->getAccountingConfig();
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
            return $this->redirect($this->generateUrl('account_config_manage'));
        }

        return $this->render('AccountingBundle:Config:manage.html.twig', array(
            'entity'        => $entity,
            'form'          => $editForm->createView(),
        ));
    }

    public function autoSearchAction(Request $request)
    {
        $item = $_REQUEST['q'];
        if ($item) {
            $go = $this->getUser()->getGlobalOption();
            $item = $this->getDoctrine()->getRepository('UserBundle:User')->searchAutoComplete($item,$go);
        }
        return new JsonResponse($item);
    }

    public function searchUserNameAction($user)
    {
        return new JsonResponse(array(
            'id'=>$user,
            'text'=>$user
        ));
    }
}

