<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Role::create(['name' => 'Admin']);
        Role::create(['name' => 'Staff']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Role::destroy(Role::whereIn('name', ['Admin', 'Staff'])->pluck('id'));
    }
};
