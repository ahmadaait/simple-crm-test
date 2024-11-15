# SIMPLE CRM - BACKEND TECHNICAL TEST

## System Requirements

1. PHP >= 8.2 (i used PHP 8.2.25)
2. Composer >= 2.1 (i used Composer 2.8.2)
3. MySQL >= 8.0 (i used MySQL 8.3.0)
4. Laravel >= 11

## Utilities

1. Visual Studio Code
2. Postman
3. Docker
4. Git

## Entity Relationship Diagram

![Entity Relationship Diagram](docs/erd.png)

## How to run the project

1. Clone the repository
    ````bash
    https://github.com/ahmadaait/simple-crm-test.git```
    ````
2. Run the following command to install the dependencies
    ```bash
     composer install
    ```
3. Create a new database and add the database credentials to the `.env` file
4. Run the following command to create the tables
    ```bash
    php artisan migrate
    ```
5. Run the following command to seed the database
    ```bash
     php artisan db:seed
    ```
6. Run the following command to start the server
    ```bash
    php artisan serve
    ```

## API Endpoints

### Authentication

1. Login
    - URL: `/api/v1/auth/login`
    - Method: `POST`
    - Request Body Example:
        ```json
        {
            "email": "johndoe@example.com",
            "password": "johndoe"
        }
        ```
2. Logout
    - URL: `/api/v1/auth/logout`
    - Method: `POST`
    - Headers:
        - `Authorization: Bearer <token>`

### Permission

Note: The permissions are created using database seeders.

1. Get All Permissions

    - URL: `/api/v1/permissions`
    - Method: `GET`
    - Headers: `Authorization Bearer <token>`
    - Query Parameters:
        - `page`: The page number
        - `perPage`: The number of items per page
        - `search`: The search query
        - `sort`: The sort field
        - `order`: The sort order
        - Example: `/api/v1/permissions?page=1&perPage=10&search=example&sort=name&order=asc`

2. Get All Permissions without pagination

    - URL: `/api/v1/permissions/all`
    - Method: `GET`
    - Headers: `Authorization Bearer <token>`
    - Example: `/api/v1/permissions/all`

### Role

1. Get All Roles

    - URL: `/api/v1/roles`
    - Method: `GET`
    - Headers: `Authorization Bearer <token>`
    - Query Parameters:
        - `page`: The page number
        - `perPage`: The number of items per page
        - `search`: The search query
        - `sort`: The sort field
        - `order`: The sort order
        - Example: `/api/v1/roles?page=1&perPage=10&search=example&sort=name&order=asc`

2. Get Role By Id

    - URL: `/api/v1/roles/{id}`
    - Method: `GET`
    - Headers: `Authorization Bearer <token>`
    - URL Parameters:
        - `id`: The role id
        - Example: `/api/v1/roles/1`

3. Create Role

    - URL: `/api/v1/roles`
    - Method: `POST`
    - Headers: `Authorization Bearer <token>`
    - Request Body Example:
        ```json
        {
            "name": "Manager",
            "permissions": [
                "users.index",
                "users.show",
                "users.store",
                "users.update",
                "users.destroy"
            ]
        }
        ```

4. Update Role

    - URL: `/api/v1/roles/{id}`
    - Method: `PATCH`
    - Headers: `Authorization Bearer <token>`
    - URL Parameters:
        - `id`: The role id
        - Example: `/api/v1/roles/1`
        - Request Body Example:
            ```json
            {
                "name": "Manager Updated",
                "permissions": [
                    "users.index",
                    "users.show",
                    "users.store",
                    "users.update",
                    "users.destroy"
                ]
            }
            ```

5. Delete Role
    - URL: `/api/v1/roles/{id}`
    - Method: `DELETE`
    - Headers: `Authorization Bearer <token>`
    - URL Parameters:
        - `id`: The role id
        - Example: `/api/v1/roles/1`

### Company

1. Get All Companies

    - URL: `/api/v1/companies`
    - Method: `GET`
    - Headers: `Authorization Bearer <token>`
    - Query Parameters:
        - `page`: The page number
        - `perPage`: The number of items per page
        - `search`: The search query
        - `sort`: The sort field
        - `order`: The sort order
        - Example: `/api/v1/companies?page=1&perPage=10&search=example&sort=name&order=asc`

2. Get Company By Id

    - URL: `/api/v1/companies/{id}`
    - Method: `GET`
    - Headers: `Authorization Bearer <token>`
    - URL Parameters:
        - `id`: The company id
    - Example: `/api/v1/companies/1`

3. Create Company

    - URL: `/api/v1/companies`
    - Method: `POST`
    - Headers: `Authorization Bearer <token>`
    - Request Body Example:
        ```json
        {
            "name": "PT Example",
            "email": "pt.example@gmail.com",
            "logo": "test.png",
            "address": "Jln Raya Kudus No.56",
            "phone": "089787676565"
        }
        ```

4. Update Company

    - URL: `/api/v1/companies/{id}`
    - Method: `PATCH`
    - Headers: `Authorization Bearer <token>`
    - URL Parameters:
        - `id`: The company id
        - Example: `/api/v1/companies/1`
    - Request Body Example:
        ```json
        {
            "name": "PT Example Updated",
            "email": "pt.example@gmail.com",
            "logo": "test.png",
            "address": "Jln Raya Kudus No.56",
            "phone": "089787676565"
        }
        ```

5. Delete Company
    - URL: `/api/v1/companies/{id}`
    - Method: `DELETE`
    - Headers: `Authorization Bearer <token>`
    - URL Parameters:
        - `id`: The company id
        - Example: `/api/v1/companies/1`

### User

1. Get All Users

    - URL: `/api/v1/users`
    - Method: `GET`
    - Headers: `Authorization Bearer <token>`
    - Query Parameters:
        - `page`: The page number
        - `perPage`: The number of items per page
        - `search`: The search query
        - `sort`: The sort field
        - `order`: The sort order
        - Example: `/api/v1/users?page=1&perPage=10&search=example&sort=name&order=asc`

2. Get User By Id

    - URL: `/api/v1/users/{id}`
    - Method: `GET`
    - Headers: `Authorization Bearer <token>`
    - URL Parameters:
        - `id`: The user id
    - Example: `/api/v1/users/1`

3. Create User

    - URL: `/api/v1/users`
    - Method: `POST`
    - Headers: `Authorization Bearer <token>`
    - Request Body Example:
        ```json
        {
            "name": "PT example manager",
            "email": "ptexample.manager@gmail.com",
            "password": "password",
            "password_confirmation": "password",
            "company_id": 2,
            "roles": ["manager"]
        }
        ```

4. Update User

    - URL: `/api/v1/users/{id}`
    - Method: `PATCH`
    - Headers: `Authorization Bearer <token>`
    - URL Parameters:
        - `id`: The user id
        - Example: `/api/v1/users/1`
    - Request Body Example:
        ```json
        {
            "name": "PT example manager updated",
            "email": "ptexample.manager@gmail.com",
            "password": "password",
            "password_confirmation": "password",
            "company_id": 2,
            "roles": ["manager"]
        }
        ```

5. Delete User
    - URL: `/api/v1/users/{id}`
    - Method: `DELETE`
    - Headers: `Authorization Bearer <token>`
    - URL Parameters:
        - `id`: The user id
        - Example: `/api/v1/users/1`

## Packages & Libraries

1. Spatie Laravel Permission - [Spatie Laravel Permission](https://spatie.be/docs/laravel-permission/v6/)
2. Json Web Token - [JWT](https://jwt.io/)
