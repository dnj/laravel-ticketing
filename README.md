# Laravel Ticketing
 
[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]
[![Software License][ico-license]][link-license]
[![Testing status][ico-workflow-test]][link-workflow-test]
 
## Introduction 

This package will help you for manage tickets and support your clients.
* Features include: 
   * Department Management 
   * Ticket Management 
   * Ticket Messages Management 
   * Ticket Attachments Management.
* Latest versions of PHP and PHPUnit and PHPCsFixer
* Best practices applied:
  * [`README.md`][link-readme] (badges included)
  * [`LICENSE`][link-license]
  * [`composer.json`][link-composer-json]
  * [`phpunit.xml`][link-phpunit]
  * [`.gitignore`][link-gitignore]
  * [`.php-cs-fixer.php`][link-phpcsfixer]

## Installation

Require this package with composer.

```shell
composer require dnj/laravel-ticketing
```

Laravel uses Package Auto-Discovery, so doesn't require you to manually add the ServiceProvider.


#### Copy the package config to your local config with the publish command:

```shell
php artisan vendor:publish --provider="dnj\Ticket\TicketServiceProvider"
```
#### Config file 

```php

  [
    // If set True we will migrate and validate title field for ticket.
    'title' => true,

    // Define your user model class for connect tickets to users. | example: App\User:class
    'user_model' => null,

    /**
     * By default, the dnj/local-filesystem package is used to store files.
     * According to the Directory class, you can specify the main path for storing file
     */
    'attachment_root' => new Directory(public_path('ticket')),

    /**
     * Specifies the number of folders to create for storage based on the file hash
     * If the hash of your file is 8c7dd922ad47494fc02c388e12c00eac and dir_layer_number is 2, 
     * the file is saved with this structure :  [path]/8c/7d/8c7dd922ad47494fc02c388e12c00eac.jpg
     */
    'dir_layer_number' => 2,

    /**
     * You can set the validations you want to store files from here.
     */
    'attachment_rules' => [
        'mimes:jpg,png,txt', 'mimetypes:text/plain,image/jpeg,image/png', 'max:1024',
  ]

```
---

ℹ️ **Note**
> By default, the log is recorded for create,update and delete methods. If you want it not to be saved, follow the code below

Example :

```php
$department = new DepartmentManager();
$department->setSaveLogs(false);
$department->store(title:'Sell Department');
```


## Department basic usage:


Search department :
***

```php

<?php
use dnj\Ticket\Managers\DepartmentManager;

$department = new DepartmentManager();

 /**
 * @param array{title?:string,created_start_date?:string,created_end_date?:string,updated_start_date?:string,updated_end_date?:string}|null $filters
 * @return iterable<IDepartment>
 */

$departments = $department->search(filters:['title'=>'sell']);

```


Create new department :
***

```php
<?php
use dnj\Ticket\Managers\DepartmentManager;

$department = new DepartmentManager();

 /**
 * @param  string $title
 * @return IDepartment
 */

$department = $department->store(title:'Sell Department');

```

Show department :
***

```php

<?php
use dnj\Ticket\Managers\DepartmentManager;

$department = new DepartmentManager();

/**
 * @param  int $id
 * @return IDepartment
 */
$department = $department->find(id:1);
```

Update department :
***

```php

<?php
use dnj\Ticket\Managers\DepartmentManager;

$department = new DepartmentManager();

/**
 * @param int $id
 * @param array{title?:string} $changes
 * @return IDepartment
 */
$department = $department->update(id:1,['title' => 'Support department']);
```

Delete department :
***

```php

<?php
use dnj\Ticket\Managers\DepartmentManager;

$department = new DepartmentManager();
/**
 * @param int $id
 */
$department->destroy(id:1);

```

## Ticket basic usage:

Search tickets :
***

