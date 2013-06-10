<?php 

/**
 * Apply the spam protection to the comments module if it is installed
 *
 * @package spamprotection
 */

class CommentSpamProtection extends Extension {

	/**
	 * Disable the AJAX commenting and update the form
	 * with the {@link SpamProtectorField} which is enabled
	 */
	public function alterCommentForm(&$form) {
		SpamProtectorManager::update_form($form, null, array(
			'Name' => 'author_name', 
			'URL' => 'author_url', 
			'Comment' => 'post_body', 
			'Email' => 'author_mail'
		));
	}
}
