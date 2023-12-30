<?php

namespace Appstore\Bundle\InventoryBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class VendorType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder

            ->add('name','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter vendor name')))
            ->add('mobile','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter vendor mobile no')))
            ->add('companyName','text', array('attr'=>array('class'=>'m-wrap span9','placeholder'=>'Enter company name'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required')))
            ))
            ->add('address','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter vendor address')))
            ->add('email','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter email address')))
            ->add('country', 'entity', array(
                'required'      => true,
                'expanded'      =>false,
                'placeholder' => 'Choose a country',
                'class' => 'Setting\Bundle\LocationBundle\Entity\Country',
                'property' => 'name',
                'attr'=>array('class'=>'span12 select2'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('m')
                        ->orderBy('m.name','ASC');
                },
            ))
            ->add('vendorCode','text', array('attr'=>array('class'=>'m-wrap span3','placeholder'=>'Short code')
            ,'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required')))
            ))
            ->add('status');
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\InventoryBundle\Entity\Vendor'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appstore_bundle_inventorybundle_vendor';
    }
}
