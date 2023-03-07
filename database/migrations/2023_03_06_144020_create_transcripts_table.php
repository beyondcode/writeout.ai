<?php

use App\Enum\TranscriptStatus;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('transcripts', function (Blueprint $table) {
            $table->uuid('id');
            $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete();
            $table->string('hash');
            $table->unsignedInteger('duration')->nullable();
            $table->string('source_language')->nullable();
            $table->string('status')->default(TranscriptStatus::PENDING->value);
            $table->longText('transcript')->nullable();
            $table->json('translations')->nullable();
            $table->text('error')->nullable();
            $table->string('prompt')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('transcripts');
    }
};
