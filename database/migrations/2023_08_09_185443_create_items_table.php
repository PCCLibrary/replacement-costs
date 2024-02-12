<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id(); // Auto-incrementing ID
            $table->string('mms_id');
            $table->string('title');
            $table->string('holding_id');
            $table->string('library_name');
            $table->string('barcode');
            $table->string('creation_date');
            $table->string('description');
            $table->string('lifecycle');
            $table->string('physical_item_id');
            $table->string('receiving_date');
            $table->string('replacement_cost');
            $table->string('order_line_type');
            $table->string('po_line_reference');
            $table->string('reporting_code_first');
            $table->string('fund_expenditure')->nullable();
            $table->integer('num_of_items_per_fund')->nullable();
            // Added status field with a default value of "new"
            $table->string('status')->default('new');
            $table->timestamps(); // 'created_at' and 'updated_at'
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('items');
    }
}
