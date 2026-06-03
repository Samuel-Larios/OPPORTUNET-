<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->text('notes_admin')->nullable()->after('reponse_admin');
            $table->timestamp('rappel_le')->nullable()->after('repondu_le');
            $table->string('rappel_note', 200)->nullable()->after('rappel_le');
        });
    }

    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropColumn([
                'notes_admin',
                'rappel_le',
                'rappel_note',
            ]);
        });
    }
};
