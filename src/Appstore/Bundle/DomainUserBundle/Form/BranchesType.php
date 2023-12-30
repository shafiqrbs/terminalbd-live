<?php

namespace Appstore\Bundle\DomainUserBundle\Form;

use Doctrine\ORM\EntityRepository;
use Setting\Bundle\LocationBundle\Repository\LocationRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class BranchesType extends AbstractType
{

    /** @var GlobalOption */
    public  $globalOption;

    /** @var  LocationRepository */
    private $location;

    function __construct(GlobalOption $globalOption, LocationRepository $location)
    {
        $this->globalOption = $globalOption;
        $this->location = $location;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Branch name'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please enter branch name '))
            )))
            ->add('address','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Branch address'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please enter  branch address'))
            )))
            ->add('mobile','text', array('attr'=>array('class'=>'m-wrap span12 mobile','placeholder'=>'Add  mobile no'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please enter  mobile no'))
            )))
            ->add('email','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Add  email address')))
            ->add('branchManager', 'entity', array(
                'expanded'      =>false,
                'multiple'      =>false,
                'required'    => true,
                'class' => 'Core\UserBundle\Entity\User',
                'property' => 'username',
                'attr'=>array('class'=>'span12 select2'),
                'empty_value' => '---Choose assign manager ---',
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('u')
                        ->andWhere("u.globalOption =".$this->globalOption->getId())
                        ->orderBy("u.username", "ASC");
                }
            ))
            ->add('location', 'entity', array(
                'required'    => false,
                'empty_value' => '---Select Location---',
                'attr'=>array('class'=>'select2 span12'),
                'class' => 'Setting\Bundle\LocationBundle\Entity\Location',
                'constraints' =>array(
                    new NotBlank(array('message'=>'Select user location'))
                ),
                'choices'=> $this->LocationChoiceList(),
                'choices_as_values' => true,
                'choice_label' => 'nestedLabel',
            ))
            ->add('status');
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\DomainUserBundle\Entity\Branches'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appstore_bundle_domainUserbundle_branches';
    }

    protected function LocationChoiceList()
    {
        return $syndicateTree = $this->location->getLocationOptionGroup();

    }
}
