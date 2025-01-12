<?php
/**
 *
 * @author Brendan Dileo
 */

// Receives input parameter via GET request, and validates the input.
$shop = filter_input(INPUT_GET, 'shop', FILTER_VALIDATE_INT); // Number indicating which shop the client is requesting inventory for.

// Checks if the shop value is false, null, less than 0, or larger than 2, which would mean the input was invalid or missing.
if ($shop === null || $shop === false || $shop < 0 || $shop > 2) {
    echo json_encode(["error" => "Invalid shop number"]); // Echos array into JSON string for the error.
    exit;
}

// Constructs the file name for the corresponding shop file based on the shop variable.
$file = "Shop" . ($shop + 1) . ".json"; // Adds 1 for 0 based index.

// Checks that the file exists.
if (file_exists($file)) {
    $json = file_get_contents($file); // Retrieves the contents from the corresponding JSON shop file.
    
    if ($json !== false) { // Checks that the contents were retrieved from the file successfully.
        $data = json_decode($json, true); // Decodes the JSON string into a php array.
        echo json_encode($data); // Encodes the sanitized array back into a JSON string and echos it.
    } else { // Executes if the contents were not retrieved from JSON file.
        echo json_encode(["error" => "Could not read the contents of the JSON file"]); // Echos out an error message if contents not retrieved.
    }
} else { // Executes if the shop file does not exist.
    echo json_encode(["error" => "The JSON file for the shop does not exist!"]); // Echos out error message if shop file not found.
}
?>