<!-- markdownlint-configure-file {
  "MD013": {
    "code_blocks": false,
    "tables": false
  },
  "MD033": false,
  "MD041": false
} -->

<div align="center">

# magento-2-clean-product-image-cache

[![Latest Stable Version](https://img.shields.io/badge/version-1.0.0-blue)](https://packagist.org/packages/blackbird/magento-2-clean-product-image-cache)
[![License: MIT](https://img.shields.io/github/license/blackbird-agency/magento-2-clean-product-image-cache.svg)](./LICENSE)

Sometimes you want to change product image without changing image name for many reason.

**Problem** : if changed images was already generated in `pub/media/catalog/product/cache/` folders you need to delete all
generated cached images to have your new image in front and in cache.

**Solution** : this module which clean product image generated cache on product save.

[How It Works](#how-it-works) •
[Installation](#installation) •
[Add your own clean cache strategy](#add-your-own-clean-cache-strategy) •
[Support](#support) •
[Contact](#contact) •
[License](#license) 

</div>

## How It Works

When you save a product in the back-office it will delete all generated image cache file from the saved product in `pub/media/catalog/product/cache/*`

## Installation

```
composer require blackbird/magento-2-clean-product-image-cache
```
```
php bin/magento setup:upgrade
```
*In production mode, do not forget to recompile and redeploy the static resources.*

## Add your own clean cache strategy

If you use a CDN like Cloudflare, you maybe need to use her API to clean the CDN cache.
We give you two way to do that : 
- create your own strategy class
- create your observer on our event `blackbird_image_cache_clean_after`

**If you are using Cloudflare** we have an extension of this module which call Cloudflare API to purge cache : [blackbird/magento-2-clean-cloudflare-image-cache](https://github.com/blackbird-agency/magento-2-clean-cloudflare-image-cache)

### Create your own strategy :

#### Create your own strategy service implementing `Blackbird\CleanProductImageCache\Api\CleanCacheStrategyInterface`

```php
class MyCleanCacheStrategy implements CleanCacheStrategyInterface
{
     /**
     * {@inheritDoc}
     */
    public function clean(ProductInterface $product): void
    {
        //Do what you need to do
    }
}
```

#### Add you strategy to the pool in your `di.xml`
```xml
...
    <type name="Blackbird\CleanProductImageCache\Model\Service\CleanCacheStrategyPool">
        <arguments>
            <argument name="cleanCacheStrategies" xsi:type="array">
                <item name="my_clean" xsi:type="array">
                    <item name="class" xsi:type="object">Vendor\Module\Model\Service\MyCleanCacheStrategy</item>
                    <item name="sortOrder" xsi:type="number">10</item>
                    <item name="enabled" xsi:type="boolean">true</item>
                </item>
            </argument>
        </arguments>
    </type>
...
```

You can use sortOrder and enabled to change execution order or disable strategies.

### Use the dispatched event :

#### Create your Observer :
```php
class AfterCleanMagentoImageCache implements ObserverInterface
{
     /**
     * {@inheritDoc}
     */
    public function execute(Observer $observer): void
    {
        //Get absolute path off all cached images for the cleaned product
        $paths = $observer->getPaths();
        //Do what you need to do
    }
}
```

#### Plug it your Observer in the events.xml : 
```xml
...
    <event name="blackbird_image_cache_clean_after">
        <observer name="event_custom_name" instance="Vendor\Module\Observer\AfterCleanMagentoImageCache" />
    </event>
...
```


## Support

- If you have any issue with this code, feel free to [open an issue](https://github.com/blackbird-agency/magento-2-clean-product-image-cache/issues/new).
- If you want to contribute to this project, feel free to [create a pull request](https://github.com/blackbird-agency/magento-2-clean-product-image-cache/compare).

## Contact

For further information, contact us:

- by email: hello@bird.eu
- or by form: [https://black.bird.eu/en/contacts/](https://black.bird.eu/contacts/)

## License

This project is licensed under the MIT License - see the [LICENSE](./LICENSE) file for details.

***That's all folks !***