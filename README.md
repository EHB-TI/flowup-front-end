# flowup-front-end

This repo exists out of 2 projects that work together to form the Front End project in Integration Project.

We have **Heartbeat** that will send a heartbeat to monitoring while doing an API call to make sure the site is up and running.

Next we have **ip2-frontend**, this is our main project.
All the website interface is from this project. It includes the frontend as the backend of the project.


## ip2-frontend


### How to deploy
To be able to run the project locally, run these commands inside the /ip2-frontend folder
> ⚠️ **WARNING**: After running composer install, revert the changes made on this package: `vyuldashev/laravel-queue-rabbitmq`

```bash
#install the neccesary packages
composer install
npm install

#build the VueJS code
npm run dev 

#Run the laravel server
php artisan serve

#Run the laravel consumer
php artisan rabbitmq:consume
```
To run the tests run the command `php artisan test`
### packages

packages laravel:

- [Laravel-saml2]("https://github.com/aacotroneo/laravel-saml2")
- [PHP-amqplib]("https://github.com/php-amqplib)
- [RabbitMQ Queue driver for Laravel]("https://github.com/vyuldashev/laravel-queue-rabbitmq")

packages vue.js:
- [Ant-design-vue]("https://www.antdv.com/docs/vue/introduce/")
- [Bootstrap-vue]("https://bootstrap-vue.org/")
- [Laravel-vue-pagination]("https://github.com/gilbitron/laravel-vue-pagination")
- [Tailwindcss]("https://tailwindcss.com/")
- [Vue-axios]("https://www.npmjs.com/package/vue-axios")
- [Vue-template-compiler]("https://www.npmjs.com/package/vue-template-compiler")


### Tutorials/ extra sources
> 🛈 **Info**: Some tutorials or external documentation used in this project

- [RabbitMQ Tutorials]("rabbitmq.com/tutorials")
- [Azure Active Directory SSO with Laravel]("https://stackoverflow.com/questions/54289010/azure-active-directory-sso-with-laravel")
- [get server ram with php](https://stackoverflow.com/questions/1455379/get-server-ram-with-php)

## Heartbeat

### Local deployment

> ⚠️ **WARNING**: The laravel server should be running to be able to send heartbeats to the API

```bash
#Install packages
npm install

#run heartbeat
node app.js
```
### Modules

- [Amqplib]("https://www.npmjs.com/package/amqplib")
- [Node-fetch]("https://www.npmjs.com/package/node-fetch")
