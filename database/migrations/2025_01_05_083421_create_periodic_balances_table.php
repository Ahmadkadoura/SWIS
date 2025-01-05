<?php

use App\Models\Item;
use App\Models\Warehouse;
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
        Schema::create('periodic_balances', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Item::class);
            $table->foreignIdFor(Warehouse::class);
            $table->date('balance_date');
            $table->integer('balance');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('periodic_balances');
    }
};
