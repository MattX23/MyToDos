# MyToDos

## Description

MyToDos is a SPA to-do list created using Laravel.

To Dos can contain a title, body, image, attachment, due date, and reminder.

Users are also able to opt-in to email reminders when a due date is set on a To Do.

## Setup

- Clone this repo: `git clone git@github.com:MattX23/MyToDos.git`
- Create a `.env` file
- Set your [MailTrap](https://mailtrap.io/) username and password in your `.env` for the `MAIL_USERNAME` and `MAIL_PASSWORD` variables
- Setup your local DB
- Run `composer install`
- Run the migrations: `php artisan migrate`
- Run `npm install`
- Run `npm run dev`
- Create seed users: `php artisan db:seed`
- To run tests: `vendor/bin/phpunit`

## Screenshots
