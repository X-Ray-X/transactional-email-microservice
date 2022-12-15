<?php

namespace Tests\Unit\Console\Commands;

use App\Models\Email;
use Tests\TestCase;

class CreateEmailTest extends TestCase
{
    public function testCreateEmailInvalid(): void
    {
        $this->artisan('email:create')
            ->expectsOutput('Starting an email creating procedure.')
            ->expectsQuestion('Sender\'s email address', '')
            ->expectsQuestion('Sender\'s name', '')
            ->expectsQuestion('Recipient\'s email address', '')
            ->expectsQuestion('Recipient\'s name', '')
            ->expectsQuestion('Email subject', '')
            ->expectsQuestion('Email HTML body', '')
            ->expectsOutput('Email could not be created.')
            ->expectsOutput('The from.email field is required.')
            ->expectsOutput('The from.name field is required.')
            ->expectsOutput('The to.email field is required.')
            ->expectsOutput('The to.name field is required.')
            ->expectsOutput('The html part field is required.');
    }

    public function testEmailCreationWithHtml(): void
    {
        $mock = $this->createPartialMock(Email::class, ['create', 'send']);

        $mock->setEmailDTO(
            [
                'email' => 'john@doe.com',
                'name' => 'John Doe',
            ],
            [
                'email' => 'jane@doe.com',
                'name' => 'Jane Doe',
            ],
            'Test Email',
            '<h1>This is a test email.</h1>'
        );

        $mock->method('create')->willReturn($mock);
        $mock->method('send')->willReturn(($mock->getEmailDTO())->toArray());

        $this->app->instance(Email::class, $mock);

        $this->artisan('email:create')
            ->expectsOutput('Starting an email creating procedure.')
            ->expectsQuestion('Sender\'s email address', 'john@doe.com')
            ->expectsQuestion('Sender\'s name', 'John Doe')
            ->expectsQuestion('Recipient\'s email address', 'jane@doe.com')
            ->expectsQuestion('Recipient\'s name', 'Jane Doe')
            ->expectsQuestion('Email subject', 'Test Email')
            ->expectsQuestion('Email HTML body', '<h1>This is a test email.</h1>')
            ->expectsOutput('Requesting new email...')
            ->expectsOutput(json_encode($mock->getEmailDTO()))
            ->expectsOutput('Done.');
    }
}
