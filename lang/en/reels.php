<?php

declare(strict_types=1);

return [
    'title' => 'Reels',
    'showcase_title' => 'Work in motion',
    'showcase_subtitle' => 'Short videos of the things I build — process, detail and the finished result.',
    'watch' => 'Watch reel',
    'open_original' => 'Open on :provider',
    'empty' => 'No reels published yet.',
    'all_providers' => 'All platforms',

    'actions' => [
        'import' => 'Import reel',
        'paste_url' => 'Paste a reel, Short or TikTok URL',
        'reorder_saved' => 'Order saved.',
    ],

    'fields' => [
        'url' => 'Video URL',
        'caption' => 'Caption',
        'poster' => 'Poster image',
        'poster_hint' => 'Optional. A custom poster loads faster and looks better than the platform thumbnail.',
        'project' => 'Related project',
        'featured' => 'Featured',
        'active' => 'Visible on the site',
    ],

    'errors' => [
        'unsupported_url' => 'No provider can handle this URL: :url',
        'unparsable_url' => 'Could not read a video id from: :url',
        'duplicate' => 'This reel is already imported (:id).',
    ],

    'messages' => [
        'imported' => 'Reel imported successfully.',
        'updated' => 'Reel updated.',
        'deleted' => 'Reel deleted.',
    ],
];
