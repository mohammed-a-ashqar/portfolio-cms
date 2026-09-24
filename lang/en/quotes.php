<?php

declare(strict_types=1);

return [
    'title' => 'Quote requests',
    'singular' => 'Quote request',
    'reference' => 'Reference',
    'budget' => 'Budget',
    'quoted_amount' => 'Quoted amount',
    'admin_notes' => 'Internal notes',
    'no_budget' => 'Not specified',

    'mail' => [
        'subject' => 'New quote request from :name',
        'greeting' => 'You have a new quote request',
        'view_in_admin' => 'Open in admin',
    ],

    'form' => [
        'title' => 'Request a quote',
        'subtitle' => 'Tell me about the project and I will come back with a scope and a price.',
        'submit' => 'Send request',
        'success' => 'Thank you. Your request :reference has been received.',
    ],

    'errors' => [
        'illegal_transition' => 'A quote cannot move from ":from" to ":to".',
    ],

    'messages' => [
        'status_updated' => 'Status updated to :status.',
        'deleted' => 'Quote request deleted.',
    ],
];
