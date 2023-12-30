<?php

namespace Appstore\Bundle\HospitalBundle\Form;


use Appstore\Bundle\HospitalBundle\Entity\HospitalConfig;
use Appstore\Bundle\HospitalBundle\Entity\Particular;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class InvoiceParticularType extends AbstractType
{

    /** @var  HospitalConfig */
    private $hospitalConfig;



    function __construct(HospitalConfig $hospitalConfig)
    {
        $this->hospitalConfig  = $hospitalConfig;
    }




    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /* @var $particular Particular */
        $particular =  $options['data']->getParticular();
        $builder

            ->add('assignDoctor', 'entity', array(
                'required'    => false,
                'class' => 'Appstore\Bundle\HospitalBundle\Entity\Particular',
                'property' => 'reportDoctor',
                'empty_value' => '---Select Assign Doctor---',
                'attr'=>array('class'=>'span12 select2'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where('e.hospitalConfig ='.$this->hospitalConfig->getId())
                        ->andWhere("e.service = 5")
                        ->andWhere("e.status = 1")
                        ->orderBy("e.name","ASC");
                }
            ))
            ->add('assignLabuser', 'entity', array(
                'required'    => false,
                'class' => 'Appstore\Bundle\HospitalBundle\Entity\Particular',
                'property' => 'reportDoctor',
                'empty_value' => '---Select Assign Lab User---',
                'attr'=>array('class'=>'span12 select2'),
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where('e.hospitalConfig ='.$this->hospitalConfig->getId())
                        ->andWhere("e.service = 9")
                        ->andWhere("e.status = 1")
                        ->orderBy("e.name","ASC");
                }
            ))
            ->add('process', 'choice', array(
                'attr'=>array('class'=>'span12 select-custom'),
                'expanded'      =>false,
                'multiple'      =>false,
                'choices' => array('Done' => 'Done', 'In-progress' => 'In-progress','Damage' => 'Damage', 'Impossible' => 'Impossible'),
            ))
            ->add('comment','textarea', array('attr'=>array('class'=>'m-wrap span12','rows'=>3,'placeholder'=>'Add any comment','autocomplete'=>'off')))
        ;
        if($particular->isAttachment() == 1){
            $builder->add('file');
        }
        if($particular->isReportContent() == 1){
            $height = $particular->getReportHeight() > 0 ? $particular->getReportHeight():10;
            $builder->add('reportContent','textarea', array('attr'=>array('class'=>'editor m-wrap span12','rows' => $height,'placeholder'=>'Enter test related any instruction for patient')));
        }
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Appstore\Bundle\HospitalBundle\Entity\InvoiceParticular'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'invoiceParticular';
    }



}
