<?php

namespace App\Console\Commands;

use App\Models\Email;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class CreateEmail extends Command
{
    private Email $email;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates an email message to be sent through the service';

    public function __construct(Email $email)
    {
        parent::__construct();

        $this->email = $email;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting an email creating procedure.');

        $payload = [
            'from' => [
                'email' => $this->ask('Sender\'s email address'),
                'name' => $this->ask('Sender\'s name'),
            ],
            'to' => [
                'email' => $this->ask('Recipient\'s email address'),
                'name' => $this->ask('Recipient\'s name'),
            ],
            'subject' => $this->ask('Email subject'),
            'htmlPart' => $this->ask('Email HTML body'),
        ];

        $validator = Validator::make($payload, $this->getValidationRules());

        if ($validator->fails()) {
            $this->info('Email could not be created.');

            $errors = $validator->errors();

            foreach ($errors->all() as $error) {
                $this->error($error);
            }

            return Command::FAILURE;
        }

        $this->info('Requesting new email...');

        $emailSent = $this->email->create(
            $payload['from'],
            $payload['to'],
            $payload['subject'],
            $payload['htmlPart'],
        )->send();

        $this->info(json_encode($emailSent));

        $this->info('Done.');

        return Command::SUCCESS;
    }

    /**
     * @return string[]
     */
    private function getValidationRules(): array
    {
        return [
            'from'          => 'required|array',
            'from.email'    => 'required|email|max:255',
            'from.name'     => 'required|string|min:1|max:255',
            'to'            => 'required|array',
            'to.email'      => 'required|email|max:255',
            'to.name'       => 'required|string|min:1|max:255',
            'subject'       => 'required|string|min:1|max:255',
            'htmlPart'      => 'required|string|min:1',
        ];
    }
}
