<?php

namespace App\Form\Api\User;

use App\Entity\Client;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClientAPIFormType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('address', TextType::class,
                [
                    'label' => false,
                    'required' => false,
                ]
            )
            ->add('phoneNumber', TextType::class,
                [
                    'label' => false,
                    'required' => false,
                ]
            )
            ->add(
                'birthday', BirthdayType::class,
                [
                    'label' => false,
                    'required' => false,
                    'input' => 'datetime',
                    'years' => range((int)date('Y'), (int)date('Y') - 60),
                ]
            );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => Client::class,
            ]
        );
    }
}