# symfony-application

This is a demo application made in Symfony framework.
This application has admin pages for user and product management. The application pages are protected using login.
Users can register a new account and after login can create product.
Admin user can manage the users and product after login.

## Installation

1. Please perform the following steps as mentioned below

    ```
   $ git clone https://github.com/Atri-Nandi/symfony-application.git symfony-app
   $ cd symfony-app/
   $ composer install
    ```
3. I have put the database connection param in the env properties file in the project root folder.
 
   This currently has a value as shown below:
    ```
   DATABASE_URL="mysql://root:@127.0.0.1:3306/projectdb?serverVersion=mariadb-10.6.5&charset=utf8mb4"
    ```

    Please update this to point to the Database in your system.

5. We need to create a database after this. Please run the below command in the project root folder to generate the database.
   ```
   php bin/console doctrine:database:create
   ```

6. Now we need to install the Database tables. Please run the below 2 commands to do so .
   ```
   php bin/console make:migration
   php bin/console doctrine:migrations:migrate
   ```

7. To Load Dummy data, we need to run the below command (17 User, 30 Product).
   Please type YES to load the data.
   ```
   php bin/console doctrine:fixtures:load
   ```
   
## Usage

### Start

Please use the below command to start the server.
   ```
   symfony server:start
   ```

### Login

Login to the application using the following link - http://127.0.0.1:8000/


The dummy data created on step 6 already creates test and admin user.

| User name | password | type
| --- | --- | --- |
| `admin_user@gmail.com` | pass_1234 | admin |
| `test_user@gmail.com` | pass_1234 |  user  |

<img src="https://github.com/Atri-Nandi/symfony-application/assets/143453503/522715fd-5979-4cd6-bb46-001ea06ad5d9"  width="800" height="400">

### Dashboard - Admin

The dashboard shows an overview of all users and products in the application.
The admin can also add/manage the users and products.

<img src="https://github.com/Atri-Nandi/symfony-application/assets/143453503/c01e4690-fb1c-47a6-b9ae-2a94fa1bdbc3"  width="800" height="400">

### Dashboard - User
The user dashboard only shows the products created by the user. It does not show details of all the other users in the system.
The products can be managed/edited from the products page.

<img src="https://github.com/Atri-Nandi/symfony-application/assets/143453503/4832d1d5-4108-408c-bc2a-394ea82f79ab"  width="800" height="400">



