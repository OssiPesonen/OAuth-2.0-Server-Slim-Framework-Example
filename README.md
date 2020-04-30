# OAuth 2.0 server example using Slim Framework

This is an opinionated demo repository for OAuth 2.0 server implementation using Slim Framework and MySQL as the storage device.

## Installation

1. Clone the repository
2. Run `composer install`
3. Rename the `.env.dist` and set your environment variable values
4. Create a private and public keys for the OAuth server (https://oauth2.thephpleague.com/installation) and store them somewhere on your computer. Set your `PRIVATE_KEY_PATH` value to point to this file, for example: `PRIVATE_KEY_PATH=C:/dir/private.key` or `PRIVATE_KEY=/Library/www/private.key`
5. Generate a random encryption key for JWT using `php -r 'echo base64_encode(random_bytes(32)), PHP_EOL;'` on your CLI and copy the result to your .env file's `ENCRYPTION_KEY` variable.
6. Set up your database and import the `DatabaseTables.sql` or alter this to fit your own storage engine, but this needs to be supported by Doctrine DBAL and you might need to alter the `AuthModel.php` file.

You can run the app quickly by executing `composer start` on your CLI tool.