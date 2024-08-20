<?php

namespace App\Form\Type;

use App\EventListener\VichFileBase64Subscriber;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichFileType;

class VichFileBase64Type extends VichFileType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);
        $builder->addEventSubscriber(new VichFileBase64Subscriber());
    }

    /**
     * @return string
     */
    public function getBlockPrefix(): string
    {
        return 'base64_file';
    }
}
