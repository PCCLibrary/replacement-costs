<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use SimpleXMLElement;

class ItemImportService
{
    private $operationType; // Property to store the operation type

    /**
     * Parses the XML response and extracts data into an array of associative arrays.
     *
     * @param string $xmlString The XML response string.
     * @param string $operationType The type of operation ("new" or "continue").
     * @return array An array of associative arrays containing the extracted data.
     * @throws Exception
     */
    public function processXmlData($xmlString, $operationType): array
    {
        $this->operationType = $operationType;
        $xml = new SimpleXMLElement($xmlString);

        // Set the namespaces for XPath queries
        $ns = $xml->getNamespaces(true);
        $xml->registerXPathNamespace('ns', $ns['']);

        // Extract and store the schema if it's not already set for a new operation
        if ($this->operationType === 'new') {
            $this->extractSchema($xml);
        }

        // Extract data using the stored schema
        $data = $this->extractData($xml);

        return $data;
    }

    /**
     * Extracts the schema from the XML response and stores it in the session.
     *
     * @param SimpleXMLElement $xml The XML response.
     */
    private function extractSchema(SimpleXMLElement $xml): void
    {
        $schema = [];
        foreach ($xml->QueryResult->ResultXml->rowset->children('xsd', true)->schema->complexType->sequence->element as $element) {
            $columnName = (string) $element->attributes()['name']; // Get the column name e.g., Column0
            $columnHeading = (string) $element->attributes('saw-sql', true)['columnHeading'];
            $schema[] = ['name' => $columnName, 'columnHeading' => $columnHeading];
        }

        // Store the schema in the session
        Session::put('schema', $schema);
    }

    /**
     * Extracts data from the XML response using the stored schema.
     *
     * @param SimpleXMLElement $xml The XML response.
     * @return array An array of associative arrays containing the extracted data.
     * @throws Exception
     */
    private function extractData(SimpleXMLElement $xml): array
    {
        // Retrieve the schema from the session
        $schema = Session::get('schema');

        if (!$schema) {
            throw new Exception('Schema is not set.');
        }

        $rowset = $xml->xpath('//ns:rowset/ns:Row');
        $data = [];
        foreach ($rowset as $row) {
            $rowData = [];
            foreach ($schema as $column) {
                $columnName = $column['name'];
                $columnHeading = $column['columnHeading'];
                $value = isset($row->$columnName) ? (string) $row->$columnName : null; // Default to null if missing
                $rowData[$columnHeading] = $value;
            }
            $data[] = $rowData;
            // Log the successful parsing of XML data
            Log::info('row data:' . json_encode($rowData));
        }
        return $data;
    }
}

