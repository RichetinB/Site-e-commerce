<?php

// src/Form/SellType.php
namespace App\Form;

use App\Entity\Article;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
class SellType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('nom', TextType::class, [
            'label' => 'Article Name',
        ])
        ->add('description', TextareaType::class, [
            'label' => 'Description',
        ])
        ->add('prix', MoneyType::class, [
            'label' => 'Price',
        ])
        ->add('quantiteEnStock', IntegerType::class, [
            'label' => 'Stock Quantity',
            'attr' => [
                'min' => 1,
                'step' => 1,
            ],
        ])
        ->add('lien_image', FileType::class, [
            'label' => 'Image (JPG, JPEG, PNG)',
            'mapped' => false, // Important for VichUploaderBundle
            'required' => false, // If the field is optional
        ]);
            
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
