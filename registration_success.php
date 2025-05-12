<?php
session_start();

if (!isset($_SESSION['registered_student'])) {
    header("Location: registration.php");
    exit();
}

$student = $_SESSION['registered_student'];
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تم التسجيل بنجاح</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }
        .success-container {
            max-width: 600px;
            margin: 100px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            text-align: center;
        }
        .success-icon {
            color: #4CAF50;
            font-size: 60px;
            margin-bottom: 20px;
        }
        .student-info {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            margin: 25px 0;
            text-align: right;
        }
        .student-info p {
            margin: 10px 0;
            font-size: 18px;
        }
        .btn {
            display: inline-block;
            background-color: #4CAF50;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
            font-size: 16px;
            transition: background-color 0.3s;
        }
        .btn:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="success-container">
        <div class="success-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        <h1>تم التسجيل بنجاح!</h1>
        <p>عزيزي/تي <?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></p>
        
        <div class="student-info">
            <h3>تفاصيل التسجيل:</h3>
            <p><strong>الاسم الكامل:</strong> <?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></p>
            <p><strong>الرقم الجامعي:</strong> <?php echo htmlspecialchars($student['student_id']); ?></p>
            <p><strong>البريد الإلكتروني:</strong> <?php echo htmlspecialchars($student['email']); ?></p>
            <p><strong>تاريخ التسجيل:</strong> <?php echo htmlspecialchars($student['registration_date']); ?></p>
        </div>
        
        <p>سيصلك رسالة تأكيد على بريدك الإلكتروني خلال 24 ساعة</p>
    </div>
</body>
</html>
<?php
unset($_SESSION['registered_student']);
?>