<?php

use App\Models\Branch;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('warehouses', function (Blueprint $table) {
            $table->id();
            $table->json('name');
            $table->string('code')->nullable();
            $table->geometry('location')->nullable();
            $table->foreignIdFor(Branch::class)->onDelete('cascade');
            $table->integer('capacity');
            $table->integer('rate')->default(0);
            $table->bigInteger('parent_id')->default(0)->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users');
            $table->boolean('is_Distribution_point');
            $table->timestamps();
            $table->softDeletes();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('warehouses');
    }
};
