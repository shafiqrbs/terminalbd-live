<?php

namespace Appstore\Bundle\HospitalBundle\Form;

use Appstore\Bundle\HospitalBundle\Repository\CategoryRepository;
use Appstore\Bundle\HospitalBundle\Repository\HmsCategoryRepository;
use Doctrine\ORM\EntityRepository;
use Setting\Bundle\LocationBundle\Repository\LocationRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class ReferredDoctorType extends AbstractType
{

    /** @var  LocationRepository */
    private $location;

    /** @var  HmsCategoryRepository */
    private $emCategory;

    /** @var  GlobalOption */
    private $globalOption;


    function __construct(HmsCategoryRepository $emCategory , GlobalOption $globalOption, LocationRepository $location)
    {
        $this->location         = $location;
        $this->emCategory       = $emCategory;
        $this->globalOption     = $globalOption;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('name','text', array('attr'=>array('class'=>'m-wrap span12','autocomplete'=>'off','placeholder'=>'Enter Doctor/Reference name'),
                    'constraints' =>array(
                        new NotBlank(array('message'=>'Please enter doctor/reference name'))
                    ))
            )
            ->add('mobile','text', array('attr'=>array('class'=>'m-wrap span12 mobile','autocomplete'=>'off','placeholder'=>'Mobile no')))
            ->add('isDoctor')
            ->add('particularCode','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter referred code')))
            ->add('commission','text', array('attr'=>array('class'=>'m-wrap span12 numeric','autocomplete'=>'off','placeholder'=>'Commission no')))
            ->add('doctorSignature','text', array('attr'=>array('class'=>'m-wrap span12','autocomplete'=>'off','placeholder'=>'Doctor signature information')))
            ->add('address','textarea', array('attr'=>array('class'=>'m-wrap span12 ','rows' => 4,'placeholder'=>'Enter customer address')))
            ->add('location', 'entity', array(
                'required'    => false,
                'empty_value' => '---Select Location---',
                'attr'=>array('class'=>'select2 span12'),
                'class' => 'Setting\Bundle\LocationBundle\Entity\Location',
                'choices'=> $this->LocationChoiceList(),
                'choices_as_values' => true,
                'choice_label' => 'nestedLabel',
            ));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\HospitalBundle\Entity\Particular'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appstore_bundle_hospitalbundle_referred_doctor';
    }

    protected function LocationChoiceList()
    {
        return $this->location->getLocationOptionGroup();

    }

    /**
     * @return mixed
     */
    protected function PathologyChoiceList()
    {
        return $this->emCategory->getParentCategoryTree($parent = 2 /** Pathology */ );

    }
    /**
     * @return mixed
     */
    protected function DepartmentChoiceList()
    {
        return $this->emCategory->getParentCategoryTree($parent = 7 /** Department */);

    }
}
