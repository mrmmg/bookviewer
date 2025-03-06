# How to Start This Project

## Step 1
Install the latest PHP version. This project requires PHP 8.1. If you want to install a newer version, you can do so.  
While installing PHP, make sure to also install all the required [Laravel PHP extensions](https://laravel.com/docs/10.x/deployment#server-requirements).

## Step 2
Install the project libraries using Composer:
```
composer install
```

## Step 3
Check the project directory. If the `.env` file does not exist, create it by copying from `.env.example`.

## Step 4
Create a database and update the `.env` file with your database credentials. Make sure to fill in all `DB_*` keys.

## Step 5
Open a shell in the PHP container and run the migrations:
```
php artisan migrate
```

## Step 6
Create a Filament admin panel user. For more information, refer to the [Filament documentation](https://filamentphp.com/docs/3.x/panels/installation#create-a-user):
```
php artisan make:filament-user
```

## Step 7
Set up Nginx (or any web server compatible with PHP).

## Step 8
Open the admin panel using the URL below, and log in with the user credentials you created in Step 6:
```
http(s)://your-domain.com/admin
```

# What Is This Project?

I created this project to help me keep track of the pages I read in PDFs. I often forget which page I last read, so I built this project to solve that problem.  

You can upload a PDF to the website (by creating a document in the admin panel). Once the document is uploaded, you will see a link that allows you to load the PDF.  

If you scroll through the PDF, the project automatically saves the last page you read into the database. The next time you open the PDF, the project will load the last page you were on.  

That's it!
