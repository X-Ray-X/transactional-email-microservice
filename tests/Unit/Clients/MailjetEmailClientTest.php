<?php

namespace Tests\Unit\Clients;

use App\Clients\MailjetEmailClient;
use App\DTO\EmailDTO;
use App\Repositories\EmailLogRepositoryInterface;
use Mailjet\Client;
use Mailjet\Request;
use Mailjet\Response;
use Tests\TestCase;

class MailjetEmailClientTest extends TestCase
{
    /**
     * @return void
     */
    public function testReturnClientInstance(): void
    {
        $mailjet = new MailjetEmailClient(
            $this->createMock(EmailLogRepositoryInterface::class),
            env('MAILJET_KEY', ''),
            env('MAILJET_SECRET', '')
        );
        $instanceType = $mailjet->getClient();

        $this->assertInstanceOf(Client::class, $instanceType);
    }

    /**
     * @return void
     * @throws \Spatie\DataTransferObject\Exceptions\UnknownProperties
     */
    public function testEmailSend(): void
    {
        $emailRepositoryMock = $this->createMock(EmailLogRepositoryInterface::class);

        $mailjet = new MailjetEmailClient(
            $emailRepositoryMock,
            '',
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

        $clientMock = $this->createMock(Client::class);

        $response = new Response($this->createMock(Request::class), new \GuzzleHttp\Psr7\Response(200, [], '{}'));

        $clientMock->method('post')->willReturn($response);

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

        $mailjet = new MailjetEmailClient(
            $emailRepositoryMock,
            '',
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

        $clientMock = $this->createMock(Client::class);

        $clientMock->method('post')->willThrowException(new \Exception());

        $mailjet->setClient($clientMock);

        $send = $mailjet->send($emailDTO);

        $this->assertFalse($send);
    }
}
