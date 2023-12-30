<?php

namespace Appstore\Bundle\BusinessBundle\Form;

use Doctrine\ORM\EntityRepository;
use Setting\Bundle\LocationBundle\Repository\LocationRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class BusinessStoreType extends AbstractType
{


    /** @var  $option GlobalOption  */
    public  $option;

    public function __construct(GlobalOption $option)
    {
        $this->option = $option;

    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('name','text', array('attr'=>array('class'=>'m-wrap span12','autocomplete'=>'off','placeholder'=>'Enter store name'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Enter store name'))
                ))
            )
            ->add('mobileNo','text', array('attr'=>array('class'=>'m-wrap span12','autocomplete'=>'off','placeholder'=>'Enter mobile no'),
                    'constraints' =>array(
                        new NotBlank(array('message'=>'Enter store name'))
                    ))
            )
            ->add('address','text', array('attr'=>array('class'=>'m-wrap span12','autocomplete'=>'off','placeholder'=>'Enter address'),
                    )
            )
            ->add('customer', 'entity', array(
                'required'    => true,
                'class' => 'Appstore\Bundle\DomainUserBundle\Entity\Customer',
                'property' => 'name',
                'empty_value' => '---Choose a DSR ---',
                'attr'=>array('class'=>'span12 m-wrap'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Enter DSR'))
                ),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.status = 1")
                        ->andWhere("e.globalOption ={$this->option->getId()}")
                        ->orderBy("e.name","ASC");
                }
            ))
            ->add('area', 'entity', array(
                'required'    => true,
                'class' => 'Appstore\Bundle\BusinessBundle\Entity\BusinessArea',
                'property' => 'name',
                'empty_value' => '---Choose a area ---',
                'attr'=>array('class'=>'span12 m-wrap'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Enter area'))
                ),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.status = 1")
                        ->andWhere("e.businessConfig ={$this->option->getBusinessConfig()->getId()}")
                        ->orderBy("e.name","ASC");
                }
            ))
            ->add('marketing', 'entity', array(
                'required'    => true,
                'class' => 'Appstore\Bundle\BusinessBundle\Entity\Marketing',
                'property' => 'name',
                'empty_value' => '---Choose a SM ---',
                'attr'=>array('class'=>'span12 m-wrap'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Enter SR'))
                ),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where("e.status = 1")
                        ->andWhere("e.businessConfig ={$this->option->getBusinessConfig()->getId()}")
                        ->orderBy("e.name","ASC");
                }
            ))


        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\BusinessBundle\Entity\BusinessStore'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'area';
    }

    protected function LocationChoiceList()
    {
        return $syndicateTree = $this->location->getLocationOptionGroup();

    }


}
