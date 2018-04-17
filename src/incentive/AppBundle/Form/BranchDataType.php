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

class BranchDataType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {


        $builder


            ->add('timeFund', 'text', array(
                'label' => 'Цагийн фонд',
                'required' => true,
                'attr' => array(
                    "class" => "form-control",
                )
            ))

            ->add('relaxTime', 'text', array(
                'label' => 'Амралттай байсан цаг',
                'required' => true,
                'attr' => array(
                    "class" => "form-control",
                )
            ))


            ->add('valueNumber', 'text', array(
                'label' => ' 	Төлөвлөгөөний %',
                'required' => true,
                'attr' => array(
                    "class" => "form-control",
                )
            ))

            ->add('valuePercent', 'text', array(
                'label' => 'Төлөвлөгөөний тоо ширхэг',
                'required' => true,
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
            'data_class' => 'incentive\AppBundle\Entity\BranchData',
            'em' => null,
        ));
    }
}