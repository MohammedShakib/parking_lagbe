<?php
// Start the session
session_start();

// Check if the user is already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: home.php"); // Redirect to home page if already logged in
    exit();
}

// Dummy credentials for example
$valid_user = 'admin';
$valid_password = 'password';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the user input from the form
    $user_id = $_POST['user_id'];
    $password = $_POST['password'];

    // Validate user credentials (for demonstration purposes)
    if ($user_id === $valid_user && $password === $valid_password) {
        // Store user data in session
        $_SESSION['user_id'] = $user_id;
        header("Location: home.php"); // Redirect to home page on successful login
        exit();
    } else {
        $error_message = "Invalid User ID or Password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Parking Lagbe - Login</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex justify-center items-center h-screen">

  <!-- Login Form -->
  <div class="bg-white p-6 rounded-lg shadow-lg w-96">
    <div class="text-center mb-4">
      <img src="img/parking_lagbe_logo-removebg.png" alt="Parking Lagbe Logo" class="w-24 h-auto mx-auto mb-4">
      <h2 class="text-2xl font-bold text-blue-600">Online Parking Login</h2>
    </div>

    <!-- Form -->
    <form action="login.php" method="POST" class="space-y-4">
      <div>
        <label for="user-id" class="block text-gray-700">User ID</label>
        <input type="text" id="user-id" name="user_id" placeholder="Enter User ID" class="w-full p-3 border border-gray-300 rounded-lg" required>
      </div>
      
      <div>
        <label for="password" class="block text-gray-700">Password</label>
        <input type="password" id="password" name="password" placeholder="Enter Password" class="w-full p-3 border border-gray-300 rounded-lg" required>
      </div>

      <!-- Display error message -->
      <?php if (isset($error_message)): ?>
        <div class="text-red-500 text-sm text-center"><?= $error_message ?></div>
      <?php endif; ?>

      <button type="submit" class="w-full py-3 bg-red-500 text-white font-semibold rounded-lg hover:bg-red-600">Login</button>
    </form>

    <!-- Links -->
    <div class="mt-4 text-center">
      <a href="reg_from.php" class="text-blue-500 text-sm">Don’t have an account? Open now</a>
    </div>
  </div>

</body>
</html>
