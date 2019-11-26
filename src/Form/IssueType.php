<?php

namespace App\Form;

use App\Entity\Issue;
use App\Entity\Sprint;
use App\Entity\Project;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class IssueType extends AbstractType
{
    public const PROJECT = 'project';

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /**@var $project Project*/
        $project = $options[self::PROJECT];

        $builder
            ->add('number', IntegerType::class, [
                'label' => 'Numéro',
                'disabled' => true
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => [
                    'placeholder' => "En tant que\nJe souhaite\nAfin de",
                    'rows' => 4
                ]

            ])
            ->add('difficulty', IntegerType::class, [
                'label' => 'Difficulté',
                'attr' => [
                    'placeholder' => 'Difficulté de votre issue',
                    'min' => 0
                ]
            ])
            ->add('priority',ChoiceType::class, [
                'label' => 'Priorité',
                'choices' => [
                    'High' => Issue::PRIORITY_HIGH,
                    'Medium' => Issue::PRIORITY_MEDIUM,
                    'Low' => Issue::PRIORITY_LOW
                ]

            ])
            ->add('status',ChoiceType::class, [
                'label' => 'Statut',
                'choices' => [
                    'ToDo' => Issue::TODO,
                    'Doing' => Issue::DOING,
                    'Done' => Issue::DONE
                ],
                'preferred_choices' => [
                    Issue::TODO
                ]
            ])
            ->add('sprint', EntityType::class, [
                'label' => 'Sprints associées',
                'class' => Sprint::class,
                'choices' => $project->getSprints(),
                'choice_label' => function (Sprint $issue) {
                    return "{$issue->getNumber()} - {$issue->getDescription()}";
                }
            ])
            ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);

        $resolver->setRequired([self::PROJECT]);
    }
}
