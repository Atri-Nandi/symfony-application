<?php

namespace App\Form;

use App\Entity\Product;
use App\Lib\Trans;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    
    /**
     * Build the generic part of the customer form.
     *
     * @param FormBuilderInterface $builder Symfony form builder
     * @param array                $options Passed option list
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => Trans::tr('product.form.name.label'),
                'attr' => ['class' => 'form-control', 'placeholder' => Trans::tr('product.form.name.placeholder')],                
            ])
            ->add('description', TextareaType::class, [
                'label' => Trans::tr('product.form.description.label'),  
                'attr' => ['class' => 'form-control', 'placeholder' => Trans::tr('product.form.description.placeholder')],              
            ])
            ->add('price', TextType::class, [
                'label' => Trans::tr('product.form.price.label'),
                'attr' => ['class' => 'form-control', 'placeholder' => Trans::tr('product.form.price.placeholder')],              
            ])
            ->add('category', ChoiceType::class, [
                'label' => Trans::tr('product.form.category.label'),
                'attr' => ['class' => 'form-control'],              
                'choices'  => [
                    'Electronics' => 'Electronics',
                    'Fashion' => 'Fashion',
                    'Decor' => 'Decor',
                    'Sport' => 'Sport',
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => Trans::tr('generic.button.submit'),
                'attr' => ['class' => 'btn btn-primary me-2'],                
            ])
            ->add('cancel', SubmitType::class, [
                'label' => Trans::tr('generic.button.cancel'),
                'attr' => ['class' => 'btn btn-outline-secondary'],                
            ]);
    }    

    /**
     * Configuration option for the existing financing form.
     *
     * @param OptionsResolver       Symfony resolver object
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
            'validation_groups' => ['productVal'],
            'csrf_protection' => false,
        ]);
    }
}








