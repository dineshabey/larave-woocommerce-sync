

## 👨🏼‍💻 Laravel WooCommerce Sync Project

### Objective

This project is developed as part of the PHP (Laravel) Developer Challenge V 3.0. The aim is to sync products from a WooCommerce shop using the WooCommerce REST API and store the product details in a local MySQL database. Additionally, it includes an API to register users, login, fetch products, and sync products.

### Table of Contents

- Requirements
- Installation
- Configuration
- Running the Project
- API Endpoints
- Sync Process
- Queue Job
- Notes

### Requirements

- PHP 8.x
- Composer
- MySQL
- Laravel 9.x
- WooCommerce Shop Credentials

### Installation

#### 1.Clone the repository:

git clone https://github.com/your-username/laravel-woocommerce-sync.git
cd laravel-woocommerce-sync

#### 2.Install dependencies:

composer install

#### 3.Create a .env file:

cp .env.example .env

#### 4.Generate an application key:

php artisan key:generate

#### 5.Set up your database in the .env file:

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_username
DB_PASSWORD=your_database_password

#### 6.Run the migrations to create the required tables:

php artisan migrate

### Configuration

- Update the WooCommerce credentials in the .env file:

WOOCOMMERCE_SHOP_URL=https://tests.kodeia.com/wordpress/wp-json/wc/v3/products
WOOCOMMERCE_CONSUMER_KEY=ck_547cc1e0c953c44c4744cd29466ad2ba65a658d6
WOOCOMMERCE_CONSUMER_SECRET=cs_c8973040acc5d8f4c581d67d611f03d5b3eb733d

### Running the Project

#### 1.Start the Laravel development server:

php artisan serve

#### 2.Run the queue worker:

php artisan queue:work

### API Endpoints

- Refer to API full document

### Sync Process

- The sync process fetches the first 15 products from the WooCommerce shop and stores them in the local database. It also downloads the product images and saves them with random filenames.

### Queue Job

- The queue job is responsible for downloading product images and updating the image_filename column in the database.

### Notes

- Ensure the queue worker is running to process the image download jobs.
- If you face any issues, check the Laravel logs in storage/logs/laravel.log.


