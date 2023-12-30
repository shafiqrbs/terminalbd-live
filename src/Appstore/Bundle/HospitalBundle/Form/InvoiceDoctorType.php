<?php

namespace Appstore\Bundle\HospitalBundle\Form;

use Appstore\Bundle\HospitalBundle\Repository\CategoryRepository;
use Appstore\Bundle\HospitalBundle\Repository\HmsCategoryRepository;
use Doctrine\ORM\EntityRepository;
use Setting\Bundle\LocationBundle\Repository\LocationRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class InvoiceDoctorType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name','text', array('attr'=>array('class'=>'m-wrap span12','autocomplete'=>'off','placeholder'=>'Enter Doctor name'),'required'=> false))
            ->add('mobile','text', array('attr'=>array('class'=>'m-wrap span12 mobile','autocomplete'=>'off','placeholder'=>'Mobile no'),'required'=> false))
            ->add('doctorSignature','textarea', array('attr'=>array('class'=>'m-wrap span12','rows'=>3,'autocomplete'=>'off','placeholder'=>'Enter Doctor signature'),'required'=> false)
            )
            ->add('doctorSignatureBangla','textarea', array('attr'=>array('class'=>'m-wrap span12','rows'=>3,'autocomplete'=>'off','placeholder'=>'Enter Doctor signature'),'required'=> false)
            )
            ->add('specialist','textarea', array('attr'=>array('class'=>'m-wrap span12','rows'=>2,'autocomplete'=>'off','placeholder'=>'Enter Doctor signature'),'required'=> false)
            )
            ->add('sendToAccount',CheckboxType::class, array('attr'=> array('class'=>'custom-control-input'),'required'=> false))
        ;

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
        return 'doctor';
    }



}
