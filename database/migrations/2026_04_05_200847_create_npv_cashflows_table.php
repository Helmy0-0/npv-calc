<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{
    public function up(): void
    {
        Schema::create('npv_cash_flows', function (Blueprint $table) {
            $table->id();

            $table->foreignId('npv_project_id')
                  ->constrained('npv_projects')
                  ->onDelete('cascade');


            $table->unsignedSmallInteger('year');       
            $table->decimal('cash_flow', 20, 2);         
            $table->decimal('discount_factor', 15, 8);  
            $table->decimal('present_value', 20, 2);      

            $table->timestamps();

            $table->index(['npv_project_id', 'year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('npv_cash_flows');
    }
};