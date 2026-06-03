<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'role_id')) {
                $table->foreignId('role_id')->nullable()->constrained('roles')->nullOnDelete();
            }

            if (! Schema::hasColumn('users', 'prenom')) {
                $table->string('prenom', 80)->nullable();
            }

            if (! Schema::hasColumn('users', 'nom')) {
                $table->string('nom', 80)->nullable();
            }

            if (! Schema::hasColumn('users', 'telephone')) {
                $table->string('telephone', 20)->nullable();
            }

            if (! Schema::hasColumn('users', 'pays')) {
                $table->string('pays', 80)->nullable();
            }

            if (! Schema::hasColumn('users', 'ville')) {
                $table->string('ville', 80)->nullable();
            }

            if (! Schema::hasColumn('users', 'photo')) {
                $table->string('photo')->nullable();
            }

            if (! Schema::hasColumn('users', 'bio')) {
                $table->text('bio')->nullable();
            }

            if (! Schema::hasColumn('users', 'genre')) {
                $table->string('genre', 20)->default('non_precise');
            }

            if (! Schema::hasColumn('users', 'date_naissance')) {
                $table->date('date_naissance')->nullable();
            }

            if (! Schema::hasColumn('users', 'profession')) {
                $table->string('profession', 120)->nullable();
            }

            if (! Schema::hasColumn('users', 'niveau_etude')) {
                $table->string('niveau_etude', 80)->nullable();
            }

            if (! Schema::hasColumn('users', 'whatsapp')) {
                $table->string('whatsapp', 20)->nullable();
            }

            if (! Schema::hasColumn('users', 'actif')) {
                $table->boolean('actif')->default(true);
            }

            if (! Schema::hasColumn('users', 'newsletter')) {
                $table->boolean('newsletter')->default(false);
            }

            if (! Schema::hasColumn('users', 'langue')) {
                $table->string('langue', 10)->default('fr');
            }

            if (! Schema::hasColumn('users', 'derniere_connexion')) {
                $table->timestamp('derniere_connexion')->nullable();
            }

            if (! Schema::hasColumn('users', 'token_verification')) {
                $table->string('token_verification')->nullable();
            }

            if (! Schema::hasColumn('users', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        $defaultRoleId = Schema::hasTable('roles')
            ? DB::table('roles')->where('nom', 'user')->value('id')
            : null;

        $superAdminRoleId = Schema::hasTable('roles')
            ? DB::table('roles')->where('nom', 'super_admin')->value('id')
            : null;

        $users = DB::table('users')
            ->select('id', 'name', 'email', 'role_id', 'prenom', 'nom', 'actif', 'newsletter', 'langue')
            ->get();

        foreach ($users as $user) {
            $updates = [];

            $fullName = trim((string) $user->name);
            $parts = preg_split('/\s+/', $fullName, 2) ?: [];
            $firstName = $parts[0] ?? 'Utilisateur';
            $lastName = $parts[1] ?? $firstName;

            if ($user->prenom === null || $user->prenom === '') {
                $updates['prenom'] = $firstName;
            }

            if ($user->nom === null || $user->nom === '') {
                $updates['nom'] = $lastName;
            }

            if ($user->actif === null) {
                $updates['actif'] = true;
            }

            if ($user->newsletter === null) {
                $updates['newsletter'] = false;
            }

            if ($user->langue === null || $user->langue === '') {
                $updates['langue'] = 'fr';
            }

            if ($user->role_id === null && $defaultRoleId !== null) {
                $updates['role_id'] = $defaultRoleId;
            }

            if ($user->email === 'larioss383@gmail.com' && $superAdminRoleId !== null) {
                $updates['role_id'] = $superAdminRoleId;
            }

            if ($updates !== []) {
                DB::table('users')->where('id', $user->id)->update($updates);
            }
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'role_id')) {
                $table->dropForeign(['role_id']);
                $table->dropColumn('role_id');
            }

            $columns = [
                'prenom',
                'nom',
                'telephone',
                'pays',
                'ville',
                'photo',
                'bio',
                'genre',
                'date_naissance',
                'profession',
                'niveau_etude',
                'whatsapp',
                'actif',
                'newsletter',
                'langue',
                'derniere_connexion',
                'token_verification',
                'deleted_at',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
