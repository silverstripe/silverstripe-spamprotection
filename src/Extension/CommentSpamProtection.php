<?php

namespace SilverStripe\SpamProtection\Extension;

use SilverStripe\Comments\Controllers\CommentingController;
use SilverStripe\Core\Extension;

/**
 * Apply the spam protection to the comments module if it is installed.
 *
 * @extends Extension<CommentingController>
 */
class CommentSpamProtection extends Extension
{
    public function alterCommentForm(&$form)
    {
        $form->enableSpamProtection([
            'name' => 'IsSpam',
            'mapping' => [
                'Name' => 'authorName',
                'Email' => 'authorEmail',
                'URL' => 'authorUrl',
                'Comment' => 'body',
                'ReturnURL' => 'contextUrl'
            ],
            'checks' => [
                'spam',
                'profanity'
            ]
        ]);
    }
}
