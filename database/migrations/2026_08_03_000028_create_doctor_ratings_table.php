<?php

// Rating is stored directly in doctors.rating (decimal 2,1) — no separate table needed.
// This file is intentionally left as a no-op.

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void {}
    public function down(): void {}
};
