<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    private array $permissions = ['receipt-table', 'receipt-add', 'receipt-edit', 'receipt-delete'];

    /**
     * سندات القبض — receipt vouchers issued from the admin dashboard.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('receipt_vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('person_name');
            $table->decimal('amount', 12, 3); // JOD: 3 decimals (fils)
            $table->date('receipt_date');
            $table->enum('payment_method', ['cash', 'cheque', 'bank_transfer', 'card'])->default('cash');
            $table->string('description')->nullable(); // "وذلك عن"
            $table->foreignId('created_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamps();

            $table->index('receipt_date');
        });

        // Register the permissions here too, so `php artisan migrate` alone is
        // enough on the server (PermissionSeeder lists them as well).
        foreach ($this->permissions as $name) {
            DB::table('permissions')->insertOrIgnore([
                'name'       => $name,
                'guard_name' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('receipt_vouchers');

        DB::table('permissions')->whereIn('name', $this->permissions)->where('guard_name', 'admin')->delete();
    }
};
