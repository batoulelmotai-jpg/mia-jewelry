<?php

namespace App\Form;

use App\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => false,
                'required' => true,
            ])
            ->add('price', MoneyType::class, [
                'label' => false,
                'required' => true,
                'currency' => 'MAD',
            ])
            ->add('category', ChoiceType::class, [
                'label' => false,
                'required' => true,
                'placeholder' => 'Sélectionner une catégorie',
                'choices' => [
                    'Bagues' => 'bagues',
                    'Bracelets' => 'bracelets',
                    'Colliers' => 'colliers',
                    'Boucles d’oreilles' => 'boucles',
                    'Pendentifs' => 'pendentifs',
                    'Montres' => 'montres',
                    'Autre' => 'autre',
                ],
            ])
            ->add('stock', IntegerType::class, [
                'label' => false,
                'required' => true,
                'attr' => ['min' => 0],
            ])
            ->add('image', FileType::class, [
                'label' => 'Image produit',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '2M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/webp',
                        ],
                        'mimeTypesMessage' => 'Upload image valide (jpg, png, webp)',
                    ])
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => false,
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}