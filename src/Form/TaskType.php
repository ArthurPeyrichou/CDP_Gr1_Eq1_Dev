<?php

namespace App\Form;

use App\Entity\Issue;
use App\Entity\Member;
use App\Entity\Project;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaskType extends AbstractType
{
    public const PROJECT = 'project';

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /**@var $project Project*/
        $project = $options[self::PROJECT];

        $builder
            ->add('number', IntegerType::class, [
                'label' => 'Numéro',
                'attr' => [
                    'placeholder' => 'Numéro de votre tâche'
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => [
                    'placeholder' => 'Description de votre tâche'
                ]
            ])
            ->add('requiredManDays', IntegerType::class, [
                'label' => 'Estimation',
                'attr' => [
                    'placeholder' => 'Estimation de travail en j/h'
                ]
            ])
            ->add('developper', EntityType::class, [
                'label' => 'Assigné à',
                'class' => Member::class,
                'choices' => array_merge($project->getMembers()->toArray(), [$project->getOwner()])
            ])
            ->add('relatedIssues', EntityType::class, [
                'label' => 'Issues associées',
                'class' => Issue::class,
                'choices' => $project->getIssues(),
                'multiple' => true
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
