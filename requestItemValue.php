<?php
/**
 * @author Brendan Dileo
 */

// Receives input parameter via GET request, and validates the input.
$shop = filter_input(INPUT_GET, 'shop', FILTER_VALIDATE_INT);
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

// Checks if the shop value is false, null, less than 0, or larger than 2, which would mean the input was invalid or missing.
if ($shop === false || $shop === null || $shop < 0 || $shop > 2) {
    echo "Error: The shop provided was invalid!";
    exit;
}

// Checks if the id value is false, null, less than 1, or larger than 9, which would mean the input was invalid or missing.
if ($id === false || $id === null || $id < 1 || $id > 9) {
    echo "Error: The Item ID provided was invalid!";
    exit;
}


$file = "itemValues.csv"; // Name of the file.
if (file_exists($file)) { // Check that the file exists.
    $handle = fopen($file, "r"); // Opens the file in read only and stores the handle in the 'handle' variable.
    $prices = array(); // Declares an array which will hold each of the item prices.

    while (true) {
        // Retrieves next line/column of the file as a csv value and stores it as an array.
        $data = fgetcsv($handle); // References entire array.
        if ($data == false) { // Checks if there is no more data to read from.
            break;
        }
        
        // Adds each valid line of data from the CSV file to the prices array.
        $prices[] = $data; // Data represents an array of item values, so an array of item values is being added.
    }

    // Checks if there is a value at the shop index and id index in the prices array.
    if (isset($prices[$shop][$id - 1])) {
        $price = $prices[$shop][$id - 1]; // Assigns the value at the specified indexes to price.
        echo $price; // Echo's out the corresponding price.
    } else { // Executes if a value is not found at the corresponding indexes.
        echo "Error: Invalid shop or item ID.";
    }
} else { // Executes if the csv file does not exist.
    echo "Error: The file could not be found!";
}
?>

