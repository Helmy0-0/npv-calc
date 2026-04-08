<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('npv_projects', function (Blueprint $table) {
            $table->id();


            $table->string('project_name');            
            $table->decimal('initial_investment', 20, 2); 
            $table->decimal('discount_rate', 8, 4);       

            $table->decimal('total_present_value', 20, 2); 
            $table->decimal('npv', 20, 2);                
            $table->string('decision');                 
            $table->string('decision_class');            
            $table->boolean('is_feasible');               

            $table->timestamps(); 
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('npv_projects');
    }
};