<?php

namespace App\Form\Api;

use App\Entity\Statement;
use App\Form\Type\VichFileBase64Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StatementAPIFormType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class,
                [
                    'label' => false,
                    'required' => true,
                ]
            )
            ->add('content', TextType::class,
                [
                    'label' => false,
                    'required' => false,
                ]
            )
            ->add('attachmentFile', VichFileBase64Type::class,
                [
                    'label' => false,
                    'required' => false,
                ]
            )
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => Statement::class,
            ]
        );
    }
}