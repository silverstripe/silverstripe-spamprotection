<?php

/**
 * Default configuration settings for the Spam Protection module.
 *
 * You should not put your own configuration in here rather use your
 * mysite/_config.php file
 *
 * @package spamprotection
 */

if(class_exists('Comment')) {
	/**
	 * If the comments module is installed then add the spam protection module 
	 * to the comments form via this extension
	 */
	Object::add_extension('CommentingController', 'CommentSpamProtection');
}