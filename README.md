Magento Custom Login
--------------------


Custom login controller to be used to demostrate that there is something wrong with Magento 2.3.4 and 2.3.5 not loading customer_data properly on js.  

The customer section is not invalidated and the customer_data is not loaded, so the Welcome message is not changed and also there are issues on the cart, can someone help me?

See https://github.com/magento/magento2/issues/28428


Installation:  Drop the Custom folder at the app folder of Magento.

Test:  Go to the Account Login page and click on the "Custom Login" link.  You will be logged as the test user, but the customer data will be not loaded so not showed on the Welcome Message.

