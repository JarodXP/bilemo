<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\User;
use App\Entity\Company;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'constraints' => [
                    new NotNull(),
                    new Email()
                ]
            ])
            ->add('firstName', TextType::class, [
                'constraints' => [
                    new NotNull(),
                    new Length([
                        'min' => 1,
                        'max' => 50,
                        'minMessage' => "The first name length must be up to 1 characters",
                        'maxMessage' => "The first name length must be less than 50 characters"
                    ])
                ]
            ])
            ->add('lastName', TextType::class, [
                'constraints' => [
                    new NotNull(),
                    new Length([
                        'min' => 1,
                        'max' => 50,
                        'minMessage' => "The last name length must be up to 1 characters",
                        'maxMessage' => "The last name length must be less than 50 characters"
                    ])
                ]
            ])
            ->add('phoneNumber', TelType::class, [
                'constraints' => [
                    new Regex([
                        'pattern' => '~^\+[0-9]{1,4}[\(0-9{1,10}\)]?[0-9]{4,30}$~',
                        'message' => 'This is not a valid phone number.'
                    ])
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'csrf_protection' => false,
        ]);
    }
}
