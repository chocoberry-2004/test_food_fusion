<?php
if (!isset($_SESSION)) {
    session_start();
}
include("./includes/db_connect.php");

$form_response = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);

    if (!empty($name) && !empty($email) && !empty($message)) {
        $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $subject, $message);

        if ($stmt->execute()) {
            $form_response = "Thank you! Your message has been submitted successfully.";
        } else {
            $form_response = "Failed to submit your message. Please try again.";
        }

        $stmt->close();
    } else {
        $form_response = "Please fill in all required fields!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Contact Us - FoodFusion</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./assets/css/style.css">
    <link rel="stylesheet" href="./assets/css/contact.css">
</head>

<body class="contact-page">
    <?php include("./includes/header.php"); ?>

    <!-- Hero Section -->
    <section class="contact-hero">
        <div class="overlay">
            <h1>Contact <span class="brand-name">FoodFusion</span></h1>
            <p>We’d love to hear from you!</p>
        </div>
    </section>

    <!-- Contact Form Section -->
    <section class="contact-form-section">
        <div class="container">
            <h2>Get in Touch</h2>
            <p>Have a question, feedback, or recipe request? Send us a message!</p>

            <form id="contactForm" method="POST" class="contact-form">
                <div class="form-group">
                    <label for="name">Your Name *</label>
                    <input type="text" id="name" name="name" placeholder="Enter your name" required>
                </div>

                <div class="form-group">
                    <label for="email">Your Email *</label>
                    <input type="email" id="email" name="email" placeholder="Enter your email" required>
                </div>

                <div class="form-group">
                    <label for="subject">Subject</label>
                    <input type="text" id="subject" name="subject" placeholder="Subject">
                </div>

                <div class="form-group">
                    <label for="message">Message *</label>
                    <textarea id="message" name="message" placeholder="Write your message..." required></textarea>
                </div>

                <button type="submit" class="btn-submit">Send Message</button>
            </form>
        </div>
    </section>
    <script src="./assets/js/hamburger_menu.js"></script>
    <script src="./assets/js/cookie.js"></script>
    <?php include("./includes/footer.php"); ?>
    <?php include("./includes/cookie_consent.php"); ?>

    <!-- Dialog Alert for Form Submission -->
    <?php if (!empty($form_response)): ?>
        <script>
            alert("<?php echo addslashes($form_response); ?>");
        </script>
    <?php endif; ?>
</body>

</html>