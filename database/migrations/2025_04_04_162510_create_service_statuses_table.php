<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('service_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('service_name'); // config/services_status.php look at services.KEYS
            $table->char('type',4)->index(); // config/services_status.php look at services.KEYS.type
            $table->enum('status', \App\Enums\ServiceStatusEnum::allValue());
            $table->json('full_response')->nullable();
            $table->json('issues')->nullable();
            $table->string('extra', 1024)->nullable();
            $table->timestamp('checked_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_statuses');
    }
};
