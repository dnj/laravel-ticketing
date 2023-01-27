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
   * Ticket Attachments Management
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
use dnj\Filesystem\Local\Directory;

return [
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
];

```
---

ℹ️ **Note**
> User activity logs are disabled by default, if you want to save them set `$userActivityLog` to true.

Example :

```php
use dnj\Ticket\Contracts\IDepartmentManager;

$departmentManager = app(IDepartmentManager::class);
$department = $departmentManager->store(
  title:'Sell Department',
  userActivityLog: true
); // returns an Department model which implementes IDepartment
```


## Working With Departments:

Search departments:

```php
use dnj\Ticket\Contracts\IDepartmentManager;
use dnj\Ticket\Contracts\IDepartment;

$departmentManager = app(IDepartmentManager::class);

 /**
 * @param array{title?:string,created_start_date?:string,created_end_date?:string,updated_start_date?:string,updated_end_date?:string}|null $filters
 * @var iterable<IDepartment> $departments
 */
$departments = $departmentManager->search(
  filters:['title' => 'sales']
);

```


Create new department:

```php
use dnj\Ticket\Contracts\IDepartmentManager;

$departmentManager = app(IDepartmentManager::class);

 /**
 * @param string $title
 * @param bool $userActivityLog = false
 * @return IDepartment
 */

$department = $departmentManager->store(title:'Sell Department');

```

Show department:

```php
use dnj\Ticket\Contracts\IDepartmentManager;

$departmentManager = app(IDepartmentManager::class);

/**
 * @param  int $id
 * @return IDepartment
 */
$department = $departmentManager->find(id:1);
```

Update department:

```php
use dnj\Ticket\Contracts\IDepartmentManager;

$departmentManager = app(IDepartmentManager::class);

/**
 * @param int $id
 * @param array{title?:string} $changes
 * @param bool $userActivityLog = false
 * @return IDepartment
 */
$department = $departmentManager->update(
  id:1,
  changes: ['title' => 'Support']
);
```

Delete department:

```php
use dnj\Ticket\Contracts\IDepartmentManager;

$departmentManager = app(IDepartmentManager::class);

/**
 * @param int $id
 * @param bool $userActivityLog = false
 * @return void
 */
$departmentManager->destroy(
  id:1,
  userActivityLog: false, // Prevent saving a log for authenticated user
);

```

***

## Working With Tickets:

Search tickets:

```php
use dnj\Ticket\Contracts\ITicketManager;
use dnj\Ticket\Contracts\ITicket;
use dnj\Ticket\Enums\TicketStatus;

$ticketManager = app(ITicketManager::class);

/**
 * @param array{title?:string,client_id?:int,department_id?:int,status?:TicketStatus[],created_start_date?:DateTimeInterface,created_end_date?:DateTimeInterface,updated_start_date?:DateTimeInterface,updated_end_date?:DateTimeInterface}|null $filters
 * @param bool $userActivityLog = false
 * @return iterable<ITicket>
 */

$tickets = $ticketManager->search(
  filters: [
    'client_id' => 1,
    'status' => [TicketStatus::UNREAD, TicketStatus::IN_PROGRESS],
  ]
);

```

Create new ticket:

```php
use dnj\Ticket\Managers\TicketManager;

$ticketManager = app(ITicketManager::class);

/**
 * @param int $clientId
 * @param int $departmentId
 * @param string $message
 * @param array<int|UploadedFile> $files
 * @param string|null $title = null
 * @param int|null $userId = null
 * @param dnj\Ticket\Enums\TicketStatus|null $status = null
 * @param bool $userActivityLog = false
 * @throws ITicketTitleHasBeenDisabledException if $title is set but title is disabled
 * @return IMessage
 */
$message = $ticketManager->store(
    clientId: 1, 
    departmentId: 1, 
    message:'First message for ticket',
    files:[],
    title:'Ticket subject', 
    userId: auth()->user()->id,
    status: null
  );

```

Show ticket:
```php
use dnj\Ticket\Managers\TicketManager;

$ticketManager = app(ITicketManager::class);

/**
 * @param int id
 * @return ITicket
 */
$ticket = $ticket->find(id:1);

```

Update ticket :

```php
use dnj\Ticket\Managers\TicketManager;

$ticketManager = app(ITicketManager::class);

/**
 * @param int $id
 * @param array{title?:string,client_id?:int,department_id?:int,status?:TicketStatus} $changes
 * @param bool $userActivityLog = false
 * @return ITicket
 */
$ticket = $ticket->update(
  id: 1,
  changes: ['client_id' => 3],
  userActivityLog: true
);

```

Delete ticket :
```php
use dnj\Ticket\Managers\TicketManager;

$ticketManager = app(ITicketManager::class);

/**
 * @param int $id
 * @param bool $userActivityLog = false
 * @return void
 */
$ticket->destroy(id:1);

```

***

## Working With Ticket Messages:

Search ticket messages :

```php

