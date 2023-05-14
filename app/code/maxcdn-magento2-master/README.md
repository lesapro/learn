# TIG MaxCDN for Magento 2
We created this extension to **improve the workability of MaxCDN with Magento 2**. At this point the moments at which MaxCDN's refreshes its
servers are depended on them. They poll periodically if new files have been uploaded or download them upon request. This means that the
first request is always slower than it could be.

* But what if you change an already existing image or file?
* What if you made changes to your JS- or CSS-code?

In these (or any other similar) scenario's, this means: if you forget to login to your MaxCDN-account and purge your pull zone(s) after you 
made changes to your codebase and/or filesystem, either your layout will break or your visitors are presented with outdated information.

You can imagine that it makes more sense to **let Magento decide when the files on your MaxCDN pull zone are refreshed**, instead of the 
other way around. This extension makes **Magento 2 and MaxCDN work together**, instead of separated from each other.

## What does it do?
* It purges all available pull zones in your account, when:
  * You press the 'Purge All Pull Zones'-button within the Cache Management-page, or
* Whenever the following happens:
  * Flush Magento Cache,
  * Flush Cache Storage,
  * Flush Catalog Images Cache,
  * Flush JS/CSS Cache,
  * Upload a product image.

At this point this extension has only been tested on Magento 2.1.*, but we will make it compatible with more recent versions in upcoming
releases.

## Installation using Composer
<pre>composer require tig/maxcdn-magento2</pre>

## Configuration
This module's configuration can be found under **Stores** > **Configuration** > **Advanced** > **MaxCDN**.

### To create your API Application
* Make sure you're logged in to your MaxCDN-account and follow [this URL](https://cp.maxcdn.com/account/api)
* Click on **Create Application**
* Enter a _Name_ and _Description_ for your API-application, the _Application URL_ and _Callback URL_ should replicate your store-URL
* Check _Allow API key full permission to this account_
* Click on **Update** and you'll be presented with the necessary credentials to configure the extension.
* **Don't forget to Whitelist your servers' IP!**

### Module Configuration
* Take note of the **Company Alias**, the **Consumer Key** and the **Consumer Secret** and enter them in the corresponding
fields of this Magento 2 module's configuration page.
* Go to **Stores** > **Configuration** > **General** > **Web** and enter your Pull Zone's CDN URL in the fields _(Secure) Base URL 
for Static View Files_ and _(Secure) Base URL for User Media Files_.
  * Make sure you append each URL with either _/pub/static_ or _/media/_ respectively.
* Within the MaxCDN Control Panel, go to **Zones** > **Pull Zones**,
* Click on **Manage** > **Settings** next to the corresponding Pull Zone.
* Within **Settings**, make sure you've checked the _Add CORS Header_-option and clicked **Update**.
* You're all set! Enjoy **MaxCDN on Magento 2**!

## FAQ
> I keep getting CORS-related errors in my console!

Make sure you've followed the guide above step by step. Check [your Pull Zone's](https://cp.maxcdn.com/zones/pull/) White-/Blacklist inside the MaxCDN Control Panel. If everything 
seems to be in order, read [this article](https://www.maxcdn.com/one/tutorial/how-to-use-cdn-with-webfonts/) for further troubleshooting.
