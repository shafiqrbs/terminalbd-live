<?php

namespace Setting\Bundle\ToolBundle\Form;

use Appstore\Bundle\InventoryBundle\Entity\InventoryConfig;
use Doctrine\ORM\EntityRepository;
use Product\Bundle\ProductBundle\Entity\CategoryRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class InvoiceType extends AbstractType
{


    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('globalOption', 'entity', array(
                    'required'    => false,
                    'class' => 'Setting\Bundle\ToolBundle\Entity\GlobalOption',
                    'empty_value' => '---Select Domain ---',
                    'property' => 'name',
                    'attr'     =>array('id' => '' , 'class' => 'm-wrap span12 select2'),
                    'query_builder' => function(EntityRepository $er){
                            return $er->createQueryBuilder('s')
                                ->andWhere("s.status = 1")
                                ->orderBy('s.name','ASC');
                        },
                ))
            ->add('name','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Client name')))
            ->add('billFor','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Client bill for'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please bill for'))
            )))
            ->add('address','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Client address')))
            ->add('mobile','text', array('attr'=>array('class'=>'m-wrap span12 mobile','placeholder'=>'Add mobile no')))
            ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Setting\Bundle\ToolBundle\Entity\InvoiceModule'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appstore_bundle_invoice_module';
    }

    /**
     * @return mixed
     */
    protected function categoryChoiceList()
    {

        return $categoryTree = $this->em->getCategoryOptions();

    }
}
