<?php

namespace App\Form;

use App\Entity\User;
use App\Lib\Trans;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AccountType extends AbstractType
{
    public const REGISTER = 1;
    public const USER_ACCOUNT = 2;

    /**
     * Build the generic part of the user form.
     *
     * @param FormBuilderInterface $builder Symfony form builder
     * @param array                $options Passed option list
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $type = $options['form_type'];
        switch ($type) {            
            case AccountType::REGISTER:
                $this->addRegisterFields($builder, $options);
                break;
            case AccountType::USER_ACCOUNT:
                $this->addUserAccountFields($builder, $options);
                break;
        }
    }    

    /**
     * Add Register form.
     *
     * @param FormBuilderInterface $builder Symfony form builder
     * @param array                $options Passed option list
     */
    private function addRegisterFields(FormBuilderInterface &$builder, array $options): void
    {
        $builder
            ->add('email', TextType::class, [
                'label' => Trans::tr('user.form.email.label'),
                'attr' => ['class' => 'form-control', 'placeholder' => Trans::tr('user.form.email.placeholder')],                
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'The password fields must match.',
                'options' => ['attr' => ['class' => 'form-control password-field', 'placeholder' => Trans::tr('user.form.password.placeholder')]],
                'required' => true,
                'first_options'  => ['label' => Trans::tr('user.form.password.label')],
                'second_options' => ['label' => Trans::tr('user.form.confirm_password.label')],
            ])            
            ->add('register', SubmitType::class, [
                'label' => Trans::tr('generic.button.register'),
                'attr' => ['class' => 'btn btn-primary d-grid w-100'],                
            ]);
    }

    /**
     * Add User Profile form.
     *
     * @param FormBuilderInterface $builder Symfony form builder
     * @param array                $options Passed option list
     */
    private function addUserAccountFields(FormBuilderInterface &$builder, array $options): void
    {
        $builder            
            ->add('firstName', TextType::class, [
                'label' => Trans::tr('user.form.first_name.label'),
                'attr' => ['class' => 'form-control', 'placeholder' => Trans::tr('user.form.first_name.placeholder')],                
            ])
            ->add('middleName', TextType::class, [
                'label' => Trans::tr('user.form.middle_name.label'),
                'attr' => ['class' => 'form-control', 'placeholder' => Trans::tr('user.form.middle_name.placeholder')],                
            ])
            ->add('lastName', TextType::class, [
                'label' => Trans::tr('user.form.last_name.label'),
                'attr' => ['class' => 'form-control', 'placeholder' => Trans::tr('user.form.last_name.placeholder')],                
            ])
            ->add('phone', TextType::class, [
                'label' => Trans::tr('user.form.phone.label'),
                'attr' => ['class' => 'form-control', 'placeholder' => Trans::tr('user.form.phone.placeholder')],                
            ])
            ->add('email', TextType::class, [
                'label' => Trans::tr('user.form.email.label'),
                'attr' => ['class' => 'form-control', 'placeholder' => Trans::tr('user.form.email.placeholder')],                
            ])
            ->add('street', TextType::class, [
                'label' => Trans::tr('user.form.street.label'),
                'attr' => ['class' => 'form-control', 'placeholder' => Trans::tr('user.form.street.placeholder')],                
            ])
            ->add('houseNumber', TextType::class, [
                'label' => Trans::tr('user.form.house_number.label'),
                'attr' => ['class' => 'form-control', 'placeholder' => Trans::tr('user.form.house_number.placeholder')],                
            ])
            ->add('postalCode', TextType::class, [
                'label' => Trans::tr('user.form.postal_code.label'),
                'attr' => ['class' => 'form-control', 'placeholder' => Trans::tr('user.form.postal_code.placeholder')],                
            ])
            ->add('city', TextType::class, [
                'label' => Trans::tr('user.form.city.label'),
                'attr' => ['class' => 'form-control', 'placeholder' => Trans::tr('user.form.city.placeholder')],                
            ])
            ->add('country', TextType::class, [
                'label' => Trans::tr('user.form.country.label'),
                'attr' => ['class' => 'form-control', 'placeholder' => Trans::tr('user.form.country.placeholder')],                
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
            'data_class' => User::class,
            'form_type' => AccountType::REGISTER,
            'validation_groups' => ['userVal'],
            'csrf_protection' => false,
        ]);
    }
}








