<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Product;
use App\Service\ObjectValidator;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class UploadProductFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('uploadFile', FileType::class, [
                'constraints' => [
                    new File([
                        'mimeTypes' => 'text/plain',
                        'mimeTypesMessage' => 'Please upload a valid CSV file',
                    ]),
                ],
                'mapped' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class, ObjectValidator::class,
        ]);
    }
}
