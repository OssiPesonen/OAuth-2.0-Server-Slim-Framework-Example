# OAuth 2.0 Server example using Slim Framework

This is an opinionated demo repository for OAuth 2.0 server implementation using Slim Framework and MySQL as the storage device.

## Installation

1. Clone the repository
2. Run `composer install`
3. Rename the `.env.dist` and set your environment variable values
4. Create a private and public keys for the OAuth server (https://oauth2.thephpleague.com/installation) and store them somewhere on your computer. Set your `PRIVATE_KEY_PATH` value to point to this file, for example: `PRIVATE_KEY_PATH=C:/dir/private.key` or `PRIVATE_KEY=/Library/www/private.key`
5. Generate a random encryption key for JWT using `php -r 'echo base64_encode(random_bytes(32)), PHP_EOL;'` on your CLI and copy the result to your .env file's `ENCRYPTION_KEY` variable.
6. Set up your database and import the `DatabaseTables.sql` or alter this to fit your own storage engine, but this needs to be supported by Doctrine DBAL and you might need to alter the `AuthModel.php` file.

You can run the app quickly by executing `composer start` on your CLI tool.

## Try it out

Now a test client already exists in the database (if you copied the whole SQL file) that says your `redirect_uri` is pointing to `http://localhost/oauth_callback`.

You need to create an `index.php` file to your `/oauth_callback` folder at your web root to receive the `code` as you will be automatically redirected there. 

**Here's an example:**

```php
<?php

echo urldecode($_GET['state']);
echo "\t\n";
echo urldecode($_GET['code']);
```

You can try it out by starting the server and executing a GET request to `http://localhost:8080/authorize` using, for example, Postman

```curl
curl --location --request GET 'http://localhost:8080/authorize?response_type=code&client_id=test&scope=email&redirect_uri=http://localhost/oauth_callback&state=myCoolState' \
--header 'Cookie: XDEBUG_SESSION=PHPSTORM'
```

Now, copy the second line (about 700+ characters) to your second POST request to `/access_token`:

```curl
curl --location --request POST 'http://localhost:8080/access_token' \
--header 'Content-Type: application/x-www-form-urlencoded' \
--data-urlencode 'client_id=test' \
--data-urlencode 'code=def50200b1424eef474f6cfe65b98772fb8587069c222cd9296edcb77b0e7c8826e250f26e2aaf5b57809d081e8591637119099a2831d8f3efb541e12049ee0d55c880b210036a5c4b3cead601716b8524514a4fb327488eaf0bed88e69d748d3541699cdbae3d85ef2d14e92f6320ee2b31b7f16ab90fa4b7bb00dba4af37204b85972726fd0bc17116483bf486dc5f78f406e804ca905ff8be7ce9303a3b77a421d4b97df100cc7872a4eadc835d818b7d9ca2a5bca81e5c917a191f3dfa320278eef8c67702044452b31c23d3c09aa92bc62533897930aed1f0a29eeb7be625a3318d4efbfe05cccb9512b7960d9c14af8dcc55baa0d16e1e97927de7099c910a02143d1d950fe3702c83e6cc0983ed0d262d896d5eaf43fbaf725d06b1344d280205efe2e189b0ce1a87481e2c831553046b52676b542d44758541fc415e1af0347130668dfb28a0bed20cc842575d584ac344bb43af36242fd15ab266083938687d23f1d9fa183d9a' \
--data-urlencode 'redirect_uri=http://localhost/oauth_callback' \
--data-urlencode 'grant_type=authorization_code' \
--data-urlencode 'client_secret=1234'
```

The resulting JSON should include the `access_token` as well as a `refresh_token` plus the expiration you set in the `dependencies.php`

