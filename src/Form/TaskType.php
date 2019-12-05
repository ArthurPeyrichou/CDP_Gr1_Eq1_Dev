<?php

namespace App\Form;

use App\Entity\Issue;
use App\Entity\Member;
use App\Entity\Project;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaskType extends AbstractType
{
    public const PROJECT = 'project';
    public const SPRINT = 'sprint';

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /**@var $project Project*/
        $project = $options[self::PROJECT];

        /**@var $sprint Project*/
        $sprint = $options[self::SPRINT];

        $builder
            ->add('number', IntegerType::class, [
                'label' => 'Numéro',
                'disabled' => true
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => [
                    'placeholder' => 'Description de votre tâche',
                    'rows' => 4
                ]
            ])
            ->add('requiredManDays', NumberType::class, [
                'label' => 'Estimation',
                'attr' => [
                    'placeholder' => 'Estimation de travail en j/h',
                    'min' => 0,
                    'step' => 0.1
                ]
            ])
            ->add('developper', EntityType::class, [
                'label' => 'Assigné à',
                'class' => Member::class,
                'choices' => $project->getMembersAndOwner(),
                'choice_label' => function (Member $member) {
                    return "{$member->getName()} - {$member->getEmailAddress()}";
                },
                'required' => false
            ])
            ->add('relatedIssues', EntityType::class, [
                'label' => 'Issues associées',
                'class' => Issue::class,
                'choices' => $sprint->getIssues(),
                'multiple' => true,
                'choice_label' => function (Issue $issue) {
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
        $resolver->setRequired([self::SPRINT]);
    }
}
