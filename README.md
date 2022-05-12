# BileMo

Créez un Web service exposant une API - Projet P7 Openclassrooms

[![Codacy Badge](https://app.codacy.com/project/badge/Grade/10937bc04a524e6c90c56a18d0eac63c)](https://www.codacy.com/gh/Urza45/BileMo/dashboard?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=Urza45/BileMo&amp;utm_campaign=Badge_Grade)

## Context

BileMo is a company offering a whole selection of high-end mobile phones.

You are in charge of the development of the BileMo company's mobile phone showcase. BileMo's business model is not to sell its products directly on the website, but to provide all the platforms that want access to the catalog via an API (Application Programming Interface). It is therefore a sale exclusively in B2B (business to business).

You will need to expose a number of APIs for applications on other web platforms to perform operations.

## Description of need

The first customer has finally signed a partnership contract with BileMo! It's the commotion to meet the needs of this first customer that will make it possible to set up all the APIs and test them right away.

After a dense meeting with the client, a certain amount of information was identified. It must be possible to:

- Consult the list of BileMo products;
- Consult the details of a BileMo product;
- Consult the list of registered users linked to a client on the website;
- Consult the details of a registered user linked to a client;
- Add a new user linked to a customer;
- Delete a user added by a customer.

Only referenced clients can access the APIs. API clients must be authenticated via OAuth or JWT.

## Presentation of data

BileMo's first partner is very demanding: it requires that you expose your data following the rules of levels 1, 2 and 3 of the Richardson model. It requested that you serve the data in JSON. If possible, the client wants responses to be cached to optimize the performance of requests to the API.

## Pre-requisites

The present project was developed with:

- PHP 7.4.9 (cli) (built: Aug  4 2020 11:52:41)
- MySQL  5.7.31 Community Server (GPL)

## Installation

1.Clone Repository on your web server :

```text
git clone git@github.com:Urza45/bilemo.git
```

2.Install dependencies, in a command prompt:

```text
composer install
```

3.Configure BDD connect on `.env` file

4.Create database, in a command prompt:

```text
php bin/console doctrine:database:create
```

5.Migrate tables on database, in a command prompt:

```text
php bin/console doctrine:migrations:migrate
```

6.Load fixtures into the database, in a command prompt:

```text
php bin/console doctrine:fixtures:load
```

(You need fixtures to have one client account and some others parameters in database)

7.Account fixtures:

- Email  : martin@email.com
- Pass   : B123_456T

- Email  : landru@email.com
- Pass   : T453_25R

8.Configure JWT token for authentication and autorization

Create private and public keys with OpenSSL:

```text
mkdir -p config/jwt
openssl genrsa -out config/jwt/private.pem -aes256 4096
openssl rsa -pubout -in config/jwt/private.pem -out config/jwt/public.pem
```

When asking for passphrase, choose one and write it in .env file

```text
    JWT_PASSPHRASE=yourpassphrase
```
