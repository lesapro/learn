#Quick View Module

This extension brings a convenient experience to the customers by allowing a single click to get the product info and add to the cart immediately without any need of the loading page for product detail and come back and forth.

##Support: 
version - 2.3.x, 2.4.x

##How to install Extension

1. Download the archive file.
2. Unzip the file
3. Create a folder [Magento_Root]/app/code/Sparsh/QuickView
4. Drop/move the unzipped files to directory '[Magento_Root]/app/code/Sparsh/QuickView'

#Enable Extension:
- php bin/magento module:enable Sparsh_QuickView
- php bin/magento setup:upgrade
- php bin/magento setup:di:compile
- php bin/magento setup:static-content:deploy
- php bin/magento cache:flush

#Disable Extension:
- php bin/magento module:disable Sparsh_QuickView
- php bin/magento setup:upgrade
- php bin/magento setup:di:compile
- php bin/magento setup:static-content:deploy
- php bin/magento cache:flush