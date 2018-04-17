<?php
/**
 * Created by PhpStorm.
 * User: gombo
 * Date: 1/9/18
 * Time: 21:02
 */

namespace incentive\AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {


        $builder

            ->add('name', 'text', array(
                'label' => 'Нэр',
                'required' => true,
                'attr' => array(
                    "class" => "form-control",
                )
            ))

            ->add('branch', 'entity', array(
                'label' => 'Салбар, нэгж',
                'class' => 'incentive\AppBundle\Entity\Branch',
                'property' => 'name',
                'required' => false,
            ))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'incentive\AppBundle\Entity\User',
            'em' => null,
        ));
    }
}