<?php

namespace Appstore\Bundle\HumanResourceBundle\Controller;

use Appstore\Bundle\DomainUserBundle\Entity\HrAttendance;
use Appstore\Bundle\DomainUserBundle\Entity\HrAttendanceMonth;
use Appstore\Bundle\HumanResourceBundle\Entity\DailyAttendance;
use Appstore\Bundle\HumanResourceBundle\Entity\EmployeeLeave;
use Appstore\Bundle\HumanResourceBundle\Form\EmployeeLeaveType;
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
class AttendanceController extends Controller
{


    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $globalOption = $this->getUser()->getGlobalOption();
        $employees = $em->getRepository('UserBundle:User')->getEmployees($globalOption);
        return $this->render('HumanResourceBundle:Attendance:index.html.twig', array(
            'globalOption'  => $globalOption,
            'employees'     => $employees,
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
        $employees = $em->getRepository('UserBundle:User')->getEmployees($globalOption);
        $blackoutdate ='';
        $calendarBlackout = $em->getRepository('HumanResourceBundle:Weekend')->findOneBy(array('globalOption' => $globalOption));
        $blackOutDate =  $calendarBlackout ->getWeekendDate();
        if($blackOutDate){
            $blackoutdate = (array_map('trim',array_filter(explode(',',$blackOutDate))));
        }
        return $this->render('HumanResourceBundle:DailyAttendance:attendance.html.twig', array(
            'globalOption'  => $globalOption,
            'employees'     => $employees,
            'blackoutdate'  => $blackoutdate,
        ));

    }

    public function employeeDetailsAction(User $user)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new EmployeeLeave();
        $form = $this->createCreateForm($entity,$user->getId());
        $globalOption = $this->getUser()->getGlobalOption();
        $monthWiseAttendance = $em->getRepository('HumanResourceBundle:Attendance')->findBy(array('employee'=>$user),array('year'=>'DESC','month'=>'ASC'));
        $leaveTypeWiseAbsence = $em->getRepository('HumanResourceBundle:EmployeeLeave')->leaveTypeWiseAbsence($user);
        $entities = $em->getRepository('HumanResourceBundle:EmployeeLeave')->findBy(array('employee'=>$user),array('updated'=>'DESC'));
        return $this->render('HumanResourceBundle:Attendance:employeeDetails.html.twig', array(
            'user'  => $user,
            'entities'  => $entities,
            'monthWiseAttendance'     => $monthWiseAttendance,
            'leaveTypeWiseAbsence'     => $leaveTypeWiseAbsence,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Vendor entity.
     *
     * @param Vendor $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(EmployeeLeave $entity,$user)
    {
        $option = $this->getUser()->getGlobalOption();
        $form = $this->createForm(new EmployeeLeaveType($option), $entity, array(
            'action' => $this->generateUrl('attendance_user_create',array('user'=> $user)),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    public function employeeLeaveCreateAction(Request $request ,User $user)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new EmployeeLeave();
        $global = $this->getUser()->getGlobalOption();
        $form = $this->createCreateForm($entity,$user->getId());
        $form->handleRequest($request);
        $startDate = $entity->getStartDate();
        $endDate = $entity->getEndDate();
        $start = new \DateTime($startDate);
        $end = new \DateTime($endDate);
        $interval = $start->diff($end);
        $offDay = $interval->format('%a');
        $offDay = (int)$offDay +1;

        $leaveDay = $em->getRepository('HumanResourceBundle:EmployeeLeave')->leaveTypeWiseAbsence($user,$entity->getLeaveSetup()->getId());
        if(!empty($leaveDay)){
            $remaining = $leaveDay['offDay'] + $offDay;
        }else{
            $remaining = $offDay;
        }

        $datetime = new \DateTime("now");
        if ($form->isValid() and $entity->getLeaveSetup()->getOffDay() >= $remaining) {
            $em = $this->getDoctrine()->getManager();
            $entity->setGlobalOption($global);
            $entity->setEmployee($user);
            $entity->setYear($datetime->format('Y'));
            $entity->setStartDate($start);
            $entity->setEndDate($end);
            $entity->setNoOffDay($offDay);
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been inserted successfully"
            );
            return $this->redirect($this->generateUrl('attendance_user', array('user' => $user->getId())));
        }
        $this->get('session')->getFlashBag()->add(
            'notice',$entity->getLeaveSetup()->getName() ." is not available"
        );
        $monthWiseAttendance = $em->getRepository('HumanResourceBundle:Attendance')->findBy(array('employee'=>$user),array('year'=>'DESC','month'=>'ASC'));
        $leaveTypeWiseAbsence = $em->getRepository('HumanResourceBundle:EmployeeLeave')->leaveTypeWiseAbsence($user);
        $entities = $em->getRepository('HumanResourceBundle:EmployeeLeave')->findBy(array(),array('updated'=>'DESC'));
        return $this->render('HumanResourceBundle:Attendance:employeeDetails.html.twig', array(
            'user'  => $user,
            'entities'     => $entities,
            'monthWiseAttendance'     => $monthWiseAttendance,
            'leaveTypeWiseAbsence'     => $leaveTypeWiseAbsence,
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    public function employeeLeaveApproveAction(EmployeeLeave $leave)
    {
        $em = $this->getDoctrine()->getManager();
        $leave->setApprovedBy($this->getUser());
        $em->persist($leave);
        $em->flush();
        $blackoutdate ='';
        $calendarBlackout = $em->getRepository('HumanResourceBundle:Weekend')->findOneBy(array('globalOption' => $leave->getGlobalOption()));
        if($calendarBlackout){
            $blackOutDate =  $calendarBlackout ->getWeekendDate();
            if($blackOutDate){
                $blackoutdate = (array_map('trim',array_filter(explode(',',$blackOutDate))));
            }
        }
        $em->getRepository('HumanResourceBundle:Attendance')->leaveAttendance($leave,$blackoutdate);
        return new Response('success');
    }

    public function employeeLeaveDeleteAction(EmployeeLeave $leave)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($leave);
        $em->flush();
        return new Response('success');
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
