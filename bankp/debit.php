<?php
// Connect to the database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bank1";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $account_number = $_POST['account_number'];
    $amount_to_debit = $_POST['amount'];

    // Validate input
    if (empty($account_number) || empty($amount_to_debit)) {
        echo "Account number and amount are required.";
    } else if (!is_numeric($amount_to_debit) || $amount_to_debit <= 0) {
        echo "Please enter a valid amount.";
    } else {
        // Fetch the current balance
        $sql = "SELECT balance FROM account2 WHERE account_number = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $account_number);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $current_balance = $row['balance'];

            if ($current_balance >= $amount_to_debit) {
                // Update the balance
                $new_balance = $current_balance - $amount_to_debit;
                $sql = "UPDATE account2 SET balance = ? WHERE account_number = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ds", $new_balance, $account_number);
                if ($stmt->execute()) {
                    echo "Amount debited successfully. New balance: " . $new_balance;
                } else {
                    echo "Error updating record: " . $conn->error;
                }
            } else {
                echo "Insufficient balance.";
            }
        } else {
            echo "Account not found.";
        }
        $stmt->close();
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Debit Amount</title>
</head>
<body>
    <h2>Debit Amount</h2>
    <form method="post" action="debit.php">
        <label for="account_number">Account Number:</label>
        <input type="text" id="account_number" name="account_number"><br><br>
        <label for="amount">Amount to Debit:</label>
        <input type="text" id="amount" name="amount"><br><br>
        <input type="submit" value="Submit">
    </form>
    <form action="home.html" method="POST">
    <br>
        <button type="submit">HOME</button>
</form>
</body>
</html>
