<?php
/**
 * Created by PhpStorm.
 * User: thibaulthenry
 * Date: 01/08/2016
 * Time: 17:53
 */

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClusterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("name", TextType::class, array(
                'label' => 'cluster.name'
            ))
            ->add("presentation", TextareaType::class, array(
                'label' => 'cluster.presentation'
            ))
            ->add('opened', ChoiceType::class, array(
                'label' => 'cluster.opened.name',
                'choices' => array(
                    'cluster.opened.1' => true,
                    'cluster.opened.0' => false
                )
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Cluster',
        ));
    }

    public function getName() {
        return 'cluster';
    }
}
