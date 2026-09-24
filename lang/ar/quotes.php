<?php

declare(strict_types=1);

return [
    'title' => 'طلبات عروض الأسعار',
    'singular' => 'طلب عرض سعر',
    'reference' => 'الرقم المرجعي',
    'budget' => 'الميزانية',
    'quoted_amount' => 'المبلغ المعروض',
    'admin_notes' => 'ملاحظات داخلية',
    'no_budget' => 'غير محددة',

    'mail' => [
        'subject' => 'طلب عرض سعر جديد من :name',
        'greeting' => 'لديك طلب عرض سعر جديد',
        'view_in_admin' => 'افتح في لوحة التحكم',
    ],

    'form' => [
        'title' => 'اطلب عرض سعر',
        'subtitle' => 'أخبرني عن مشروعك وسأعود إليك بنطاق عمل وسعر.',
        'submit' => 'إرسال الطلب',
        'success' => 'شكراً لك. تم استلام طلبك :reference.',
    ],

    'errors' => [
        'illegal_transition' => 'لا يمكن نقل الطلب من ":from" إلى ":to".',
    ],

    'messages' => [
        'status_updated' => 'تم تحديث الحالة إلى :status.',
        'deleted' => 'تم حذف الطلب.',
    ],
];
