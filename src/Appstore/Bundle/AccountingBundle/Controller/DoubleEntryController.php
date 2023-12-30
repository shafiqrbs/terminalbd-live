<?php

namespace Appstore\Bundle\AccountingBundle\Controller;

use Appstore\Bundle\AccountingBundle\Entity\AccountJournalItem;
use Appstore\Bundle\AccountingBundle\Form\DoubleEntryType;
use Knp\Snappy\Pdf;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\SecurityExtraBundle\Annotation\RunAs;
use Appstore\Bundle\AccountingBundle\Entity\AccountJournal;
use Symfony\Component\HttpFoundation\Response;

/**
 * AccountJournal controller.
 *
 */
class DoubleEntryController extends Controller
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

        $data['mode'] = "double-entry";
        $entities = $em->getRepository('AccountingBundle:AccountJournal')->findDoubleEntrySearch( $this->getUser(),$data);
        $pagination = $this->paginate($entities);
        return $this->render('AccountingBundle:DoubleEntry:index.html.twig', array(
            'entities' => $pagination,
            'searchForm' => $data,
        ));
    }

    /**
     * @Secure(roles="ROLE_DOMAIN_ACCOUNTING_JOURNAL,ROLE_DOMAIN")
     */

    public function journalDetailsAction()
    {
        $option = $this->getUser()->getGlobalOption();
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;

        $accountHead = $this->getDoctrine()->getRepository('AccountingBundle:AccountHead')->findBy(array('isParent' => 1),array('name'=>'ASC'));
         $accountSubHeads = $this->getDoctrine()->getRepository('AccountingBundle:AccountHead')->findBy(array('globalOption' => $option),array('name'=>'ASC'));
        $heads = $this->getDoctrine()->getRepository('AccountingBundle:AccountHead')->getAllChildrenAccount( $this->getUser()->getGlobalOption()->getId());

        $entities = $em->getRepository('AccountingBundle:AccountJournalItem')->findDoubleEntrySearch( $this->getUser(),$data);
        if(empty($data['pdf'])){

            $pagination = $this->paginate($entities);
            return $this->render('AccountingBundle:DoubleEntry:journalItem.html.twig', array(
                'accountHead' => $accountHead,
                'accountSubHeads' => $accountSubHeads,
                'heads' => $heads,
                'entities' => $pagination,
                'searchForm' => $data,
            ));

        }else{
            $html = $this->renderView(
                'AccountingBundle:DoubleEntry:journalItemPdf.html.twig', array(
                    'globalOption' => $option,
                    'accountHead' => $accountHead,
                    'accountSubHeads' => $accountSubHeads,
                    'entities' => $entities->getResult(),
                    'searchForm' => $data,
                )
            );
            $this->downloadPdf($html,'journal-item.pdf');
        }


    }

    /**
     * Creates a new AccountJournal entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new AccountJournal();
        $data = $request->request->all();
        $form   = $this->createCreateForm($entity);
        if($data['totalDebit'] == $data['totalDebit']){

            $em = $this->getDoctrine()->getManager();
            $entity->setGlobalOption($this->getUser()->getGlobalOption());
            $entity->setMode('double-entry');
            $entity->setAmount($data['totalDebit']);
            $em->persist($entity);
            $em->flush();
            $this->getDoctrine()->getRepository('AccountingBundle:AccountJournalItem')->insertDoubleEntry($entity,$data);
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been added successfully"
            );
            return $this->redirect($this->generateUrl('account_double_entry'));
        }
        $this->get('session')->getFlashBag()->add(
            'notice',"May be you are missing to select bank or mobile account"
        );
        $accountHead = $this->getDoctrine()->getRepository('AccountingBundle:AccountHead')->findBy(array('isParent' => 1),array('name'=>'ASC'));
        $heads = $this->getDoctrine()->getRepository('AccountingBundle:AccountHead')->getAllChildrenAccount( $this->getUser()->getGlobalOption()->getId());

        return $this->render('AccountingBundle:DoubleEntry:new.html.twig', array(
            'entity' => $entity,
            'accountHead' => $accountHead,
            'heads' => $heads,
            'form' => $form->createView(),
        ));

    }

    /**
     * Creates a form to edit a AccountJournal entity.
     *
     * @param AccountJournal $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(AccountJournal $entity)
    {
        $form = $this->createForm(new DoubleEntryType(),$entity, array(
            'action' => $this->generateUrl('account_double_entry_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form',
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
        $entity = new AccountJournal();
        $em = $this->getDoctrine()->getManager();
        $form   = $this->createCreateForm($entity);
        $accountHead = $this->getDoctrine()->getRepository('AccountingBundle:AccountHead')->findBy(array('isParent' => 1),array('name'=>'ASC'));
        $heads = $this->getDoctrine()->getRepository('AccountingBundle:AccountHead')->getAllChildrenAccount( $this->getUser()->getGlobalOption()->getId());
        return $this->render('AccountingBundle:DoubleEntry:new.html.twig', array(
            'entity' => $entity,
            'accountHead' => $accountHead,
            'heads' => $heads,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a AccountJournal entity.
     *
     */
    public function showAction($id)
    {
        exit;
    }

	/**
	 * @Secure(roles="ROLE_DOMAIN_ACCOUNTING_JOURNAL,ROLE_DOMAIN")
	 */

    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AccountingBundle:AccountJournal')->find($id);
        $editForm = $this->createEditForm($entity);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AccountJournal entity.');
        }
        $accountHead = $this->getDoctrine()->getRepository('AccountingBundle:AccountHead')->findBy(array('isParent' => 1),array('name'=>'ASC'));
        $heads = $this->getDoctrine()->getRepository('AccountingBundle:AccountHead')->getAllChildrenAccount( $this->getUser()->getGlobalOption()->getId());

        $subHeads = $this->getDoctrine()->getRepository('AccountingBundle:AccountHead')->getChildrenAccount( $this->getUser()->getGlobalOption()->getId(),$entity);

        return $this->render('AccountingBundle:DoubleEntry:new.html.twig', array(
            'entity' => $entity,
            'accountHead' => $accountHead,
            'heads' => $heads,
            'subHeads' => $subHeads,
            'form'   => $editForm->createView(),
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
            $form = $this->createForm(new DoubleEntryType(),$entity, array(
            'action' => $this->generateUrl('account_double_entry_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form',
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
        $data = $request->request->all();
        $entity = $em->getRepository('AccountingBundle:AccountJournal')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AccountJournal entity.');
        }
        $editForm = $this->createEditForm($entity);
        if($data['totalDebit'] == $data['totalDebit']){

            $em = $this->getDoctrine()->getManager();
            $entity->setGlobalOption($this->getUser()->getGlobalOption());
            $entity->setMode('double-entry');
            $entity->setAmount($data['totalDebit']);
            $em->persist($entity);
            $em->flush();
            $this->getDoctrine()->getRepository('AccountingBundle:AccountJournalItem')->insertDoubleEntry($entity,$data);
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been added successfully"
            );
            return $this->redirect($this->generateUrl('account_double_entry_edit',array('id'=> $entity->getId())));

        }
        $heads = $this->getDoctrine()->getRepository('AccountingBundle:AccountHead')->getAllChildrenAccount( $this->getUser()->getGlobalOption()->getId());

        $subHeads = $this->getDoctrine()->getRepository('AccountingBundle:AccountHead')->getChildrenAccount( $this->getUser()->getGlobalOption()->getId(),$entity);

        return $this->render('AccountingBundle:DoubleEntry:new.html.twig', array(
            'entity'      => $entity,
            'heads'      => $heads,
            'subHeads'      => $subHeads,
            'form'   => $editForm->createView(),
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
            $this->getDoctrine()->getRepository('AccountingBundle:Transaction')->insertDoubleEntryTransaction($entity);
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
	 * @Secure(roles="ROLE_DOMAIN_ACCOUNTING_JOURNAL,ROLE_DOMAIN")
	 */

    public function itemDeleteAction(AccountJournalItem $item)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$item) {
            throw $this->createNotFoundException('Unable to find AccountJournal entity.');
        }
        $em->remove($item);
        $em->flush();
        return new Response('success');
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
		return $this->redirect($this->generateUrl('account_double_entry'));

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


    public function approveJournalResetAction()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        $em = $this->getDoctrine()->getManager();
        $data['mode'] = "double-entry";
        $entities = $em->getRepository('AccountingBundle:AccountJournal')->findDoubleEntrySearch( $this->getUser(),$data);
        $pagination = $entities->getResult();

        /* @var $entity AccountJournal */

        foreach ( $pagination as $entity ){
            if (!empty($entity) and $entity->getProcess() == 'approved') {
                $em->createQuery("DELETE AccountingBundle:AccountCash e WHERE e.globalOption = {$entity->getGlobalOption()->getId()} AND e.accountJournal ={$entity->getId()}")->execute();
                $em->createQuery("DELETE AccountingBundle:Transaction e WHERE e.globalOption = {$entity->getGlobalOption()->getId()} AND e.accountJournal ={$entity->getId()}")->execute();
                $this->getDoctrine()->getRepository('AccountingBundle:Transaction')->resetDoubleEntryTransaction($entity);
            } else {
                return new Response('failed');
            }
        }
        return $this->redirect($this->generateUrl('account_double_entry'));

    }

    public function downloadPdf($html,$fileName = '')
    {
        $wkhtmltopdfPath = 'xvfb-run --server-args="-screen 0, 1280x1024x24" /usr/bin/wkhtmltopdf --use-xserver';
        $snappy          = new Pdf($wkhtmltopdfPath);
        $pdf             = $snappy->getOutputFromHtml($html);
        header('Content-Type: application/pdf');
        header("Content-Disposition: attachment; filename={$fileName}");
        echo $pdf;
        return new Response('');
    }

}
