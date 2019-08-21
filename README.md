# Example project based on the Async Bot framework

Currently this is a very basic example which uses the StackOverflow chat driver to log into a chat room and the Timer plugin to post the time in the room every 30 seconds.

## Requirements

- PHP 7.4

## Usage

- Get a StackOverflow account at: https://stackoverflow.com/users/signup?ssrc=head&returnurl=https%3a%2f%2fstackoverflow.com%2f
  - Do not use 3rd party sign up
- Fill in your username and password in `./bin/botman.php`

```php
new Authenticator($httpClient, new Credentials('your_username', 'your_password')),
```

- Run the script `php ./bin/botman.php`

Currently it only posts the time every 30 seconds in: https://chat.stackoverflow.com/rooms/198198/asyncbot-playground
