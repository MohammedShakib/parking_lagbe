<?php
// Start session to manage user registration data
session_start();

// Include database connection file
include 'db_config.php';

// Initialize variables for error handling
$error_message = "";
$registration_success = false;

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $user_id = $_POST['user_id'];
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];
    
    // Basic validation
    $is_valid = true;
    
    // Check if user_id already exists
    $check_user = "SELECT * FROM users WHERE user_id = '$user_id'";
    $result = $conn->query($check_user);
    if ($result->num_rows > 0) {
        $error_message = "User ID already exists. Please choose a different one.";
        $is_valid = false;
    }
    
    // Check if email already exists
    $check_email = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($check_email);
    if ($result->num_rows > 0) {
        $error_message = "Email already registered. Please use a different email.";
        $is_valid = false;
    }
    
    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Invalid email format.";
        $is_valid = false;
    }
    
    // Validate password strength (at least 8 characters)
    if (strlen($password) < 8) {
        $error_message = "Password must be at least 8 characters long.";
        $is_valid = false;
    }
    
    // If validation passes, proceed with registration
    if ($is_valid) {
        // Sanitize inputs to avoid SQL injection
        $user_id = mysqli_real_escape_string($conn, $user_id);
        $full_name = mysqli_real_escape_string($conn, $full_name);
        $email = mysqli_real_escape_string($conn, $email);
        $phone = mysqli_real_escape_string($conn, $phone);
        
        // Encrypt password for security
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        
        // Insert registration details into the database
        $sql = "INSERT INTO users (user_id, full_name, email, phone, password)
                VALUES ('$user_id', '$full_name', '$email', '$phone', '$password_hash')";
        
        if ($conn->query($sql) === TRUE) {
            // Set session variables after successful registration
            $_SESSION['user_id'] = $user_id;
            $_SESSION['full_name'] = $full_name;
            $registration_success = true;
            
            // Redirect to login page after successful registration
            header("Location: login.php");
            exit();
        } else {
            $error_message = "Registration failed: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Parking Lagbe - Registration</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex justify-center items-center min-h-screen p-4">
  <!-- Registration Form -->
  <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md">
    <div class="text-center mb-4">
      <!-- Logo Display -->
      <img src="img/parking_lagbe_logo-removebg.png" alt="Parking Lagbe Logo" class="w-48 h-auto mx-auto mb-4">
      <h2 class="text-2xl font-bold text-blue-600">Create Your Parking Lagbe Account</h2>
    </div>
    
    <?php if ($registration_success): ?>
      <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        Registration successful! Redirecting to login page...
      </div>
    <?php endif; ?>
    
    <?php if ($error_message): ?>
      <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        <?php echo $error_message; ?>
      </div>
    <?php endif; ?>
    
    <!-- Registration Form -->
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" class="space-y-4">
      <!-- User ID -->
      <div>
        <label for="user-id" class="block text-gray-700">User ID</label>
        <input type="text" id="user-id" name="user_id" placeholder="Enter User ID" class="w-full p-3 border border-gray-300 rounded-lg" required value="<?php echo isset($_POST['user_id']) ? htmlspecialchars($_POST['user_id']) : ''; ?>">
      </div>
      
      <!-- Full Name -->
      <div>
        <label for="full-name" class="block text-gray-700">Full Name</label>
        <input type="text" id="full-name" name="full_name" placeholder="Enter Full Name" class="w-full p-3 border border-gray-300 rounded-lg" required value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>">
      </div>
      
      <!-- Email -->
      <div>
        <label for="email" class="block text-gray-700">Email</label>
        <input type="email" id="email" name="email" placeholder="Enter Email" class="w-full p-3 border border-gray-300 rounded-lg" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
      </div>
      
      <!-- Phone -->
      <div>
        <label for="phone" class="block text-gray-700">Phone Number</label>
        <input type="tel" id="phone" name="phone" placeholder="Enter Phone Number" class="w-full p-3 border border-gray-300 rounded-lg" required value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
      </div>
      
      <!-- Password -->
      <div>
        <label for="password" class="block text-gray-700">Password</label>
        <input type="password" id="password" name="password" placeholder="Enter Password" class="w-full p-3 border border-gray-300 rounded-lg" required>
        <p class="text-sm text-gray-500 mt-1">Password must be at least 8 characters long</p>
      </div>
      
      <button type="submit" class="w-full py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition duration-200">Register</button>
      
      <div class="text-center text-sm">
        Already have an account? <a href="login.php" class="text-blue-600 hover:underline">Login here</a>
      </div>
    </form>
  </div>
  
  <!-- Optional: Add JavaScript for client-side validation -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const form = document.querySelector('form');
      const password = document.getElementById('password');
      
      form.addEventListener('submit', function(event) {
        let valid = true;
        
        // Password validation
        if (password.value.length < 8) {
          alert('Password must be at least 8 characters long');
          valid = false;
        }
        
        if (!valid) {
          event.preventDefault();
        }
      });
    });
  </script>
</body>
</html>