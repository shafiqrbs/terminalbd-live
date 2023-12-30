<?php

namespace Appstore\Bundle\AccountingBundle\Controller;

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
class AccountJournalController extends Controller
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
        $data['mode'] = "journal";
        $entities = $em->getRepository('AccountingBundle:AccountJournal')->findWithSearch( $this->getUser(),$data);
        $pagination = $this->paginate($entities);
        $accountHead = $this->getDoctrine()->getRepository('AccountingBundle:AccountHead')->findBy(array('isParent' => 1),array('name'=>'ASC'));
        $heads = $this->getDoctrine()->getRepository('AccountingBundle:AccountHead')->getAllChildrenAccount( $this->getUser()->getGlobalOption()->getId());

        $debit = $this->getDoctrine()->getRepository('AccountingBundle:AccountJournal')->accountCashOverview($this->getUser(),'Debit',$data);
        $credit = $this->getDoctrine()->getRepository('AccountingBundle:AccountJournal')->accountCashOverview($this->getUser(),'Credit',$data);
        $overview = array('debit' => $debit,'credit' => $credit);
        $transactionMethods = $this->getDoctrine()->getRepository('SettingToolBundle:TransactionMethod')->findBy(array('status'=>1),array('name'=>'asc'));
        $employees = $em->getRepository('UserBundle:User')->getEmployees($this->getUser()->getGlobalOption());

        return $this->render('AccountingBundle:AccountJournal:index.html.twig', array(
            'entities' => $pagination,
            'searchForm' => $data,
            'overview' => $overview,
            'accountHead' => $accountHead,
            'employees' => $employees,
            'heads' => $heads,
            'transactionMethods' => $transactionMethods,
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
        if ($form->isValid() && empty($method)) {
            $em = $this->getDoctrine()->getManager();
            $entity->setGlobalOption($this->getUser()->getGlobalOption());
            if(!empty($this->getUser()->getProfile()->getBranches())){
                $entity->setBranches($this->getUser()->getProfile()->getBranches());
            }
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been added successfully"
            );
            return $this->redirect($this->generateUrl('account_journal'));
        }elseif (($form->isValid() && $method == 'cash') ||
            ($form->isValid() && $method == 'bank' && $entity->getAccountBank()) ||
            ($form->isValid() && $method == 'mobile' && $entity->getAccountMobileBank())
        ) {
            $em = $this->getDoctrine()->getManager();
            $entity->setGlobalOption($this->getUser()->getGlobalOption());
            if(!empty($this->getUser()->getProfile()->getBranches())){
                $entity->setBranches($this->getUser()->getProfile()->getBranches());
            }
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been added successfully"
            );
            return $this->redirect($this->generateUrl('account_journal'));
        }
        $this->get('session')->getFlashBag()->add(
            'notice',"May be you are missing to select bank or mobile account"
        );
        return $this->render('AccountingBundle:AccountJournal:new.html.twig', array(
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
        $form = $this->createForm(new AccountJournalType($globalOption), $entity, array(
            'action' => $this->generateUrl('account_journal_create'),
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
        return $this->render('AccountingBundle:AccountJournal:new.html.twig', array(
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

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('AccountingBundle:AccountJournal:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing AccountJournal entity.
     *
     */


    /**
     * Creates a form to delete a AccountJournal entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('account_journal_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
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
            if($entity->getTransactionMethod()){
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
        exit;
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
        $this->getDoctrine()->getRepository('AccountingBundle:Transaction')->approvedDeleteRecord($entity,'Journal');
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
		return $this->redirect($this->generateUrl('account_journal'));

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

    public function journalExcelAction()
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
