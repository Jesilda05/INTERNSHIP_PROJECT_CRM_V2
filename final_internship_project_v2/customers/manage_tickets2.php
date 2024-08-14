<?php
session_start();
include('../mainconn/db_connect.php');
include('../mainconn/authentication.php');

// Checking for user authentication
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Customer') {
    header('Location: ../login.php');
    exit();
}

// Get customer ID and cast it to int
$cust_id = (int)$_SESSION['user_id'];

// Handle category filter
if (isset($_POST['category'])) {
    $cat = filter_var($_POST['category'], FILTER_SANITIZE_STRING);
} else {
    $cat = 'all';
}

// Prepare SQL query based on the selected category
if ($cat === 'all') {
    $sql = "SELECT * FROM tickets WHERE customer_id = ? ORDER BY created_at DESC";
    $prestmt = $conn->prepare($sql);
    $prestmt->bind_param('i', $cust_id);
} else {
    $sql = "SELECT * FROM tickets WHERE customer_id = ? AND category = ? ORDER BY created_at DESC";
    $prestmt = $conn->prepare($sql);
    $prestmt->bind_param('is', $cust_id, $cat);
}

// Execute the statement
$prestmt->execute();
$res = $prestmt->get_result();

// Error handling
if ($conn->error) {
    echo "SORRY! We couldn't retrieve your data due to the following error.";
    error_log($conn->error);
}

?>

<?php include('header2.php'); ?>

<h2><b>Manage Quotations</b></h2>

<form method="post" action="">
    <strong>Select Category:</strong>
    <select name="category" id="category">
        <option value="all" <?php echo ($cat === 'all') ? 'selected' : ''; ?>>All</option>
        <option value="Fiction" <?php echo ($cat === 'Fiction') ? 'selected' : ''; ?>>Fiction</option>
        <option value="Non-Fiction" <?php echo ($cat === 'Non-Fiction') ? 'selected' : ''; ?>>Non-Fiction</option>
    </select>
    <input type="submit" value="Filter">
</form>

<table border="1">
    <thead>
        <tr>
            <th><strong>Category</strong></th>
            <th><strong>Subject</strong></th>
            <th><strong>Description</strong></th>

            <th><strong>Created At</strong></th>
            <th><strong>Actions</strong></th>
        </tr>
    </thead>
    <tbody>
        <?php if ($res->num_rows > 0): ?>
            <?php while ($row = $res->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['category']); ?></td>
                    <td><?php echo htmlspecialchars($row['subject']); ?></td>
                    <td><?php echo htmlspecialchars($row['description']); ?></td>
                    <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                    <td>
                        <a href="edit_ticket2.php?id=<?php echo $row['id']; ?>">Edit</a> |
                        <a href="delete_ticket2.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this quotation?');">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="4">NO tickets FOUND IN THE TABLE.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<?php include('footer.php'); ?>

<?php
$res->close();
$prestmt->close();
$conn->close();
?>
