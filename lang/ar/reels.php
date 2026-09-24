<?php

declare(strict_types=1);

return [
    'title' => 'الريلز',
    'showcase_title' => 'أعمالي بالفيديو',
    'showcase_subtitle' => 'مقاطع قصيرة لما أبنيه — طريقة العمل، التفاصيل، والنتيجة النهائية.',
    'watch' => 'شاهد الريل',
    'open_original' => 'افتح على :provider',
    'empty' => 'لا توجد ريلز منشورة بعد.',
    'all_providers' => 'كل المنصات',

    'actions' => [
        'import' => 'استيراد ريل',
        'paste_url' => 'الصق رابط ريل أو Short أو تيك توك',
        'reorder_saved' => 'تم حفظ الترتيب.',
    ],

    'fields' => [
        'url' => 'رابط الفيديو',
        'caption' => 'التعليق',
        'poster' => 'صورة الغلاف',
        'poster_hint' => 'اختيارية. الغلاف المخصص أسرع تحميلاً وأجمل من صورة المنصة.',
        'project' => 'المشروع المرتبط',
        'featured' => 'مميّز',
        'active' => 'ظاهر في الموقع',
    ],

    'errors' => [
        'unsupported_url' => 'لا توجد استراتيجية تدعم هذا الرابط: :url',
        'unparsable_url' => 'تعذّر استخراج معرّف الفيديو من: :url',
        'duplicate' => 'هذا الريل مستورد مسبقاً (:id).',
    ],

    'messages' => [
        'imported' => 'تم استيراد الريل بنجاح.',
        'updated' => 'تم تحديث الريل.',
        'deleted' => 'تم حذف الريل.',
    ],
];
