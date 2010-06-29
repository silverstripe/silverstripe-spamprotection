# Spam Protection Module Installation

## Install

1. Unzip this file (spamprotection-0.3.tar.gz) inside your SilverStripe installation directory.
It should be at the same level as the 'cms' and 'sapphire' modules.

2. Ensure the directory name for the module is 'spamprotection'. 

3. Visit your SilverStripe site in a webbrowser and run www.yoursite.com/dev/build

5. We now need to setup some basic features to get the module up and running. Open up _config.php
inside project directory (typically 'mysite/_config.php') with your favourite text editor.
Read the instructions below to setup the initial configuration of the module.


## Configuring

Before putting the following code in '_config.php', make sure you have a additional protector class installed. 
The SpamProtector module only provides the backbone. To make use of the module you have to have an additional protector
such as Mollom or Recaptcha. Both of these modules can be downloaded off the SilverStripe.org website. 

	SpamProtectorManager::set_spam_protector('MollomSpamProtector');


## Updating a form to include Spam Protection

This following code should appear after the form creation. 

	// your existing form code here...
	$form = new Form( .. );
	
	// add this line
	$protector = SpamProtectorManager::update_form($form, 'Message');

This code add an instance of a 'SpamProtectorField' class specified in SETTING UP THE MODULE section. The newly created field will have 
MollomField field. The first parameter is a Form object in which the field will be added into and the second parameter tells 
SpamProtectorManagor to place the new field before a field named 'Message'. 


## Using Spam Protection with User Forms
