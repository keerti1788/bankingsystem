<?php
// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bank1";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// If the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form input values
    $account_number= $_POST['account_number'];
    $amount = $_POST['amount'];

    // Check if amount is valid
    if ($amount <= 0) {
        echo "<p>Please enter a valid amount greater than zero.</p>";
    } else {
        // Retrieve account details based on account_id
        $sql = "SELECT * FROM account2 WHERE account_number= ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $account_number);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $account = $result->fetch_assoc();

            // Add the amount to the existing balance
            $new_balance = $account['balance'] + $amount;

            // Update the balance in the database
            $update_sql = "UPDATE account2 SET balance = ? WHERE account_number = ?";
            $stmt_update = $conn->prepare($update_sql);
            $stmt_update->bind_param("di", $new_balance, $account_number);

            if ($stmt_update->execute()) {
                echo "<p>Amount credited successfully! New balance: " . $new_balance . "</p>";
            } else {
                echo "<p>Error: Could not credit the amount.</p>";
            }

            $stmt_update->close();
        } else {
            echo "<p>No account found with the provided account number.</p>";
        }

        $stmt->close();
    }
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Credit Amount</title>
</head>
<body>
    <h2>Credit Amount to Account</h2>
    <form action="credit.php" method="POST">
        <label for="account_number">Account number:</label>
        <input type="number" id="account_number" name="account_number" required><br><br>

        <label for="amount">Amount to Credit:</label>
        <input type="number" id="amount" name="amount" required><br><br>

        <button type="submit">Credit Amount</button>
    </form>
    <form action="home.html" method="POST">
    <br>
        <button type="submit">HOME</button>
</form>
</body>
</html>
