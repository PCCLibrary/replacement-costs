<?php

namespace App\Repositories;

use App\Models\Item;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class ItemRepository
 * @package App\Repositories
 * @version December 13, 2023, 10:59 pm UTC
 */
class ItemRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [];

    /**
     * Return searchable fields
     *
     * @return array
     */
    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Item::class;
    }

    /**
     * Save parsed, fetched items to the database.
     *
     * This method processes the provided items, checks if they exist in the database,
     * and then saves new ones. It returns counts for newly added items and those
     * that already exist.
     *
     * @param array $items Array of items formatted for database insertion.
     * @return array Result containing the number of items inserted, updated, and a success message.
     * @throws Exception
     */
    public function addOrUpdateItems(array $items)
    {
        $newlyAddedCount = 0;
        $alreadyExistsCount = 0;

        $columnMapping = [
            "MMS Id" => "mms_id",
            "Title" => "title",
            "Holding Id" => "holding_id",
            "Library Name" => "library_name",
            "Barcode" => "barcode",
            "Creation Date" => "creation_date",
            "Description" => "description",
            "Lifecycle" => "lifecycle",
            "Physical Item Id" => "physical_item_id",
            "Receiving Date (Calendar)" => "receiving_date",
            "Replacement cost" => "replacement_cost",
            "Order Line Type" => "order_line_type",
            "PO Line Reference" => "po_line_reference",
            "Reporting Code - 1st" => "reporting_code_first",
            "Fund Expenditure" => "fund_expenditure",
            "Num of Items per Fund" => "num_of_items_per_fund"
        ];

        DB::beginTransaction();

        try {
            foreach ($items as $item) {
                $formattedItem = [];

                // Map columns to database fields
                foreach ($columnMapping as $column => $dbField) {
                    if (isset($item[$column])) {
                        $formattedItem[$dbField] = $item[$column];
                    }
                }

                // Set a default value for description if not present
                if (!isset($formattedItem['description'])) {
                    $formattedItem['description'] = ''; // Set your desired default value here
                }

                // Set the value for 'replacement_cost' using 'fund_expenditure' if it's set
                if (isset($formattedItem['fund_expenditure']) && !isset($formattedItem['replacement_cost'])) {
                    $formattedItem['replacement_cost'] = number_format($formattedItem['fund_expenditure'], 2, '.', '');
                }

                // Check if item with given barcode already exists in the database
                if (Item::where('barcode', $formattedItem['barcode'])->exists()) {
                    $alreadyExistsCount++;
                } else {
                    Item::create($formattedItem);
                    $newlyAddedCount++;
                }
            }

            DB::commit();

            Log::info($newlyAddedCount.' Items saved to the database. '.$alreadyExistsCount.' already exist.');

            // Flash the messages for user feedback
            session()->flash('success', $newlyAddedCount . ' Items saved to the database.');
            session()->flash('info', $alreadyExistsCount . ' Items already exist in the database.');

            return [
                'success' => true,
                'newlyAddedCount' => $newlyAddedCount,
                'alreadyExistsCount' => $alreadyExistsCount
            ];

        } catch (Exception $e) {
            DB::rollback();
            // Logging the error for debugging purposes
            Log::error("Error saving items to database: " . $e->getMessage());
            // Rethrow the exception to handle it in the calling function or display an error message to the user.
            throw $e;
        }
    }

    /**
     * Delete all items from the database.
     *
     * @return void
     * @throws Exception
     */
    public function deleteAllItems()
    {
        try {
            $deletedCount = Item::query()->delete();
            Log::info($deletedCount . ' items deleted from the database.');

        } catch (Exception $e) {
            // Logging the error for debugging purposes
            Log::error("Error deleting items from the database: " . $e->getMessage());
            // Rethrow the exception to handle it in the calling function or display an error message to the user.
            throw $e;
        }
    }

    /**
     * Fetch items based on their status.
     *
     * @param string $status The status of the items you want to fetch.
     * @param int $limit The maximum number of items you want to fetch. Default is 10.
     *
     * @return \Illuminate\Database\Eloquent\Collection Retrieved items.
     */
    public function fetchItemsByStatus(string $status, int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        return Item::where('status', $status)->limit($limit)->get();
    }
}
