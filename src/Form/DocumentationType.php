<?php

namespace App\Form;

use App\Entity\Documentation;
use App\Entity\Project;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DocumentationType extends AbstractType
{    public const PROJECT = 'project';
    public function buildForm(FormBuilderInterface $builder, array $options)
    {  /**@var $project Project*/
        $project = $options[self::PROJECT];

        $builder
            ->add('name', TextType::class,[
                'label' => 'Nom',
                'attr' => [
                    'placeholder' => 'Nom de votre Documentation'
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => [
                    'placeholder' => 'Description de votre description',
                    'rows' => 4
                ]
            ])
            ->add('link',UrlType::class, [
                'label' => 'Lien vers la ressource ',
                'attr' => [
                    'placeholder' => 'Le lien vers la ressource '
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
        ]);
        $resolver->setRequired([self::PROJECT]);
    }
}
