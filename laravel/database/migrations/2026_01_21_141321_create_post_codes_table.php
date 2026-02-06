<?php

// database/migrations/xxxx_xx_xx_xxxxxx_create_post_codes_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('post_codes', function (Blueprint $table) {
    $table->id();
    $table->string('city');
    $table->string('postal_code');
    $table->string('country')->default('HR');
    $table->timestamps();

    $table->unique(['city', 'country']); // 👈 KEY PART
});

    }

    public function down(): void
    {
        Schema::dropIfExists('post_codes');
    }
};
