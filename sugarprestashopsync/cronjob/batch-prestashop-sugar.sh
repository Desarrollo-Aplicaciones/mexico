#!/bin/sh
umask 002

cd /var/www/sugarprestashopsync/prestashop/sugarcrm

/usr/bin/php -q sync-prestashop-sugar.php
