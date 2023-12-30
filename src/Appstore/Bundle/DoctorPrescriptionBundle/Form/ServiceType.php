<?php

namespace Appstore\Bundle\DoctorPrescriptionBundle\Form;

use Appstore\Bundle\HospitalBundle\Entity\Category;
use Appstore\Bundle\HospitalBundle\Repository\CategoryRepository;
use Appstore\Bundle\HospitalBundle\Repository\HmsCategoryRepository;
use Doctrine\ORM\EntityRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class ServiceType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('name','text', array('attr'=>array('class'=>'m-wrap span12','autocomplete'=>'off','placeholder'=>'Enter service name'),
                    'constraints' =>array(
                        new NotBlank(array('message'=>'Please enter particular name'))
                    ))
            )
            ->add('serviceFormat', 'choice', array(
                'attr'=>array('class'=>'span12 m-wrap select-custom'),
                'expanded'      =>false,
                'multiple'      =>false,
                'empty_value' => '---Choose service format---',
                'constraints' =>array(
                    new NotBlank(array('message'=>'Select service format'))
                ),
                'choices' => array(
                    'label-value' => 'Label Value',
                    'text-field' => 'Text Field',
                    'checkbox-text-field' => 'Checkbox with Text Field',
                    'checkbox' => 'Checkbox',
                    'textarea' => 'Text-area',
                    'file' => 'File Upload'
                ),
            ))
            ->add('status')
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\DoctorPrescriptionBundle\Entity\DpsService'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appstore_bundle_dps_service';
    }


}
