<?php

namespace App\Form;
use App\Entity\Issue;
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
                'attr' => ['placeholder' => 'entrez le nom de votre issue',
                            'class' => 'form-control']
            ])
            ->add('description', TextareaType::class, [
                'attr' => ['placeholder' => 'Description de votre issue',
                            'class' => 'form-control']

            ])
            ->add('difficulty', IntegerType::class, [
                'attr' => ['placeholder' => 'Difficulté de votre issue',
                            'class' => 'form-control']
            ])
            ->add('priority',ChoiceType::class, [
                'choices' => ['HAUT'=> 'HAUT',
                                'MEDIUM'=>'MEDIUM',
                                    'BAS'=>'BAS'],
                'preferred_choices' => ['HAUT'],

            ])

            ->add('status',ChoiceType::class, [
                'choices' => ['TODO'=>'TODO', 'DOING'=>'DOING', 'DONE'=>'DONE'],
                'preferred_choices' => ['TODO'],

    ]);
    }

   public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([

        ]);
    }
}