<?php

namespace Appstore\Bundle\MedicineBundle\Controller;

use Appstore\Bundle\MedicineBundle\Form\MedicineEditType;
use Appstore\Bundle\MedicineBundle\Form\MedicineType;
use Appstore\Bundle\MedicineBundle\Entity\MedicineBrand;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * MedicineBrand controller.
 *
 */
class MedicineController extends Controller
{

    /**
     * Lists all MedicineBrand entities.
     *
     */
    public function indexAction()
    {

        $em = $this->getDoctrine()->getManager();
        $option = $this->getUser()->getGlobalOption();
        $entities = $em->getRepository('MedicineBundle:MedicineBrand')->findBy(array('globalOption' => $option));
        $entity = new MedicineBrand();
        $form   = $this->createCreateForm($entity);
        return $this->render('MedicineBundle:MedicineBrand:medicine.html.twig', array(
            'pagination' => $entities,
            'entity' => $entity,
            'form'   => $form->createView(),
        ));

    }

    /**
     * Creates a new MedicineBrand entity.
     *
     */
    public function createAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $data = $request->request->all();
        $option = $this->getUser()->getGlobalOption();
        $entity = new MedicineBrand();
        $exist = $this->getDoctrine()->getRepository('MedicineBundle:MedicineBrand')->findOneBy(array('name'=>$entity->getName(),'strength'=> $entity->getStrength(),'medicineForm'=>$entity->getMedicineForm()));
        if(empty($exist)) {
            $em = $this->getDoctrine()->getManager();
            $entity->setGlobalOption($option);
            $entity->setStrength($data['strength']);
            $entity->setMedicineForm($data['medicineForm']);
            $entity->setPackSize($data['packSize']);
            $entity->setName($data['brand']);
            $entity->setPrice($data['mrp']);
            $generic = $this->getDoctrine()->getRepository('MedicineBundle:MedicineGeneric')->checkGenericName($data['generic']);
            $entity->setMedicineGeneric($generic);
            $company = $this->getDoctrine()->getRepository('MedicineBundle:MedicineCompany')->checkCompanyName($data['companyName']);
            $entity->setMedicineCompany($company);
            $em->persist($entity);
            $em->flush();
            $this->getDoctrine()->getRepository('MedicineBundle:MedicineStock')->insertGlobalToLocalStock($option,$entity);
            return $this->redirect($this->generateUrl('medicine_user'));
        }
        $this->get('session')->getFlashBag()->add(
            'error',"Data has been already exist"
        );
        $entities = $em->getRepository('MedicineBundle:MedicineBrand')->findBy(array('globalOption' => $option));
        $form   = $this->createCreateForm($entity);
        return $this->render('MedicineBundle:MedicineBrand:medicine.html.twig', array(
            'pagination' => $entities,
            'entity' => $entity,
            'form'   => $form->createView(),
        ));

    }

    /**
     * Creates a form to create a MedicineBrand entity.
     *
     * @param MedicineBrand $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(MedicineBrand $entity)
    {
        $form = $this->createForm(new MedicineType(), $entity, array(
            'action' => $this->generateUrl('medicine_create', array('id' => $entity->getId())),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
                'id' => 'medicine',
            )
        ));
        return $form;
    }


    /**
     * Displays a form to edit an existing MedicineBrand entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('MedicineBundle:MedicineBrand')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find MedicineBrand entity.');
        }
        $editForm = $this->createEditForm($entity);
        return $this->render('MedicineBundle:MedicineBrand:edit.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a MedicineBrand entity.
    *
    * @param MedicineBrand $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(MedicineBrand $entity)
    {
        $form = $this->createForm(new MedicineEditType(), $entity, array(
            'action' => $this->generateUrl('medicine_update', array('id' => $entity->getId())),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;


    }
    /**
     * Edits an existing MedicineBrand entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('MedicineBundle:MedicineBrand')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find MedicineBrand entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);
        if ($editForm->isValid()) {
            $em->flush();
            return $this->redirect($this->generateUrl('medicine'));
        }

        return $this->render('MedicineBundle:MedicineBrand:edit.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }

    public function copyAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('MedicineBundle:MedicineBrand')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find MedicineBrand entity.');
        }
        $medicine = new MedicineBrand();
        $medicine->setGlobalOption($entity->getGlobalOption());
        $medicine->setName($entity->getName());
        $medicine->setMedicineCompany($entity->getMedicineCompany());
        $medicine->setMedicineGeneric($entity->getMedicineGeneric());
        $medicine->setMedicineForm($entity->getMedicineForm());
        $em->persist($medicine);
        $em->flush();
        return $this->redirect($this->generateUrl('medicine_edit', array('id' => $medicine->getId())));
    }

}
