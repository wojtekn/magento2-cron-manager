Magento 2 Crontab Manager Module
=============================

A Magento 2 module which adds CLI commands to enable and disable Magento 2 crontab entries.

Installation
------------

Please, use [Composer](https://getcomposer.org) and add `wojtekn/magento2-cron-manager` to your dependencies eg.

    $ composer require wojtekn/magento2-cron-manager

Then enable the module and run setup upgrade to be sure that module is installed and enabled:

    $ php bin/magento setup:upgrade

If you're using `production` or `default` Magento 2 mode you need to run Dependency Injection compilation process:

    $ php bin/magento setup:di:compile

Usage
-----

### Before you start

Make sure to backup your current crontab entries to be able to revert them if something goes wrong.

You can do this by running:

    crontab -l > ~/crontab.backup

### Simple usage

Before first use edit crontab using default editor by running `crontab -e` and add tags showing where Magento 2 related jobs start and end. That part of the crontab should look as follows:

    # [start:magento]
    * * * * * php <magento install dir>/bin/magento cron:run --group=index | grep -v "Ran jobs by schedule" >> <magento install dir>/var/log/magento.cron.log
    * * * * * php <magento install dir>/bin/magento cron:run --group=mailchimp | grep -v "Ran jobs by schedule" >> <magento install dir>/var/log/magento.cron.log
    # [end:magento]

Now run following command to disable CRON entries:

    php bin/magento cron:crontab:disable

And this one to enable CRON entries:

    php bin/magento cron:crontab:enable

### Advanced usage

If you run multiple Magento environments on the same server you may want to define multiple crontab groups and enable/disable only particular one.

To define custom group, replace "magento" key with another label eg. "custom-group":

    # [start:magento]
    * * * * * php <magento install dir>/bin/magento cron:run --group=index | grep -v "Ran jobs by schedule" >> <magento install dir>/var/log/magento.cron.log
    * * * * * php <magento install dir>/bin/magento cron:run --group=mailchimp | grep -v "Ran jobs by schedule" >> <magento install dir>/var/log/magento.cron.log
    # [end:magento]
    
    # [start:custom-group]
    * * * * * php <magento install dir>/bin/magento cron:run --group=another | grep -v "Ran jobs by schedule" >> <magento install dir>/var/log/magento.cron.log
    # [end:custom-group]

Then you can run following command to disable CRON entries from "custom-group" group:

    php bin/magento cron:crontab:disable --group=custom-group

And this one to enable those CRON entries:

    php bin/magento cron:crontab:enable --group=custom-group

Default value for `group` parameter is `magento`.

To Do
-----

* allow listing cron entries

Credits
-------

* Developed by [Wojtek Naruniec](https://naruniec.me/)
* Loosely inspired by [MediovskiTechnology/php-crontab-manager](https://github.com/MediovskiTechnology/php-crontab-manager)