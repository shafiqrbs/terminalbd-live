<?php

namespace Setting\Bundle\ToolBundle\Controller;

use Appstore\Bundle\MedicineBundle\Entity\MedicineBrand;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Setting\Bundle\ToolBundle\Entity\MedicineImport;
use Setting\Bundle\ToolBundle\Form\MedicineEditType;
use Setting\Bundle\ToolBundle\Form\MedicineImportType;
use Setting\Bundle\ToolBundle\Form\MedicineType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\SecurityExtraBundle\Annotation\RunAs;
use Appstore\Bundle\InventoryBundle\Entity\ItemColor;
use Setting\Bundle\ToolBundle\Form\ColorType;
use Symfony\Component\HttpFoundation\Response;

/**
 * ItemColor controller.
 *
 */
class MedicineController extends Controller
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
        $data = $_REQUEST;
        $entities = $this->getDoctrine()->getRepository('MedicineBundle:MedicineBrand')->findWithSearch($data);
        $pagination = $this->paginate($entities);
        return $this->render('SettingToolBundle:MedicineBrand:index.html.twig', array(
            'pagination' => $pagination,
            'searchForm' => $data,
        ));
    }

    /**
     * Lists all MedicineBrand entities.
     *
     */
    public function newAction()
    {

        $em = $this->getDoctrine()->getManager();
        $entity = new MedicineBrand();
        $form   = $this->createCreateForm($entity);
        return $this->render('SettingToolBundle:MedicineBrand:medicine.html.twig', array(
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
        $file = $request->files->all()['medicine']['file'];
        $option = $this->getUser()->getGlobalOption();
        $entity = new MedicineBrand();
        $entity->setGlobalOption($option);
        $entity->setStrength($data['strength']);
        $entity->setMedicineForm($data['medicineForm']);
        $entity->setPackSize($data['packSize']);
        $entity->setName($data['brand']);
        $exist = $this->getDoctrine()->getRepository('MedicineBundle:MedicineBrand')->findOneBy(array('name'=>$entity->getName(),'strength'=> $entity->getStrength(),'medicineForm'=>$entity->getMedicineForm()));
        if(empty($exist)){
            $generic = $this->getDoctrine()->getRepository('MedicineBundle:MedicineGeneric')->checkGenericName($data['generic']);
            $entity->setMedicineGeneric($generic);
            $company = $this->getDoctrine()->getRepository('MedicineBundle:MedicineCompany')->checkCompanyName($data['companyName']);
            $entity->setMedicineCompany($company);
            $fileName = $file->getClientOriginalName();
            $imgName = uniqid() . '.' . $fileName;
            $path = $entity->getUploadDir() . $imgName;
            if (!file_exists($entity->getUploadDir())) {
                mkdir($entity->getUploadDir(), 0777, true);
            }
            $this->get('helper.imageresizer')->resizeImage(480, $path, $file);
            $entity->setPath($imgName);
            $em->persist($entity);
            $em->flush();
            return $this->redirect($this->generateUrl('medicinebrand'));
        }
        $this->get('session')->getFlashBag()->add(
            'error',"Data has been already exist"
        );
        $form   = $this->createCreateForm($entity);
        return $this->render('SettingToolBundle:MedicineBrand:medicine.html.twig', array(
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
            'action' => $this->generateUrl('medicinebrand_create', array('id' => $entity->getId())),
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
        return $this->render('SettingToolBundle:MedicineBrand:edit.html.twig', array(
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
            'action' => $this->generateUrl('medicinebrand_update', array('id' => $entity->getId())),
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
        $exist = $this->getDoctrine()->getRepository('MedicineBundle:MedicineBrand')->findOneBy(array('name'=>$entity->getName(),'medicineForm' => $entity->getMedicineForm(),'strength'=>$entity->getStrength()));
        if ($editForm->isValid() and empty($exist)) {
            if($entity->upload()){
                $entity->removeUpload();
                $entity->upload();
            }else{
                $entity->upload();
            }
            $em->flush();
            $referer = $request->headers->get('referer');
            return new RedirectResponse($referer);
        }
        return $this->render('SettingToolBundle:MedicineBrand:edit.html.twig', array(
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
        return $this->redirect($this->generateUrl('medicinebrand_edit', array('id' => $medicine->getId())));
    }

    /**
     * @Secure(roles="ROLE_medicinebrand_STOCK")
     */

    public function deleteAction(Request $request , MedicineBrand $entity)
    {
        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find MedicineStock entity.');
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
        }catch (\Exception $e) {
            $this->get('session')->getFlashBag()->add(
                'notice', 'Please contact system administrator further notification.'
            );
        }
        $referer = $request->headers->get('referer');
        return new RedirectResponse($referer);
    }

    /**
     * Status a Page entity.
     *
     */
    public function statusAction(Request $request, MedicineBrand $entity)
    {

        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find District entity.');
        }
        $status = $entity->getStatus();
        if($status == 1){
            $entity->setStatus(0);
        } else{
            $entity->setStatus(1);
        }
        $em->flush();
        $this->get('session')->getFlashBag()->add(
            'success',"Status has been changed successfully"
        );
        $referer = $request->headers->get('referer');
        return new RedirectResponse($referer);
    }



    public function medicineDetailsAction(){

        $data = $_REQUEST['medicine'];
        $entity = $this->getDoctrine()->getRepository('MedicineBundle:MedicineBrand')->find($data);
        $items = array();
        if($entity) {
            $items =array(
                'generic' => $entity->getMedicineGeneric()->getName(),
                'company' => $entity->getMedicineCompany()->getName(),
                'medicineForm' => $entity->getMedicineForm()
            );
        }
        return new Response(json_encode($items));
    }

    public function autoSearchMedicineAction(Request $request)
    {
        $item = $_REQUEST['term'];
        $items = array();

        if ($item) {
            $entities = $this->getDoctrine()->getRepository('MedicineBundle:MedicineBrand')->searchMedicineAutoComplete($item);
            foreach ($entities as $entity):
                $items[] = array('id' => $entity['id'], 'value' => $entity['text']);
            endforeach;
        }
        return new JsonResponse($items);

    }

    public function selectSearchMedicineAction(Request $request)
    {
        $item = trim($_REQUEST['q']);
        if ($item) {
            $entities = $this->getDoctrine()->getRepository('MedicineBundle:MedicineBrand')->searchMedicineAutoComplete($item);
        }
        return new JsonResponse($entities);

    }

    public function autoSearchGenericAction(Request $request)
    {
        $item = trim($_REQUEST['term']);
        $items = array();
        if ($item) {
            $entities = $this->getDoctrine()->getRepository('MedicineBundle:MedicineBrand')->searchMedicineAutoComplete($item);
            foreach ($entities as $entity):
                $items[] = array('id' => $entity['id'], 'value' => $entity['text']);
            endforeach;
        }
        return new JsonResponse($items);
    }


    public function searchProductNameAction($medicine)
    {
        return new JsonResponse(array(
            'id' => $medicine,
            'text' => $medicine
        ));
    }

    public function searchBrandNameAutoCompleteAction(Request $request)
    {
        $item = trim($_REQUEST['term']);
        $items = array();
        if ($item) {
            $entities = $this->getDoctrine()->getRepository('MedicineBundle:MedicineBrand')->searchBrandNameAutoComplete($item);
            foreach ($entities as $entity):
                $items[] = array('id' => $entity['id'], 'value' => $entity['text']);
            endforeach;
        }
        return new JsonResponse($items);
    }

    public function searchCompanyNameAutoCompleteAction(Request $request)
    {
        $item = trim($_REQUEST['term']);
        $items = array();
        if ($item) {
            $entities = $this->getDoctrine()->getRepository('MedicineBundle:MedicineBrand')->searchCompanyNameAutoComplete($item);
            foreach ($entities as $entity):
                $items[] = array('id' => $entity['id'], 'value' => $entity['text']);
            endforeach;
        }
        return new JsonResponse($items);
    }

    public function searchGenericNameAutoCompleteAction(Request $request)
    {
        $item = trim($_REQUEST['term']);
        $items = array();
        if ($item) {
            $entities = $this->getDoctrine()->getRepository('MedicineBundle:MedicineBrand')->searchGenericNameAutoComplete($item);
            foreach ($entities as $entity):
                $items[] = array('id' => $entity['id'], 'value' => $entity['text']);
            endforeach;
        }
        return new JsonResponse($items);
    }

    public function searchMedicineGenericNameAutoCompleteAction(Request $request)
    {
        $item = trim($_REQUEST['term']);
        $items = array();
        if ($item) {
            $entities = $this->getDoctrine()->getRepository('MedicineBundle:MedicineBrand')->searchMedicineGenericAutoComplete($item);
            foreach ($entities as $entity):
                $items[] = array('id' => $entity['id'], 'value' => $entity['text']);
                //   $items[] = array('value' => $entity['id'], 'label' => $entity['text'], 'desc' => $entity['text'], 'icon' => 'img/avatar.png');

            endforeach;
        }
        return new JsonResponse($items);
    }

    public function searchPackSizeAutoCompleteAction(Request $request)
    {
        $item = trim($_REQUEST['term']);
        $items = array();
        if ($item) {
            $entities = $this->getDoctrine()->getRepository('MedicineBundle:MedicineBrand')->searchPackSizeAutoComplete($item);
            foreach ($entities as $entity):
                $items[] = array('id' => $entity['id'], 'value' => $entity['text']);
            endforeach;
        }
        return new JsonResponse($items);
    }

    public function searchStrengthAutoCompleteAction(Request $request)
    {
        $item = trim($_REQUEST['term']);
        $items = array();
        if ($item) {
            $entities = $this->getDoctrine()->getRepository('MedicineBundle:MedicineBrand')->searchStrengthAutoComplete($item);
            foreach ($entities as $entity):
                $items[] = array('id' => $entity['id'], 'value' => $entity['text']);
            endforeach;
        }
        return new JsonResponse($items);
    }

    public function searchMedicineFormAutoCompleteAction(Request $request)
    {
        $item = $_REQUEST['term'];
        $items = array();
        if ($item) {
            $entities = $this->getDoctrine()->getRepository('MedicineBundle:MedicineBrand')->searchMedicineFormAutoComplete($item);
            foreach ($entities as $entity):
                $items[] = array('id' => $entity['id'], 'value' => $entity['text']);
            endforeach;
        }
        return new JsonResponse($items);
    }

    public function selectMedicineFormAction()
    {

        $entities = $this->getDoctrine()->getRepository('MedicineBundle:MedicineBrand')->selectMedicineFormAutoComplete();
        $items = array();
        $items[]=array('value' => '','text'=> '-MedicineForm-');
        foreach ($entities as $entity):
            $items[]=array('value' => $entity['id'],'text'=> $entity['id']);
        endforeach;
        return new JsonResponse($items);

    }

    public function selectStrengthAction()
    {
        $entities = $this->getDoctrine()->getRepository('MedicineBundle:MedicineBrand')->selectStrengthAutoComplete();
        $items = array();
        $items[]=array('value' => '','text'=> '-Strength-');
        foreach ($entities as $entity):
            $items[]=array('value' => $entity['id'],'text'=> $entity['id']);
        endforeach;
        return new JsonResponse($items);

    }

    public function inlineItemUpdateAction(Request $request)
    {
        $data = $request->request->all();
        $em = $this->getDoctrine()->getManager();
        $entity = $this->getDoctrine()->getRepository('MedicineBundle:MedicineBrand')->find($data['pk']);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PurchaseItem entity.');
        }

        if($data['name'] == 'MedicineForm'){

            $exist = $this->getDoctrine()->getRepository('MedicineBundle:MedicineBrand')->findOneBy(array('name'=>$entity->getName(),'strength'=> $entity->getStrength() , 'medicineForm'=>$data['value']));
            if(empty($exist)){
                $entity->setMedicineForm($data['value']);
            }

        }elseif($data['name'] == 'Strength'){

            $exist = $this->getDoctrine()->getRepository('MedicineBundle:MedicineBrand')->findOneBy(array('name'=>$entity->getName(),'strength'=> $data['value'] , 'medicineForm'=> $entity->getMedicineForm()));
            if(empty($exist)){
                $entity->setStrength($data['value']);
            }

        }else{

            $setValue = "set{$data['name']}";
            $entity->$setValue($data['value']);

        }
        $em->persist($entity);
        $em->flush();
        return new Response('success');

    }


}
