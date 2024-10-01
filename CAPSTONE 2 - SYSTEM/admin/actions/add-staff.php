<?php
session_start();
include('../../db_conn.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../phpmailer/src/Exception.php';
require '../phpmailer/src/PHPMailer.php';
require '../phpmailer/src/SMTP.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = mysqli_real_escape_string($conn, $_POST['staffFirstName']);
    $middleName = mysqli_real_escape_string($conn, $_POST['staffMiddleName']);
    $lastName = mysqli_real_escape_string($conn, $_POST['staffLastName']);
    $email = mysqli_real_escape_string($conn, $_POST['staffEmail']);
    $role = mysqli_real_escape_string($conn, $_POST['staffRole']); // Retrieving the role from POST data
    $avatar = null;

    // Handle the avatar upload
    if (isset($_FILES['staffAvatar']) && $_FILES['staffAvatar']['error'] === UPLOAD_ERR_OK) {
        $avatarData = file_get_contents($_FILES['staffAvatar']['tmp_name']);
    }

    // Check if email exists in any of the tables
    $tables = ['admin_account', 'staff_account', 'student_account', 'teacher_account'];
    foreach ($tables as $table) {
        $query = "SELECT email FROM $table WHERE email = ?";
        if ($stmt = mysqli_prepare($conn, $query)) {
            mysqli_stmt_bind_param($stmt, 's', $email);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);
            if (mysqli_stmt_num_rows($stmt) > 0) {
                $_SESSION['message'] = 'Email already in use. Please use a different email.';
                $_SESSION['message_type'] = 'danger';
                header('Location: ../staff-list.php');
                exit;
            }
            mysqli_stmt_close($stmt);
        }
    }

    // Generate a random password
    $plainPassword = bin2hex(random_bytes(8));
    $hashedPassword = password_hash($plainPassword, PASSWORD_BCRYPT);

    // Prepare the SQL statement to insert the new staff data
    $query = "INSERT INTO staff_account (firstName, middleName, lastName, email, password, avatar, staff_role, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";

    if ($stmt = mysqli_prepare($conn, $query)) {
        // Bind the parameters, including 'b' for the binary avatar data and 's' for the staff_role
        mysqli_stmt_bind_param($stmt, 'ssssbss', $firstName, $middleName, $lastName, $email, $hashedPassword, $avatarData, $role);

        // Send the avatar data as a stream
        if (!empty($avatarData)) {
            mysqli_stmt_send_long_data($stmt, 5, $avatarData);
        }

        // Execute the query
        if (mysqli_stmt_execute($stmt)) {
            // Send the plain password to the user's email using PHPMailer
            $mail = new PHPMailer(true);
            try {
                //Server settings
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'maryjosetteacademy2004@gmail.com'; // Your Gmail email address
                $mail->Password = 'wzfg mllu gjzx gmuq'; // Gmail app password (app-specific password)
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                $mail->Port = 465;

                //Recipients
                $mail->setFrom('maryjosetteacademy2004@gmail.com', 'Evalu8 - Mary Josette Academy');
                $mail->addAddress($email); // Add a recipient

                //Content
                $mail->isHTML(true);
                $mail->Subject = 'Your Login Credentials';
                $mail->Body    = "Hello $firstName, <br>Your login credentials are:<br>Email: $email<br>Password: $plainPassword<br>Please change your password after logging in.";
                $mail->AltBody = "Hello $firstName, \nYour login credentials are:\nEmail: $email\nPassword: $plainPassword\nPlease change your password after logging in.";

                $mail->send();
                $_SESSION['message'] = 'New staff member added successfully and credentials sent to their email!';
                $_SESSION['message_type'] = 'success';
            } catch (Exception $e) {
                $_SESSION['message'] = "Mailer Error: " . $mail->ErrorInfo;
                $_SESSION['message_type'] = 'danger';
            }
        } else {
            $_SESSION['message'] = 'Error: ' . mysqli_stmt_error($stmt);
            $_SESSION['message_type'] = 'danger';
        }
        mysqli_stmt_close($stmt);
    } else {
        $_SESSION['message'] = 'Error preparing statement: ' . mysqli_error($conn);
        $_SESSION['message_type'] = 'danger';
    }

    header('Location: ../staff-list.php');
    exit;
} else {
    header('Location: ../staff-list.php');
    exit;
}
?>
