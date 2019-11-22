<?php

namespace App\Form;

use App\Entity\Sprint;

use App\Entity\Project;
use App\Entity\Release;
use Symfony\Component\Form\AbstractType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReleaseType extends AbstractType
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
                    'placeholder' => 'Le numero de votre release'
                ]
            ])

            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => [
                    'placeholder' => 'Description de votre release',
                    'rows' => 4
                ]
            ])
            ->add('date',DateType::class, [
                'label' => 'Date de release',
                'widget' => 'single_text',
            ])
            ->add('link',UrlType::class, [
                'label' => 'Lien vers l\'archive de release',
                'attr' => [
                    'placeholder' => 'Le lien vers votre release'
                ]
            ])
            ->add('sprint', EntityType::class, [
                'label' => 'Sprint associé',
                'class' => Sprint::class,
                'choices' => $project->getSprints(),
                'choice_label' => function (Sprint $sprint) {
                    return "{$sprint->getNumber()} - {$sprint->getDescription()}";
                }
            ]);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([

        ]);

        $resolver->setRequired([self::PROJECT]);

    }
}
