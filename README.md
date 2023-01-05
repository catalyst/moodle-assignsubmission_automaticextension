Automatic extension - Moodle assignsubmission plugin
====================
Initially created for WR#396436

This plugin allows students to request an automatic extension. A "Request extension" button will be presented on the assignment view page, pressing the button will take the student to a confirmation page, after confirming an extension will be applied using the site settings.

## Site settings
- **conditions** - HTML that will be presented to the student on the confirmation screen when the student requests an extension
- **maximumrequests** - the number of extension requests a user can make for each assignment (setting this to 0 will disable the plugin)
- **extensionlength** - the length of each extension period in hours, each subsequent request will increase the extension by this amount (setting this to 0 will disable the plugin)