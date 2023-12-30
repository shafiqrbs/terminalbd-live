<?php

namespace Appstore\Bundle\AccountingBundle\Controller;

use Appstore\Bundle\AccountingBundle\Entity\AccountMobileBank;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\SecurityExtraBundle\Annotation\RunAs;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Appstore\Bundle\AccountingBundle\Form\AccountMobileBankType;
use Symfony\Component\HttpFoundation\Response;

/**
 * AccountMobileBankController controller.
 *
 */
class AccountMobileBankController extends Controller
{


    /**
     * @Secure(roles="ROLE_DOMAIN_ACCOUNTING_CONFIG")
     */


    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $globalOption = $this->getUser()->getGlobalOption();
        $entities = $em->getRepository('AccountingBundle:AccountMobileBank')->findWithSearch($globalOption,$data);
        return $this->render('AccountingBundle:MobileBankAccount:index.html.twig', array(
            'entities' => $entities,
            'searchForm' => $data,
           // 'overview' => $overview,
        ));
    }

    /**
     * Creates a new AccountMobileBank entity.
     * @Secure(roles="ROLE_DOMAIN_ACCOUNTING_CONFIG")
     */

    public function createAction(Request $request)
    {
        $entity = new AccountMobileBank();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $entity->setGlobalOption($this->getUser()->getGlobalOption());
            $name = $entity->getMobile().','.$entity->getServiceName();
            $entity->setName($name);
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been added successfully"
            );
            $this->getDoctrine()->getRepository('AccountingBundle:AccountHead')->insertMobileBankAccount($entity);
            return $this->redirect($this->generateUrl('accountmobilebank'));
        }

        return $this->render('AccountingBundle:MobileBankAccount:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a AccountMobileBank entity.
     *
     * @param AccountMobileBank $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(AccountMobileBank $entity)
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $form = $this->createForm(new AccountMobileBankType($globalOption), $entity, array(
            'action' => $this->generateUrl('accountmobilebank_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
      return $form;
    }

    /**
     * Displays a form to create a new AccountMobileBank entity.
     * @Secure(roles="ROLE_DOMAIN_ACCOUNTING_CONFIG")
     */

    public function newAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new AccountMobileBank();
        $form   = $this->createCreateForm($entity);
        return $this->render('AccountingBundle:MobileBankAccount:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a AccountMobileBank entity.
     * @Secure(roles="ROLE_DOMAIN_ACCOUNTING_CONFIG")
     */

    public function showAction($id)
    {

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AccountingBundle:AccountMobileBank')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AccountMobileBank entity.');
        }
        return $this->render('AccountingBundle:MobileBankAccount:show.html.twig', array(
            'entity'      => $entity,
        ));

    }

    /**
     * Displays a form to edit an existing AccountMobileBank entity.
     * @Secure(roles="ROLE_DOMAIN_ACCOUNTING_CONFIG")
     */

    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AccountingBundle:AccountMobileBank')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AccountMobileBank entity.');
        }

        $editForm = $this->createEditForm($entity);
        return $this->render('AccountingBundle:MobileBankAccount:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }


    /**
    * Creates a form to edit a AccountMobileBank entity.
    *
    * @param AccountMobileBank $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(AccountMobileBank $entity)
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $form = $this->createForm(new AccountMobileBankType($globalOption), $entity, array(
            'action' => $this->generateUrl('accountmobilebank_update',array('id'=>$entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form purchase',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    /**
     * Edits an existing AccountMobileBank entity.
     * @Secure(roles="ROLE_DOMAIN_ACCOUNTING_CONFIG")
     */

    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AccountingBundle:AccountMobileBank')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AccountMobileBank entity.');
        }
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);
        if ($editForm->isValid()) {
            $name = $entity->getMobile().','.$entity->getServiceName();
            $entity->setName($name);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been updated successfully"
            );
            $this->getDoctrine()->getRepository('AccountingBundle:AccountHead')->insertMobileBankAccount($entity);
            return $this->redirect($this->generateUrl('accountmobilebank_edit', array('id' => $id)));
        }

        return $this->render('AccountingBundle:MobileBankAccount:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }

    /**
     * Deletes a Expenditure entity.
     * @Secure(roles="ROLE_DOMAIN_ACCOUNTING_CONFIG")
     */

    public function deleteAction(AccountMobileBank $entity)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AccountPurchase entity.');
        }
        try {

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
        return $this->redirect($this->generateUrl('accountmobilebank'));
    }

}
