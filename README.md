# YenePay checkout payment method plugin

This plugin allows you to quickly and easily add YenePay as a payment method to your Wordpress/Woocommerce site.

We encourage you to read through this README to get the most our of what this library has to offer. We want this library to be community driven and we really appreciate any support we can get from the community.

This library has been tested on Worpress up to v4.9.6 as well as Woocommerce v3.2.4

## Getting Started

These instructions will guide you on how to integrate and test YenePay's payment method with your Woocommerce driven site. We have setup a sandbox environment for you to test and play around the integration process.

## Pre-requisite

1. To add YenePay to your application and start collecting payments, you will first need to register on YenePay as a merchant and get your seller code. You can do that from https://www.yenepay.com/merchant
2. Once registered, log into your yenepay account by going to https://www.yenepay.com and clicking on Login. After logging in, you will be taken to the account manager page. From there, open up the Settings page by clicking the user avatar on the top right corner of the Account Manager page.
3. From the Settings page take a note of two important values
    i. the User Code on the Profile tab and 
    ii. the PDT Key on the Notifications tab

You will need these values when setting up this plugin.

## Installation

Step 1: Download the contents of this repository. 

Step 2: Copy the folder "woo-yenepay", then go to your wordpress site root folder and paste it in the "plugins" folder. This is typically located in {your-wordpress-site-folder}\wp-content\plugins. 

Step 3: Open your wordpress site's admin page and navigate to Plugins > Installed Plugins. Then find WooCommerce YenePay Payment Gateway and click on Activate

Step 4: From the admin site, go to Woocommerce > Settings and select the Checkout tab. Then click on YenePay from the list of available payment methods at the top of the page. If you don't see it here, make sure you have activated the YenePay payment plugin as mentioned in Step 3 above.

Step 5: Once on the YenePay Payment Gateway settings page, fill in the YenePay Merchant Code with the value of your User Code and YenePay PDT token with your PDT Key. These values can be obtained from your Account Manager page as indicated in the pre-requisite section above.

You also have an option to use our Sandbox application to test out your integration before going live. We highly recommend using this feature when testing the integration.