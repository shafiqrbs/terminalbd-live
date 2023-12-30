<?php

namespace Appstore\Bundle\AccountingBundle\Controller;

use Appstore\Bundle\AccountingBundle\Entity\AccountHead;
use Appstore\Bundle\AccountingBundle\Form\ProfitWithdrawalType;
use Core\UserBundle\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\SecurityExtraBundle\Annotation\RunAs;
use Appstore\Bundle\AccountingBundle\Entity\AccountJournal;
use Appstore\Bundle\AccountingBundle\Form\AccountJournalType;
use Symfony\Component\HttpFoundation\Response;

/**
 * AccountJournal controller.
 *
 */
class ProfitWithdrawalController extends Controller
{

    public function paginate($entities)
    {

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $this->get('request')->query->get('page', 1)/*page number*/,
            25  /*limit per page*/
        );
        return $pagination;
    }

	/**
	 * @Secure(roles="ROLE_DOMAIN_ACCOUNTING_JOURNAL,ROLE_DOMAIN")
	 */

	public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $data['mode'] = "withdrawal";
        $entities = $em->getRepository('AccountingBundle:AccountJournal')->findWithSearch( $this->getUser(),$data);
        $pagination = $this->paginate($entities);
       $employees = $em->getRepository('UserBundle:User')->getEmployees($this->getUser()->getGlobalOption());
        return $this->render('AccountingBundle:ProfitWithdrawal:index.html.twig', array(
            'entities' => $pagination,
            'overview' => '',
            'searchForm' => $data,
            'employees' => $employees,
        ));
    }

    /**
     * Creates a new AccountJournal entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new AccountJournal();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        $method = empty($entity->getTransactionMethod()) ? '' : $entity->getTransactionMethod()->getSlug();
        if (($form->isValid() && $method == 'cash') ||
            ($form->isValid() && $method == 'bank' && $entity->getAccountBank()) ||
            ($form->isValid() && $method == 'mobile' && $entity->getAccountMobileBank())
        ){
            $em = $this->getDoctrine()->getManager();
            $entity->setGlobalOption($this->getUser()->getGlobalOption());
            if($entity->getTransactionMethod()->getSlug() == "cash"){
                $cash = $this->getDoctrine()->getRepository('AccountingBundle:AccountHead')->findOneBy(array("slug" => "cash-in-hand"));
                $entity->setAccountHeadCredit($cash);
            }elseif($entity->getTransactionMethod()->getSlug() == "bank"){
                $bank = $this->getDoctrine()->getRepository('AccountingBundle:AccountHead')->findOneBy(array("slug" => "bank-account"));
                $entity->setAccountHeadCredit($bank);
            }elseif($entity->getTransactionMethod()->getSlug() == "mobile"){
                $mobile = $this->getDoctrine()->getRepository('AccountingBundle:AccountHead')->findOneBy(array("slug" => "mobile-account"));
                $entity->setAccountHeadCredit($mobile);
            }
            $profit = $this->getDoctrine()->getRepository('AccountingBundle:AccountHead')->findOneBy(array("slug" => "profit-loss"));
            $entity->setAccountHeadDebit($profit);
            $entity->setMode("withdrawal");
            $entity->setTransactionType("Credit");
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been added successfully"
            );
            return $this->redirect($this->generateUrl('account_profit_withdrawal'));
        }
        $this->get('session')->getFlashBag()->add(
            'notice',"May be you are missing to select bank or mobile account"
        );
        return $this->render('AccountingBundle:ProfitWithdrawal:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a AccountJournal entity.
     *
     * @param AccountJournal $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(AccountJournal $entity)
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $form = $this->createForm(new ProfitWithdrawalType($globalOption), $entity, array(
            'action' => $this->generateUrl('account_profit_withdrawal_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form purchase',
                'novalidate' => 'novalidate',
            )
        ));
      return $form;
    }


	/**
	 * @Secure(roles="ROLE_DOMAIN_ACCOUNTING_JOURNAL,ROLE_DOMAIN")
	 */

    public function newAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new AccountJournal();
        $form   = $this->createCreateForm($entity);
        $banks = $em->getRepository('SettingToolBundle:Bank')->findAll();
        $accountHead = $this->getDoctrine()->getRepository('AccountingBundle:AccountHead')->findBy(array('isParent' => 1),array('name'=>'ASC'));
        $heads = $this->getDoctrine()->getRepository('AccountingBundle:AccountHead')->getAllChildrenAccount( $this->getUser()->getGlobalOption()->getId());
        return $this->render('AccountingBundle:ProfitWithdrawal:new.html.twig', array(
            'entity' => $entity,
            'accountHead' => $accountHead,
            'heads' => $heads,
            'banks' => $banks,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a AccountJournal entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AccountingBundle:AccountJournal')->find($id);
         if (!$entity) {
            throw $this->createNotFoundException('Unable to find AccountJournal entity.');
        }

        return $this->render('AccountingBundle:AccountJournal:show.html.twig', array(
            'entity'      => $entity,
        ));
    }

	/**
	 * @Secure(roles="ROLE_DOMAIN_ACCOUNTING_JOURNAL,ROLE_DOMAIN")
	 */

    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AccountingBundle:AccountJournal')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AccountJournal entity.');
        }

        $editForm = $this->createEditForm($entity);
        return $this->render('AccountingBundle:AccountJournal:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a AccountJournal entity.
    *
    * @param AccountJournal $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(AccountJournal $entity)
    {
        $globalOption = $this->getUser()->getGlobalOption();
        $form = $this->createForm(new ProfitWithdrawalType($globalOption), $entity, array(
            'action' => $this->generateUrl('account_journal_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form purchase',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }
    /**
     * Edits an existing AccountJournal entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AccountingBundle:AccountJournal')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AccountJournal entity.');
        }
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('account_journal_edit', array('id' => $id)));
        }

        return $this->render('AccountingBundle:AccountJournal:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        ));
    }


    public function paymentAction(Request $request)
    {
        $data = $request->request->all();
        $entity = new AccountJournal();
        $em = $this->getDoctrine()->getManager();
        $entity->setGlobalOption($this->getUser()->getGlobalOption());
        $parent = $em->getRepository('AccountingBundle:AccountJournal')->find($data['parent']);
        $entity->setToUser($parent->getCreatedBy());
        $entity->setAmount($data['amount']);
        $entity->setRemark($data['remark']);
        $em->persist($entity);
        $em->flush();
        exit;
    }

    /**
     * Displays a form to edit an existing AccountJournal entity.
     *
     */
    public function inlineUpdateAction(Request $request)
    {
        $data = $request->request->all();
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AccountingBundle:AccountJournal')->find($data['pk']);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AccountJournal entity.');
        }
        $entity->setAmount($data['value']);
        $em->flush();
        exit;
    }

    public function approveAction(AccountJournal $entity)
    {
        if (!empty($entity) and $entity->getProcess() != 'approved') {
            $em = $this->getDoctrine()->getManager();
            $entity->setProcess('approved');
            $entity->setApprovedBy($this->getUser());
            $accountClose = $this->getUser()->getGlobalOption()->getAccountingConfig()->isAccountClose();
            if($accountClose == 1){
                $datetime = new \DateTime("yesterday 23:59:59");
                $entity->setCreated($datetime);
                $entity->setUpdated($datetime);
            }
            $em->flush();
            if(!empty($entity->getTransactionMethod())){
	            $this->getDoctrine()->getRepository('AccountingBundle:AccountCash')->insertAccountCash($entity,'Journal');
            }
            $this->getDoctrine()->getRepository('AccountingBundle:Transaction')->insertAccountJournalTransaction($entity);
            return new Response('success');
        } else {
            return new Response('failed');
        }

    }


	/**
	 * @Secure(roles="ROLE_DOMAIN_ACCOUNTING_JOURNAL,ROLE_DOMAIN")
	 */

    public function deleteAction(AccountJournal $entity)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AccountJournal entity.');
        }
        $em->remove($entity);
        $em->flush();
        return new Response('success');
    }


    /**
     * Deletes a AccountJournal entity.
     *
     */
    public function stakeholderProfitAction(User $entity)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AccountJournal entity.');
        }
        $amount = $this->getDoctrine()->getRepository('AccountingBundle:Transaction')->getStakeHolderProfitAccount($entity);
        return new Response(abs($amount));
    }


    /**
     * Deletes a AccountJournal entity.
     *
     */
    public function approveDeleteAction(AccountJournal $entity)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AccountJournal entity.');
        }
        $em->remove($entity);
        $em->flush();
        return new Response('success');
    }


	/**
	 * @Secure(roles="ROLE_DOMAIN_ACCOUNT_REVERSE,ROLE_DOMAIN")
	 */

	public function journalReverseAction(AccountJournal $entity){

		$em = $this->getDoctrine()->getManager();
		$this->getDoctrine()->getRepository('AccountingBundle:AccountJournal')->accountReverse($entity);
		$entity->setProcess(null);
		$entity->setApprovedBy(null);
		$entity->setAmount(0);
		$em->flush();
		return $this->redirect($this->generateUrl('account_profit_withdrawal'));

	}

	public function journalPdfAction()
	{

		set_time_limit(0);
		ignore_user_abort(true);
		$array = array();
		$em = $this->getDoctrine()->getManager();
		$data = $_REQUEST;
		$entities = $em->getRepository('AccountingBundle:AccountJournal')->findWithSearch( $this->getUser(),$data);
		$entities = $entities->getResult();
		$array[] = 'Created,User Name,Method,Ref No,Account Head Debit,Account Head Credit,Debit,Credit';

		/* @var $entity AccountJournal */

		foreach ($entities as $key => $entity){

			$method = !empty($entity['methodName']) ? $entity['methodName']:'';
			$creditHead = !empty($entity['accountHeadDebitName']) ? $entity['accountHeadDebitName']:'';
			$debitHead = !empty($entity['accountHeadCreditName']) ? $entity['accountHeadCreditName']:'';

			$debit = $entity['transactionType'] == 'Debit' ? $entity['amount']:'';
			$credit = $entity['transactionType'] == 'Credit' ? $entity['amount']:'';
			$startDate = isset($data['startDate'])  ? $data['startDate'] : '';
			$rows = array(
				$entity['updated']->format('d-m-Y'),
				$entity['userName'],
				$method,
				$entity['accountRefNo'],
				$debitHead,
				$creditHead,
				$debit,
				$credit
			);
			$array[] = implode(',', $rows);
		}
		$startDate = isset($data['startDate'])  ? $data['startDate'] : '';
		$compareStart = new \DateTime($startDate);
		$start =  $compareStart->format('d-m-Y');
		$fileName = $start.'-account-journal.csv';
		$content = implode("\n", $array);
		$response = new Response($content);
		$response->headers->set('Content-Type', 'text/csv');
		$response->headers->set('Content-Type', 'application/octet-stream');
		$response->headers->set('Content-Disposition', 'attachment; filename='.$fileName);
		return $response;
	}

}
