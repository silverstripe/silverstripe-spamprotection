<?php

/**
 * Default configuration settings for the Spam Protection module.
 *
 * You should not put your own configuration in here rather use your
 * mysite/_config.php file
 *
 * @package spamprotection
 */

<<<<<<< HEAD
/**
 * If the comments module is installed then add the spam protection module 
 * to the comments form via this extension.
 *
 * Place this line in your mysite/_config.php
 */

// Object::add_extension('CommentingController', 'CommentSpamProtection');
=======
if(class_exists('Comment')) {
	/**
	 * If the comments module is installed then add the spam protection module 
	 * to the comments form via this extension
	 */
	CommentingController::add_extension('CommentSpamProtection');
}
>>>>>>> FIX: 3.1 extension API updates
