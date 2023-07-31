<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMasterCertificateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('master_certificate', function (Blueprint $table) {
            $table->id();
            $table->string('event_name', 150);
            $table->text('event_description');
            $table->date('event_date');
            $table->text('event_signed')->default('[]');
            $table->enum('status',['Publish','Draft','Finished'])->default('Publish');
            $table->unsignedBigInteger('post_by');
            $table->softDeletes();
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
        Schema::dropIfExists('master_certificate');
    }
}
