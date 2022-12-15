<?php

namespace Tests\Unit\Clients;

use App\Clients\SendgridEmailClient;
use App\DTO\EmailDTO;
use App\Repositories\EmailLogRepositoryInterface;
use SendGrid\Response;
use Tests\TestCase;
use SendGrid;

class SendgridEmailClientTest extends TestCase
{
    /**
     * @return void
     */
    public function testReturnClientInstance(): void
    {
        $sendgrid = new SendgridEmailClient(
            $this->createMock(EmailLogRepositoryInterface::class),
            env('SENDGRID_API_KEY', '')
        );
        $instanceType = $sendgrid->getClient();

        $this->assertInstanceOf(SendGrid::class, $instanceType);
    }

    /**
     * @return void
     * @throws \Spatie\DataTransferObject\Exceptions\UnknownProperties
     */
    public function testEmailSend(): void
    {
        $emailRepositoryMock = $this->createMock(EmailLogRepositoryInterface::class);

        $mailjet = new SendgridEmailClient(
            $emailRepositoryMock,
            ''
        );

        $emailDTO = new EmailDTO([
            'id' => 'decea8b6-25e5-4c7b-a765-a11b5a6fad4c',
            'from' => [
                'email' => 'john@doe.com',
                'name' => 'John Doe',
            ],
            'to' => [
                'email' => 'jane@doe.com',
                'name' => 'Jane Doe',
            ],
            'subject' => 'Test Email',
            'htmlPart' => '<h1>This is a test email.</h1>'
        ]);

        $clientMock = $this->createMock(SendGrid::class);

        $response = new Response(202, '{}');

        $clientMock->method('send')->willReturn($response);

        $emailRepositoryMock->method('update')->willReturn(true);

        $mailjet->setClient($clientMock);

        $send = $mailjet->send($emailDTO);

        $this->assertTrue($send);
    }

    /**
     * @return void
     * @throws \Spatie\DataTransferObject\Exceptions\UnknownProperties
     */
    public function testEmailSendFail(): void
    {
        $emailRepositoryMock = $this->createMock(EmailLogRepositoryInterface::class);

        $sendgrid = new SendgridEmailClient(
            $emailRepositoryMock,
            ''
        );

        $emailDTO = new EmailDTO([
            'id' => 'decea8b6-25e5-4c7b-a765-a11b5a6fad4c',
            'from' => [
                'email' => 'john@doe.com',
                'name' => 'John Doe',
            ],
            'to' => [
                'email' => 'jane@doe.com',
                'name' => 'Jane Doe',
            ],
            'subject' => 'Test Email',
            'htmlPart' => '<h1>This is a test email.</h1>'
        ]);

        $clientMock = $this->createMock(SendGrid::class);

        $clientMock->method('send')->willThrowException(new \Exception());

        $sendgrid->setClient($clientMock);

        $send = $sendgrid->send($emailDTO);

        $this->assertFalse($send);
    }
}
