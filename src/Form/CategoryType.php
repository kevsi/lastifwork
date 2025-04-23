<?php
// src/Form/CategoryType.php
namespace App\Form;

use App\Entity\Category;
use App\Enum\CategoryStatus;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom',
                'required' => true,
                'attr' => ['placeholder' => 'Nom de la catégorie']
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
                'attr' => ['placeholder' => 'Description de la catégorie']
            ])
            ->add('slug', TextType::class, [
                'label' => 'Slug',
                'required' => false,
                'attr' => ['placeholder' => 'Laissez vide pour génération automatique']
            ])
            ->add('parent', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'required' => false,
                'placeholder' => 'Aucune (catégorie racine)',
                'query_builder' => function ($er) {
                    return $er->createQueryBuilder('c')
                        ->orderBy('c.name', 'ASC');
                },
            ])
            ->add('color', TextType::class, [
                'required' => false,
                'attr' => [
                    'class' => 'form-control visually-hidden',
                    'id' => 'colorInput'
                ]
            ])
            ->add('status', EnumType::class, [
                'class' => CategoryStatus::class,
                'required' => true,
                'label' => 'Statut',
                'choice_label' => function(CategoryStatus $choice): string {
                    return match($choice) {
                        CategoryStatus::ACTIVE => 'Actif',
                        CategoryStatus::INACTIVE => 'Inactif',
                        CategoryStatus::ARCHIVED => 'Archivé',
                        // Ajoutez d'autres cas si nécessaire
                    };
                }
            ]);
        
        // Pas de champs createdAt et updatedAt ici
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
        ]);
    }
}