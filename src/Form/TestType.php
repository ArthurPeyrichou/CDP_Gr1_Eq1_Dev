<?php

namespace App\Form;

use App\Entity\Test;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom',
                'attr' => [
                    'placeholder' => 'Nom de votre test',
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => [
                    'placeholder' => "Etant donné que\nQuand\nAlors\n",
                    'rows' => 4
                ]

            ])
            ->add('state',ChoiceType::class, [
                'label' => 'Statut',
                'choices' => [
                    'ToDo' => Test::TODO,
                    'Succeeded' => Test::SUCCEEDED,
                    'Failed' => Test::FAILED
                ],
                'preferred_choices' => [
                    Test::TODO
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([

        ]);
    }
}
