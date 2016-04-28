#PHP CLI/Library for the Skype Bot API

###API Docs: https://developer.microsoft.com/en-us/skype/bots/docs

##Installation

There are 2 ways to install it: 
 
  - Download the Phar
  - Install as a Composer Package

###Download the Phar

download the latest version from the [Releases section](https://github.com/radutopala/skype-bot-php/releases/latest) or from the cli:

```
$ wget https://github.com/radutopala/skype-bot-php/releases/download/1.0.0/skype.phar && chmod +x skype.phar
```

###Install as a Composer Package

```
$ composer require radutopala/skype-bot-php
```

##Usage

### programmatic:

```php
<?php

use Skype\Client;

$client = new Client([
    'clientId' => '<yourClientId>',
    'clientSecret' => '<yourClientSecret>',
]);
$api = $client->authorize()->api('conversation');   // Skype\Api\Conversation
$api->activity('8:<skypeUsername>', 'Your message');
```

### cli:

Here some usage examples.

```
$ bin/skype auth <yourClientId>
$ bin/skype conversation:activity <to> <message>
```

Or with the phar file. 

```
php skype.phar auth <yourClientId>
php skype.phar conversation:activity <to> <message>
```

##Tips
 - If used as a library, the HTTP Guzzle Client will automatically try to re-authenticate using a Guzzle middleware, if the `access_token` will expire in the following 10 minutes.
 - If used as a phar, you can update it to latest version using `skype.phar self-update`
 - If used as a library, you can store the token configs in your own preffered file path, as follows:
 
   ```
   $client = new Client([
        'clientId' => '<yourClientId>',
        'clientSecret' => '<yourClientSecret>',
        'fileTokenStoragePath' => '<yourOwnPath>',
   ]);
 ```

More docs to come soon.
