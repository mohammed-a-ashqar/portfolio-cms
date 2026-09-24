<?php

declare(strict_types=1);

return [
    'project_status' => [
        'draft' => 'Draft',
        'published' => 'Published',
        'archived' => 'Archived',
    ],

    'quote_status' => [
        'new' => 'New',
        'in_review' => 'In review',
        'quoted' => 'Quoted',
        'accepted' => 'Accepted',
        'rejected' => 'Rejected',
        'closed' => 'Closed',
    ],

    'message_status' => [
        'unread' => 'Unread',
        'read' => 'Read',
        'replied' => 'Replied',
        'archived' => 'Archived',
    ],

    'billing_period' => [
        'one_time' => 'One-time',
        'hourly' => 'Hourly',
        'monthly' => 'Monthly',
        'yearly' => 'Yearly',
    ],

    'billing_period_suffix' => [
        'hourly' => '/ hour',
        'monthly' => '/ month',
        'yearly' => '/ year',
    ],

    'reel_provider' => [
        'instagram' => 'Instagram',
        'youtube' => 'YouTube',
        'tiktok' => 'TikTok',
        'manual' => 'Self-hosted',
    ],

    'user_role' => [
        'admin' => 'Administrator',
        'editor' => 'Editor',
        'viewer' => 'Viewer',
    ],
];
