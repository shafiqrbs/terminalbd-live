<?php

namespace Setting\Bundle\ContentBundle\Form;
use Doctrine\ORM\EntityRepository;
use Setting\Bundle\ToolBundle\Entity\GlobalOption;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class SiteContentType extends AbstractType
{

	private $globalOption;

	public function __construct(GlobalOption $globalOption)
	{
		$this->globalOption = $globalOption;
	}


	/**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name','text', array('attr'=>array('class'=>'m-wrap span12','placeholder'=>'Enter Name'),
                'constraints' =>array(
                    new NotBlank(array('message'=>'Please input required')),
                    new Length(array('max'=>200))
                )
            ))
            ->add('file','file', array('attr'=>array('class'=>'default')))
            ->add('content','textarea', array('attr'=>array('class'=>'ckeditor m-wrap span12','rows'=>15)))
	        ->add('businessSector', 'choice', array(
		        'attr'=>array('class'=>'m-wrap span12'),
		        'choices' => array('portal' => 'Portal','education' => 'Education',  'ecommerce' => 'E-commerce', 'reservation' => 'Reservation', 'fleet' => 'Fleet Management'),
	        ))

	        ->add('parent', 'entity', array(
                'required'    => false,
                'class' => 'Setting\Bundle\ContentBundle\Entity\SiteContent',
                'empty_value' => '---Select parent page---',
                'property' => 'name',
                'attr'=>array('class'=>'m-wrap span12'),
                'query_builder' => function(EntityRepository $er){
                        return $er->createQueryBuilder('o')
                            ->andWhere("o.status = 1")
                            ->orderBy('o.name','ASC');
                    },
            ))
	        ->add('photo_gallery', 'entity', array(
		        'required'    => false,
		        'class' => 'Setting\Bundle\MediaBundle\Entity\PhotoGallery',
		        'empty_value' => '---Select Photo Gallery---',
		        'property' => 'name',
		        'attr'=>array('class'=>'select2 span12'),
		        'query_builder' => function(EntityRepository $er){
			        return $er->createQueryBuilder('o')
			                  ->where("o.status = 1")
			                  ->andWhere("o.globalOption =".$this->globalOption->getId())
			                  ->orderBy('o.name','ASC');
		        },
	        ))

	        ->add('isPage')
            ->add('status');
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Setting\Bundle\ContentBundle\Entity\SiteContent'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'setting_bundle_contentbundle_sitecontent';
    }
}
