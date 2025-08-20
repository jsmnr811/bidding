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
            $table->string('region_id')->nullable()->after('lastname');
            $table->string('province_id')->nullable()->after('region_id');
            $table->string('affiliation')->nullable()->after('province_id');
            $table->string('designation')->nullable()->after('affiliation');
            $table->enum('gender', ['Male', 'Female', 'Other'])->nullable()->after('designation');
            $table->string('phone')->nullable()->after('gender');
            $table->string('email')->nullable()->unique()->after('phone');
            $table->string('vulnerability')->nullable()->after('email');
            $table->string('food_restriction')->nullable()->after('vulnerability');
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