```php

<?php
use dnj\Ticket\Managers\TicketManager;

$ticket = new TicketManager();

/**
 * @param array{title?:string,client_id?:int,department_id?:int,status?:TicketStatus[],created_start_date?:DateTimeInterface,created_end_date?:DateTimeInterface,updated_start_date?:DateTimeInterface,updated_end_date?:DateTimeInterface}|null $filters
 * @return iterable<ITicket>
 */

$tickets = $ticket->search(filters:[
  'client_id'=>1,
  'status'=>['UNREAD','IN_PROGRESS'],
  ]);

```

Create new ticket :
***

```php
<?php
use dnj\Ticket\Managers\TicketManager;
use dnj\Ticket\Enums\TicketStatus;

$ticket = new TicketManager();

/**
 * @param int $clientId
 * @param int $departmentId
 * @param string $message
 * @param array<int|UploadedFile> $files
 * @param string $title = null
 * @param ?int $userId = null
 * @param TicketStatus $status = null
 * @throws ITicketTitleHasBeenDisabledException if $title is set but title is disabled
 * @return IMessage
 */
$ticket = $ticket->store(
    clientId:1, 
    departmentId:1, 
    message:'First message for ticket',
    files:[],
    title:'Ticket subject', 
    userId: auth()->user()->id,
    status: null
  );

```

Show ticket :
***

```php
<?php
use dnj\Ticket\Managers\TicketManager;

$ticket = new TicketManager();

/**
 * @param int id
 * @return ITicket
 */
$ticket = $ticket->find(id:1);

```

Update ticket :
***

```php

<?php
use dnj\Ticket\Managers\TicketManager;

$ticket = new TicketManager();

/**
 * @param int $id
 *@param array{title?:string,client_id?:int,department_id?:int,status?:TicketStatus} $changes
 * @return ITicket
 */
$ticket = $ticket->update(id:1,['client_id'=>3]);

```

Delete ticket :
***

```php
<?php
use dnj\Ticket\Managers\TicketManager;

$ticket = new TicketManager();

/**
 * @param int $id
 */
$ticket->destroy(id:1);

```

## Ticket Message basic usage:

Search ticket messages :
***

```php

<?php
use dnj\Ticket\Managers\TicketMessageManager;

$ticketMessages = new TicketMessageManager();

/**
 * @param int $ticketId
 * @param array{user_id?:int,sort?:string,created_start_date?:DateTimeInterface,created_end_date?:DateTimeInterface,updated_start_date?:DateTimeInterface,updated_end_date?:DateTimeInterface}|null $filters
* @return iterable<IMessage>
*/

$ticketMessages = $ticketMessages->search(id:5,filters:[
  'user_id'=>1,
  'sort' => 'desc'
  ]);

```

Create new ticket message :
***

```php
<?php
use dnj\Ticket\Managers\TicketMessageManager;
use dnj\Ticket\Enums\TicketStatus;

$ticketMessage = new TicketMessageManager();

/**
 * @param int $ticketId
 * @param string $message
 * @param array<int|UploadedFile> $files
 * @param ?int $userId = null
 * @return IMessage
 */
$ticketMessage = $ticketMessage->store(
    ticketId:1,
    message:'Second message for ticket',
    files:[113],
    userId: auth()->user()->id,
    status: null
  );

```

Show ticket message :
***

```php
<?php
use dnj\Ticket\Managers\TicketMessageManager;

$ticketMessage = new TicketMessageManager();

/**
 * @param int id
 * @return IMessage
 */
$ticketMessage = $ticketMessage->find(id:4);

```

Update ticket message :
***

```php

<?php
use dnj\Ticket\Managers\TicketMessageManager;

$ticketMessage = new TicketMessageManager();

/**
 *@param int $id
 *@param array{message?:string,userId?:int} $changes
 *@return TicketMessage
 */
$ticketMessage = $ticketMessage->update(id:1,['message'=>'Ticket message has updated']);

```

Delete ticket message :
***

```php
<?php
use dnj\Ticket\Managers\TicketMessageManager;

$ticketMesage = new TicketMessageManager();

/**
 * @param int $id
 */
$ticketMesage->destroy(id:1);

```

## Ticket Attachment basic usage:

Search ticket attachment :
***

