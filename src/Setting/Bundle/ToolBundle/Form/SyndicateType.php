<?php

namespace Setting\Bundle\ToolBundle\Form;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Setting\Bundle\ToolBundle\Repository\SyndicateRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class SyndicateType extends AbstractType
{

    /** @var  SyndicateRepository */

    private $em;

    function __construct(SyndicateRepository $em)
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
                    new Length(array('max'=>200))
                )
            ))
            ->add('entityName','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter name'),
                'constraints' =>array(
                    new Length(array('max'=>200))
                )
            ))

            ->add('domainProperty', 'choice', array(
                'attr'=>array('class'=>' m-wrap span10 selectbox'),
                'empty_value' => '--- Select domain property ---',
                'choices' => array(
                    'application'      => 'Application',
                    'businesses'      => 'Businesses',
                    'education'     => 'Education',
                    'ecommerce'     => 'Ecommerce',
                    'food'          => 'Food',
                    'medical'       => 'Medical',
                    'property'      => 'Property',
                    'reservation'   => 'Reservation',
                    'service'       => 'Service',
                    'transport'     => 'Transport',
                    'others'         => 'Others'
                ),
            ))


     /*       ->add('parent', 'entity', array(
                'required'    => true,
                'empty_value' => '--- Select parent ---',
                'attr'=>array('class'=>'location m-wrap span10 selectbox'),
                'class' => 'SettingToolBundle:Syndicate',
                'property' => 'nestedLabel',
                'choices'=> $this->SyndicateChoiceList()
            ))*/

            ->add('parent', 'entity', array(
                'required'      => false,
                'empty_value' => '--- Select parent sector ---',
                'multiple'      =>false,
                'expanded'      =>false,
                'class'         => 'SettingToolBundle:Syndicate',
                'property'      => 'name',
                'attr'          =>array('class'=>'m-wrap span12'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('d')
                        ->where("d.level = 1")
                        ->orderBy('d.name','ASC');
                }
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
            'data_class' => 'Setting\Bundle\ToolBundle\Entity\Syndicate'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'setting_bundle_toolbundle_syndicate';
    }

    /**
     * @return mixed
     */
    protected function SyndicateChoiceList()
    {
        return $locationTree = $this->em->getFlatSyndicateTree();

    }

}
