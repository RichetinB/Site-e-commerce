<?php
// src/Form/ArticleEditType.php

namespace App\Form;

use App\Entity\Article;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class ArticleEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('nom', TextType::class)
        ->add('description', TextareaType::class)
        ->add('prix', MoneyType::class)
        ->add('quantiteEnStock', IntegerType::class, [
            'label' => 'Quantité en stock',
            'attr' => [
                'min' => 1,
                'step' => 1,
            ],
        ])
        ->add('lien_image', FileType::class, [
            'label' => 'Image (JPG, JPEG, PNG)',
            'mapped' => false, // Important pour VichUploaderBundle
            'required' => false, // Si le champ est facultatif
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
            
        ]);
    }
}
