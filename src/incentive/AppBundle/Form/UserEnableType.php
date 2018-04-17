<?php

namespace incentive\AppBundle\Form;

use Doctrine\ORM\EntityManagerInterface;
use incentive\AppBundle\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserEnableType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('enabled', 'choice',
                array(
                    'choices' => array(
                        '1' => 'Нээлттэй',
                        '0' => 'Хаалттай',
                    ),
                    'label' => 'Төлөв өөрчлөх',
                    'required' => false,
                    'attr' => array(
                        "class" => "form-control",
                    )
                )
            );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'incentive\AppBundle\Entity\CmsUser',
            'em' => null,
        ));
    }
}
