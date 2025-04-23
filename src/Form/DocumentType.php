<?php

namespace App\Form;

use App\Entity\Document;
use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Entity\Workspace;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;

class DocumentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Nom du document',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer un nom pour le document',
                    ]),
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
                'mapped' => false,
            ])
            ->add('keywords', TextType::class, [
                'label' => 'Mots-clés (séparés par des virgules)',
                'required' => false,
                'mapped' => false,
            ])
            ->add('tag', TextType::class, [
                'label' => 'Tag',
                'required' => false,
                'mapped' => false,
            ])
            ->add('confidential', CheckboxType::class, [
                'label' => 'Document confidentiel',
                'required' => false,
                'mapped' => false,
            ])
            ->add('expirationDate', DateType::class, [
                'label' => 'Date d\'expiration (si applicable)',
                'required' => false,
                'widget' => 'single_text',
                'mapped' => false,
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'label' => 'Catégorie',
                'placeholder' => 'Sélectionner une catégorie',
                'required' => true,
            ])
            ->add('workspace', EntityType::class, [
                'class' => Workspace::class,
                'choice_label' => 'name',
                'label' => 'Espace de travail',
                'placeholder' => 'Sélectionner un espace de travail',
                'required' => false,
            ])
            ->add('documentFile', FileType::class, [
                'label' => 'Fichier',
                'mapped' => false,
                'required' => true,
                'constraints' => [
                    new File([
                        'maxSize' => '100M',
                        'mimeTypes' => [
                            'application/pdf',
                            'application/msword',
                            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                            'application/vnd.ms-excel',
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'text/plain',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Veuillez télécharger un fichier valide (PDF, DOCX, XLSX, TXT, PNG)',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Document::class,
        ]);
    }
}