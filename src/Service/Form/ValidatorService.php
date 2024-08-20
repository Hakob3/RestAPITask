<?php

namespace App\Service\Form;

use Symfony\Component\Form\FormInterface;

class ValidatorService
{
    /** @var array */
    public array $errorMessages;

    /** @const  int */
    public const VALIDATOR_CODE = 300;

    /**
     * @param FormInterface $form
     * @return ValidatorService
     */
    public function getFormErrorsMessage(FormInterface $form): self
    {
        $this->errorMessages = $this->getErrorMessagesFromForm($form);

        return $this;
    }

    /**
     * @param FormInterface $form
     * @return array
     */
    private function getErrorMessagesFromForm(FormInterface $form): array
    {
        $errors = [];
        foreach ($form->getErrors() as $error) {
            if ($form->isRoot()) {
                $errors['#'][] = $error->getMessage();
            } else {
                $errors['message'] = $error->getMessage();
            }
        }
        foreach ($form->all() as $child) {
            if ($form->isSubmitted() && !$child->isValid()) {
                $errors[][$child->getName()] = $this->getErrorMessagesFromForm($child);
            }
        }

        return $errors;
    }
}