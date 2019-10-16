# Setting Up The Project
## Installing Git
Download the Git Command Line Interface from [https://git-scm.com/download/win](https://git-scm.com/download/win). Install it on your system, so we can use Git from the Command Line.

## Cloning the repository
You will need the files from this repository. This can be easily achieved by cloning the repository by running the following command in your project directory.
```
git clone https://github.com/johannesmu/php-class-d19 .
```
Don't leave out the period at the end. 

# Setup of Server Side
## Install Composer
You need to install Composer on your system, to manage the backend dependencies of the project. 
### Installing Manually
You can get a composer binary [https://getcomposer.org/download/1.8.6/composer.phar](https://getcomposer.org/download/1.8.6/composer.phar) and install it manually.

After downloading the binary, you need to place it in your system and add the path to the PATH variable of your system.
### Installing with the Installer
You can use the installer from [https://getcomposer.org/Composer-Setup.exe](https://getcomposer.org/Composer-Setup.exe) to automatically install composer on your system.

## Setup project dependencies
In this project we will be using a templating system called Twig. This can be installed by running the command below in your project directory:
```
composer require "twig/twig": "1.31.*"
```
The command will create a file called *composer.json* in the project directory.

## Setup for autoloading our own classes using composer
Open the *composer.json* file from the above step and you will see something like
```
{
    "require": {
        "twig/twig": "1.31.*"
    }
}
```
You will need to add to this file the path and system used to resolve class names into the name of the files where the classes are defined
```
{
    "require": {
        "twig/twig": "1.31.*"
    },
    "autoload":{
        "psr-4": {
            "aitsyd\\" : "classes/"
        }
    }
}
```
This will tell Composer to find your class definitions in a directory *classes* and will load them with the namespace *aitsyd*. Namespace is used to separate your own classes from other classes that may be added to the project later. The namespace that you used is often referred to as the *vendor* name.

It will also match the name of classes to the names of the files that contain their definitions. For example, if you have a class called *Database*, Composer will try to find a file called *Database.php* in the *classes* directory and load it.

# Setup of Front End
## Creating a package.json file
To create the file, we will use a *Node JS* utility called *npm*. In your project directory, run the command
```
npm init
```
The utility will ask you several questions. You can keep pressing ENTER to use the default suggested values.

## Installing Bootstrap, jQuery and Popper.js
```
npm install bootstrap jquery popper.js --save
```
The *--save* switch will ensure that the packages installed will be added as a dependency of your project.



## Adding database tables
Download the *.sql* files from the *db_tables* directory of this repository. Open PhpMyAdmin and create a database that we will use for the project. Then import the *sql* files one by one, in the following order:
- product.sql
- category.sql
- image.sql
- product_image.sql
- product_quantity.sql
- product_category.sql

These will provide some sample data for our project.

# Connecting to the database
In the root of the project directory, create a file named *.htaccess* . We will use this file to store the credentials of the database account.
```
SetEnv dbuser your database user
SetEnv dbpass your database password
SetEnv dbname your database name
SetEnv dbhost your database host
```



