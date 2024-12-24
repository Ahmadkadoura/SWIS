<?php

use App\Models\Item;
use App\Models\WarehouseItem;
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
        Schema::create('ctns', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(WarehouseItem::class);
            $table->foreignIdFor(Item::class);
            $table->integer('quantity');
            $table->string("CTN");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ctns');
    }
};
