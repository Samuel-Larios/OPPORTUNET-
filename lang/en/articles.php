<?php

return [
    'meta' => [
        'title' => 'Articles',
    ],
    'page' => [
        'label' => 'Articles',
        'title' => 'Discover our articles, insights, and updates',
        'subtitle' => 'Explore practical, inspiring, and strategic content for your professional, spiritual, and personal journey.',
        'stat_total' => 'published articles',
        'stat_featured' => 'featured articles',
        'stat_visible' => 'visible results',
    ],
    'search' => [
        'placeholder' => 'Search for an article, topic, or keyword...',
        'aria' => 'Search articles',
        'live' => 'Live search enabled',
        'searching' => 'Updating...',
    ],
    'filters' => [
        'all_categories' => 'All categories',
        'reset' => 'Reset',
    ],
    'results' => [
        'count' => '{0} No articles found|{1} :count article found|[2,*] :count articles found',
    ],
    'badges' => [
        'featured' => 'Featured',
    ],
    'card' => [
        'default_badge' => 'Article',
        'published' => 'Published',
        'reading_time' => 'Reading time',
        'reading_time_fallback' => '5 min',
        'views' => 'Views',
        'view_details' => 'Read article',
    ],
    'empty' => [
        'title' => 'No article matches your search',
        'text' => 'Try another keyword or a broader category.',
    ],
    'pagination' => [
        'label' => 'Articles pagination',
        'previous' => 'Previous',
        'next' => 'Next',
        'page' => 'Page :current of :last',
    ],
    'detail' => [
        'back' => 'Back to articles',
        'side_label' => 'Reading',
        'side_title' => 'Keep exploring',
        'side_text' => 'Find more useful articles to deepen your vision, progress, and decision-making.',
        'all_articles' => 'View all articles',
        'summary' => 'Quick summary',
        'standard' => 'Standard article',
        'gallery_title' => 'Image gallery',
        'gallery_count' => '{1} :count image|[2,*] :count images',
        'labels' => [
            'category' => 'Category',
            'status' => 'Highlight',
            'reading_time' => 'Reading time',
            'comments' => 'Comments',
        ],
        'related_label' => 'Read next',
        'related_title' => 'Similar articles',
    ],
    'comments' => [
        'label' => 'Comments',
        'title' => 'Share your reaction',
        'subtitle' => 'Comments are reviewed by the team before publication on the article.',
        'count' => '{0} No approved comments|{1} :count approved comment|[2,*] :count approved comments',
        'empty' => 'No approved comment yet.',
        'success' => 'Your comment has been received. It will be reviewed before publication.',
        'closed' => 'Comments are currently disabled for this article.',
        'logged_in_as' => 'You are commenting as :author.',
        'login_required' => 'Sign in or create an account to leave a comment.',
        'login_action' => 'Sign in',
        'register_action' => 'Create an account',
        'reply_to' => 'Replying to :author',
        'cancel_reply' => 'Cancel reply',
        'reply_action' => 'Reply',
        'submit' => 'Send my comment',
        'fields' => [
            'author_name' => 'Your name',
            'author_email' => 'Your email',
            'content' => 'Your comment',
        ],
        'validation' => [
            'parent' => 'The comment you are replying to is not valid.',
        ],
    ],
];
