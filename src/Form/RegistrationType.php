<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => ['placeholder' => 'Votre pseudo',
                            'class' => 'form-control'],
                'label' => 'Pseudo'
                ])
            ->add('emailAddress', EmailType::class, [
                'attr' => ['placeholder' => 'Votre adresse mail',
                            'class' => 'form-control'],
                'label' => 'Adresse mail'
                ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => ['label' => 'Mot de passe',
                                    'attr' => ['placeholder' => 'Votre mot de passe',
                                                'class' => 'form-control']],
                'second_options' => ['label' => 'Confirmation mot de passe',
                                    'attr' => ['placeholder' => 'Confirmer votre mot de passe',
                                                'class' => 'form-control']]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
