<?php
/**
 * Created by PhpStorm.
 * User: gombo
 * Date: 1/9/18
 * Time: 21:02
 */

namespace incentive\AppBundle\Form\SearchForm;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BranchSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {


        $builder
//            ->add('parent', 'entity', array(
//                'label' => 'Дээд ангилал',
//                'class' => 'incentive\AppBundle\Entity\Branch',
//                'property' => 'name',
//                'required' => false,
//            ))

            ->add('name', 'text', array(
                'label' => 'Нэр',
                'required' => false,
                'attr' => array(
                    "class" => "form-control",
                )
            ))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'incentive\AppBundle\Entity\Branch',
            'em' => null,
        ));
    }
}