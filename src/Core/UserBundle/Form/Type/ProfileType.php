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
use Setting\Bundle\LocationBundle\Repository\LocationRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


class ProfileType extends AbstractType
{


    /** @var  LocationRepository */
    private $location;


    function __construct( LocationRepository $location)
    {
        $this->location = $location;
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
           $builder
                ->add('name','text', array('required' => false,'attr'=>array('class'=>'m-wrap span12 form-control','placeholder'=>'Enter your full name'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Enter your full name required')),
                    new Length(array('max'=>200))
                    ))
                )
               ->add('mobile','text', array('attr'=>array('class'=>'mobile form-control','placeholder'=>'Enter your mobile no'),
                       'constraints' =>array(
                           new NotBlank(array('message'=>'Enter your mobile no required')),
                           new Length(array('max'=>200))
                       ))
               )
               /*->add('location', 'entity', array(
                   'required'    => false,
                   'empty_value' => '---Select Location---',
                   'attr'=>array('class'=>'select-location span12 m-wrap form-control'),
                   'class' => 'Setting\Bundle\LocationBundle\Entity\Location',
                   'constraints' =>array(
                       new NotBlank(array('message'=>'Select user location'))
                   ),
                   'choices'=> $this->LocationChoiceList(),
                   'choices_as_values' => true,
                   'choice_label' => 'nestedLabel',
               ))*/
                ->add('address','text', array('required' => false,'attr'=>array('class'=>'m-wrap span12 form-control','placeholder'=>'Enter your full address')))
                ->add('termsConditionAccept','checkbox', array(
                    'attr'=>array('class'=>''),
                    'constraints' =>array(
                        new NotBlank(array('message'=>'Must need to accept terms & condition')),
                    )
                ));

    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Core\UserBundle\Entity\Profile'
        ));
    }

    public function getName()
    {
        return 'manage_profile';
    }
    protected function LocationChoiceList()
    {
        return $syndicateTree = $this->location->getLocationOptionGroup();

    }
}
