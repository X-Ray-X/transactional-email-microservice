# Transactional email microservice - code challenge

The original requirements for this assignment can be found in the **__justeattakeaway.com_** folder.

## Prerequisites:
* [Docker](https://docs.docker.com/get-docker/)

## How to:

### Add the following entry to your /etc/hosts file:

> 127.0.0.1 pgsql

The email API endpoint can be accessed at http://localhost/api/v1/emails.

### Run the command from project's root catalog:

> // first installation with some additional steps: \
> make first-run \
> \
> // later use: \
> make up \
> make down

### Provide your API keys for Mailjet and Sendgrid services in the .env file:
> SENDGRID_API_KEY= \
> \
> MAILJET_KEY= \
> MAILJET_SECRET=

### Testing

> make test

For more please check the contents of Makefile.

### Database access

Use the following credentials in a database client of your choice:

> DB_HOST: pgsql \
> DB_PORT: 5432 \
> DB_DATABASE: transactional_email_microservice \
> DB_USERNAME: sail \
> DB_PASSWORD: password

## Registering additional fallback email providers:

- Create a new email client class implementing the `Clients/EmailClient.php` interface.
- Register your new client as a singleton in `Providers/EmailServiceProvider.php`.
- Add `$app->make(YourEmailClient::class)` to the binding script of `Workers/EmailWorker.php` in `Providers/AppServiceProvider.php`.
- The EmailWorker class will go over the list of provided email clients and fulfill the request with the first service available.
