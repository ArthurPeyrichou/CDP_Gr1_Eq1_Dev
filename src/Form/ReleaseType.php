<?php

namespace App\Form;

use App\Entity\Release;
use Doctrine\DBAL\Types\DateType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReleaseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('number', IntegerType::class, [
                'attr' => ['placeholder' => 'Le numero de votre release',
            'class' => 'form-control']
            ])

            ->add('description', TextareaType::class, [
                'attr' => ['placeholder' => 'Description de votre release',
                    'class' => 'form-control']
            ])
            ->add('date',DateType::class,[
                'widget' => 'choice',
            ])
            ->add('link',UrlType::class, [
            'attr' => ['placeholder' => 'Le lien vers votre release',
            'class' => 'form-control']
              ]);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([

        ]);
    }
}
