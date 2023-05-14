#!/bin/bash
rm -rf var/cache/*
rm -rf var/page_cache/*
rm -rf generated/code/*
/usr/local/lsws/lsphp74/bin/php -dmemory_limit=-1 bin/magento cache:clean
/usr/local/lsws/lsphp74/bin/php -dmemory_limit=-1 bin/magento cache:flush
/usr/local/lsws/lsphp74/bin/php -dmemory_limit=-1 bin/magento setup:upgrade
/usr/local/lsws/lsphp74/bin/php -dmemory_limit=-1 bin/magento setup:di:compile
/usr/local/lsws/lsphp74/bin/php -dmemory_limit=-1 bin/magento setup:static-content:deploy -f
chmod 777 -Rf var pub/static generated