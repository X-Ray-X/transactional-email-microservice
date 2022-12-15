.PHONY: first-run prepare-env up down composer-install migrate migrate-rollback test

first-run: prepare-env up composer-install generate-key migrate

prepare-env:
	cp .env.example .env

up:
	./vendor/bin/sail up -d

down:
	./vendor/bin/sail down

composer-install:
	./vendor/bin/sail composer install

generate-key:
	./vendor/bin/sail artisan key:generate

migrate:
	./vendor/bin/sail artisan migrate

migrate-rollback:
	./vendor/bin/sail artisan migrate:rollback

test:
	./vendor/bin/sail test
