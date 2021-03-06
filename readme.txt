=== YenePay Checkout Payment Gateway ===
Contributors: airmax7
Tags: YenePay, YenePay checkout, online payment, woocommerce payment
Requires at least: 4.9
Tested up to: 5.5.1
Requires PHP: 5.6.20
License: GPL v2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

YenePay checkout payment gateway plugin for WooCommerce; start accepting Ethiopian mobile wallet payments on your store. 

== Description ==
With this plugin you can now start accepting mobile wallet payments from Ethiopian banks on your store. Supported mobile wallets include CBE-birr, Amole, HelloCash and M-Birr with more being added every time.

== Installation ==

Pre-requisite

1. To add YenePay to your application and start collecting payments, you will first need to register on YenePay as a merchant and get your seller code. You can do that from https://www.yenepay.com/merchant
2. Once registered, log into your yenepay account by going to https://www.yenepay.com and clicking on Login. After logging in, you will be taken to the account manager page. From there, open up the Settings page by clicking the user avatar on the top right corner of the Account Manager page.
3. From the Settings page take a note of two important values
    * the User Code on the Profile tab and 
    * the PDT Key on the Notifications tab

You will need these values when setting up this plugin.


Installation

You can add the payment plugin in two ways:
A. Install from wordpress.org plugins directory (recommended)
    1. Go to the \'Plugins\' menu in your WordPress admin and search for 'YenePay'
    2. Install and activate the plugin \'YenePay Checkout Payment Gateway\'
B. Build from source
    1. Clone the repo
    2. Go to your clone directory and run \'composer install --no-dev\'
    3. Once the above finishes installing, zip the clone directory and upload the file via the WordPress Plugin installer wizard.
    4. Activate the new plugin.


Setup

1. Install and Activate the plugin using the steps above
2. From the admin site, go to Woocommerce > Settings and select the Checkout tab or Payments tab depending on your WordPress version. 
3. Then click on YenePay from the list of available payment methods. If you don't see it here, make sure you have activated the YenePay payment plugin.
4. Once on the YenePay Payment Gateway settings page, fill in the YenePay Merchant Code with the value of your User Code and YenePay PDT token with your PDT Key. These values can be obtained from your Account Manager page as indicated in the pre-requisite section above.

You also have an option to use our Sandbox application to test out your integration before going live. We highly recommend using this feature when testing the integration. 
When doing Sandbox testing, make sure the values you use for the Merchant Code and PDT Token are from the generated sandbox merchant user. Details on how to generate sandbox users can be found here: https://community.yenepay.com/docs/yenepay-sandbox/how-to-create-sandbox-users/


== Screenshots ==
1. Make sure the Enable YenePay Payment is checked. When ready to move to production, uncheck the Use YenePay Sandbox checkbox and update the values of the Merchant Code and PDT Key with the corresponding values of your live YenePay account