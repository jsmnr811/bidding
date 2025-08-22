<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('geomapping_users', function (Blueprint $table) {
            $table->string('firstname')->nullable()->after('id');
            $table->string('middlename')->nullable()->after('firstname');
            $table->string('lastname')->nullable()->after('middlename');
            $table->string('ext_name')->nullable()->after('lastname');
            $table->enum('sex', ['Male', 'Female'])->nullable()->after('ext_name');

            $table->string('institution')->nullable()->after('sex');
           $table->string('office')->nullable()->after('institution');
            $table->string('designation')->nullable()->after('office');
            $table->string('region_id')->nullable()->after('lastname');
            $table->string('province_id')->nullable()->after('region_id');

            $table->string('email')->nullable()->unique()->after('province_id');
            $table->string('contact_number')->nullable()->after('email');

            $table->string('food_restriction')->nullable()->after('contact_number');

            $table->string('role')->nullable()->after('login_code');
            $table->string('group_number')->nullable()->after('role');
            $table->string('table_number')->nullable()->after('group_number');

            $table->string('image')->nullable()->after('food_restriction');
        });
    }

    public function down(): void
    {
        Schema::table('geomapping_users', function (Blueprint $table) {
            $table->dropColumn([
                'firstname',
                'middlename',
                'lastname',
                'region_id',
                'province_id',
                'affiliation',
                'designation',
                'gender',
                'phone',
                'email',
                'vulnerability',
                'food_restriction',
                'image',
            ]);
        });
    }
};
