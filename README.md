# Akrien Granter
Online AAL application granting tool using AAL API

### How to use
* Make a POST request to add.php with `username` (username of the user) and `key` (the key from your config file) fields
* Parse JSON response

### Setting up
* Copy all required files from /backend
* Generate a Client ID (UUID generator can be user as well)
* Rename `YourClientID` to your newely generated one and replace it under the "Constants" section (add.php)

#### Variables 
* `appID` - Your AAL Application ID (can be obtained in the app settings)
* `userToken` - Your AAL Account Token (F12 on aal.party -> application -> local storage -> copy `session`)
* `authKey` - Script access key (set your own)
* `discordLink` - Discord Webhook logging URL
* `lockdown` - Script lockdown

### Credits
* Chris Schuld (http://chrisschuld.com/) for `Browser.php`
* nopjmp (https://github.com/nopjmp/discord-webhooks) for `Embed.php` and `Client.php`
* Timo Huovinen (https://stackoverflow.com/a/46091843) for his edited `OS.php` file :3
