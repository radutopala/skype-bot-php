#PHP CLI/Library for the Skype Bot API

###API Docs: https://developer.microsoft.com/en-us/skype/bots/docs

###Installation as a Composer Package

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

More docs to come soon.
