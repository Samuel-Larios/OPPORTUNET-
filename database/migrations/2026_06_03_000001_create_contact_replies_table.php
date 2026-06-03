<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contact_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contact_id')->constrained('contacts')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('message');
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->index(['contact_id', 'sent_at']);
        });

        DB::table('contacts')
            ->select(['id', 'traite_par', 'reponse_admin', 'repondu_le', 'updated_at', 'created_at'])
            ->whereNotNull('reponse_admin')
            ->orderBy('id')
            ->chunkById(100, function ($contacts): void {
                $rows = [];

                foreach ($contacts as $contact) {
                    $timestamp = $contact->repondu_le ?? $contact->updated_at ?? $contact->created_at ?? now();

                    $rows[] = [
                        'contact_id' => $contact->id,
                        'user_id' => $contact->traite_par,
                        'message' => $contact->reponse_admin,
                        'sent_at' => $timestamp,
                        'created_at' => $timestamp,
                        'updated_at' => $timestamp,
                    ];
                }

                if ($rows !== []) {
                    DB::table('contact_replies')->insert($rows);
                }
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_replies');
    }
};
