# Food Label Maker Task | Ibrahim Muhaisen

## Description

It is a backend project to create promo codes, and use it.

## Installation

To get started with this project, follow the steps below:

1. Clone this repository into your local machine:

git clone https://github.com/IbrahimMuh96/food-label-maker.git  

2. Install the required dependencies by running the following command:

composer install

3. Create a copy of the `.env.example` file and rename it to `.env`:

cp .env.example .env

4. Generate an application key by running the following command:

php artisan key:generate

5. Create a new database and update the database credentials in the `.env` file accordingly.

6. Run the database migrations by running the following command:

php artisan migrate

7. Run seeder to initiate the permissions and roles:

php artisan db:seed

## Usage

To use this project, follow the steps below:

1. Start the development server by running the following command:

php artisan serve


