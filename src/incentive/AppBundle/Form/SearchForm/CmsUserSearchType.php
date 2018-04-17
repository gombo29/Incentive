<?php

namespace incentive\AppBundle\Form\SearchForm;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CmsUserSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('username', 'text', array(
                'required' => false,
                'attr' => array(
                    "class" => "form-control",
                )
            ));
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
