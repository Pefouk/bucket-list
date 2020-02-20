<?php

namespace App\Form;

use App\Entity\Categories;
use App\Entity\Liste;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ListeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('author', TextType::class, [
                'label' => 'Auteur'
            ])
            ->add('title', TextType::class, [
                'label' => 'Idée',
                'attr' => [
                    'placeholder' => 'Sauter en parachute, aller aux US etc...'
                ]])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Je veut faire ça car...'
                ]
            ])
            ->add('categorie', EntityType::class, [
                'class' => Categories::class,
                'choice_label' => 'nom',
                'label' => 'Categorie'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Liste::class,
        ]);
    }
}
