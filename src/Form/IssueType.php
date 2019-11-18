<?php

namespace App\Form;

use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class IssueType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom',
                'attr' => [
                    'placeholder' => 'Nom de votre issue',
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => [
                    'placeholder' => 'Description de votre issue',
                ]

            ])
            ->add('difficulty', IntegerType::class, [
                'label' => 'Difficulté',
                'attr' => [
                    'placeholder' => 'Difficulté de votre issue',
                ]
            ])
            ->add('priority',ChoiceType::class, [
                'label' => 'Priorité',
                'choices' => [
                    'HAUT'=> 'HAUT',
                    'MEDIUM'=>'MEDIUM',
                    'BAS'=>'BAS'
                ],
                'preferred_choices' => ['HAUT'],

            ])
            ->add('status',ChoiceType::class, [
                'label' => 'Statut',
                'choices' => [
                    'TODO'=>'TODO',
                    'DOING'=>'DOING',
                    'DONE'=>'DONE'
                ],
                'preferred_choices' => ['TODO'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([

        ]);
    }
}
