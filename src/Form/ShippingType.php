<?php

namespace App\Form;

use App\Entity\Shipping;
use Faker\Provider\Text;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ShippingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('city', TextType::class, [])
            ->add('postalCode', TextType::class)
            ->add('address', TextareaType::class)
            ->add('firstName', TextType::class)
            ->add('lastName', TextType::class)
            ->add('email', EmailType::class)
            ->add('mobileNo', TextType::class)
            ->add('country', CountryType::class, [
                'placeholder'=> 'Choose the country'
            ])
            ->add('state', TextType::class)
            ->add('paymentMethod', ChoiceType::class, [
                'placeholder' => 'Choose a payment method',
                'expanded' => true,
                'choices' => [
                    'Paypal' => 'paypal',
                    'Payoneer' => 'payoneer',
                    'Check Payment' => 'check_payment',
                    'Direct Bank Transfer' => 'DBT',
                    'Cash on Delivery' => 'cash_on_delivery',
                ]
            ])
            ->add('createAccount', CheckboxType::class, [
                'mapped' => false,
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Shipping::class,
        ]);
    }
}
