<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Item
 * @package App\Models
 * @version February 9, 2024, 10:59 pm UTC
 *
 */
class Item extends Model
{
    use SoftDeletes;

    use HasFactory;

    public $table = 'items';


    protected $dates = ['deleted_at'];


    /**
     * Fillable fields for mass assignment.
     *
     * @var array
     */
    public $fillable = [
        'mms_id',
        'title',
        'holding_id',
        'library_name',
        'barcode',
        'creation_date',
        'description',
        'lifecycle',
        'physical_item_id',
        'receiving_date',
        'replacement_cost',
        'order_line_type',
        'po_line_reference',
        'reporting_code_first',
        'fund_expenditure',
        'num_of_items_per_fund',
        'status',
    ];


    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [

    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];


}
