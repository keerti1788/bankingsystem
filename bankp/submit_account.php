<?php
// Database connection details
$servername = "localhost"; // Replace with your database server
$username = "root";        // Replace with your database username
$password = "";            // Replace with your database password
$dbname = "bank1";         // Replace with your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
$account_id = $stmt->insert_id;
// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $name = $_POST['name'];
    $email = $_POST['email'];
    $adhar = $_POST['adhar'];
    $phone = $_POST['phone'];

    // Validate Aadhar and phone number
    if (!preg_match("/^\d{12}$/", $adhar)) {
        echo "<h2>Invalid Aadhar Number</h2>";
        echo "<p>Aadhar number must be a 12-digit numeric value. Please try again.</p>";
    } elseif (!preg_match("/^\d{10}$/", $phone)) {
        echo "<h2>Invalid Phone Number</h2>";
        echo "<p>Phone number must be a 10-digit numeric value. Please try again.</p>";
    } else {
        // Check if account with the same email or aadhar already exists
        $check_sql = "SELECT * FROM account2 WHERE email = ? OR adhar = ?";
        $stmt_check = $conn->prepare($check_sql);
        $stmt_check->bind_param("ss", $email, $adhar);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();

        // If a matching record is found, notify the user
        if ($result_check->num_rows > 0) {
            echo "<h2>Account already exists!</h2>";
            echo "<p>An account with the same email or Aadhar number already exists. Please try again with different details.</p>";
            echo '<form action="show_details.php" method="GET">';
            echo '<input type="hidden" name="id" value="' . $account_id . '">';
            echo '<button type="submit">Show Account Details</button>';
            echo '</form>';
        } else {
            // Prepare and bind the SQL statement to insert data
            $stmt = $conn->prepare("INSERT INTO account2 (name, email, adhar, phone) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $name, $email, $adhar, $phone);

            // Execute the statement and check if it was successful
            if ($stmt->execute()) {
                // Get the inserted account's ID (or use email/adhar as a unique identifier)
               

                echo "<h2>Account successfully created!</h2>";
                echo "<p>Thank you for opening an account with us.</p>";

                // Show the "Show Account Details" button
                echo '<form action="show_details.php" method="GET">';
                echo '<input type="hidden" name="id" value="' . $account_id . '">';
                echo '<button type="submit">Show Account Details</button>';
                echo '</form>';
            } else {
                echo "<h2>Error occurred!</h2>";
                echo "<p>There was an issue with submitting your data. Please try again later.</p>";
            }

            // Close the insert statement
            $stmt->close();
        }

        // Close the check statement
        $stmt_check->close();
    }

    // Close the connection
    $conn->close();
} else {
    echo "<h2>Error</h2>";
    echo "<p>No data was received.</p>";
}
?>
