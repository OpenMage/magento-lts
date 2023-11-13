---
draft: false
date: 2016-12-23
readtime: 5
authors:
   - sreichel
categories:
   - Events
   - Sitemap
hide:
  - toc

---

# Exclude certain products from Magento sitemap.xml generation[^1]

Since Magento 1.9.0. you can do this without touching any core file.

There are two new events you can observe:

```
sitemap_categories_generating_before
```

```
sitemap_products_generating_before
```

<!-- more -->

To exclude products based on attribute, you can do this:

## Event lookup

Add an observer to `sitemap_products_generating_before`

```
app\code\community\My\Module\etc\config.xml
```

```xml
<events>
    <sitemap_products_generating_before>
        <observers>
            <my_module>
                <class>my_module/observer</class>
                <method>excludeProductsFromSitemap</method>
            </my_module>
        </observers>
    </sitemap_products_generating_before>
</events>
```

## Event execution

```
app\code\community\My\Module\Model\Observer.php
```

```php
public function excludeProductsFromSitemap(Varien_Event_Observer $observer)
{
   $collection = $observer->getCollection();
   $items = $collection->getItems();

   $excludeIds = Mage::getModel('catalog/product')
       ->getCollection()
       ->setStoreId($observer->getStoreId()) # requieres Magento 1.9.3.0
       ->addAttributeToFilter('use_in_sitemap', 0)
       ->getAllIds();

   foreach ($excludeIds as $id) {
       unset($items[$id]);
   }
   
   $collection->setItems($items);
}
```

## Add attribute

1. add a product attribute `yes/no` named `use_in_sitemap` (maybe default value `yes`)
2. add this attribute to all attribute sets
3. set the products you want to exclude to `no`

<small>___Note__: until Magento 1.9.3.0 the attribute should be set to `global` scope._</small>

[^1]: https://magento.stackexchange.com/a/151684/46249
