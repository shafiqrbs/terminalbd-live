<?php

namespace Appstore\Bundle\DomainUserBundle\Controller;
use Core\UserBundle\Form\DomainEditUserType;
use FOS\UserBundle\Model\UserManager;
use Core\UserBundle\Entity\User;
use Core\UserBundle\Form\DomainEditSignType;
use Core\UserBundle\Form\DomainSignType;
use Setting\Bundle\ToolBundle\Entity\Designation;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Appstore\Bundle\DomainUserBundle\Entity\DomainUser;


/**
 * DomainUser controller.
 *
 */
class DomainUserController extends Controller
{

    /**
     * Lists all DomainUser entities.
     *
     */
    public function indexAction()
    {

        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $option = $user->getGlobalOption();
        $entities = $this->getDoctrine()->getRepository('UserBundle:User')->getEmployeeEntities($option);
        return $this->render('DomainUserBundle:DomainUser:index.html.twig', array(
            'entities' => $entities,
        ));
    }


    /**
     * Creates a new DomainUser entity.
     *
     */
    public function createAction(Request $request)
    {

        $user = $this->getUser();
        $globalOption = $user->getGlobalOption();
        $entity = new User();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
       $data = $request->request->all();
       $password = $data['user']['plainPassword']['first'];
        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $entity->setGlobalOption($globalOption);
            $entity->getProfile()->upload();
            $entity->setDomainOwner(2);
            $entity->setUserGroup('user');
            $entity->setAppPassword($password);
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been added successfully"
            );
            return $this->redirect($this->generateUrl('domain_user'));
        }

        return $this->render('DomainUserBundle:DomainUser:new.html.twig', array(
            'globalOption' => $globalOption,
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a DomainUser entity.
     *
     * @param DomainUser $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(User $entity)
    {
        $user = $this->getDoctrine()->getRepository('UserBundle:User');
        $globalOption = $this->getUser()->getGlobalOption();
        $location = $this->getDoctrine()->getRepository('SettingLocationBundle:Location');
        $designation = $this->getDoctrine()->getRepository('SettingToolBundle:Designation');
        $form = $this->createForm(new DomainSignType($user,$globalOption,$location,$designation), $entity, array(
            'action' => $this->generateUrl('domain_create'),
            'method' => 'POST',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }

    /**
     * Displays a form to create a new DomainUser entity.
     *
     */
    public function newAction()
    {
        $entity = new User();
        $globalOption = $this->getUser()->getGlobalOption();
        $form   = $this->createCreateForm($entity);

        return $this->render('DomainUserBundle:DomainUser:new.html.twig', array(
            'globalOption' => $globalOption,
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a DomainUser entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('UserBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find DomainUser entity.');
        }
        return $this->render('DomainUserBundle:DomainUser:show.html.twig', array(
            'user'      => $entity,
        ));
    }

    /**
     * Displays a form to edit an existing DomainUser entity.
     *
     */
    public function editAction(User $user)
    {

        $em = $this->getDoctrine()->getManager();
        $editForm = $this->createEditForm($user);
        $globalOption = $user->getGlobalOption();
        return $this->render('DomainUserBundle:DomainUser:edit.html.twig', array(
            'globalOption' => $globalOption,
            'entity'      => $user,
            'form'   => $editForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a DomainUser entity.
     *
     * @param User $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(User $entity)
    {

        $user = $this->getDoctrine()->getRepository('UserBundle:User');
        $globalOption = $this->getUser()->getGlobalOption();
        $designation = $this->getDoctrine()->getRepository('SettingToolBundle:Designation');
        $form = $this->createForm(new DomainEditUserType($user,$globalOption,$designation), $entity, array(
            'action' => $this->generateUrl('domain_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }
    /**
     * Edits an existing DomainUser entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        /* @var $entity User */
        $entity = $em->getRepository('UserBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find DomainUser entity.');
        }
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            if($entity->getProfile()->upload() && !empty($entity->getProfile()->getFile())){
                $entity->getProfile()->removeUpload();
            }
            if($entity->getProfile()->signatureUpload() && !empty($entity->getProfile()->getSignatureFile())){
                $entity->getProfile()->removeSignatureUpload();
            }
            $entity->getProfile()->upload();
            $entity->getProfile()->signatureUpload();
            $em->flush();
            return $this->redirect($this->generateUrl('domain_edit', array('id' => $id)));
        }
        return $this->render('DomainUserBundle:DomainUser:edit.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),

        ));
    }

    /**
     * Deletes a DomainUser entity.
     *
     */
    public function deleteAction(User $user)
    {
        $em = $this->getDoctrine()->getManager();
        $user->setIsDelete(true);
        $user->setEnabled(false);
        $em->flush();
        $this->get('session')->getFlashBag()->add(
            'notice',"Data has been deleted successfully"
        );
        return $this->redirect($this->generateUrl('domain_user'));
    }



    /**
     * Creates a form to edit a DomainUser entity.
     *
     * @param User $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditProfileForm(User $entity)
    {

        $globalOption = $this->getUser()->getGlobalOption();
        $location = $this->getDoctrine()->getRepository('SettingLocationBundle:Location');
        $designation = $this->getDoctrine()->getRepository('SettingToolBundle:Designation');
        $form = $this->createForm(new DomainEditSignType($globalOption,$location,$designation), $entity, array(
            'action' => $this->generateUrl('domain_update_profile'),
            'method' => 'PUT',
            'attr' => array(
                'class' => 'horizontal-form',
                'novalidate' => 'novalidate',
            )
        ));
        return $form;
    }


    /**
     * Displays a form to edit an existing DomainUser entity.
     *
     */
    public function editProfileAction()
    {

        $user = $this->getUser();
        $editForm = $this->createEditProfileForm($user);
        $globalOption = $user->getGlobalOption();
        return $this->render('DomainUserBundle:DomainUser:profile.html.twig', array(
            'globalOption' => $globalOption,
            'entity'      => $user,
            'form'   => $editForm->createView(),
        ));
    }

    /**
     * Edits an existing DomainUser entity.
     *
     */
    public function updateProfileAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $this->getUser();

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find DomainUser entity.');
        }

        $editForm = $this->createEditProfileForm($entity);
        $editForm->handleRequest($request);
        if ($editForm->isValid()) {
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',"Data has been updated successfully"
            );
            return $this->redirect($this->generateUrl('domain_edit_profile'));
        }

        return $this->render('DomainUserBundle:DomainUser:profile.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),

        ));
    }


    public function autoSearchAction(Request $request)
    {
        $item = $_REQUEST['q'];
        if ($item) {
            $go = $this->getUser()->getGlobalOption();
            $item = $this->getDoctrine()->getRepository('UserBundle:User')->searchAutoComplete($item,$go);
        }
        return new JsonResponse($item);
    }

    public function autoSearchProfileNameAction(Request $request)
    {
        $item = $_REQUEST['q'];
        if ($item) {
            $go = $this->getUser()->getGlobalOption();
            $item = $this->getDoctrine()->getRepository('UserBundle:User')->searchAutoCompleteProfile($item,$go);
        }
        return new JsonResponse($item);
    }

    public function searchUserNameAction($user)
    {
        return new JsonResponse(array(
            'id' => $user,
            'text' => $user
        ));
    }

    public function forgetPasswordAction(User $user)
    {
        $password = '@123456';
        $user->setPlainPassword($password);
        $user->setAppPassword($password);
        $this->get('fos_user.user_manager')->updateUser($user);
        $this->get('session')->getFlashBag()->add(
            'success',"Password reset successfully"
        );
      //  $dispatcher = $this->container->get('event_dispatcher');
      //  $dispatcher->dispatch('setting_tool.post.change_password', new \Setting\Bundle\ToolBundle\Event\PasswordChangeSmsEvent($user,$password));
        return $this->redirect($this->generateUrl('domain_user'));
    }

  

}
