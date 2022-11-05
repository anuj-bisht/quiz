<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->integer('category_vendor_id');    
            $table->string('name');    
            
            $table->text('regofc_address');    
            $table->string('regofc_website');    
            $table->string('regofc_telephone');    
            $table->string('regofc_fax');    
            $table->string('regofc_contact_person');    
            $table->string('regofc_email');    
            $table->string('regofc_designation');    
            $table->string('regofc_mobile');    
            
            $table->text('workofc_address');    
            $table->string('workofc_website');    
            $table->string('workofc_telephone');    
            $table->string('workofc_fax');    
            $table->string('workofc_contact_person');    
            $table->string('workofc_email');    
            $table->string('workofc_designation');    
            $table->string('workofc_mobile');    
            
            
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
        Schema::dropIfExists('vendors');
    }
}
