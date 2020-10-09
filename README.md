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

You can add the payment plugin in two ways:
1. Install from wordpress.org plugins directory (recommended)
    1. Go to the \'Plugins\' menu in your WordPress admin and search for 'YenePay'
    2. Install and activate the plugin \'YenePay Checkout Payment Gateway\'
2. Build from source
    1. Clone the repo or download the latest stable release version of the repo
    2. Using command line, restore the dependency packages by running \'composer install --no-dev\' from within the clone directory
    3. Once the above finishes installing, zip the clone directory and upload the file via the WordPress Plugin installer wizard.
    4. Activate the new plugin.

## Setup

1. Install and Activate the plugin using the steps above
2. From the admin site, go to Woocommerce > Settings and select the Checkout tab or Payments tab depending on your WordPress version. 
3. Then click on YenePay from the list of available payment methods. If you don't see it here, make sure you have activated the YenePay payment plugin.
4. Once on the YenePay Payment Gateway settings page, fill in the YenePay Merchant Code with the value of your User Code and YenePay PDT token with your PDT Key. These values can be obtained from your Account Manager page as indicated in the pre-requisite section above.

You also have an option to use our Sandbox application to test out your integration before going live. We highly recommend using this feature when testing the integration. 

When doing Sandbox testing, make sure the values you use for the Merchant Code and PDT Token are from the generated sandbox merchant user. Details on how to generate sandbox users can be found here: https://community.yenepay.com/docs/yenepay-sandbox/how-to-create-sandbox-users/

You also have an option to use our Sandbox application to test out your integration before going live. We highly recommend using this feature when testing the integration.