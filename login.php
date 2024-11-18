<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">

    <style>
        .payment-container {
            background-color: #14438A;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            color:#fff;
            max-width: 600px;
        }
        .error-message {
            color: red;
            margin-top: 10px;
        }
    </style>
</head>
<body>

<div class="container d-flex justify-content-center align-items-center" style="height: 100vh;">
    <div class="payment-container">
        <div class="d-flex justify-content-center align-items-center">
            <img src="logo.png" class="img-fluid rounded" alt="Description of Image" style="max-width: 200px;">
        </div>

        <h2>Login</h2>

        <form id="login-form">
            <!-- Username and Password Section -->
            <div class="section active" id="login-details">
                <div class="row">
                    <div class="col-sm-6">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" id="username" class="form-control" name="username" placeholder="Enter username" required>
                    </div>
                    <div class="col-sm-6">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" id="password" class="form-control" name="password" placeholder="Enter password" required>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit" id="login" class="btn btn-primary btn-block" style="margin-top:20px">Login</button>
        </form>

        <!-- Error Message Display -->
        <div id="error-message" class="error-message"></div>
    </div>
</div>

<!-- Bootstrap JS and dependencies -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>

<script>
   $(document).ready(function() {
    $('#login-form').submit(function(event) {
        event.preventDefault();  // Prevent form submission

        const username = $('#username').val();
        const password = $('#password').val();

        // Basic validation
        if (username === "" || password === "") {
            $('#error-message').text("Both fields are required.");
            return;
        }

        // Send the data via AJAX
        $.ajax({
            url: 'check_login.php',
            method: 'POST',
            data: {
                username: username,
                password: password
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    // Redirect to the homepage if login is successful
                    document.location.href = "index.php";

                } else {
                    // Show the error message from the response
                    $('#error-message').text(response.message);
                }
            },
            error: function(xhr, status, error) {
                $('#error-message').text('An error occurred. Please try again.');
            }
        });
    });
});


</script>

</body>
</html>
