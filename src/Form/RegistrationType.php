<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => ['placeholder' => 'Votre pseudo'],
                'label' => 'Pseudo'
                ])
            ->add('emailAddress', EmailType::class, [
                'attr' => ['placeholder' => 'Votre adresse mail'],
                'label' => 'Adresse mail'
                ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => ['label' => 'Mot de passe',
                                    'attr' => ['placeholder' => 'Votre mot de passe']],
                'second_options' => ['label' => 'Confirmation mot de passe',
                                    'attr' => ['placeholder' => 'Confirmer votre mot de passe']]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
