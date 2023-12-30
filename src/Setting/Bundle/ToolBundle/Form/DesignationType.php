<?php

namespace Setting\Bundle\ToolBundle\Form;

use Doctrine\ORM\EntityRepository;
use Setting\Bundle\ToolBundle\Repository\DesignationRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class DesignationType extends AbstractType
{
    /** @var  DesignationRepository */
    private $em;


    function __construct(DesignationRepository $em )
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

            ->add('name','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter expense category name'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required')),

                )
            ))
            ->add('parent', 'entity', array(
                'required'    => true,
                'empty_value' => '---Select Parent Designation---',
                'attr'=>array('class'=>'ExpenseCategory m-wrap span12 select2'),
                'class' => 'Setting\Bundle\ToolBundle\Entity\Designation',
                'property' => 'nestedLabel',
                'choices'=> $this->DesignationChoiceList()
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
            'data_class' => 'Setting\Bundle\ToolBundle\Entity\Designation'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'setting_bundle_toolbundle_designation';
    }

    /**
     * @return mixed
     */
    protected function DesignationChoiceList()
    {
        return $this->em->getFlatDesignationTree();

    }



}
