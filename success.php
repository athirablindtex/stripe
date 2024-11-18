<?php
// Check if a payment was successful (you can adjust this according to your payment confirmation logic)
$paymentSuccess = true; // This will be dynamically set based on the payment outcome
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful</title>

    <!-- Bootstrap CSS CDN -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f6f9;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin-top: 50px;
            padding: 30px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .success-icon {
            font-size: 50px;
            color: #14438A;
            margin-bottom: 20px;
        }

        .header {
            font-size: 24px;
            color: #14438A;
            font-weight: 700;
            margin-bottom: 20px;
        }

        .message {
            font-size: 18px;
            margin-bottom: 30px;
            color: #333;
        }

        .amount {
            font-size: 20px;
            font-weight: 700;
            color: #14438A;
            margin-bottom: 30px;
        }

        .button {
            padding: 12px 24px;
            background-color: #14438A;
            color: white;
            font-size: 16px;
            font-weight: 500;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .button:hover {
            background-color: #218838;
        }

        /* Responsive Styling */
        @media (max-width: 576px) {
            .container {
                padding: 20px;
                margin-top: 20px;
            }

            .header {
                font-size: 20px;
            }

            .message {
                font-size: 16px;
            }

            .success-icon {
                font-size: 40px;
            }
        }
    </style>
</head>
<body>
<?php $paidAmount=$_GET['amount'];?>
    <div class="container">
        <div class="success-icon">&#10004;</div> <!-- Green check mark -->
        <div class="header">Payment Successful!</div>
        <div class="message">Your payment has been successfully processed.</div>
          <!-- Display Paid Amount in GBP -->
          <div class="amount">Amount Paid: £<?php echo number_format($paidAmount, 2); ?> GBP</div>
        
        <a href="index.php" class="button">Go to Homepage</a> <!-- Redirect to homepage or other page -->
    </div>

    <!-- Bootstrap JS, Popper.js, and jQuery (Optional for Modal/Popover/Collapse) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.7/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
