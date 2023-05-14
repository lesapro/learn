## Installation
* To install this module do next:

    - **1**: Copy folder `Isobar/OneFieldName` into `app/code`
      
    - **2**: Run:
        ```Commandlile 
            composer require isobar/onefieldname
        ```
    - **3**: Run:
        ```Commandlile 
            php bin/magento module:enable Isobar_OneFieldName
        ```
    - **4**: Run:
        ```Commandlile 
            php bin/magento setup:upgrade
        ```
    - **5**: If not shown on frontend, clear `pub/static` folder:
        ```Commandlile 
            rm -rf pub/static
        ```
    - **6**: Run:
        ```Commandlile 
            php bin/magento setup:static-content:deploy -f
        ```
