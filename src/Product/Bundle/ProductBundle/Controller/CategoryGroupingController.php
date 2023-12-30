<?php

namespace Product\Bundle\ProductBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Product\Bundle\ProductBundle\Entity\CategoryGrouping;
use Product\Bundle\ProductBundle\Form\CategoryGroupingType;

/**
 * CategoryGrouping controller.
 *
 */
class CategoryGroupingController extends Controller
{

    /**
     * Lists all CategoryGrouping entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->get('security.context')->getToken()->getUser();
        $grouping = $em->getRepository('ProductProductBundle:CategoryGrouping')->findOneBy(array('user'=>$user));
        $array = array();
        if($grouping){

            $groups = $grouping->getCategories();
            foreach($groups as $row ){
                $array[] = $row->getId();
            }
        }

        $categories = $em->getRepository('ProductProductBundle:Category')->findBy(array('status'=>1,'parent'=>NULL),array('name'=>'asc'));
        if($categories)

            $entities = $this->getDoctrine()->getRepository('ProductProductBundle:Category')->getGroupCategories($categories,$array);
            $entity = new CategoryGrouping();
            $form   = $this->createCreateForm($entity);

        return $this->render('ProductProductBundle:CategoryGrouping:index.html.twig', array(
            'entities' => $entities,
            'form'   => $form->createView(),
        ));

    }

    /**
     * Creates a new CategoryGrouping entity.
     *
     */

    public function createAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->get('security.context')->getToken()->getUser();

        $entity = $em->getRepository('ProductProductBundle:CategoryGrouping')->findOneBy(array('user'=>$user));
        if($entity){
            $entity = $entity;
        }else{
            $entity = new CategoryGrouping();
        }

        $data = $request->request->get('categories');
        $array = array();
        foreach($data as $row){
            $array[] = $em->getRepository('ProductProductBundle:Category')->find($row);
        }

        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {

            $entity->setUser($user);
            $entity->setCategories($array);
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('categorygrouping'));
        }

        return $this->render('ProductProductBundle:CategoryGrouping:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a CategoryGrouping entity.
     *
     * @param CategoryGrouping $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(CategoryGrouping $entity)
    {

        $form = $this->createForm(new CategoryGroupingType(), $entity, array(
            'action' => $this->generateUrl('categorygrouping_create', array('id' => $entity->getId())),
            'method' => 'POST',
            'attr' => array(
                'class' => 'form-horizontal',
                'novalidate' => 'novalidate',
            )
        ));

        return $form;


    }

    /**
     * Displays a form to create a new CategoryGrouping entity.
     *
     */
    public function newAction()
    {
        $entity = new CategoryGrouping();
        $form   = $this->createCreateForm($entity);

        return $this->render('ProductProductBundle:CategoryGrouping:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a CategoryGrouping entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ProductProductBundle:CategoryGrouping')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find CategoryGrouping entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ProductProductBundle:CategoryGrouping:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing CategoryGrouping entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ProductProductBundle:CategoryGrouping')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find CategoryGrouping entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ProductProductBundle:CategoryGrouping:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a CategoryGrouping entity.
    *
    * @param CategoryGrouping $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(CategoryGrouping $entity)
    {
        $form = $this->createForm(new CategoryGroupingType(), $entity, array(
            'action' => $this->generateUrl('categorygrouping_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing CategoryGrouping entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ProductProductBundle:CategoryGrouping')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find CategoryGrouping entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('categorygrouping_edit', array('id' => $id)));
        }

        return $this->render('ProductProductBundle:CategoryGrouping:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a CategoryGrouping entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('ProductProductBundle:CategoryGrouping')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find CategoryGrouping entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('categorygrouping'));
    }

    /**
     * Creates a form to delete a CategoryGrouping entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('categorygrouping_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
