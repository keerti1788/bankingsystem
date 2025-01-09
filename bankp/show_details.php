<?php
// Database connection details
$servername = "localhost"; // Replace with your database server
$username = "root";        // Replace with your database username
$password = "";            // Replace with your database password
$dbname = "bank1";         // Replace with your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get account ID from the URL (via GET)
if (isset($_GET['id'])) {
    $account_id = $_GET['id'];

    // Retrieve account details using the account ID
    $sql = "SELECT * FROM account2 WHERE account_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $account_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if account is found
    if ($result->num_rows > 0) {
        $account = $result->fetch_assoc();

        // If account number is not assigned, generate a new one
        if (empty($account['account_number'])) {
            // Generate a unique 12-digit account number
            do {
                $account_number = random_int(100000000000, 999999999999); // 12 digits
                $check_account_sql = "SELECT * FROM account2 WHERE account_number = ?";
                $stmt_check = $conn->prepare($check_account_sql);
                $stmt_check->bind_param("s", $account_number);
                $stmt_check->execute();
                $result_check = $stmt_check->get_result();
            } while ($result_check->num_rows > 0);

            $stmt_check->close();

            // Update the account with the new account number
            $update_sql = "UPDATE account2 SET account_number = ? WHERE account_id = ?";
            $stmt_update = $conn->prepare($update_sql);
            $stmt_update->bind_param("si", $account_number, $account_id);
            $stmt_update->execute();
            $stmt_update->close();

            // Update the account array to include the new account number
            $account['account_number'] = $account_number;
        }

        // Display account details
        echo "<h2>Account Details</h2>";
        echo "<p><strong>Name:</strong> " . $account['name'] . "</p>";
        echo "<p><strong>Email:</strong> " . $account['email'] . "</p>";
        echo "<p><strong>Aadhar Number:</strong> " . $account['adhar'] . "</p>";
        echo "<p><strong>Phone Number:</strong> " . $account['phone'] . "</p>";
        echo "<p><strong>Account Number:</strong> " . $account['account_number'] . "</p>";
        echo "<p><strong>Balance:</strong> " . $account['balance'] . "</p>";
        echo "<br>";
        echo '<a href="credit.php?id=' . $account_id . '">Credit Amount</a>';
        echo "<br>";
        echo "<br>";
        echo '<a href="debit.php?id=' . $account_id . '">debit Amount</a>';
    } else {
        echo "<p>No account found with the provided ID.</p>";
    }

    // Close the statement
    $stmt->close();
} else {
    echo "<p>No account ID provided.</p>";
}

// Close the connection
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
</head>
<body>
    
<form action="home.html" method="POST">
    <br>
        <button type="submit">HOME</button>
</form>
</body>
</html>