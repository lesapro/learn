#!/bin/bash
ALERT=""
domain=""
dbname=""
dbuser=""
dbpass=""
_input() {
    false
    while [ $? -eq 1 ]; do
        read -r -p "Nhap domain (Khong co www) [0 = Thoat]: " domain
         # read -r -p "Nhap dbname: " dbname
         #  read -r -p "Nhap dbuser: " dbuser
         #    read -r -p "Nhap dbpass: " dbpass
        domain=$(echo "${domain}" | tr '[:upper:]' '[:lower:]')
            echo "${domain}" | grep -q "\."
            #  dbname=$(echo "${dbname}" | tr '[:upper:]' '[:lower:]')
            # echo "${dbname}" | grep -q "\."
            #  dbpass=$(echo "${dbpass}" | tr '[:upper:]' '[:lower:]')
            # echo "${dbpass}" | grep -q "\."
    done
}
if [ -z "${ALERT}" ]; then
    _input
else
    clear
    printf "%s\n" "${ALERT}"
fi

#php bin/magento setup:install --base-url=http://${domain}/ --db-host=localhost --db-name=91ka_m243johitnl --db-user=m243johitnl_91ka --db-password=SabCw194wOn2 --admin-firstname=FirstName --admin-lastname=LastName --admin-email=your@emailaddress.com --admin-user=magentoadmin --admin-password=admin@123 --language=en_US --currency=USD --timezone=America/Chicago --use-rewrites=1

php bin/magento config:set web/cookie/cookie_domain ${domain}

php bin/magento config:set web/unsecure/base_url http://${domain}/

php bin/magento config:set web/secure/base_url https://${domain}/

find . -type f -exec chmod 664 {} \;
find . -type d -exec chmod 775 {} \;
find var pub/static pub/media  generated/ app/etc -type f -exec chmod g+w {} \;
find var pub/static pub/media generated/ app/etc -type d -exec chmod g+ws {} \;
php bin/magento setup:upgrade
php bin/magento setup:di:compile
php bin/magento setup:static-content:deploy -f
rm -rf var/cache/*
rm -rf var/page_cache/*
rm -rf generated/code/*
php bin/magento cache:clean
php bin/magento cache:flush
chmod 777 -Rf var pub generated 
