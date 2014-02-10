<?php 

/**
 * Apply the spam protection to the comments module if it is installed.
 *
 * @package spamprotection
 */

class CommentSpamProtection extends Extension {

	public function alterCommentForm(&$form) {
		$form->enableSpamProtection(array(
			'mapping' => array(
				'Name' => 'authorName',
				'Email' => 'authorEmail',
				'URL' => 'authorUrl',
				'Comment' => 'body',
				'ReturnURL' => 'contextUrl'
			),
			'checks' => array(
				'spam',
				'profanity'
			)
		));
	}
}
