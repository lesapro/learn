# [Magento 2 Language Switcher](https://magefan.com/magento-2-auto-language-switcher-multi-language-store) by Magefan

This Magento 2 module allows you to automatically show language (switch store view) on your store based on visitor county or browser language.

## Features
  * Display Language Based On Customer Browser Language
  * Display Language Based On Customer Geographic Location
  * GeoIP Database
  * Robots Restrictions
  * Integration With Custom Themes

## Storefront Demo
https://als.demo.magefan.com/gear/bags.html
## Admin Panel Demo
https://als.demo.magefan.com/admin/


## Installation Method 1 - Installing via Composer (prefer)
  * Open command line
  * Using command "cd" navigate to your magento2 root directory
  * Run commands: 
```
  composer config repositories.magefan composer https://magefan.com/repo/
  composer require magefan/module-auto-language-switcher
  #Authentication Data can be found in your [Magefan Account](https://magefan.com/downloadable/customer/products/)
  php bin/magento setup:upgrade
  php bin/magento setup:di:compile
  php bin/magento setup:static-content:deploy
```


## Installation Method 2 (Long One)
  * Install GeoIP2 PHP API (https://github.com/maxmind/GeoIP2-php).
  * Install Magefan Community Extension (https://github.com/magefan/module-community)
  * Install Magefan GeoIp Extension (https://github.com/magefan/module-geoip)
  * Unzip Magefan Auto Language Switcher Extension Archive
  * In your Magento 2 root directory create a folder app/code/Magefan/AutoLanguageSwitcher
  * Copy files and folders from archive to that folder
  * In command line, using "cd", navigate to your Magento 2 root directory
  * Run commands:
```
php bin/magento setup:upgrade
php bin/magento setup:di:compile
php bin/magento setup:static-content:deploy
```

## Support
If you have any issues, please [contact us](mailto:support@magefan.com)

## Need More Features?
Please contact us to get a quote
https://magefan.com/contact

## License
The code is licensed under [EULA](https://magefan.com/end-user-license-agreement).
