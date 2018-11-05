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

Before first use edit crontab using default editor by running `crontab -e` and add tags showing where Magento 2 related jobs start and end. That part of the crontab should look as follows:


    # [start:magento]
    * * * * * php <magento install dir>/bin/magento cron:run --group=index | grep -v "Ran jobs by schedule" >> <magento install dir>/var/log/magento.cron.log
    * * * * * php <magento install dir>/bin/magento cron:run --group=mailchimp | grep -v "Ran jobs by schedule" >> <magento install dir>/var/log/magento.cron.log
    # [end:magento]

Now run following command to disable CRON entries:

    php bin/magento cron:crontab:disable

And this one to enable CRON entries:

    php bin/magento cron:crontab:enable

To Do
-----

* change temp file name to random one, remove file after using
* don't save crontab file if there is nothing to save
* allow listing cron entries

Credits
-------

* Developed by [Wojtek Naruniec](https://naruniec.me/)
* Loosely inspired by [MediovskiTechnology/php-crontab-manager](https://github.com/MediovskiTechnology/php-crontab-manager)