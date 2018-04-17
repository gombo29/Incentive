<?php
/**
 * Created by PhpStorm.
 * User: gombo
 * Date: 1/9/18
 * Time: 21:02
 */

namespace incentive\AppBundle\Form;

use Doctrine\Common\Persistence\ObjectManager;
use incentive\AppBundle\Entity\Branch;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class JobTitleType extends AbstractType
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

            ->add('point', 'number', array(
                'label' => 'Оноо',
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
            'data_class' => 'incentive\AppBundle\Entity\JobTitle',
            'em' => null,
        ));
    }
}