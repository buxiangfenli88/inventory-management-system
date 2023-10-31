<?php

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $user = User::where('username', 'admin')->first();

        if ($user) {
            return;
        }

        $user = new User([
            'username' => 'admin',
            'email' => 'admin@gmail.com',
            'name' => 'Admin',
            'password' => Hash::make('password'),
        ]);
        $user->save();

        $user->assignRole(UserRole::ADMIN);

        $user->save();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        User::where('username', 'admin')->delete();
    }
};
