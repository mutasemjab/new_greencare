<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('visit_form_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visit_form_id')->constrained('visit_forms')->cascadeOnDelete();
            $table->foreignId('visit_form_field_id')->nullable()->constrained('visit_form_fields')->nullOnDelete();
            $table->string('field_question');
            $table->enum('field_type', ['text', 'number', 'choice', 'checklist']);
            $table->text('answer_text')->nullable();
            $table->json('answer_json')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('visit_form_answers');
    }
};
