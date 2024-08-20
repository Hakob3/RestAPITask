<?php

namespace App\EventListener;

use App\Entity\User;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Random\RandomException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Event\PreSubmitEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

readonly class VichFileBase64Subscriber implements EventSubscriberInterface
{
    /** @const int */
    private const FILE_NAME_LENGTH = 10;

    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SUBMIT => "preSubmit"
        ];
    }

    /**
     * @param PreSubmitEvent $event
     * @throws RandomException
     */
    public function preSubmit(PreSubmitEvent $event): void
    {
        if ($event->getData() === null) {
            return;
        }
        $base64String = ($event->getData())['file'];
        if (!$base64String) {
            return;
        }
        $explodedFile = explode(',', $base64String);
        $fileData = base64_decode($explodedFile[1]);

        preg_match('/^data:(.*?);base64,/', $base64String, $mimeMatches);
        $mimeType = $mimeMatches[1];

        $fileName = substr(
                bin2hex(random_bytes(self::FILE_NAME_LENGTH)), 0, self::FILE_NAME_LENGTH
            ) . '.' . explode('/', $mimeType)[1];
        $tempFilePath = sys_get_temp_dir() . '/' . $fileName;

        file_put_contents($tempFilePath, $fileData);

        $uploadedFile = new UploadedFile(
            $tempFilePath,
            $fileName,
            $mimeType,
            null,
            true
        );
        $event->setData(['file' => $uploadedFile]);
    }
}