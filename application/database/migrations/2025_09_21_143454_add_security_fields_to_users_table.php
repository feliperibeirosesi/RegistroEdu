<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('status', ['pending_email_verification', 'waiting_admin_approval', 'approved', 'rejected'])
                  ->default('pending_email_verification')->after('role');
            $table->string('email_verification_token')->nullable()->after('email_verified_at');
            $table->timestamp('admin_approved_at')->nullable()->after('email_verification_token');
            $table->uuid('approved_by')->nullable()->after('admin_approved_at');
            $table->text('rejection_reason')->nullable()->after('approved_by');

            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropColumn([
                'status', 'email_verification_token',
                'admin_approved_at', 'approved_by', 'rejection_reason'
            ]);
        });
    }
};
