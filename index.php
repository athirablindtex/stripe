<?php
session_start();
if(!isset($_SESSION['logged_in']) &&($_SESSION['logged_in']!==true))
{
header('Location:login.php');
}
// Check if the logout button was clicked
if (isset($_POST['logout'])) {
    // Destroy the session
    session_destroy();
    
    // Redirect to the login page
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bolton Blinds Payment</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://js.stripe.com/v3/"></script>
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script> -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">


    <style>
        /* Custom style for logout button */
        .logout-btn {
            position: absolute;
            top: 20px;
            right: 20px;
            background: #f8f9fa;
            border: 1px solid #ddd;
            padding: 10px 15px;
            border-radius: 5px;
            font-size: 16px;
            color: #007bff;
            cursor: pointer;
            transition: background 0.3s;
        }

        .logout-btn:hover {
            background: #007bff;
            color: white;
        }

        .logout-btn i {
            margin-right: 8px;
        }
    </style>
</head>
<body>

<div class="container d-flex justify-content-center align-items-center" style="height: 100vh;">
        <!-- Logout Button -->
        <form method="POST">
            <button type="submit" name="logout" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i> Logout
            </button>
        </form>
        <div class="payment-container">
        <div class="d-flex justify-content-center align-items-center" style="">
    <img src="logo.png" class="img-fluid rounded" alt="Description of Image" style="max-width: 200px;">
</div>

            <h2>Make Payment

            </h2>

            <form id="payment-form">
                <!-- Customer + Product Details Section -->
                <div class="section active" id="customer-product-details">
                    <!-- <h4>Customer & Product Details</h4> -->
                    <div class="row">
                        <div class="col-sm-6">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" id="name" class="form-control" name="name" placeholder="Enter customer name" required>
                        </div>
                        <div class="col-sm-6">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" id="email" class="form-control" name="email" placeholder="customer@example.com" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-6">
                            <label for="phone" class="form-label">Description</label>
                            <input type="text" id="phone" class="form-control" placeholder="" name="phone" >
                        </div>
                        <div class="col-sm-6">
                            <label for="amount" class="form-label">Amount(&pound; )</label>
                            <input type="text" id="amount" class="form-control" placeholder="Enter amount" name="amount" required>
                        </div>
                    </div>

                    <!-- Card Element will be inserted here -->
                    <div id="card-element"></div>
                </div>

                <!-- Pay Now Button -->
                <button type="submit" id="payNowBtn">Pay Now</button>
            </form>

            <div id="payment-message"></div> <!-- Message container for status -->
        </div>
    </div>


    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>

    <script>
        // Initialize Stripe
        const stripe = Stripe('pk_test_51QIr9nExmAZQgYMTlfYlUMHoZmZrvyFRztdWOTW5N7kakPOjhixybdAVMCBtRqflBLrKiSWw9pAk4M7kCx3n9seE00QZ478zxH'); 
       // pk_test_51QIr9nExmAZQgYMTlfYlUMHoZmZrvyFRztdWOTW5N7kakPOjhixybdAVMCBtRqflBLrKiSWw9pAk4M7kCx3n9seE00QZ478zxH // test public key
      //sk_test_51QIr9nExmAZQgYMTsXsrgQ4VQKCFelST6QiSM0YhqlokIABT0oDNmHrXThfDFHbV2kpCxw1zMxDz9UNk7w1IP6kh00Ch1mtalk //secret key test
        const elements = stripe.elements();
        const card = elements.create('card');
        card.mount('#card-element'); // Mount the card element on the form

        $(document).ready(function() {
            $('#payment-form').submit(function(event) {
                event.preventDefault();

                const name = $('#name').val();
                const email = $('#email').val();
                const amount = parseFloat($('#amount').val()); // 
                const phone = $('#phone').val();
             
                if (isNaN(amount) || amount < 0.5) 
            {
         
              
                $('#payment-message').text('Amount Should be or Greater than or Equal to .5 ');

            }
            else {
             
                // Call the server to create a PaymentIntent and get the client secret
                $.ajax({
                    url: 'http://162.214.72.98/~developmenttes/stripe/process_payment.php', // Replace with your server-side script to create PaymentIntent
                    method: 'POST',
                    data: {
                        amount: amount,
                        name:name,
                        email:email,
                        phone:phone // Send the amount to the server
                    },
                    success: function(response) {
                      
                        let client_secret=response.clientSecret;
                        if (client_secret) {
                            // Confirm the payment with Stripe
                            stripe.confirmCardPayment(client_secret, {
                                payment_method: {
                                    card: card,
                                    billing_details: {
                                        name: name,
                                        email: email
                                        
                                    }
                                }
                            }).then(function(result) {
                                if (result.error) {
                                    // Show error message to your customer
                                    $('#payment-message').text(result.error.message);
                                } else {
                                    // Payment successful
                                    if (result.paymentIntent.status === 'succeeded') {
                                        window.location.href = 'https://payment.boltonblinds.co.uk/success.php?amount='+amount;
                                        
                                    }
                                }
                            });
                        } else {
                            $('#payment-message').text('Error creating payment intent');
                        }
                    },
                    error: function(xhr, status, error) {
                        $('#payment-message').text(`An error occurred: ${error}`);
                    }
                });
            }
            });
        });
    </script>

</body>
</html>
