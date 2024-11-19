---
tags:
- Modules
---

# Bitcoin

## `rvelhote/opennode-magento`
Magento 1.9 Plugin for OpenNode Bitcoin Payment Gateway

This module is still in development little by little

1. Configure API keys in the backoffice
2. Select the *Bitcoin* payment method during checkout
3. When placing the order you will be redirected to a page where customers are presented with
   a couple of QR Codes with the payment addresses (or links to pay with the wallet)
4. Customers can pay and then move to the default Magento success page. A task is continuously checking for the payment
   status in the background and informs the user of the progress
5. A cronjob will cancel *Pending Payment* orders automatically

The module was only tested with the default theme and Onepage Checkout.

#### Source
- https://github.com/rvelhote/opennode-magento
