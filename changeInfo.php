<?php
session_start();

require 'includes/database-connection.php';

if(!isset($_SESSION['fname'])){
    header("Location: login.php");
    exit();
}

if(isset($_SESSION['custID'])){
    $custID = $_SESSION['custID'];
}

function getCustomerInfo($custID, $pdo) {
    try {
        $sql = "SELECT cust_info_table.*, cust_cards_table.card_number 
                FROM cust_info_table 
                LEFT JOIN cust_cards_table ON cust_info_table.custID = cust_cards_table.custID
                WHERE cust_info_table.custID = :custID";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['custID' => $custID]);
        return $stmt->fetch(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}

function updateCardNumber($custID, $cardNumber, $pdo) {
    try {
        $sql = "UPDATE cust_cards_table SET card_number = :card_number WHERE custID = :custID";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['custID' => $custID, 'card_number' => $cardNumber]);
        return $stmt->rowCount();

    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['updateCard'])) {
    $custID = $_POST['custID'];
    $cardNumber = $_POST['card_number'];

    $updateCount = updateCardNumber($custID, $cardNumber, $pdo);
    if ($updateCount > 0) {
        echo "<p>Card number updated successfully.</p>";
    } else {
        echo "<p>Update failed or no changes made.</p>";
    }
}

/*
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['fetchInfo'])) {
    // Handle fetch
    $custID = $_POST['custID'];
    $customerInfo = getCustomerInfo($custID, $pdo);
} */

$customerInfo = getCustomerInfo($custID, $pdo);

?>


<!DOCTYPE>
<html>
<head>
<meta charset="UTF-8">
  		<meta name="viewport" content="width=device-width, initial-scale=1.0">
  		<title>Customer Information</title>
  		<link rel="stylesheet" href="./style.css">
  		<link rel="preconnect" href="https://fonts.googleapis.com">
		<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
		<link href="https://fonts.googleapis.com/css2?family=Lilita+One&display=swap" rel="stylesheet">
</head>
    <body>
        <h1>Customer Information</h1>
        <div class="body">
            <p><strong>First Name:</strong> <?= $customerInfo['fname'] ?></p>
			<p><strong>Last Name:</strong> <?= $customerInfo['lname'] ?></p>
			<p><strong>Customer ID:</strong> <?= $customerInfo['custID'] ?></p>
			<p><strong>Join Date:</strong> <?= $customerInfo['join_date'] ?></p>
    </div>
         <?php if (isset($_SESSION['fname'])): ?>
            <a href="logout.php" class=submit >Log Out</a>
         <?php endif; ?>

        <?php if (isset($customerInfo) && $customerInfo): ?>
           <form action="" method="post">
            <input type="hidden" name="custID" value="<?php echo htmlspecialchars($customerInfo['custID']); ?>">
            <input type="text" name="fname" value="<?php echo htmlspecialchars($customerInfo['fname']); ?>" placeholder="First Name">
            <input type="text" name="lname" value="<?php echo htmlspecialchars($customerInfo['lname']); ?>" placeholder="Last Name">
            <input type="text" name="card_number" value="<?php echo htmlspecialchars($customerInfo['card_number'] ?? ''); ?>" placeholder="Card Number">
            <input type="submit" name="updateInfo" value="Update Information">
            <input type="submit" name="updateCard" value="Update Card Number">
           </form>
        <?php endif; ?>
        ?>
     </body>

</html>