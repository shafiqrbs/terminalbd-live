<?php

namespace Setting\Bundle\ContentBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class AdmissionType extends AbstractType
{

    private $user;
    private $branch;

    public function __construct($user,$branch)
    {
        $this->user = $user;
        $this->branch = $branch;
    }
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter title'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required')),

                )
            ))
            ->add('contactPerson','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter contact person'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required')),

                )
            ))
            ->add('mobile','text', array('attr'=>array('class'=>'m-wrap span12 mobile','placeholder'=>'Enter mobile no'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required')),

                )
            ))
            ->add('phone','text', array('attr'=>array('class'=>'m-wrap span12 phone')))
            ->add('email','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter email address'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required')),

                )
            ))
            ->add('isPayment')
            ->add('isOnlineRegistration')
            ->add('shifting', 'choice', array(
                'multiple'      =>true,
                'expanded'      =>true,
                'attr'=>array('class'=>''),
                'choices' => array('Morning' => 'Morning',  'Afternoon' => 'Afternoon', 'Evening' => 'Evening'),
            ))
            ->add('tuitionFee','text', array('attr'=>array('class'=>'m-wrap span12 ')))
            ->add('classDuration','text', array('attr'=>array('class'=>'m-wrap span12 ')))
            ->add('coursePeriod','text', array('attr'=>array('class'=>'m-wrap span12 ')))
            ->add('qualification','text', array('attr'=>array('class'=>'m-wrap span12 ')))

            ->add('content','textarea', array('attr'=>array('class'=>'wysihtml5 m-wrap span12','rows'=>8)))
            ->add('address','text', array('attr'=>array('class'=>'m-wrap span12')))
            ->add('file','file', array('attr'=>array('class'=>'default')))
            ->add('startDate','date', array('attr'=>array('class'=>'selectbox')))
            ->add('startHour','text', array('attr'=>array('class'=>'m-wrap small clockface_1 span10')))
            ->add('endHour','text', array('attr'=>array('class'=>'m-wrap small clockface_1 span10')))
            ->add('endDate','date', array('attr'=>array('class'=>'selectbox')))
            ->add('courseLevel', 'entity', array(
                'required'      => false,
                'class'         => 'Setting\Bundle\ToolBundle\Entity\CourseLevel',
                'empty_value' => '---Select course level---',
                'property'      => 'name',
                'attr'          =>array('class'=>'m-wrap span10 selectbox'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('c')
                        ->andWhere("c.status = 1")
                        ->orderBy('c.id','ASC');
                }
            ))
            ->add('course', 'entity', array(
                'required'      => false,
                'class'         => 'Setting\Bundle\ToolBundle\Entity\Course',
                'property'      => 'name',
                'empty_value' => '---Select course---',
                'attr'          =>array('class'=>'selectbox span10'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('c')
                        ->andWhere("c.status = 1")
                        ->orderBy('c.id','ASC');
                }
            ))
            ->add('isPromotion')
            ->add('status');
        if($this->branch == 'branch') {

            $builder->add('branches', 'entity', array(
                'required' => false,
                'multiple' => true,
                'expanded' => true,
                'class' => 'Setting\Bundle\ContentBundle\Entity\Branch',
                'property' => 'name',
                'attr' => array('class' => ''),
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('b')
                        ->where("b.status = 1")
                        ->andWhere("b.user = $this->user ");
                }
            ));
        }


    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Setting\Bundle\ContentBundle\Entity\Admission'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'setting_bundle_contentbundle_admission';
    }
}
