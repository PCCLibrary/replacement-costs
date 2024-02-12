<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Log;
use SimpleXMLElement;

class ItemImportService
{

    /**
     * Parses the XML response and extracts data into an array of associative arrays.
     *
     * @param string $xmlString The XML response string.
     * @return array An array of associative arrays containing the extracted data.
     * @throws Exception
     */
    public function processXmlData($xmlString): array
    {
        $xml = new SimpleXMLElement($xmlString);

        // Set the namespaces for XPath queries
        $ns = $xml->getNamespaces(true);
        $xml->registerXPathNamespace('ns', $ns['']);

        // Get the rowset element
        $rowset = $xml->xpath('//ns:rowset/ns:Row');

        // Create a mapping of column names to their display names
        $columnMapping = [];
        foreach ($xml->QueryResult->ResultXml->rowset->children('xsd', true)->schema->complexType->sequence->element as $element) {
            $columnName = (string) $element->attributes()['name']; // Get the column name e.g., Column0
            $columnHeading = (string) $element->attributes('saw-sql', true)['columnHeading'];
            $columnMapping[$columnName] = $columnHeading; // Map column name to its display name
        }
        Log::debug('$columnMapping:', $columnMapping);

        // Output the data as an array with the column names as keys
        $data = [];
        foreach ($rowset as $row) {
            $rowData = [];
            foreach ($columnMapping as $columnName => $display) {
                $value = isset($row->$columnName) ? (string) $row->$columnName : null; // Default to null if missing
                $rowData[$display] = $value;
//                Log::debug("Column: $columnName, Display: $display, Value: $value");
            }
            $data[] = $rowData;
            // Log the successful parsing of XML data
            Log::info('row data:' . json_encode($rowData));
        }

        return $data;
    }


}
