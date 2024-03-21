<?php

use App\Models\Modulo;
use App\Models\Tema;
use App\Models\User;
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
        Schema::create('modulo_user', function (Blueprint $table) {
            $table->foreignIdFor(Modulo::class);
            $table->foreignIdFor(User::class);
            $table->foreignIdFor(Tema::class)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modulo_user');
    }
};
