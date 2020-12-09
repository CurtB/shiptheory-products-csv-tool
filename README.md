## Live demo 
- This app is deployed and available to use, **[https://shiptheory-products-csv-tool-ebwn2.ondigitalocean.app/](https://shiptheory-products-csv-tool-ebwn2.ondigitalocean.app/)**

## Prerequisites
- Download and install composer if you don't already have it: [https://getcomposer.org/download/](https://getcomposer.org/download/)
## Installation
Download or use terminal/console to 'clone' this project onto your machine.
```bash
git clone https://github.com/CurtB/shiptheory-products-csv-tool.git
```
In terminal/console navigate into the projects root directory (shiptheory-products-csv-tool-main)
```bash
cd shiptheory-products-csv-tool
```
Initialise the project with composer.
```bash
composer install
```
Rename `.env.example` file to `.env`inside your project root.
```bash
mv .env.example .env
```
Generate laravel project keys.
```bash
php artisan key:generate
```
Start local server
```bash
php artisan serve
```
Open the web address in your browser displayed after 'Starting Laravel development server:' usually something like: http://127.0.0.1:8000

## This app is built using the PHP framework Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains over 1500 video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## What is Shiptheory?

Shiptheory is an online service that automates the organisation and shipment of products between your website and your shipment carriers. It is a robust and extensive application with plenty of support for setting up and maintaining the most efficient fulfilment workflow possible. It supports all major e-commerce platforms and shipping companies. If yours is not on the list it can be added on request.

## Connecting to the Shiptheory API

If you just want to connect to Shiptheory in your own app refer to the /app/Traits/ShiptheoryAPI.php file.

### Useful links 

- **[Shiptheory API documentation](https://shiptheory.com/developer/index.html)**
- **[Shiptheory website](https://shiptheory.com/)**
- **[Shiptheory Barcode scanner app](https://play.google.com/store/apps/details?id=com.shiptheory.barcodescanner)**
- **[Shipment CSV import tool](http://178.62.69.143/)**

## License

This app is open-sourced software you are free to use, edit and copy as you please but you do so at your own risk. The author assumes no responsibility for this software.