```php

<?php
use dnj\Ticket\Managers\TicketAttachmentManager;

$ticketAttachment = new TicketAttachmentManager();

/**
* @param int $messageId
* @return iterable<IAttachment>
*/

$ticketAttachment = $ticketAttachment->search(messageId:5);

```

Create new ticket attachment :
***

```php
<?php
use dnj\Ticket\Managers\TicketAttachmentManager;
use Illuminate\Http\UploadedFile;

$ticketAttachment = new TicketAttachmentManager();

/**
 * @param UploadedFile $file
 * @param int $messageId
 * @return IAttachment
 */
$ticketAttachment = $ticketAttachment->storeByUpload(
    file:UploadedFile::fake()->image('avatar.jpg'),
    messageId:2,
  );

```

Show ticket attachment :
***

```php
<?php
use dnj\Ticket\Managers\TicketAttachmentManager;

$ticketAttachment = new TicketAttachmentManager();

/**
 * @param int id
 * @return IAttachment
 */
$ticketAttachment = $ticketAttachment->find(id:4);

```

Find orphans ticket attachment :
***

This method helps you find stray files that are not attached to a message 

```php
<?php
use dnj\Ticket\Managers\TicketAttachmentManager;

$ticketAttachment = new TicketAttachmentManager();

/**
 * @param int id
 * @return iterable
 */
$ticketAttachment->findOrphans(id:4);

```

Update ticket attachment :
***

```php

<?php
use dnj\Ticket\Managers\TicketAttachmentManager;

$ticketAttachment = new TicketAttachmentManager();

/**
 *@param int $id
 *@param array{message_id?:int} $changes
 *@return IAttachment
 */
$ticketAttachment = $ticketAttachment->update(id:1,message_id:5);

```

Delete ticket attachment :
***

```php
<?php
use dnj\Ticket\Managers\TicketAttachmentManager;

$ticketAttachment = new TicketAttachmentManager();

/**
 * @param int $id
 */
$ticketAttachment->destroy(id:1);

```

## How to use package API

A document in YAML format has been prepared for better familiarization and use of package web services. which is placed in the [`docs`][link-docs] folder.

To use this file, you can import it on the [stoplight.io](https://stoplight.io) site and see all available web services.


## Contribution

Contributions are what make the open source community such an amazing place to learn, inspire, and create. Any contributions you make are greatly appreciated.

If you have a suggestion that would make this better, please fork the repo and create a pull request. You can also simply open an issue with the tag "enhancement". Don't forget to give the project a star! Thanks again!

1. Fork the Project
2. Create your Feature Branch (`git checkout -b feature/AmazingFeature`)
3. Commit your Changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the Branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request


## Security
If you discover any security-related issues, please email [hi@dnj.co.ir](hi@dnj.co.ir) instead of using the issue tracker.

## License

The MIT License (MIT). Please see [License File][link-license] for more information.


[ico-version]: https://img.shields.io/packagist/v/dnj/laravel-ticketing.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/dnj/laravel-ticketing.svg?style=flat-square
[ico-workflow-test]: https://github.com/dnj/laravel-ticketing/actions/workflows/ci.yml/badge.svg

[link-workflow-test]: https://github.com/dnj/laravel-ticketing/actions/workflows/ci.yml
[link-packagist]: https://packagist.org/packages/dnj/laravel-ticketing
[link-license]: https://github.com/dnj/laravel-ticketing/blob/master/LICENSE
[link-downloads]: https://packagist.org/packages/dnj/laravel-ticketing
[link-readme]: https://github.com/dnj/laravel-ticketing/blob/master/README.md
[link-docs]: https://github.com/dnj/laravel-ticketing/blob/master/docs/openapi.yaml
[link-composer-json]: https://github.com/dnj/laravel-ticketing/blob/master/composer.json
[link-phpunit]: https://github.com/dnj/laravel-ticketing/blob/master/phpunit.xml
[link-gitignore]: https://github.com/dnj/laravel-ticketing/blob/master/.gitignore
[link-phpcsfixer]: https://github.com/dnj/laravel-ticketing/blob/master/.php-cs-fixer.php
[link-author]: https://github.com/dnj
