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

class PlanType extends AbstractType
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

            ->add('startDate', 'datetime', array(
                'label' => 'Төлөвлөгөө авч эхлэх хугацаа',
                'required' => true,
                'format' => 'yyyy-MM-dd HH:mm',
                'widget' => 'single_text',
                'attr' => [ 'datetime' => 'picker']

            ))

            ->add('endDate', 'datetime', array(
                'label' => 'Төлөвлөгөө авч дуусах хугацаа',
                'required' => true,
                'format' => 'yyyy-MM-dd HH:mm',
                'widget' => 'single_text',
                'attr' => [ 'datetime' => 'picker']
            ))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'incentive\AppBundle\Entity\Plan',
            'em' => null,
        ));
    }
}