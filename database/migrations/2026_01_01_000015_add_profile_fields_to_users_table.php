<?php

declare(strict_types=1);

use App\Enums\UserRole;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('role', 24)->default(UserRole::Viewer->value)->after('email');
            $table->string('avatar')->nullable()->after('role');
            $table->string('locale', 5)->default('en')->after('avatar');
            $table->timestamp('last_login_at')->nullable()->after('locale');
            $table->boolean('is_active')->default(true)->after('last_login_at');

            $table->index('role');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropIndex(['role']);
            $table->dropColumn(['role', 'avatar', 'locale', 'last_login_at', 'is_active']);
        });
    }
};
