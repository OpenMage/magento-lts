# Example

For example, if you want to add custom logic for `customer_login` event.
Add in your `config.xml`:

```xml
<config>
    <global><!-- or adminhtml or frontend -->
        <events>
            <customer_login>
                <observers>
                    <yourNamespace_yourModule>
                        <class>yourShortClass/observer</class>
                        <method>yourMethod</method>
                    </yourNamespace_yourModule>
                </observers>
            </customer_login>
        </events>
    </global>
</config>
```

And in your `Observer.php`:

```php
class YourNamespace_YourModule_Model_Observer
{
    // EVENT customer_login
    public function yourMethod(Varien_Event_Observer $observer)
    {
        $customer = $observer->getData('customer');
    }
}
```
