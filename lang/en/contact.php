<?php

declare(strict_types=1);

return [
    'title' => 'Messages',
    'singular' => 'Message',

    'form' => [
        'title' => 'Get in touch',
        'subtitle' => 'Have a project in mind, or just want to say hello?',
        'submit' => 'Send message',
        'success' => 'Thank you. Your message has been sent.',
    ],

    'fields' => [
        'name' => 'Name',
        'email' => 'Email',
        'phone' => 'Phone',
        'subject' => 'Subject',
        'message' => 'Message',
        'company' => 'Company',
    ],

    'mail' => [
        'subject' => 'New message from :name',
        'subject_with' => 'New message: :subject',
        'greeting' => 'You have a new message from the site',
    ],

    'errors' => [
        'too_many_messages' => 'You have sent several messages recently. Please wait a little before sending another.',
    ],

    'messages' => [
        'marked_read' => 'Marked as read.',
        'replied' => 'Reply recorded.',
        'deleted' => 'Message deleted.',
    ],
];
