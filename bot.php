<?php
// Bot Token
$bot_token = "----";
// User Chat ID
$chat_id = "----";

// Database Connection
$servername = "----";
$username = "----";
$password = "----";
$dbname = "streamers";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

// Last sent data id
$last_sent_id = 0;

while (true) {
// Get the last sent data id from file
if(file_exists("last_sent_id.txt")) {
    $last_sent_id = (int)file_get_contents("last_sent_id.txt");
}


// SQL query
$sql = "SELECT id, nickname, count_streams, followers, link FROM submissions WHERE id > $last_sent_id";
$result = $conn->query($sql);

// Check if there are any results
if ($result->num_rows > 0) {
    // Fetch the data
    while($row = $result->fetch_assoc()) {
        $id = $row["id"];
        if($id <= $last_sent_id) {
            continue;
        }
        $nickname = $row["nickname"];
        $count_streams = $row["count_streams"];
        $followers = $row["followers"];
        $link = $row["link"];
        // Compose message
        $message = "Новая заявка на вступление%0ANickname: $nickname%0ACount Streams: $count_streams%0AFollowers: $followers%0ALink: $link";
        // Send message to Telegram
        file_get_contents("https://api.telegram.org/bot$bot_token/sendMessage?chat_id=$chat_id&text=$message");
        // Update the last sent data id
        $last_sent_id = $id;
        sleep(10);
         // Exit the loop
        break;
    }
    // Save the last sent data id to file
    file_put_contents("last_sent_id.txt", $last_sent_id);
    } 
// Wait for 5 minutes
    sleep(300);
}
$conn->close();
