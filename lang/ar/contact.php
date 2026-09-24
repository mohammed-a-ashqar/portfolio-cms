<?php

declare(strict_types=1);

return [
    'title' => 'الرسائل',
    'singular' => 'رسالة',

    'form' => [
        'title' => 'تواصل معي',
        'subtitle' => 'لديك مشروع في ذهنك، أو تود إلقاء التحية فقط؟',
        'submit' => 'إرسال الرسالة',
        'success' => 'شكراً لك. تم إرسال رسالتك.',
    ],

    'fields' => [
        'name' => 'الاسم',
        'email' => 'البريد الإلكتروني',
        'phone' => 'الهاتف',
        'subject' => 'الموضوع',
        'message' => 'الرسالة',
        'company' => 'الشركة',
    ],

    'mail' => [
        'subject' => 'رسالة جديدة من :name',
        'subject_with' => 'رسالة جديدة: :subject',
        'greeting' => 'لديك رسالة جديدة من الموقع',
    ],

    'errors' => [
        'too_many_messages' => 'أرسلت عدة رسائل مؤخراً. يرجى الانتظار قليلاً قبل إرسال رسالة أخرى.',
    ],

    'messages' => [
        'marked_read' => 'تم وضع علامة مقروءة.',
        'replied' => 'تم تسجيل الرد.',
        'deleted' => 'تم حذف الرسالة.',
    ],
];
