for x in {1..100} ; do
    if [ -f "/home/demotheme.live/public_html/pub/sitemap-1-$x.xml" ]
    then
       /home/demotheme.live/public_html/M2-crawler.sh -m https://demotheme.live/sitemap-1-$x.xml
    fi
    sleep 1
done
