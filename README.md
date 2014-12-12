# Laravel JSON Exception Formatter

A custom interface for the Laravel [JSON Exception Formatter](https://github.com/Radweb/JSON-Exception-Formatter) package.

This package is designed to work with exceptions that extend the [antarctica/laravel-base-exceptions](https://packagist.org/packages/antarctica/laravel-base-exceptions) package, but does not require this.

## Installing

Require this package in your `composer.json` file:

```json
{
	"require": {
		"antarctica/laravel-json-exception-formatter": "dev-develop"
	}
}
```

Run `composer update`.

Register the service provider in `app/config/app.php`:

```php
'providers' => array(

	'Antarctica\JsonExceptionFormatter\JsonExceptionFormatterServiceProvider',
	
),
```

Note: this package automatically requires and registers the [JSON Exception Formatter](https://github.com/Radweb/JSON-Exception-Formatter) package behind the scenes.

## Usage

Registering the service provider is enough to automatically call this package. The underlying [JSON Exception Formatter](https://github.com/Radweb/JSON-Exception-Formatter) is triggered whenever a request that requests a JSON response (i.e. `accept: application/json`) and an exception is thrown.

This package is a [Custom Formatter](https://github.com/Radweb/JSON-Exception-Formatter#custom-formatters) of this underlying package.

It provides an alternative representation for exceptions as errors for both debug and non-debug environments.

The main difference in our custom implementation is the inclusion of the custom properties available to exceptions that extend the [HttpException](https://github.com/antarctica/laravel-base-exceptions#httpexception) exception from the [antarctica/laravel-base-exceptions](https://packagist.org/packages/antarctica/laravel-base-exceptions) package.

Exceptions extending other exceptions will be rendered without this extra information, but do not remove any information (i.e. this package becomes transparent).

In addition to extra error information, debug environments will include the PHP stack trace in responses to aid to diagnostics.

### Example usage

Exception on which responses are based upon:

```php
<?php

use Antarctica\LaravelBaseExceptions\Exception\HttpException;

class AuthenticationException extends HttpException {

    protected $statusCode = 401;

    protected $kind = 'authentication_failure';

    protected $details = [
        "authentication_error" => [
            "Ensure your credentials are correct and that your user account is still active, or contact the maintainer of this API for assistance."
        ]
    ];
}
```

Exception in a non-debug environment:

```json
{
    "errors": [
        {
            "details": {
                "authentication_error": [
                    "Ensure your credentials are correct and that your user account is still active, or contact the maintainer of this API for assistance."
                ]
            },
            "type": "authentication_failure"
        }
    ]
}
```

Exception in a debug environment:

```json
{
    "errors": [
        {
            "details": {
                "authentication_error": [
                    "Ensure your credentials are correct and that your user account is still active, or contact the maintainer of this API for assistance."
                ]
            },
            "exception": "Antarctica\\LaravelTokenAuth\\Exception\\Auth\\AuthenticationException",
            "file": "/app/vendor/antarctica/laravel-token-auth/src/Antarctica/LaravelTokenAuth/Service/Token/TokenServiceJwtAuth.php",
            "kind": "authentication_failure",
            "line": 88,
            "stack_trace": [
                {
                    "file": "/app/vendor/antarctica/laravel-token-auth/src/Antarctica/LaravelTokenAuth/Service/TokenUser/TokenUserService.php",
                    "line": 91,
                    "function": "authOnce",
                    "class": "Antarctica\\LaravelTokenAuth\\Service\\Token\\TokenServiceJwtAuth",
                    "type": "->",
                    "args": [
                        {
                            "username": "xxx",
                            "password": "xxx"
                        }
                    ]
                },
                ...
                {
                    "file": "/app/public/index.php",
                    "line": 49,
                    "function": "run",
                    "class": "Illuminate\\Foundation\\Application",
                    "type": "->",
                    "args": []
                }
            ]
        }
    ]
}
```


## Developing

To aid development and keep your local computer clean, a VM (managed by Vagrant) is used to create an isolated environment with all necessary tools/libraries available.

### Requirements

* Mac OS X
* Ansible `brew install ansible`
* [VMware Fusion](http://vmware.com/fusion)
* [Vagrant](http://vagrantup.com) `brew cask install vmware-fusion vagrant`
* [Host manager](https://github.com/smdahlen/vagrant-hostmanager) and [Vagrant VMware](http://www.vagrantup.com/vmware) plugins `vagrant plugin install vagrant-hostmanager && vagrant plugin install vagrant-vmware-fusion`
* You have a private key `id_rsa` and public key `id_rsa.pub` in `~/.ssh/`
* You have an entry like [1] in your `~/.ssh/config`

[1] SSH config entry

```shell
Host bslweb-*
    ForwardAgent yes
    User app
    IdentityFile ~/.ssh/id_rsa
    Port 22
```

### Provisioning development VM

VMs are managed using Vagrant and configured by Ansible.

```shell
$ git clone ssh://git@stash.ceh.ac.uk:7999/basweb/laravel-json-exception-formatter.git
$ cp ~/.ssh/id_rsa.pub laravel-json-exception-formatter/provisioning/public_keys/
$ cd laravel-json-exception-formatter
$ ./armadillo_standin.sh

$ vagrant up

$ ssh bslweb-laravel-json-exception-formatter-dev-node1
$ cd /app

$ composer install

$ logout
```

### Committing changes

The [Git flow](https://github.com/fzaninotto/Faker#formatters) workflow is used to manage development of this package.

Discrete changes should be made within *feature* branches, created from and merged back into *develop* (where small one-line changes may be made directly).

When ready to release a set of features/changes create a *release* branch from *develop*, update documentation as required and merge into *master* with a tagged, [semantic version](http://semver.org/) (e.g. `v1.2.3`).

After releases the *master* branch should be merged with *develop* to restart the process. High impact bugs can be addressed in *hotfix* branches, created from and merged into *master* directly (and then into *develop*).

### Issue tracking

Issues, bugs, improvements, questions, suggestions and other tasks related to this package are managed through the BAS Web & Applications Team Jira project ([BASWEB](https://jira.ceh.ac.uk/browse/BASWEB)).

### Clean up

To remove the development VM:

```shell
vagrant halt
vagrant destroy
```

The `laravel-json-exception-formatter` directory can then be safely deleted as normal.

