<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            // ANTES (no permitía NULL y rompía al dejar el campo vacío en el formulario):
            // $table->string('fiscal_info');

            // DESPUÉS (consistente con la validación 'nullable' del controlador):
            $table->string('fiscal_info', 255)->nullable();
            $table->string('fiscal_data', 255)->nullable();
            $table->string('delivery_address');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};