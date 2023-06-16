=== Sign-up Sheets ===
Contributors: fetchdesigns
Tags: sign up, signup, volunteer, timeslot, PTO, PTA, church, photographer, Non-profit, club, sign-up, signup sheet, sign up sheet
Tested up to: 6.2
Stable tag: 2.2.8
License: GPLv2 or later

An online sign-up sheet manager where your users can sign up for tasks


== Description ==

This plugin lets you quickly and easily setup sign-up sheets on your WordPress site.  Use it for volunteer and timeslot sign-ups, personnel and resource coordination, and much more.

The free version of Sign-Up Sheets includes the following features:

* Unlimited number of sign-up sheets and sign-up spot
* Administrator can clear sign-up spots as needed
* Ability to copy a sheet or a task
* Confirmation emails on sign-up
* reCAPTCHA (optional) - NEW as of version 2.2
* Export sign-up information for a single sheet or all sheets to a CSV
* And much more... [full list of Sign-up Sheets features](https://www.fetchdesigns.com/sign-up-sheets-wordpress-plugin/)

The Pro version of Sign-Up Sheets includes the following features:

* Ability to create Custom Task fields and Custom Sign-up form fields
* Automatically sends reminder emails before an event (optional)
* Allows customization of the confirmation and reminder emails per sheet
* Assign categories to sheets and filter by category with the [sign_up_sheet] shortcode
* Spot Locking - Locks and holds a spot for 3 minutes when a user accesses the sign-up form (optional)
* Compact View to condense tasks with a large number of spots into a single line (optional)
* Enable sign-up limits per task
* And much more... [full list of Sign-up Sheets features](https://www.fetchdesigns.com/sign-up-sheets-wordpress-plugin/)

Sign-up Sheets is currently being used by organizations and businesses for non-profit and church volunteer opportunities, schools and PTO/PTA volunteering, club sign-ups, photographer and meeting room timeslot sign-ups, and more.  It's a great alternative to monthly paid services like SignUpGenius and allows you to control your own sign-ups right on your WordPress site.


== Installation ==

1. Download the plugin
2. From your WordPress Admin panel, click the Plugins Menu
3. Deactivate and delete any previous versions of Sign-up Sheets including the free version.
4. Within the Plugins menu, click the "Add New" button
5. Click the "Upload Plugin" button from the menu at the top
6. Select the Sign-up Sheets zip file you downloaded and click the "Install Now" button
7. After installation is complete, click "Activate Plugin"

Manual Install - FTP
1. Download the plugin and extract the files
2. Copy the `sign-up-sheets` directory and all its files to your `/wp-content/plugins/` directory
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Create a new blank page and add the shortcode [sign_up_sheet]


== Frequently Asked Questions ==

= How do I create a Sign-up Sheet page on my site? =
You can do this by creating any page or post and adding the shortcode `[sign_up_sheet]` to the content.  Then, go to the "Sign-up Sheets" section of your WP admin and create a new sheet.

= Is Sign-up Sheets GDPR compliant? =
Please read our [GDPR and Sign-up Sheet](https://www.fetchdesigns.com/doc/gdpr-sign-up-sheets-wordpress-plugin/) article for how to configure your sign-ups to adhere to GDPR.

= If I update to the Pro version, will I lose my information from the free version? =
No, you will not lose any information and will retain all of your current sign-up sheets and sign-ups.

= Can I change the "from" address on the confirmation email? =
Yes, in `Settings > Sign-up Sheets` you can specify any email you want.  It defaults to the email address set in `Settings > General`.

= How can I suggest an idea for the plugin? =
Send us an email through our website at https://www.fetchdesigns.com/contact/  We appreciate any and all feedback, but can't guarantee it will make it into the next version.  If you are in need a modification immediately, we are available for hire.  Please contact us at the link above and we can provide a quote.

= What is the difference between "Custom Task Fields" and "Custom Sign-up Fields (pro version only) =
**Custom Task Fields:** (on the Sign-up Sheet) are fields that appear when you create or edit a sheet in the admin on each task that you create. They are for display purposes only on the front-end of the Sign-up Sheet.
**Custom Sign-up Fields:** (on the Sign-up Form) are fields that users fill out on the front-end on the form they use to sign-up for an open spot

= How do I display sheets from only 1 specific category (Pro version only) =
To filter by category, you can include the category id # in the shortcode to determine which category will display on that page.   As an example, the following shortcode would show all sheets associated with category #5... `[sign_up_sheet category_id="5"]`

= When are email reminders sent? (Pro version only) =
When you have the "reminder" setting turned on in `Settings > Sign-up Sheets`, a WordPress event will be triggered to check for reminders needing to be sent out.  This happens when someone visits your site, but no more than once per hour.  You can set how many days prior to the event you would like reminders to go out.

= How do I change the sheet list heading? =
The list title defaults to 'Current Sign-up Sheets'.  To customize this, you can add the option `list_title` to your shortcode (example: `[sign_up_sheet list_title=""]`).  If you are using the Pro version and filtering by a specific category, you can also have this default to the name of the category by adding the option `list_title_is_category` (example: `[sign_up_sheet category_id=4 list_title_is_category=true]`).

= What dynamic variables can I use in my confirmation email? (Pro version only) =
{site_name}
{site_url}
{from_email}
{removal_link}
{signup_details}
{signup_firstname}
{signup_lastname}
{signup_email}


== Screenshots ==

1. Admin Sign-up Sheets Listing
2. Admin Sign-up Sheet Details
3. Admin Edit Sign-up Sheet
4. Frontend Example


== Upgrade Notice ==

= 2.2.8 | 2023-04-19 =
* Added sign-up dates and assigned users on Manage Sign-ups page in admin
* Fixed bug with new sorting feature from 2.2.7 causing a fatal error when running on PHP 7.4 and earlier

= 2.2.7 | 2023-04-16 =
* Added new sheet listing order options of descending order for date or sheet ID
* Added support for the Loco Translate plugin
* Added translation support to the admin Help page
* Added FDSUS_DISABLE_MIGRATE_2_0_to_2_1 constant to allow disabling the migration process if needed
* Updated styles to allow horizontal scrolling for sheet's task listing table content extends past smaller screens such as on mobile
* Updated spot number text to be translatable (Ex: "#1:")
* Updated Help page "System Information" and moved it to the main WordPress Site Health Info page
* Fixed bug where removed sign-ups don't flush 3rd party cache plugins
* Fixed confirmation email issue with missing removal link and other sign-up user info variables when W3 Total Cache Database Cache is enabled

= 2.2.6 =
* Fixed translations in admin related to custom post type names and "At a Glance" on dashboard
* Added additional validation on sign-up if sheet or task are no longer active
* Removed unused "session" cookie logic
* Fixed conflict with reCAPTCHA v2 Checkbox from validating if disabled in SUS, but another reCAPTCHA exists outside the SUS plugin.
* Updated sign-up form check to prevent double submissions to clear after 3 seconds to allow re-submission

= 2.2.5 =
* Fixed issue preventing excerpts from displaying

= 2.2.4 =
* Added option to disable the logged-in user auto-populate of name and email on the sign-up form
* Fixed conflict on the_title filter to prevent errors if other plugins/themes do not include the required ID argument
* Fixed notices from appearing when submitting sign-up form when get_the_excerpt is used prior
* Fixed missing right arrow in Safari after "View & sign-up" on sheet listing
* Fixed sign-up form from failing if only mail sending errors out after sign-up is successful

= 2.2.3 =
* Added privacy improvements to integrate sign-ups with the WP Export and Erase Personal Data functionality
* Added ability to remove email address from sign-up form
* Updated setting page save to prevent conflicts
* Fixed bug where user roles that don't have access to read sign-up sheets and manage_options are prevented from viewing admin pages (such a subscribers viewing their admin dashboard)

= 2.2.2 =
* Added support for LightSpeed Cache
* Added admin dashboard "At a Glance" item for Sign-up Sheets
* Updated translations to include escape for improved security
* Updated color of sort icon when editing tasks on Edit Sheet page to improve accessibility
* Removed unused "H" icon on edit sheet task list in admin

= 2.2.1 =
* Added Sign-up Sheets Gutenberg Block
* Updated "View & sign-up" links on sheet page to improve accessibility for screen readers
* Fixed compatibility issue with block themes like Twenty Twenty-Two
* Fixed issue with reCAPTCHA in case-sensitive file systems
* Fixed JS console error when not on sign-up form
* Removed unused JS
* Fixed SUS Manager Role on activation

= 2.2.0.1 =
* Fixed error when opening theme customizer and various other admin tasks
* Fixed missing table borders on some themes

= 2.2 =
* Updated data structure to use WordPress custom post types to allow for features previously only available in the Pro version
* Added option for reCAPTCHA SPAM prevention and ability to switch between reCAPTCHA v2 "checkbox" or "invisible" (previously only on Pro)
* Added automatically generated sheet URLs with customizable SEO-friendly URL... example.com/sheet/your-sheet (previously only on Pro)
* Added ability to copy a task when editing a sheet (previously only on Pro)
* Added ability to sort frontend sheet listing by date or sheet id (previously only on Pro)
* Added ability to display all sign-up data on the frontend (previously only on Pro)
* Added ability to set “Phone” and “Address” field as optional or remove altogether (previously only on Pro)
* Added ability to set publish scheduling of sheets (previously only on Pro)
* Added WYSIWYG editor on sheet description editor (previously only on Pro)
* Added ability to export specific sheets (previously only on Pro)
* Added option for email validation on sign-up form (previously only on Pro)
* Added verification before submitting sign-up for same task using same email address (previously only on Pro)
* Added ability to allow multiple shortcodes on the same page (previously only on Pro)
* Added admin search on sheets grid (previously only on Pro)
* Added accessibility improvements to task table
* Added required attribute to applicable fields in sign-up form
* Added View and Edit Sheet links on Manage Sign-ups pages for easier access between screens
* Added minification on frontend JS file for improved performance
* Added ability to modify States dropdown via the "fdsus_states" filter
* Updated required field error messaging to list field names that are missing to make it more clear
* Updated to require a minimum of WordPress 5.5+


== Changelog ==

= 2.2.8 | 2023-04-19 =
* Added sign-up dates and assigned users on Manage Sign-ups page in admin
* Fixed bug with new sorting feature from 2.2.7 causing a fatal error when running on PHP 7.4 and earlier

= 2.2.7 =
* Added new sheet listing order options of descending order for date or sheet ID
* Added support for the Loco Translate plugin
* Added translation support to the admin Help page
* Added FDSUS_DISABLE_MIGRATE_2_0_to_2_1 constant to allow disabling the migration process if needed
* Updated styles to allow horizontal scrolling for sheet's task listing table content extends past smaller screens such as on mobile
* Updated spot number text to be translatable (Ex: "#1:")
* Updated Help page "System Information" and moved it to the main WordPress Site Health Info page
* Fixed bug where removed sign-ups don't flush 3rd party cache plugins
* Fixed confirmation email issue with missing removal link and other sign-up user info variables when W3 Total Cache Database Cache is enabled

= 2.2.6 =
* Fixed translations in admin related to custom post type names and "At a Glance" on dashboard
* Added additional validation on sign-up if sheet or task are no longer active
* Removed unused "session" cookie logic
* Fixed conflict with reCAPTCHA v2 Checkbox from validating if disabled in SUS, but another reCAPTCHA exists outside the SUS plugin.
* Updated sign-up form check to prevent double submissions to clear after 3 seconds to allow re-submission

= 2.2.5 =
* Fixed issue preventing excerpts from displaying

= 2.2.4 =
* Added option to disable the logged-in user auto-populate of name and email on the sign-up form
* Fixed conflict on the_title filter to prevent errors if other plugins/themes do not include the required ID argument
* Fixed notices from appearing when submitting sign-up form when get_the_excerpt is used prior
* Fixed missing right arrow in Safari after "View & sign-up" on sheet listing
* Fixed sign-up form from failing if only mail sending errors out after sign-up is successful

= 2.2.3 =
* Added privacy improvements to integrate sign-ups with the WP Export and Erase Personal Data functionality
* Added ability to remove email address from sign-up form
* Updated setting page save to prevent conflicts
* Fixed bug where user roles that don't have access to read sign-up sheets and manage_options are prevented from viewing admin pages (such a subscribers viewing their admin dashboard)

= 2.2.2 =
* Added support for LightSpeed Cache
* Added admin dashboard "At a Glance" item for Sign-up Sheets
* Updated translations to include escape for improved security
* Updated color of sort icon when editing tasks on Edit Sheet page to improve accessibility
* Removed unused "H" icon on edit sheet task list in admin

= 2.2.1 =
* Added Sign-up Sheets Gutenberg Block
* Updated "View & sign-up" links on sheet page to improve accessibility for screen readers
* Fixed compatibility issue with block themes like Twenty Twenty-Two
* Fixed issue with reCAPTCHA in case-sensitive file systems
* Fixed JS console error when not on sign-up form
* Removed unused JS
* Fixed SUS Manager Role on activation

= 2.2.0.1 =
* Fixed error when opening theme customizer and various other admin tasks
* Fixed missing table borders on some themes

= 2.2 =
* Updated data structure to use WordPress custom post types to allow for features previously only available in the Pro version
* Added option for reCAPTCHA SPAM prevention and ability to switch between reCAPTCHA v2 "checkbox" or "invisible" (previously only on Pro)
* Added automatically generated sheet URLs with customizable SEO-friendly URL... example.com/sheet/your-sheet (previously only on Pro)
* Added ability to copy a task when editing a sheet (previously only on Pro)
* Added ability to sort frontend sheet listing by date or sheet id (previously only on Pro)
* Added ability to display all sign-up data on the frontend (previously only on Pro)
* Added ability to set “Phone” and “Address” field as optional or remove altogether (previously only on Pro)
* Added ability to set publish scheduling of sheets (previously only on Pro)
* Added WYSIWYG editor on sheet description editor (previously only on Pro)
* Added ability to export specific sheets (previously only on Pro)
* Added option for email validation on sign-up form (previously only on Pro)
* Added verification before submitting sign-up for same task using same email address (previously only on Pro)
* Added ability to allow multiple shortcodes on the same page (previously only on Pro)
* Added admin search on sheets grid (previously only on Pro)
* Added accessibility improvements to task table
* Added required attribute to applicable fields in sign-up form
* Added View and Edit Sheet links on Manage Sign-ups pages for easier access between screens
* Added minification on frontend JS file for improved performance
* Added ability to modify States dropdown via the "fdsus_states" filter
* Updated required field error messaging to list field names that are missing to make it more clear
* Updated to require a minimum of WordPress 5.5+

= 1.0.14 =
* Fixed sanitization security issue
* Updated more text to allow for translations
* Fixed slashes being added before quotes on add/edit sheet form when an error message is thrown
* Fixed missing closing table tag on View Sign-ups page causing misaligned data in some cases
* Fixed error handling of get_sheet and get_task to clean up debug log
* Moved POT file from /i18n/ to more standardized /languages/ directory

= 1.0.13 =
* Added support for adding translations
* Updated JS in admin when adding/removing tasks to work with latest version of jQuery (required to support WP 5.5)
* Fixed sheet date from displaying 1970 date when editing the sheet after it throws an error
* Fixed PHP notices

= 1.0.12 =
* Fixed date issue when editing sheets for d/m/Y format
* Fixed PHP notices on sheet save
* Updated Sign-up icon
* Updated linking to Fetch Designs

= 1.0.11 =
* Fixed fatal error when activating plugin in WP 4.5

= 1.0.10 =
* Fixed bug where trashed sheets were showing up on Export All
* Fixed fatal error if activating Pro before deactivating the free version

= 1.0.9 =
* Fixed bug where trashed sheets with no date specified would display on frontend
* Fixed bug where trashed individual sheet pages were available on the frontend
* Removed debug statement that was causing issues in certain browsers

= 1.0.8 =
* Fixed security bug on export
* Fixed sheet edit screen to prevent the quantity of available tasks from being decreased below the number of current signups

= 1.0.7 =
* Corrected export CSV headers

= 1.0.6 =
* Security fix for sign-up form

= 1.0.5 =
* Fixed task sorting
* Added additional error detail on adding a signup

= 1.0.4 =
* Fixed export CSV bug when WordPress is installed in a subfolder
* Added option for detailed debug messages

= 1.0.3 =
* Fixed compatibility bug with WordPress v3.5 prepare statement
* Cleaned markup for standards compliance and missing closing tags

= 1.0.2 =
* Fixed bug with `[sign_up_sheet]` shortcode sometimes messing up headers in certain themes

= 1.0.1 =
* Fixed bug with sites using query strings on sign-up sheet page

= 1.0 =
* Initial public version