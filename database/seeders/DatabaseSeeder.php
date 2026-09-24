<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\BillingPeriod;
use App\Enums\ProjectStatus;
use App\Enums\ReelProvider;
use App\Enums\UserRole;
use App\Models\Category;
use App\Models\ContactMessage;
use App\Models\Package;
use App\Models\Project;
use App\Models\QuoteRequest;
use App\Models\Reel;
use App\Models\Service;
use App\Models\Skill;
use App\Models\Technology;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Demo content so a fresh clone looks like a real site.
 *
 * Deliberately does NOT use WithoutModelEvents: slugs and sort order are set
 * by model hooks, and silencing them would seed empty slugs.
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'password' => 'password',
                'role' => UserRole::Admin,
                'is_active' => true,
                'email_verified_at' => now(),
            ],
        );

        $technologies = collect(['Laravel', 'PHP', 'MySQL', 'Tailwind CSS', 'Alpine.js', 'Vue.js', 'Redis', 'Docker'])
            ->map(fn (string $name) => Technology::query()->firstOrCreate(['name' => $name]));

        $categories = collect([
            ['en' => 'Web applications', 'ar' => 'تطبيقات ويب'],
            ['en' => 'E-commerce', 'ar' => 'متاجر إلكترونية'],
            ['en' => 'APIs', 'ar' => 'واجهات برمجية'],
        ])->map(fn (array $name) => Category::query()->create(['name' => $name, 'is_active' => true]));

        $projects = [
            [
                'title' => ['en' => 'Clinic management system', 'ar' => 'نظام إدارة عيادات'],
                'summary' => ['en' => 'Appointments, patient records and billing for a multi-branch clinic.', 'ar' => 'مواعيد وسجلات مرضى وفوترة لعيادة متعددة الفروع.'],
            ],
            [
                'title' => ['en' => 'Auto parts marketplace', 'ar' => 'سوق قطع غيار السيارات'],
                'summary' => ['en' => 'Multi-vendor store with fitment search by make, model and year.', 'ar' => 'متجر متعدد البائعين مع بحث التوافق حسب الماركة والموديل والسنة.'],
            ],
            [
                'title' => ['en' => 'School ERP', 'ar' => 'نظام إدارة مدارس'],
                'summary' => ['en' => 'Attendance, grading and parent portal in Arabic and English.', 'ar' => 'حضور ودرجات وبوابة أولياء أمور بالعربية والإنجليزية.'],
            ],
            [
                'title' => ['en' => 'Helpdesk ticketing API', 'ar' => 'واجهة برمجية لنظام تذاكر الدعم'],
                'summary' => ['en' => 'REST API with SLA timers, queues and webhook notifications.', 'ar' => 'واجهة REST مع مؤقتات SLA وطوابير وإشعارات webhook.'],
            ],
        ];

        foreach ($projects as $index => $data) {
            $project = Project::query()->create([
                ...$data,
                'description' => ['en' => $data['summary']['en']."\n\nBuilt with a layered architecture: repositories, actions and events."],
                'category_id' => $categories[$index % $categories->count()]->getKey(),
                'client_name' => fake()->company(),
                'status' => ProjectStatus::Published,
                'is_featured' => $index < 3,
                'completed_at' => now()->subMonths($index * 3),
            ]);

            $project->technologies()->sync($technologies->random(3)->pluck('id'));
        }

        foreach ([
            ['en' => 'Laravel', 'group' => 'Backend', 'p' => 95],
            ['en' => 'MySQL', 'group' => 'Backend', 'p' => 85],
            ['en' => 'Tailwind CSS', 'group' => 'Frontend', 'p' => 90],
            ['en' => 'Vue.js', 'group' => 'Frontend', 'p' => 75],
            ['en' => 'Docker', 'group' => 'DevOps', 'p' => 70],
        ] as $skill) {
            Skill::query()->create([
                'name' => ['en' => $skill['en']],
                'group' => $skill['group'],
                'proficiency' => $skill['p'],
                'is_active' => true,
            ]);
        }

        $service = Service::query()->create([
            'title' => ['en' => 'Web application development', 'ar' => 'تطوير تطبيقات الويب'],
            'excerpt' => ['en' => 'From idea to production: design, build and deploy.', 'ar' => 'من الفكرة إلى الإنتاج: تصميم وبناء ونشر.'],
            'is_active' => true,
            'is_featured' => true,
        ]);

        foreach ([
            ['Starter', 150000, false, ['Up to 5 pages', 'Admin panel', 'One language']],
            ['Business', 350000, true, ['Up to 15 pages', 'Admin panel', 'Arabic + English', 'SEO setup']],
            ['Retainer', 80000, false, ['20 hours / month', 'Priority support', 'Monthly report']],
        ] as $i => [$name, $price, $popular, $features]) {
            Package::query()->create([
                'service_id' => $service->getKey(),
                'name' => ['en' => $name],
                'price_minor' => $price,
                'currency' => 'USD',
                'billing_period' => $i === 2 ? BillingPeriod::Monthly : BillingPeriod::OneTime,
                'features' => ['en' => $features],
                'delivery_days' => $i === 2 ? null : 14 + $i * 14,
                'is_popular' => $popular,
                'is_active' => true,
            ]);
        }

        // YouTube reels get real thumbnails without any API call.
        foreach (['dQw4w9WgXcQ', 'jNQXAC9IVRw', 'kJQP7kiw5Fk'] as $id) {
            Reel::query()->create([
                'provider' => ReelProvider::YouTube,
                'external_id' => $id,
                'url' => "https://www.youtube.com/shorts/{$id}",
                'embed_url' => "https://www.youtube-nocookie.com/embed/{$id}?rel=0&modestbranding=1",
                'thumbnail_url' => "https://i.ytimg.com/vi/{$id}/hqdefault.jpg",
                'caption' => ['en' => 'Behind the build', 'ar' => 'خلف الكواليس'],
                'is_active' => true,
            ]);
        }

        Testimonial::query()->create([
            'author_name' => 'Sara Ahmed',
            'author_title' => ['en' => 'Product manager'],
            'author_company' => 'Acme',
            'content' => ['en' => 'Delivered on time, communicated clearly, and the code was a pleasure to take over.', 'ar' => 'سلّم في الموعد، وتواصل بوضوح، والكود كان سهل الاستلام.'],
            'rating' => 5,
            'is_featured' => true,
            'is_active' => true,
        ]);

        QuoteRequest::factory()->count(3)->create(['service_id' => $service->getKey()]);
        ContactMessage::factory()->count(3)->create();
    }
}
