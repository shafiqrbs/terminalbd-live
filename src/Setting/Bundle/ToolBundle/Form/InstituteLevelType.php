<?php

namespace Setting\Bundle\ToolBundle\Form;

use Setting\Bundle\ToolBundle\Repository\InstituteLevelRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class InstituteLevelType extends AbstractType
{


    /** @var  InstituteLevelRepository */

    private $em;

    function __construct(InstituteLevelRepository $em)
    {
        $this->em = $em;
    }


    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

            $builder
                ->add('name','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter name'),
                    'constraints' =>array(
                        new NotBlank(array('message'=>'Please input required')),
                    )
                ))
                ->add('parent', 'entity', array(
                    'required'    => true,
                    'empty_value' => '--- Select parent ---',
                    'attr'=>array('class'=>'location m-wrap span10 selectbox'),
                    'class' => 'SettingToolBundle:InstituteLevel',
                    'property' => 'nestedLabel',
                    'choices'=> $this->instituteChoiceList()
                ))

                ->add('status')
            ;

    }

    /**
     * @return mixed
     */
    protected function instituteChoiceList()
    {
        return $locationTree = $this->em->getFlatInstituteLevelTree();

    }


    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Setting\Bundle\ToolBundle\Entity\InstituteLevel'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'setting_bundle_toolbundle_institutelevel';
    }
}
