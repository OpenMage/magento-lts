---
tags:
  - Events
  - Debug
  - Development
---

# Events & Observer

Quick overview how observers work.

## Event lookup

If you want to add custom logic for `customer_login` event, add in your `config.xml`.

!!! example

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

## Event execution

And in your `Observer.php`.

!!! example

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

## Development

For debugging dispatched events, add a `xdebug` breakpoint to class `Mage`

!!! tip

    ```php
    public static function dispatchEvent($name, array $data = [])
    {
        Varien_Profiler::start('DISPATCH EVENT:' . $name);
        $result = self::app()->dispatchEvent($name, $data);
        Varien_Profiler::stop('DISPATCH EVENT:' . $name);
        return $result;
    }
    ```