use dnj\Ticket\Contracts\IMessageManager;
use dnj\Ticket\Contracts\IMessage;

$messageManager = app(IMessageManager::class);

/**
 * @param int $ticketId
 * @param array{user_id?:int,sort?:string,created_start_date?:DateTimeInterface,created_end_date?:DateTimeInterface,updated_start_date?:DateTimeInterface,updated_end_date?:DateTimeInterface}|null $filters
* @return iterable<IMessage>
*/

$ticketMessages = $messageManager->search(
  ticketId:5,
  filters:[
    'user_id' => 1,
  ]
);

```

Create new ticket message :
***

```php
use dnj\Ticket\Contracts\IMessageManager;
use dnj\Ticket\Contracts\IMessage;
use dnj\Ticket\Enums\TicketStatus;

$messageManager = app(IMessageManager::class);

/**
 * @param int $ticketId
 * @param string $message
 * @param array<int|UploadedFile> $files
 * @param int|null $userId = null
 * @param bool $userActivityLog = false
 * @return IMessage
 */
$ticketMessage = $messageManager->store(
    ticketId: 1,
    message: 'Second message for ticket',
    files: [113],
    userId: auth()->user()->id
  );

```

Show ticket message:
***

```php
use dnj\Ticket\Contracts\IMessageManager;
use dnj\Ticket\Contracts\IMessage;

$messageManager = app(IMessageManager::class);

/**
 * @param int id
 * @return IMessage
 */
$ticketMessage = $messageManager->find(id:4);

```

Update ticket message:
***

```php

use dnj\Ticket\Contracts\IMessageManager;
use dnj\Ticket\Contracts\IMessage;

$messageManager = app(IMessageManager::class);

/**
 * @param int $id
 * @param array{message?:string,userId?:int} $changes
 * @param bool $userActivityLog = false
 * @return IMessage
 */
$ticketMessage = $messageManager->update(
  id:1,
  changes: ['message'=>'Ticket message has updated']
);

```

Delete ticket message:

```php
use dnj\Ticket\Contracts\IMessageManager;

$messageManager = app(IMessageManager::class);

/**
 * @param int $id
 * @param bool $userActivityLog = false
 * @return void
 */
$messageManager->destroy(id:1);

```

***

## Ticket Attachment basic usage:

Search ticket attachment :
***

```php

use dnj\Ticket\Contracts\IAttachmentManager;
use dnj\Ticket\Contracts\IAttachment;

$attachmentManager = app(IAttachmentManager::class);

/**
* @param int $messageId
* @return iterable<IAttachment>
*/

$ticketAttachment = $attachmentManager->search(messageId:5);

```

Create new ticket attachment:

```php
use dnj\Ticket\Contracts\IAttachmentManager;
use dnj\Ticket\Contracts\IAttachment;
use Illuminate\Http\UploadedFile;

$attachmentManager = app(IAttachmentManager::class);

/**
 * @param UploadedFile $file
 * @param int $messageId
 * @param bool $userActivityLog = false
 * @return IAttachment
 */
$attachment = $attachmentManager->storeByUpload(
  file: UploadedFile::fake()->image('avatar.jpg'),
  messageId: 2,
);

```

Show the attachment model using it's ID:

```php
use dnj\Ticket\Contracts\IAttachmentManager;
use dnj\Ticket\Contracts\IAttachment;

$attachmentManager = app(IAttachmentManager::class);

/**
 * @param int $id
 * @return IAttachment
 */
$attachment = $attachmentManager->find(id:4);

```

Find orphan attachments:

This method helps you find stray files that are not attached to a message 

```php
use dnj\Ticket\Contracts\IAttachmentManager;
use dnj\Ticket\Contracts\IAttachment;

$attachmentManager = app(IAttachmentManager::class);

/**
 * @param int id
 * @return iterable<IAttachment>
 */
$orphans = $attachmentManager->findOrphans();

```

Update attachment:

```php

use dnj\Ticket\Contracts\IAttachment;
use dnj\Ticket\Contracts\IAttachmentManager;

$attachmentManager = app(IAttachmentManager::class);

/**
 * @param int $id
 * @param array{message_id?:int} $changes
 * @param bool $userActivityLog = false
 * @throws dnj\Ticket\Contracts\Exceptions\IAttachmentAlreadyAsignedException if you double assign a attachment
 * @return IAttachment
 */
$attachment = $attachmentManager->update(
  id:1,
  changes: [message_id => 5]
);

```

Delete ticket attachment:

```php
use dnj\Ticket\Contracts\IAttachmentManager;

$attachmentManager = app(IAttachmentManager::class);

/**
 * @param int $id
 * @param bool $userActivityLog = false
 * @return void
 */
$attachmentManager->destroy(id:1);

```

## HOWTO use Restful API

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
If you discover any security-related issues, please email [security@dnj.co.ir](mailto:security@dnj.co.ir) instead of using the issue tracker.

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
