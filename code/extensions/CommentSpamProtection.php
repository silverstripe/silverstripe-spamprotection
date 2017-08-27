<?php

/**
 * Apply the spam protection to the comments module if it is installed.
 *
 * @package spamprotection
 */

class CommentSpamProtection extends Extension
{
    public function alterCommentForm(&$form)
    {
        $form->enableSpamProtection(array(
            'name' => 'IsSpam',
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
