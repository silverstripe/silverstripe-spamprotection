# SpamProtection Module

[![CI](https://github.com/silverstripe/silverstripe-spamprotection/actions/workflows/ci.yml/badge.svg)](https://github.com/silverstripe/silverstripe-spamprotection/actions/workflows/ci.yml)
[![Silverstripe supported module](https://img.shields.io/badge/silverstripe-supported-0071C4.svg)](https://www.silverstripe.org/software/addons/silverstripe-commercially-supported-module-list/)

## Installation

```sh
composer require silverstripe/spamprotection
```

## Maintainer Contact

 * Saophalkun Ponlu
   <phalkunz (at) silverstripe (dot) com>

 * Will Rossiter
   <will (at) fullscreen (dot) io>

## Documentation

This module provides a generic, consistent API for adding spam protection to
your Silverstripe Forms. This does not provide any spam protection out of the
box. For that, you must also download one of the spam protection
implementations. Currently available options are:

* reCAPTCHA v2 (two implementations: [one](https://github.com/chillu/silverstripe-recaptcha), [two](https://github.com/UndefinedOffset/silverstripe-nocaptcha))
* [MathSpamProtection](https://github.com/silverstripe/silverstripe-mathspamprotection)
* [Akismet](https://github.com/silverstripe/silverstripe-akismet)
* [Mollom](https://github.com/silverstripe-archive/silverstripe-mollom)
* [Cloudflare Turnstile](https://github.com/silverstripe-terraformers/turnstile-captcha/)

As a developer you can also provide your own protector by creating a class which
implements the `\SilverStripe\SpamProtection\SpamProtector` interface. More on that below.

## Configuring

After installing this module and a protector of your choice (i.e mollom) you'll
need to rebuild your database through `dev/build` and set the default protector
via SilverStripe's config system. This will update any Form instances that have
spam protection hooks with that protector.

*mysite/_config/spamprotection.yml*

```yaml
---
name: mycustomspamprotection
---
SilverStripe\SpamProtection\Extension\FormSpamProtectionExtension:
  default_spam_protector: MollomSpamProtector
```

To add spam protection to your form instance call `enableSpamProtection`.

```php
// your existing form code
$form = new Form(/* .. */);
$form->enableSpamProtection();
```

The logic to perform the actual spam validation is controlled by each of the
individual `SpamProtector` implementations since they each require a different
implementation client side or server side.

### Options

`enableSpamProtection` takes a hash of optional configuration values.

```php
$form->enableSpamProtection(array(
    'protector' => MathSpamProtector::class,
    'name' => 'Captcha'
));
```

Options to configure are:

* `protector`: a class name string or class instance which implements
`\SilverStripe\SpamProtection\SpamProtector`. Defaults to your
`SilverStripe\SpamProtection\Extension\FormSpamProtectionExtension.default_spam_protector` value.

* `name`: the form field name argument for the Captcha. Defaults to `Captcha`.
* `title`: title of the Captcha form field. Defaults to `''`
* `insertBefore`: name of existing field to insert the spam protection field prior to
* `mapping`: an array mapping of the Form fields to the standardised list of
field names. The list of standardised fields to pass to the spam protector are:

```
title
body
contextUrl
contextTitle
authorName
authorMail
authorUrl
authorIp
authorId
```

## Defining your own `SpamProtector`

Any class that implements `\SilverStripe\SpamProtection\SpamProtector` and the `getFormField()` method can
be set as the spam protector. The `getFormField()` method returns the
`FormField` to be inserted into the `Form`. The `FormField` returned should be
in charge of the validation process.

```php
<?php

use CaptchaField;
use SilverStripe\SpamProtection\SpamProtector;

class CustomSpamProtector implements SpamProtector
{
    public function getFormField($name = null, $title = null, $value = null)
    {
        // CaptchaField is an imagined class which has some functionality.
        // See silverstripe-mollom module for an example.
        return new CaptchaField($name, $title, $value);
	}
}
```

## Using Spam Protection with User Forms

This module provides an `EditableSpamProtectionField` wrapper which you can add
to your UserForm instances. After installing this module and running `/dev/build`
to rebuild the database, your Form Builder interface will have an option for
`Spam Protection Field`. The type of spam protection used will be based on your
currently selected SpamProtector instance.

## Releasing code with Spam Protection support

Spam protection is useful to provide but in some cases we do not want to require
the developer to use spam protection. In that case, modules can provide the
following pattern:

```php
use SilverStripe\Forms\Form;
use SilverStripe\SpamProtection\Extension\FormSpamProtectionExtension;

$form = new Form(/* .. */);

if ($form->hasExtension(FormSpamProtectionExtension::class)) {
    $form->enableSpamProtection();
}
```
