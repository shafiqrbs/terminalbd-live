<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Core\UserBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserFormType extends AbstractType
{

    private $newEntry;

    /**
     * @param bool   $newEntry
     */
    public function __construct($newEntry = true)
    {
        $this->newEntry = $newEntry;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fullName', 'text', array('class' => 'm-wrap placeholder-no-fix'));

        if($this->newEntry){
            $builder
                ->add('username', null, array('label' => 'form.username', 'translation_domain' => 'FOSUserBundle'))
                ->add('plainPassword', 'repeated', array(
                    'type' => 'password',
                    'options' => array('translation_domain' => 'FOSUserBundle'),
                    'first_options' => array('label' => 'form.password'),
                    'second_options' => array('label' => 'form.password_confirmation'),
                    'invalid_message' => 'fos_user.password.mismatch',
                ));
        }

        $builder
            ->add('email', 'email', array('label' => 'form.email', 'translation_domain' => 'FOSUserBundle'))
            ->add('groups', 'entity', array(
                'class' => 'Core\UserBundle\Entity\Group',
                'query_builder' => function(EntityRepository $er) {
                        return $er->createQueryBuilder('g')
                            ->andWhere("g.name != :group")
                            ->setParameter('group', 'Super Administrator');
                    },
                'property' => 'name',
                'multiple' => true,
            ))
            ->add('profile', new ProfileFormType())
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Core\UserBundle\Entity\User',
            'validation_groups'  => 'registration',
        ));
    }

    public function getName()
    {
        return 'manage_user';
    }
}
