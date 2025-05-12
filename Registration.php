<?php
session_start();


$conn = new mysqli("localhost", "root", "", "registration");
if ($conn->connect_error) {
    die("فشل الاتصال بقاعدة البيانات: " . $conn->connect_error);
}

$create_table = "CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    user_type VARCHAR(50) NOT NULL, 
    student_id VARCHAR(20),
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    agreed_to_terms BOOLEAN NOT NULL DEFAULT FALSE,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    last_login TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";


if (!$conn->query($create_table)) {
    die("خطأ في إنشاء الجدول: " . $conn->error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $student_id = trim($_POST['student_id']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $agreed_to_terms = isset($_POST['agree']) ? 1 : 0;


    $error = "";
    
    if (empty($first_name) || empty($last_name) || empty($student_id) || empty($email) || empty($password)) {
        $error = "جميع الحقول مطلوبة";
    }
    elseif ($password !== $confirm_password) {
        $error = "كلمات المرور غير متطابقة";
    }
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "البريد الإلكتروني غير صالح";
    }
    elseif (strlen($password) < 8) {
        $error = "كلمة المرور يجب أن تكون 8 أحرف على الأقل";
    }
    elseif (!$agreed_to_terms) {
        $error = "يجب الموافقة على الشروط والأحكام";
    }

    if (empty($error)) {
        $check_query = $conn->prepare("SELECT id FROM students WHERE student_id = ? OR email = ?");
        $check_query->bind_param("ss", $student_id, $email);
        $check_query->execute();
        $result = $check_query->get_result();
        
        if ($result->num_rows > 0) {
            $error = "الرقم الجامعي أو البريد الإلكتروني مسجل مسبقاً";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            $insert_query = $conn->prepare("INSERT INTO students (first_name, last_name, student_id, email, password, agreed_to_terms) 
                                          VALUES (?, ?, ?, ?, ?, ?)");
            $insert_query->bind_param("sssssi", $first_name, $last_name, $student_id, $email, $hashed_password, $agreed_to_terms);
            
            if ($_SERVER["REQUEST_METHOD"] == "POST") {

                $user_type = trim($_POST['user_type']);  
            
                $insert_query = $conn->prepare("INSERT INTO students (first_name, last_name, user_type, student_id, email, password, agreed_to_terms) 
                                                VALUES (?, ?, ?, ?, ?, ?, ?)");
                $insert_query->bind_param("ssssssi", $first_name, $last_name, $user_type, $student_id, $email, $hashed_password, $agreed_to_terms);
            
                if ($insert_query->execute()) {
                    $_SESSION['registered_student'] = [
                        'first_name' => $first_name,
                        'last_name' => $last_name,
                        'user_type' => $user_type,  
                        'student_id' => $student_id,
                        'email' => $email,
                        'registration_date' => date('Y-m-d H:i:s')
                    ];
                    header("Location: registration_success.php");
                    exit();
                } else {
                    $error = "حدث خطأ أثناء التسجيل: " . $conn->error;
                }
            }
        }
    }
} 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $agreed_to_terms = isset($_POST['agree']) ? 1 : 0;
    $user_type = trim($_POST['user_type']);  

    $error = "";

    if (empty($first_name) || empty($last_name) || empty($email) || empty($password) || empty($user_type)) {
        $error = "جميع الحقول مطلوبة";
    }
    elseif ($password !== $confirm_password) {
        $error = "كلمات المرور غير متطابقة";
    }
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "البريد الإلكتروني غير صالح";
    }
    elseif (strlen($password) < 8) {
        $error = "كلمة المرور يجب أن تكون 8 أحرف على الأقل";
    }
    elseif (!$agreed_to_terms) {
        $error = "يجب الموافقة على الشروط والأحكام";
    }

    if (empty($error)) {
        // التحقق من البيانات المكررة
        $check_query = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check_query->bind_param("s", $email);
        $check_query->execute();
        $result = $check_query->get_result();
        
        if ($result->num_rows > 0) {
            $error = "البريد الإلكتروني مسجل مسبقاً";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            if ($user_type == 'Student') {
                $student_id = trim($_POST['student_id']);  
                $insert_query = $conn->prepare("INSERT INTO students (first_name, last_name, student_id, email, password, agreed_to_terms) 
                                                VALUES (?, ?, ?, ?, ?, ?)");
                $insert_query->bind_param("sssssi", $first_name, $last_name, $student_id, $email, $hashed_password, $agreed_to_terms);
            } elseif ($user_type == 'Academic Staff') {
                $insert_query = $conn->prepare("INSERT INTO academic_staff (first_name, last_name, email, password, agreed_to_terms) 
                                                VALUES (?, ?, ?, ?, ?)");
                $insert_query->bind_param("ssssi", $first_name, $last_name, $email, $hashed_password, $agreed_to_terms);
            } elseif ($user_type == 'University Administration') {
                $insert_query = $conn->prepare("INSERT INTO university_admin (first_name, last_name, email, password, agreed_to_terms) 
                                                VALUES (?, ?, ?, ?, ?)");
                $insert_query->bind_param("ssssi", $first_name, $last_name, $email, $hashed_password, $agreed_to_terms);
            } elseif ($user_type == 'Affairs') {
                $insert_query = $conn->prepare("INSERT INTO affairs (first_name, last_name, email, password, agreed_to_terms) 
                                                VALUES (?, ?, ?, ?, ?)");
                $insert_query->bind_param("ssssi", $first_name, $last_name, $email, $hashed_password, $agreed_to_terms);
            }

            if ($insert_query->execute()) {
                $_SESSION['registered_user'] = [
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'user_type' => $user_type, 
                    'email' => $email,
                    'registration_date' => date('Y-m-d H:i:s')
                ];
                header("Location: registration_success.php");
                exit();
            } else {
                $error = "حدث خطأ أثناء التسجيل: " . $conn->error;
            }
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration </title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body,html{margin:0;padding:0;box-sizing:border-box;font-size:15px;font-family:'Times New Roman',Times,serif;background-color:#0b0a0a}
        .header{direction: ltr;display:flex;justify-content:space-between;font-size:15px;align-items:center;background-color:#000;color:#fff;padding:10px 50px;position:fixed;top:0;left:0;width:100%;z-index:1000;box-shadow:0 5px 10px rgba(0,0,0,0.3)}
        .header .logo img{margin-left:0;width:60px;height:auto;display:block}
        .header nav{display:flex;gap:40px;margin:0 auto}

.header nav a{color:#f9f8f6;text-decoration:none;border-radius:5px;font-size:1.6rem;font-weight:bold;position:relative;padding:5px 15px;transition:color 0.3s ease}
        .header nav a::after{position:absolute;bottom:-3px;left:0;width:0;height:2px;background-color:#d3ad7f;transition:width 0.3s ease}
        .header nav a:hover{color:#d3ad7f;border-bottom:.1rem solid #d3ad7f;padding-bottom:.5rem}
        .header nav a:hover::after{width:100%}
        .header .user-icon i{margin-left:100px;margin-right:130px;font-size:15px;color:#fff;cursor:pointer;transition:color 0.3s ease}
        .header .user-icon i:hover{color:#d3ad7f}
        .form-container{direction:ltr;text-align:left;max-width:600px;margin:50px auto;background-color:#0b0a0a;padding:20px;border-radius:10px;box-shadow:0 4px 6px #777474}
        h1{text-align:center;color:#fff;margin-bottom:20px;font-size: 30px;}
        .form-group{margin-bottom:15px;color:#fff}
        .form-group label{display:block;font-size:20px;font-weight:bold;margin-bottom:5px}
        .form-group input{width:90%;padding:10px;font-size:16px;border-radius:5px;border:1px solid #ccc;background-color:#0b0a0a;color:#fff}
        .form-group input::placeholder{color:#bbb}
        .form-group input:focus{border-color:#d3ad7f;outline:none}
        .form-btn{width:100%;padding:12px;font-size:16px;background-color:#d3ad7f;color:#fff;border:none;border-radius:5px;cursor:pointer;transition:background-color 0.3s ease}
        .form-btn:hover{background-color:#b88f63}
        .terms{color:#fff;text-align:center;margin-top:20px}
        .terms a{color:#d3ad7f;text-decoration:none}
        .terms a:hover{color:#d3ad7f}
        .dropdown{position:relative;display:inline-block;font-size:15px;list-style: none;}
        .dropdown ul{display:none;position:absolute;background:#000;min-width:250px;z-index:1;right:0;font-size:15px;direction:ltr;list-style: none}
        .dropdown.active ul{display:block}
        .error-message{color:red;text-align:center;margin-bottom:15px;font-size:16px}

.form-group input,

.form-group select {
    width: 90% !important; 
    padding: 10px;
    font-size: 16px;
    border-radius: 5px;
    border: 1px solid #ccc;
    background-color: #0b0a0a;
    color: #fff;
    direction: ltr;
}

.form-group input:focus,
.form-group select:focus {
    border-color: #d3ad7f;
    outline: none;
}

.terms {
    display: flex;
    align-items: center;
    gap: 5px;
}

.terms label {
    font-size: 16px;
    margin: 0;
}
    </style>
</head>
<body>
<header class="header">
    <div class="logo"><img src="\NTI_PHP\OTU.PHP\img\logo.png" alt="شعار الجامعة"></div>
    <nav>
        <a href="http://localhost:8080/OTU/index.php">Home</a>
        <a href="http://localhost:8080/OTU/index.php#about"> About</a>
        <a href="http://localhost:8080/OTU/index.php#services"> Services</a>
        <a href="http://localhost:8080/OTU/news_site-1/news_site/">News</a>
        <div class="dropdown">
            <a href="http://localhost:8080/OTU/index.php#colleges"><span>Colleges </span> <i class="fas fa-chevron-down"></i></a>
            <ul>
                <li><a href="\OTU\IT.html">تكنولوجيا المعلومات</a></li>
                <li><a href="\OTU\Habiba\S.html">تكنولوجيا السكك الحديد</a></li>
                <li><a href="\OTU\Habiba\F.html">التصنيع الغذائي</a></li>
                <li><a href="\OTU\Habiba\W.html">ماكينات الغزل والنسيج</a></li>
            </ul>
        </div>
        <a href="http://localhost:8080/OTU/index.php#contact"> Contact </a>
    </nav>
    <div class="user-icon"><i class="fas fa-user"></i></div>
</header>

<div class="form-container">
    <h1>Registration </h1>
    <?php if (!empty($error)): ?>
        <div class="error-message"><?php echo $error; ?></div>
    <?php endif; ?>
    <form method="post" action="">
        <div class="form-group">
            <label for="first_name"> First Name</label>
            <input type="text" id="first_name" name="first_name" placeholder="First Name " required value="<?php echo isset($_POST['first_name']) ? htmlspecialchars($_POST['first_name']) : ''; ?>">
        </div>
        <div class="form-group">
            <label for="last_name">Last Name </label>
            <input type="text" id="last_name" name="last_name" placeholder="Last Name" required value="<?php echo isset($_POST['last_name']) ? htmlspecialchars($_POST['last_name']) : ''; ?>">
        </div>

        <div class="form-group">
    <label for="user_type">User Type</label> 
    <select id="user_type" name="user_type" required>
        <option value="" disabled selected>Select your role</option> 
        <option value="University Administration">University Administration</option>
        <option value="Academic Staff">Academic Staff</option>
        <option value="Affairs">Affairs</option>
        <option value="Student">Student</option> 
    </select>
</div>

        
        <div class="form-group">
            <label for="student_id"> Student_id</label>
            <input type="text" id="student_id" name="student_id" placeholder=" Student_id" required value="<?php echo isset($_POST['student_id']) ? htmlspecialchars($_POST['student_id']) : ''; ?>">
        </div>
        <div class="form-group">
            <label for="email">Email </label>
            <input type="email" id="email" name="email" placeholder=" Email" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
        </div>
        <div class="form-group">
            <label for="password"> Password </label>
            <input type="password" id="password" name="password" placeholder="Password (8 characters minimum) " required>
        </div>
        <div class="form-group">
            <label for="confirm_password"> Confirm_password </label>
            <input type="password" id="confirm_password" name="confirm_password" placeholder="  Confirm_password" required>
        </div>
        <div class="terms">
            <input type="checkbox" id="agree" name="agree" required <?php echo (isset($_POST['agree'])) ? 'checked' : ''; ?>>
            <label for="agree">I agree to the <a href="terms.html">Terms and Conditions</a></label>        </div>
        <button type="submit" class="form-btn">Registration</button>
    </form>
</div>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function(){
    $('#student_id').closest('.form-group').hide(); 

    $('#user_type').on('change', function() {
        if ($(this).val() === 'Student') {
            $('#student_id').closest('.form-group').show();
            $('#student_id').prop('required', true);
        } else {
            $('#student_id').closest('.form-group').hide();
            $('#student_id').prop('required', false);
            $('#student_id').val(''); 
        }
    });
});

$('.dropdown > a').click(function(e){
    e.preventDefault();
    $(this).parent().toggleClass('active');
});
</script>

</body>
</html>