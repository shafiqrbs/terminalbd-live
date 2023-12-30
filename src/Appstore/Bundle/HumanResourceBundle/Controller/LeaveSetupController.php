<?php

namespace Appstore\Bundle\HumanResourceBundle\Controller;

use Appstore\Bundle\HospitalBundle\Entity\HmsVendor;
use Appstore\Bundle\HospitalBundle\Form\VendorType;
use Appstore\Bundle\HumanResourceBundle\Entity\LeaveSetup;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * LeaveSetupController
 *
 */
class LeaveSetupController extends Controller
{

    /**
     * Lists all Vendor entities.
     *
     */
    public function leaveSetupAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new HmsVendor();
        $option = $this->getUser()->getGlobalOption();
        $entities = $this->getDoctrine()->getRepository('HumanResourceBundle:LeavePolicy')->findBy(array('status' => 1),array('id'=>'ASC'));
        $leaveSetup = $this->getDoctrine()->getRepository('HumanResourceBundle:LeaveSetup')->findBy(array('globalOption' => $option));

        /** @var  $leaveSetups */

        $leaveSetups = array();

        if (!empty($leaveSetup)){
            foreach ($leaveSetup as $row):
                $leaveSetups[$row->getLeavePolicy()->getId()] = $row;
            endforeach;
        }

        return $this->render('HumanResourceBundle:LeaveSetup:index.html.twig', array(
            'entities' => $entities,
            'leaveSetup' => $leaveSetups,
            'entity' => $entity,
        ));
    }

    public function updateAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $option = $this->getUser()->getGlobalOption();
        $data = $request->request->all();
        $i = 0;
        if ($data and isset($data['leaveSetup']) and !empty($data['leaveSetup'])) {
            foreach ($data['leaveSetup'] as $row) {
                $entity = $this->getDoctrine()->getRepository('HumanResourceBundle:LeaveSetup')->findOneBy(array('globalOption' => $option, 'leavePolicy' => $row));
                if (empty($entity)) {
                    $entity = New LeaveSetup();
                }
                $policy = $this->getDoctrine()->getRepository('HumanResourceBundle:LeavePolicy')->find($row);
                $entity->setGlobalOption($option);
                $entity->setLeavePolicy($policy);
                $entity->setName($policy->getName());
                $entity->setOffDay($data['offDay'][$i]);
                $em->persist($entity);
                $i++;
            }
            $em->flush();
        }
        return $this->redirect($this->generateUrl('leave_setup'));


    }


}
