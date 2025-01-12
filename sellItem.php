<?php
/**
*
* @author Brendan Dileo
*
*/

// Data Retrieval

// Received input parameters via GET request, validated input.
$shop = filter_input(INPUT_POST, 'shop', FILTER_VALIDATE_INT);
$item = filter_input(INPUT_POST, 'item', FILTER_DEFAULT); // May not be best practice, but wasnt sure what was required as I could not see rubric.
$gold = (int)filter_input(INPUT_POST, 'gold', FILTER_VALIDATE_INT);

// Checks if the shop value is false or null, which would mean the input was invalid or missing.
if ($shop === false || $shop === null) {
    echo json_encode(["error" => "The shop input provided was invalid!"]); // Echos array into JSON string for the error.
    exit;
}

// Checks if the item value is false or null, which would mean the input was invalid or missing.
if ($item === false || $item === null) {
    echo json_encode(["error" => "The item input provided was invalid!"]); // Echos array into JSON string for the error.
    exit;
}

// Checks if the gold value is false, null, or less than 0, which would mean the input was invalid or missing.
if ($gold === false || $gold === null || $gold < 0) {
    echo json_encode(["error" => "The gold input provided was invalid!"]); // Echos array into JSON string for the error.
    exit;
}

// Constructs the file name for the corresponding shop file based on the shop variable.
$file = "Shop" . ($shop + 1) . ".json"; // Adds 1 for 0 based index.

if (!file_exists($file)) { // Checks if the shop file does not exist.
    echo json_encode(["error" => "Cannot access file!", "File:"  => $file]); // Echos array into JSON string for the error.
    exit;
}

// Gets content of shop file converting the JSON string into a php associative array.
$shopData = json_decode(file_get_contents($file), true);

// Checks if the value is null, meaning the JSON could not be decoded.
if ($shopData === null) {
    echo json_encode(["error" => "The JSON could not be decoded!", "File: " => $file]);
    exit;
}

// Decodes the JSON string representing the item the user wants to sell into a php associative array.
$items = json_decode($item, true);

// Checks if the value is null, meaning the JSON could not be decoded.
if ($items === null) {
    echo json_encode(["error" => "The item input provided was invalid!"]); // Echos array into JSON string for the error.
    exit;
}


$index = -1; // Initializes index to -1 as array indices start at 0. Used to check if a item has been found in the shop.
foreach ($shopData as $key => $shopItem) { // Loops through the shopData array accessing both the arrays key and value.
    if ($shopItem['id'] == $items['id']) { // Checks if the id of the item being sold is the same as the id of the item in the shop.
        $index = $key; // Stores the index/position of the item in the shop that matches the id of the item being sold.
        break;
    }
}

if ($items['quantity'] > 0) { // Checks if the user has enough of the item to sell.
    if ($index == -1) { // Checks if the item was not found. If its not, it is added to reflect it being sold to a new shop.
    // Declares a associative array containing the fields of the item being sold.
    $shopData[] = [ // Adds this new array to the shopData array containing all of the shop data.
            "id" => $items['id'],
            "name" => $items['name'],
            "description" => $items['description'],
            "price" => (int)$items['price'] * 2, // Since the item is new, price is double what the item was just sold for. 
            "quantity" => 1 // New items have an initial quantity of 1.
        ];
        $index = count($shopData) - 1; // Assigns the index to the last index in the shopData array.
    } else { // Executes if the item is already in the shop.
        $shopData[$index]['quantity']++; // Increments the number of the items in the shop by 1 to reflect the item being sold.
    }

    $items['quantity']--; // Decrements the number of items the player has by 1, to reflect the fact it was sold.
    $gold += (int)$items['price']; // Increments the players gold by the price the item was sold for.

    $JSONData = json_encode($shopData, JSON_PRETTY_PRINT); // Converts the shopData array into a JSON string.
    file_put_contents($file, $JSONData); // Puts the JSON string back into the corresponding shop file to reflect an item being sold.

    // Initializes an associative array to hold the response fields indicating the player has successfully sold an item.
    $data = [
        "success" => "true",
        "message" => "Oh ya! I'll take those off your hands all day!",
        "gold" => $gold,
        "debug" => "Item: ID: {$items['id']}, Name: {$items['name']} was successfully sold for {$items['price']}!" // Debug message, had issues with starting quantity at 1.
    ];
} else { // Executes if the player does not have enough of the item to sell.
    // Initializes an associative array to hold the response fields indicating the player does not have enough of the item to sell.
    $data = [
        "success" => "false",
        "message" => "Seems like ya don't have enough of those!",
        "gold" => $gold,
        "debug" => "Item: ID: {$items['id']}, Name: {$items['name']} could not be sold for {$items['price']}!"
    ];
}

echo json_encode($data);
?>
