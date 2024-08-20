<?php

namespace App\Tests\Unit\EventListeners;

use App\EventListener\VichFileBase64Subscriber;
use Random\RandomException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Form\Event\PreSubmitEvent;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Form\FormInterface;

class VichFileBase64SubscriberTest extends KernelTestCase
{
    /**
     * @var VichFileBase64Subscriber
     */
    private VichFileBase64Subscriber $subscriber;

    protected function setUp(): void
    {
        $this->subscriber = new VichFileBase64Subscriber();
    }

    /**
     * @group test
     * @throws RandomException
     */
    public function testPreSubmit(): void
    {
        $file = file_get_contents(sprintf("%s/%s", __DIR__, "test.png"));

        $base64String = 'data:image/png;base64,' . base64_encode($file);
        $event = new PreSubmitEvent(
            $this->createMock(FormInterface::class),
            ['file' => $base64String]
        );
        $this->subscriber->preSubmit($event);
        $data = $event->getData();

        $this->assertArrayHasKey('file', $data);
        $this->assertInstanceOf(UploadedFile::class, $data['file']);
        $this->assertEquals('image/png', $data['file']->getMimeType());
    }
}
