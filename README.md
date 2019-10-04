# Example project based on the Async Bot framework

Currently this is a very basic example which uses the StackOverflow chat driver to log into a chat room and the Timer plugin to post the time in the room every 30 seconds.

## Requirements

- PHP 7.4

## Usage

- Get a StackOverflow account at: https://stackoverflow.com/users/signup?ssrc=head&returnurl=https%3a%2f%2fstackoverflow.com%2f
  - Do not use 3rd party sign up
- Copy the config file `./config.php.dist` to `./config.php`
- Fill in your credentials in `./config.php`
- Run the script `php ./bin/botman.php`
