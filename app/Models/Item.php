<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;


/**
 * Class Item
 * @package App\Models
 * @version December 13, 2023, 10:59 pm UTC
 *
 * @property int $id
 * @property string $mms_id
 * @property string $title
 * @property string $holding_id
 * @property string $library_name
 * @property string $barcode
 * @property string $creation_date
 * @property string|null $description
 * @property string $lifecycle
 * @property string $physical_item_id
 * @property string $receiving_date
 * @property string $replacement_cost
 * @property string $order_line_type
 * @property string $po_line_reference
 * @property string $reporting_code_first
 * @property string|null $fund_expenditure
 * @property int|null $num_of_items_per_fund
 * @property string $status
 * @property int|null $last_operation_id
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
        'last_operation_id',

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
