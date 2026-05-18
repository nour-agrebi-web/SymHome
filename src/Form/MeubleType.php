<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Meuble;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class MeubleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom du meuble',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex: Canapé moderne',
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 4,
                    'placeholder' => 'Description du meuble...',
                ],
            ])
            ->add('prix', MoneyType::class, [
                'label' => 'Prix',
                'currency' => 'TND',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex: 850',
                ],
            ])
            ->add('stock', IntegerType::class, [
                'label' => 'Stock disponible',
                'attr' => [
                    'class' => 'form-control',
                    'type' => 'number',
                    'min' => 0,
                    'step' => 1,
                    'placeholder' => 'Ex: 10',
                ],
                'constraints' => [
                    new Assert\NotNull([
                        'message' => 'Le stock est requis.',
                    ]),
                    new Assert\Type([
                        'type' => 'integer',
                        'message' => 'Le stock doit être un nombre entier.',
                    ]),
                    new Assert\GreaterThanOrEqual([
                        'value' => 0,
                        'message' => 'Le stock doit être positif ou nul.',
                    ]),
                ],
            ])
            ->add('image', TextType::class, [
                'label' => 'Image',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex: canape.jpg',
                ],
            ])
            ->add('categorie', EntityType::class, [
                'label' => 'Catégorie',
                'class' => Categorie::class,
                'choice_label' => 'nom',
                'placeholder' => 'Choisir une catégorie',
                'attr' => [
                    'class' => 'form-select',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Meuble::class,
        ]);
    }
}