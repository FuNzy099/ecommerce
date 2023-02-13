<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                // Permet d'appliquer un label au champ input
                'label' => 'Nom du produit :',

                // Permet de gerer les attributs html du champ
                'attr' => [
                    // 'class' => 'form-control',                  // Permet d'appliquer une classe CSS venant de Bootstrap pour styliser le champ input
                    'placeholder' => 'Tapez le nom du produit'  // Permet d'appliquer une placeholder au sein même du input
                ]
            ])

            ->add('shortDescription', TextareaType::class, [
                'label' => 'Description courte :',
                'attr' => [
                    // 'class' => 'form-control',
                    'placeholder' => 'Tapez une description suffisement courte mais parlante pour le visiteur'
                ]
            ])

            ->add('price', MoneyType::class, [
                'label' => 'Prix du produit :',
                'attr' => [
                    // 'class' => 'form-control',
                    'placeholder' => 'Tapez le prix du produit en euro'
                ]
            ])

            ->add('mainPicture', UrlType::class, [
                'label' => 'Image du produit :',
                'attr' => [
                    'placeholder' => 'Tapez une URL d\'image !'
                ]
            ])

            ->add('category', EntityType::class, [
                'label' => 'Catégorie :',
                // 'attr' => [
                //     'class' => 'form-control'
                // ],
                'placeholder' => '-- Choisir une catégorie --',
                'class' => Category::class,
                'choice_label' => function(Category $category){
                    return strtoupper($category -> getName());
                },
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
