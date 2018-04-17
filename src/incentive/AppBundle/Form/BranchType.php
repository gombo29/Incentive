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

class BranchType extends AbstractType
{

    private $em;
    private $remid;

    public function __construct(ObjectManager $em, $remid)
    {
        $this->em = $em;
        $this->remid = $remid;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $entity = new Branch();

        $builder
            ->add('cattmp', 'choice', array(
                'label' => 'Дээд ангилал',
                'empty_value'=>'-- Сонгох --',
                'required' => false,
                'choices' => $entity->getChoiceArray($this->em, $this->remid),
            ))

            ->add('branchType', 'entity', array(
                'label' => 'Төрөл',
                'class' => 'incentive\AppBundle\Entity\BranchType',
                'property' => 'name',
                'required' => true,
            ))

            ->add('name', 'text', array(
                'label' => 'Нэр',
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
            'data_class' => 'incentive\AppBundle\Entity\Branch',
            'em' => null,
        ));
    }
}