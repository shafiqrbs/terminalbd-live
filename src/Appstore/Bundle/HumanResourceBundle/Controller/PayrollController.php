<?php

namespace Appstore\Bundle\HumanResourceBundle\Controller;



use Appstore\Bundle\HumanResourceBundle\Entity\EmployeePayroll;
use Appstore\Bundle\HumanResourceBundle\Entity\Payroll;
use Appstore\Bundle\HumanResourceBundle\Entity\PayrollSheet;
use Appstore\Bundle\HumanResourceBundle\Form\PayrollType;

use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;



/**
 * Customer controller.
 *
 */
class PayrollController extends Controller
{

    public function paginate($entities)
    {

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $this->get('request')->query->get('page', 1)/*page number*/,
            25  /*limit per page*/
        );
        $pagination->setTemplate('SettingToolBundle:Widget:pagination.html.twig');
        return $pagination;
    }

    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $globalOption = $this->getUser()->getGlobalOption();
        $entities = $em->getRepository('HumanResourceBundle:Payroll')->findWithSearch($globalOption->getId());
        $pagination = $this->paginate($entities);
        return $this->render('HumanResourceBundle:Payroll:index.html.twig', array(
            'globalOption'  => $globalOption,
            'entities'     => $pagination,
        ));
    }

    /**
     * Creates a new Particular entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Payroll();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $config = $this->getUser()->getGlobalOption();
            $entity->setGlobalOption($config);
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been inserted successfully"
            );
            $this->getDoctrine()->getRepository('HumanResourceBundle:PayrollSheet')->insertUpdateParticular($entity);
            return $this->redirect($this->generateUrl('payroll_sheet', array('id' => $entity->getId())));
        }
        return $this->render('HumanResourceBundle:Payroll:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }


    public function newAction()
    {
        $entity = new Payroll();
        $form = $this->createCreateForm($entity);
        return $this->render('HumanResourceBundle:Payroll:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Particular entity.
     *
     * @param Payroll $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Payroll $entity)
    {
        $option = $this->getUser()->getGlobalOption();
        $form = $this->createForm(new PayrollType($option), $entity, array(
            'action' => $this->generateUrl('payroll_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'form-horizontal',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }


    public function updateAction(Request $request , Payroll $entity)
    {
        /* @var $entity EmployeePayroll */

        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        $data = $request->request->all();
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success', "Data has been inserted successfully"
            );
            $this->getDoctrine()->getRepository('HumanResourceBundle:EmployeePayroll')->insertUpdateParticular($entity, $data);
            $this->getDoctrine()->getRepository('HumanResourceBundle:EmployeePayroll')->insertUpdate($entity);
        }
        return $this->render('HumanResourceBundle:EmployeePayroll:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    public function deleteAction(Payroll $entity)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Particular entity.');
        }
        try {

            $em->remove($entity);
            $em->flush();

        } catch (ForeignKeyConstraintViolationException $e) {
            $this->get('session')->getFlashBag()->add(
                'notice',"Data has been relation another Table"
            );
        }catch (\Exception $e) {
            $this->get('session')->getFlashBag()->add(
                'notice', 'Please contact system administrator further notification.'
            );
        }
        return $this->redirect($this->generateUrl('payroll'));


    }

    public function sheetAction(Payroll $entity)
    {
        $global = $this->getUser()->getGlobalOption()->getId();
        $particulars = $this->getDoctrine()->getRepository('HumanResourceBundle:PayrollSetting')->findBy(array('globalOption'=>$global),array('mode'=>'ASC'));
        $attendances = $this->getDoctrine()->getRepository('HumanResourceBundle:Attendance')->monthlyEmployeeAttendance($this->getUser()->getGlobalOption());
        $presents = array();
        foreach ($attendances as $attendance){
            $presents[$attendance['userId']] = $attendance;
        }
        if($entity->getGlobalOption()->getId() == $global) {
            $data = "";
            /* @var $sheet PayrollSheet */
            $totalDay = '';
            $present = '';
            $absence = '';
            $weekend = '';
            foreach ($entity->getPayrollSheets() as $sheet) {

                $userId = $sheet->getEmployee()->getEmployee()->getId();
                if( in_array($userId,array_keys($presents))){
                    $totalDay =  $presents[$userId]['totalDay'];
                    $present =  $presents[$userId]['present'];
                    $absence =  $presents[$userId]['absence'];
                    $weekend =  $presents[$userId]['weekend'];
                }
                $data .= "<tr>";
                $data .= "<td>{$sheet->getEmployee()->getEmployeeName()}</td>";
                $data .= "<td>{$totalDay}</td>";
                $data .= "<td>{$weekend}</td>";
                $data .= "<td>{$absence}</td>";
                $data .= "<td>{$present}</td>";
                $data .= "<td>{$sheet->getBasicAmount()}</td>";
                /*
                 * $allowance = json_decode($sheet->getParticularAllowance(),true);
                 * foreach ($particulars as $particular ):
                    $data .="<td>";
                    if(!empty($allowance)) {
                        $data .= in_array($particular->getId(),array_keys($allowance)) ? $allowance[$particular->getId()] :'';
                    }
                    $data .="</td>";
                endforeach;*/
                $data .= "<td>{$sheet->getAllowanceAmount()}</td>";
                $data .= "<td>{$sheet->getDeductionAmount()}</td>";
                $data .= "<td>{$sheet->getAdvanceAmount()}</td>";
                $data .= "<td>{$sheet->getLoanInstallment()}</td>";
                $data .= "<td>{$sheet->getArearAmount()}</td>";
                $data .= "<td>{$sheet->getTotalAmount()}</td>";
                $data .= "<td>{$sheet->getPayableAmount()}</td>";
                $data .= "<td><input type='checkbox' id='hold' name='hold-{$sheet->getId()}' value='1'></td>";
                $data .= "<td><div class='btn-group'>
<a href='/' class='btn mini yellow'><i class='fa fa-sign-out'></i></a>
</div>
</td>";
                $data .= "</tr>";
            }

        }
        return $this->render('HumanResourceBundle:Payroll:sheet.html.twig', array(
            'entity'        => $entity,
            'particulars'   => $particulars,
            'data'   => $data,
        ));


    }


    public function employeeSheetDetailsAction(Payroll $entity)
    {
        $global = $this->getUser()->getGlobalOption()->getId();
        $particulars = $this->getDoctrine()->getRepository('HumanResourceBundle:PayrollSetting')->findBy(array('globalOption'=>$global),array('mode'=>'ASC'));
        $attendances = $this->getDoctrine()->getRepository('HumanResourceBundle:Attendance')->monthlyEmployeeAttendance($this->getUser()->getGlobalOption());
        $presents = array();
        foreach ($attendances as $attendance){
            $presents[$attendance['userId']] = $attendance;
        }
        if($entity->getGlobalOption()->getId() == $global) {
            $data = "";
            /* @var $sheet PayrollSheet */
            $totalDay = '';
            $present = '';
            $absence = '';
            $weekend = '';
            foreach ($entity->getPayrollSheets() as $sheet) {

                $userId = $sheet->getEmployee()->getEmployee()->getId();
                if( in_array($userId,array_keys($presents))){
                    $totalDay =  $presents[$userId]['totalDay'];
                    $present =  $presents[$userId]['present'];
                    $absence =  $presents[$userId]['absence'];
                    $weekend =  $presents[$userId]['weekend'];
                }
                $data .= "<tr>";
                $data .= "<td>{$sheet->getEmployee()->getEmployeeName()}</td>";
                $data .= "<td>{$sheet->getEmployee()->getSalaryType()}</td>";
                $data .= "<td>{$totalDay}</td>";
                $data .= "<td>{$weekend}</td>";
                $data .= "<td>{$absence}</td>";
                $data .= "<td>{$present}</td>";
                $data .= "<td>{$sheet->getBasicAmount()}</td>";
                /*
                 * $allowance = json_decode($sheet->getParticularAllowance(),true);
                 * foreach ($particulars as $particular ):
                    $data .="<td>";
                    if(!empty($allowance)) {
                        $data .= in_array($particular->getId(),array_keys($allowance)) ? $allowance[$particular->getId()] :'';
                    }
                    $data .="</td>";
                endforeach;*/
                $data .= "<td>{$sheet->getAllowanceAmount()}</td>";
                $data .= "<td>{$sheet->getDeductionAmount()}</td>";
                $data .= "<td>{$sheet->getAdvanceAmount()}</td>";
                $data .= "<td>{$sheet->getLoanInstallment()}</td>";
                $data .= "<td>{$sheet->getArearAmount()}</td>";
                $data .= "<td>{$sheet->getTotalAmount()}</td>";
                $data .= "<td>{$sheet->getPayableAmount()}</td>";
                $data .= "<td><input type='checkbox' id='hold' name='hold-{$sheet->getId()}' value='1'></td>";
                $data .= "<td><div class='btn-group'>
<a href='/' class='btn mini yellow'><i class='fa fa-sign-out'></i></a>
</div>
</td>";
                $data .= "</tr>";
            }

        }
        return $this->render('HumanResourceBundle:Payroll:sheet.html.twig', array(
            'payroll'        => $entity,
            'particulars'   => $particulars,
            'data'   => $data,
        ));


    }



}
