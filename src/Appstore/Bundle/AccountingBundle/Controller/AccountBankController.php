<?php

namespace Appstore\Bundle\AccountingBundle\Controller;

use Appstore\Bundle\AccountingBundle\Entity\AccountBank;
use Appstore\Bundle\AccountingBundle\Form\AccountBankType;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * AccountBank controller.
 *
 */
class AccountBankController extends Controller
{


    /**
     * @Secure(roles="ROLE_DOMAIN_ACCOUNTING_CONFIG")
     */


    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $globalOption = $this->getUser()->getGlobalOption();
        $entities = $em->getRepository('AccountingBundle:AccountBank')->findWithSearch($globalOption,$data);

       // $this->getDoctrine()->getRepository(AccountBank::class)->selectBankRecords();
        return $this->render('AccountingBundle:AccountBank:index.html.twig', array(
            'entities' => $entities,
            'searchForm' => $data,
           // 'overview' => $overview,
        ));
    }

    /**
     * Creates a new AccountBank entity.
     * @Secure(roles="ROLE_DOMAIN_ACCOUNTING_CONFIG")
     */

    public function createAction(Request $request)
    {
        $entity = new AccountBank();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $entity->setGlobalOption($this->getUser()->getGlobalOption());
            $name = $entity->getBank()->getName().','.$entity->getBranch();
            $entity->setName($name);
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been added successfully"
            );
            $this->getDoctrine()->getRepository('AccountingBundle:AccountHead')->insertBankAccount($entity);
            return $this->redirect($this->generateUrl('accountbank'));
        }

        return $this->render('AccountingBundle:AccountBank:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a AccountBank entity.
     *
     * @param AccountBank $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(AccountBank $entity)
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $form = $this->createForm(new AccountBankType($globalOption), $entity, array(
            'action' => $this->generateUrl('accountbank_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
      return $form;
    }

    /**
     * Displays a form to create a new AccountBank entity.
     * @Secure(roles="ROLE_DOMAIN_ACCOUNTING_CONFIG")
     */

    public function newAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new AccountBank();
        $form   = $this->createCreateForm($entity);
        return $this->render('AccountingBundle:AccountBank:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a AccountBank entity.
     * @Secure(roles="ROLE_DOMAIN_ACCOUNTING_CONFIG")
     */

    public function showAction($id)
    {

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AccountingBundle:AccountBank')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AccountBank entity.');
        }
        return $this->render('AccountingBundle:AccountBank:show.html.twig', array(
            'entity'      => $entity,
        ));

    }

    /**
     * Displays a form to edit an existing AccountBank entity.
     * @Secure(roles="ROLE_DOMAIN_ACCOUNTING_CONFIG")
     */

    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AccountingBundle:AccountBank')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AccountBank entity.');
        }

        $editForm = $this->createEditForm($entity);
        return $this->render('AccountingBundle:AccountBank:new.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }


    /**
    * Creates a form to edit a AccountBank entity.
    *
    * @param AccountBank $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(AccountBank $entity)
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $form = $this->createForm(new AccountBankType($globalOption), $entity, array(
            'action' => $this->generateUrl('accountbank_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form purchase',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }


    /**
     * Edits an existing AccountBank entity.
     * @Secure(roles="ROLE_DOMAIN_ACCOUNTING_CONFIG")
     */


    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AccountingBundle:AccountBank')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AccountBank entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $name = $entity->getBank()->getName().','.$entity->getBranch();
            $entity->setName($name);
            $em->flush();
            $this->getDoctrine()->getRepository('AccountingBundle:AccountHead')->insertBankAccount($entity);
            return $this->redirect($this->generateUrl('accountbank'));
        }

        return $this->render('AccountingBundle:AccountBank:new.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        ));
    }

    /**
     * Deletes a Expenditure entity.
     * @Secure(roles="ROLE_DOMAIN_ACCOUNTING_CONFIG")
     */

    public function deleteAction(AccountBank $entity)
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
        return $this->redirect($this->generateUrl('accountbank'));
    }

}
