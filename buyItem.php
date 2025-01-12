<?php
/**
* @author Brendan Dileo
*/

// Data Retrieval

// Receives input parameters via GET request, and validates the input.
$shop = filter_input(INPUT_GET, 'shop', FILTER_VALIDATE_INT);
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$gold = filter_input(INPUT_GET, 'gold', FILTER_VALIDATE_INT);


// Checks if the shop value is false or null, which would mean the input was invalid or missing.
if ($shop === false || $shop === null) {
    echo json_encode(["error" => "The shop input provided was invalid!"]); // Echos array into JSON string for the error.
    exit;
}

// Checks if the id value is false or null, which would mean the input was invalid or missing.
if ($id === false || $id === null) {
    echo json_encode(["error" => "The id input provided was invalid!"]); // Echos array into JSON string for the error.
    exit;
}

// Checks if the gold value is false, null, or less than 0, which would mean the input was invalid or missing.
if ($gold === false || $gold === null || $gold < 0) {
    echo json_encode(["error" => "The gold input provided was invalid!"]); // Echos array into JSON string for the error.
    exit;
}

// Constructs the file name for the corresponding shop file based on the shop variable.
$file = "Shop" . ($shop + 1) . ".json"; // Adds 1 for 0 based index.

// Checks if the file does not exist.
if (!file_exists($file)) {
    echo json_encode(["error" => "Could not access the file!", "File: " => $file]); // Echos array into JSON string for the error.
    exit;
}

// Reads content of shop file converting the JSON string into a php associative array.
$shopData = json_decode(file_get_contents($file), true);

// Checks if the value is null, meaning the JSON could not be decoded.
if ($shopData === null) {
    echo json_encode(["error" => "The JSON could not be decoded!", "File" => $file]); // Echos array into JSON string to display error and cause.
    exit;
}

$index = -1; // Initializes index to -1 as array indices start at 0. Used to check if a item has been found.
foreach ($shopData as $key => $item) { // Loops through the shopData associative array accessing both the key and value in the array.
    if ($item['id'] == $id) { // Checks if the current items id is the same as the id of the item being purchased.
        $index = $key; // Store the position of the item that will be purchased.
        break;
    } 
}

// Checks if the item was not found.
if ($index == -1) {
    echo json_encode(["success" => false, "message" => "The item was not found!"]); // Echos array into JSON to display cause of error.
    exit;
}

// Buying Logic

$data = null;

// Checks if the player has enough gold to buy the item.
if ($gold >= $shopData[$index]['price']) {
    
    // If the user has enough gold, their purchase is reflected by decrementing players gold and shops quantity of the item.
    $gold = $gold - $shopData[$index]['price']; // Decrements users gold by price of the item.
    $shopData[$index]['quantity']--; // Decrements the quantity of the item in the shop by 1.

    // Initializes an associative array which holds the fields for the purchased item.
    $itemPurchased = [
        "id" => $shopData[$index]['id'],
        "name" => $shopData[$index]['name'],
        "description" => $shopData[$index]['description'],
        "quantity" => $shopData[$index]['quantity'], // Post purchase quantity.
    ];

    // Checks if the quantity of the item that was purchased has reached 0.
    if ($shopData[$index]['quantity'] <= 0) {
        unset($shopData[$index]); // If it has, the item is removed from the shop.
    }

    // Converts the shopData array into a JSON string, reindexing the array keys incase an item has been removed.
    $jsonData = json_encode(array_values($shopData), JSON_PRETTY_PRINT);
    file_put_contents($file, $jsonData); // Puts the JSON string contents back into the corresponding shops file.

    // Initializes an associative array which holds the fields of a successful purchase of an item from the corresponding shop.
    $data = [
        "success" => "true",
        "message" => "OH! Thank ya for this purchase!",
        "gold" => $gold,
        "item" => $itemPurchased, // Includes additional associative array containing the information about the item purchased.
        "debug" => "Item ID: {$id}, Name: {$itemPurchased['name']} was purchased successfully!" // Debug message as I was having issues with ID's.
    ];
} else { // Executes if the player does not have enough gold to buy the item.
    echo json_encode([ // Echos a JSON string converted from a php array indicating an unsuccessful purchase.
        "success" => "false",
        "message" => "Oy! Ya might be all out of coin there!",
        "gold" => $gold,
        "item" => null, // No was purchased.
        "debug" => "The user attempted to purchase Item ID: {$id}, Name: {$shopData[$index]['name']}, but the user has insufficient gold: {$gold}! The purchase was not successful!" // Debug message as I had issues with gold.
    ]);
    exit;
}

// Converts the associative array 'data' representing a successful purchase into a JSON string and outputs it.
echo json_encode($data);
?>
