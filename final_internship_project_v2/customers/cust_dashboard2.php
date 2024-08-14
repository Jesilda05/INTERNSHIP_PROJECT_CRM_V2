<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard</title>
    <link rel="stylesheet" href="../assets/css/cust_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
    <?php
    // Database connection
    include '../mainconn/db_connect.php';

    // Fetch total counts
    $quotations_count = $conn->query("SELECT COUNT(*) AS total FROM quotations")->fetch_assoc()['total'];
    $tickets_count = $conn->query("SELECT COUNT(*) AS total FROM tickets")->fetch_assoc()['total'];
    $feedbacks_count = $conn->query("SELECT COUNT(*) AS total FROM feedback")->fetch_assoc()['total'];
    ?>
    <div class="container">
        <!-- main start -->
        <div class="main">
            <!-- top-bar start -->
            <div class="top-bar">
                <div class="user">
                    <img src="./asset/img/doctor-icon.png" alt="User Image">
                </div>
            </div>
            <!-- top-bar end -->

            <!-- card start -->
            <div class="cards">
                <div class="card">
                    <div class="card-content">
                        <?php
                        // Fetch total quotations
                        $quotationCount = $conn->query("SELECT COUNT(*) AS total FROM quotations")->fetch_assoc()['total'];
                        ?>
                        <div class="number"><?php echo $quotationCount; ?></div>
                        <div class="card-name">Total Quotations</div>
                    </div>
                    <div class="icon-box">
                        <i class="fas fa-file-alt"></i>
                    </div>
                </div>
                <div class="card">
                    <div class="card-content">
                        <?php
                        // Fetch total tickets
                        $ticketCount = $conn->query("SELECT COUNT(*) AS total FROM tickets")->fetch_assoc()['total'];
                        ?>
                        <div class="number"><?php echo $ticketCount; ?></div>
                        <div class="card-name">Total Tickets</div>
                    </div>
                    <div class="icon-box">
                        <i class="fas fa-ticket-alt"></i>
                    </div>
                </div>
                <div class="card">
                    <div class="card-content">
                        <?php
                        // Fetch total feedbacks
                        $feedbackCount = $conn->query("SELECT COUNT(*) AS total FROM feedback")->fetch_assoc()['total'];
                        ?>
                        <div class="number"><?php echo $feedbackCount; ?></div>
                        <div class="card-name">Total Feedbacks</div>
                    </div>
                    <div class="icon-box">
                        <i class="fas fa-comments"></i>
                    </div>
                </div>
            </div>
            <!-- card end -->

            <!-- sidebar start -->
            <div class="sidebar">
                <ul>
                    <li>
                        <a href="create_quotation2.php">
                            <i class="fas fa-plus"></i>
                            <div class="title">Create Quotation</div>
                        </a>
                    </li>
                    <li>
                        <a href="manage_quotations2.php">
                            <i class="fas fa-list"></i>
                            <div class="title">Manage Quotations</div>
                        </a>
                    </li>
                    <li>
                        <a href="create_ticket2.php">
                            <i class="fas fa-plus"></i>
                            <div class="title">Create Ticket</div>
                        </a>
                    </li>
                    <li>
                        <a href="manage_tickets2.php">
                            <i class="fas fa-list"></i>
                            <div class="title">Manage Tickets</div>
                        </a>
                    </li>
                    <li>
                        <a href="create_feedback2.php">
                            <i class="fas fa-plus"></i>
                            <div class="title">Create Feedback</div>
                        </a>
                    </li>
                    <li>
                        <a href="manage_feedback2.php">
                            <i class="fas fa-list"></i>
                            <div class="title">Manage Feedback</div>
                        </a>
                    </li>
                </ul>
            </div>
            <!-- sidebar end -->
        </div>
        <!-- main end-->
    </div>
    <!-- container end -->
</body>
</html>
