<?php

namespace Appstore\Bundle\HumanResourceBundle\Controller;

use Appstore\Bundle\DomainUserBundle\Entity\HrAttendance;
use Appstore\Bundle\DomainUserBundle\Entity\HrAttendanceMonth;
use Appstore\Bundle\HumanResourceBundle\Entity\Attendance;
use Appstore\Bundle\HumanResourceBundle\Entity\DailyAttendance;
use Core\UserBundle\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Appstore\Bundle\DomainUserBundle\Entity\Customer;
use Appstore\Bundle\DomainUserBundle\Form\CustomerType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\Date;

/**
 * Customer controller.
 *
 */
class DailyAttendanceController extends Controller
{

    public function paginate($entities)
    {

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entities,
            $this->get('request')->query->get('page', 1)/*page number*/,
            50  /*limit per page*/
        );
        return $pagination;
    }


    /**
     * Lists all Customer entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $_REQUEST;
        $globalOption = $this->getUser()->getGlobalOption();
        $entities = $em->getRepository('HumanResourceBundle:DailyAttendance')->attendanceYearMonth($globalOption,$data);
        return $this->render('HumanResourceBundle:DailyAttendance:index.html.twig', array(
            'entities' => $entities,
            'searchForm' => $data,
        ));
    }


    /**
     * Displays a form to create a new Customer entity.
     *
     */
    public function attendanceAction()
    {
        $em = $this->getDoctrine()->getManager();
        $globalOption = $this->getUser()->getGlobalOption();
        $blackoutdate ='';

        $employees = $em->getRepository('UserBundle:User')->getEmployees($globalOption);
        $calendarBlackout = $em->getRepository('HumanResourceBundle:Weekend')->findOneBy(array('globalOption' => $globalOption));
        $blackOutDate =  $calendarBlackout ->getWeekendDate();
        if($blackOutDate){
            $blackoutdate = (array_map('trim',array_filter(explode(',',$blackOutDate))));
        }
        $this->getDoctrine()->getRepository('HumanResourceBundle:Attendance')->setupMonthlyAttendance($globalOption,$employees,$blackoutdate);
        $currentMonthEmployeeAttendance = $this->getDoctrine()->getRepository('HumanResourceBundle:Attendance')->currentMonthEmployeeAttendance($globalOption);

        return $this->render('HumanResourceBundle:DailyAttendance:attendance.html.twig', array(
            'globalOption'                          => $globalOption,
            'employees'                             => $employees,
            'currentMonthEmployeeAttendance'        => $currentMonthEmployeeAttendance,
            'blackoutdate'                          => $blackoutdate,
        ));

    }


    public function addAttendanceAction(Attendance $attendance)
    {
        $present = $_REQUEST['present'];
        $em = $this->getDoctrine()->getManager();
        $datetime = new \DateTime("now");
        $today  = $datetime->format('d');
        $month  = $datetime->format('F');
        $year   = $datetime->format('Y');
        $exist = $this->getDoctrine()->getRepository('HumanResourceBundle:DailyAttendance')->checkLeaveToday($attendance);
        if($exist){
            $entity = $exist;
        }else{
            $entity = New DailyAttendance();
        }
        $entity->setAttendance($attendance);
        $entity->setUser($attendance->getEmployee());
        $entity->setGlobalOption($attendance->getGlobalOption());
        if($present > 0 ){
            $entity->setPresent(true);
            $entity->setPresentDay($present);
            $entity->setPresentIn(true);
            $entity->setPresentOut(true);
        }else{
            $entity->setPresent(false);
            $entity->setPresentDay(null);
            $entity->setPresentIn(true);
            $entity->setPresentOut(true);
        }
        $entity->setMonth($month);
        $entity->setYear($year);
        $em->persist($entity);
        $em->flush();
        $this->getDoctrine()->getRepository('HumanResourceBundle:Attendance')->employeeTotalPresentDay($attendance);
        exit;

    }

    public function showAction($month,$year)
    {
        $showMonth = new \DateTime("$month $year");
        $em = $this->getDoctrine()->getManager();
        $globalOption = $this->getUser()->getGlobalOption();
        $blackoutdate ='';
        $employees = $em->getRepository('UserBundle:User')->getEmployeeEntities($globalOption);
        $calendarBlackout = $em->getRepository('HumanResourceBundle:Weekend')->findOneBy(array('globalOption' => $globalOption));
        $blackOutDate =  $calendarBlackout ->getWeekendDate();
        if($blackOutDate){
            $blackoutdate = (array_map('trim',array_filter(explode(',',$blackOutDate))));
        }
        return $this->render('HumanResourceBundle:DailyAttendance:show.html.twig', array(
            'showMonth' => $showMonth,
            'globalOption'  => $globalOption,
            'employees'     => $employees,
            'blackoutdate'  => $blackoutdate,
        ));
    }

    public function employeeList()
    {
        $em = $this->getDoctrine()->getManager();
        $globalOption = $this->getUser()->getGlobalOption();
        $employees = $em->getRepository('UserBundle:User')->getEmployees($globalOption);
        return $this->render('HumanResourceBundle:DailyAttendance:employee.html.twig', array(
            'globalOption'  => $globalOption,
            'employees'     => $employees,
        ));
    }

    public function employeeAttendance(User $user)
    {
        $em = $this->getDoctrine()->getManager();
        $globalOption = $this->getUser()->getGlobalOption();
        $employees = $em->getRepository('UserBundle:User')->getEmployees($globalOption);
        return $this->render('HumanResourceBundle:DailyAttendance:employee.html.twig', array(
            'globalOption'  => $globalOption,
            'employees'     => $employees,
        ));
    }



    public function manualAttendanceAction()
    {
        $em = $this->getDoctrine()->getManager();
        $globalOption = $this->getUser()->getGlobalOption();
        $employees = $em->getRepository('UserBundle:User')->getEmployees($globalOption);
        return $this->render('HumanResourceBundle:DailyAttendance:manualAttendance.html.twig', array(
            'globalOption'  => $globalOption,
            'employees'     => $employees,
        ));

    }

    public function manualAttendanceCreate(Request $request)
    {

    }


}
