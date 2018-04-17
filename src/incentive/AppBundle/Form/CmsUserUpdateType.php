<?php
/**
 * Created by PhpStorm.
 * User: gombo
 * Date: 1/9/18
 * Time: 21:02
 */

namespace incentive\AppBundle\Form;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use incentive\AppBundle\Entity\Branch;
use incentive\AppBundle\Entity\CmsUser;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CmsUserUpdateType extends AbstractType
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
            ->add('lastname', 'text', array(
                'label' => 'Овог нэр',
                'required' => false,
                'attr' => array(
                    "class" => "form-control",
                )
            ))

            ->add('firstname', 'text', array(
                'label' => 'Өөрийн нэр',
                'required' => false,
                'attr' => array(
                    "class" => "form-control",
                )
            ))

            ->add('email', 'email', array(
                'label' => 'Цахим шуудан',
                'required' => true,
                'attr' => array(
                    "class" => "form-control",
                )
            ))
            ->add('mobile', 'text', array(
                'label' => 'Утасны дугаар',
                'required' => true,
                'attr' => array(
                    "class" => "form-control",
                )
            ))


            ->add('cattmp', 'choice', array(
                'label' => 'Салбар, нэгж',
                'empty_value'=>'-- Сонгох --',
                'required' => false,
                'choices' => $entity->getChoiceArray($this->em, $this->remid),
            ))
        ;
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