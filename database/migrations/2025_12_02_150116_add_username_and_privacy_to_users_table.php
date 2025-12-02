<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->unique()->after('name'); // Username unik
            $table->boolean('is_private')->default(false)->after('password'); // Akun private/public
            $table->text('bio')->nullable()->after('is_private'); // Bio profil
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['username', 'is_private', 'bio']);
        });
    }
};
