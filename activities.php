<?php
session_start();

$host = 'localhost';
$dbname = 'activities';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $full_name = htmlspecialchars($_POST['full_name']);
        $university_id = htmlspecialchars($_POST['university_id']);
        $grade = htmlspecialchars($_POST['grade']);
        $phone = htmlspecialchars($_POST['phone']);
        $activities = $_POST['activities'] ?? [];

        if (empty($activities)) {
            throw new Exception("يجب اختيار نشاط واحد على الأقل");
        }

        $pdo->beginTransaction();

        $stmt = $pdo->prepare("SELECT id FROM students WHERE university_id = ?");
        $stmt->execute([$university_id]);
        $student = $stmt->fetch();

        if (!$student) {
            $stmt = $pdo->prepare("INSERT INTO students (full_name, university_id, grade, phone) VALUES (?, ?, ?, ?)");
            $stmt->execute([$full_name, $university_id, $grade, $phone]);
            $student_id = $pdo->lastInsertId();
        } else {
            $student_id = $student['id'];
            $stmt = $pdo->prepare("UPDATE students SET full_name = ?, grade = ?, phone = ? WHERE id = ?");
            $stmt->execute([$full_name, $grade, $phone, $student_id]);
            
            $stmt = $pdo->prepare("DELETE FROM student_activities WHERE student_id = ?");
            $stmt->execute([$student_id]);
        }

        $success_messages = [];
        
        foreach ($activities as $activity_id) {
            try {
                $stmt = $pdo->prepare("INSERT INTO student_activities (student_id, activity_id) VALUES (?, ?)");
                $stmt->execute([$student_id, $activity_id]);
                
                $stmt = $pdo->prepare("SELECT name FROM activities WHERE id = ?");
                $stmt->execute([$activity_id]);
                $activity = $stmt->fetch();
                
                $success_messages[] = "تم التسجيل في نشاط: " . ($activity['name'] ?? 'غير معروف');
            } catch (PDOException $e) {
                $error_messages[] = "خطأ في تسجيل النشاط: " . $e->getMessage();
            }
        }

        $pdo->commit();
        
        $_SESSION['success_messages'] = $success_messages;
        $_SESSION['error_messages'] = $error_messages ?? [];
        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
        
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = $e->getMessage();
    }
}

$activities = $pdo->query("SELECT id, name, description FROM activities")->fetchAll();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نظام تسجيل الأنشطة الطلابية</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4e73df;
            --secondary: #1cc88a;
            --light: #f8f9fc;
            --dark: #5a5c69;
        }
        
        body {
            font-family: 'Tajawal', sans-serif;
            background-color: #f5f7fa;
        }
        
        .card {
            border-radius: 15px;
            box-shadow: 0 6px 15px rgba(0,0,0,0.1);
            border: none;
            overflow: hidden;
        }
        
        .card-header {
            background: linear-gradient(135deg, rgb(210, 164, 13),rgb(141, 12, 12));
            border-bottom: none;
        }
        
        .form-control, .form-select {
            border-radius: 8px;
            padding: 12px 15px;
            border: 2px solid #e0e3e9;
            transition: all 0.3s;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.25rem ,rgb(141, 12, 12);
        }
        
        .activity-card {
            border-radius: 10px;
            border: 1px solid #e0e3e9;
            transition: all 0.3s;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }
        
        .activity-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
            border-color: var(--primary);
        }
        
        .activity-card.selected {
            border-color: var(--primary);
            background-color: rgba(78, 115, 223, 0.05);
        }
        
        .activity-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, rgb(210, 164, 13),rgb(141, 12, 12));
            color: white;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, rgb(210, 164, 13),rgb(141, 12, 12));
            border: none;
            padding: 12px 24px;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: all 0.3s;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg,rgb(141, 12, 12), rgb(210, 164, 13));
            transform: translateY(-2px);
        }
        
        .floating {
            animation: floating 3s ease-in-out infinite;
        }
        
        @keyframes floating {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }
        
        .alert {
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card animate__animated animate__fadeInUp">
                    <div class="card-header text-white text-center py-4">
                        <div class="floating mb-3">
                            <i class="fas fa-calendar-alt fa-4x"></i>
                        </div>
                        <h2 class="fw-bold mb-0">تسجيل الأنشطة الطلابية</h2>
                        <p class="mb-0">اختر الأنشطة التي ترغب فى الاشتراك بها </p>
                    </div>
                    
                    <div class="card-body p-4 p-md-5">
                        <?php if(isset($error)): ?>
                            <div class="alert alert-danger animate__animated animate__fadeIn">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                <?= $error ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if(!empty($_SESSION['success_messages'])): ?>
                            <div class="alert alert-success animate__animated animate__fadeIn">
                                <i class="fas fa-check-circle me-2"></i>
                                <?php foreach($_SESSION['success_messages'] as $msg): ?>
                                    <div><?= $msg ?></div>
                                <?php endforeach; ?>
                                <?php unset($_SESSION['success_messages']); ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if(!empty($_SESSION['error_messages'])): ?>
                            <div class="alert alert-warning animate__animated animate__fadeIn">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <?php foreach($_SESSION['error_messages'] as $msg): ?>
                                    <div><?= $msg ?></div>
                                <?php endforeach; ?>
                                <?php unset($_SESSION['error_messages']); ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="post" class="needs-validation" novalidate>
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="full_name" class="form-label fw-bold">الاسم الثلاثي</label>
                                        <input type="text" class="form-control" id="full_name" name="full_name" required>
                                        <div class="invalid-feedback">يرجى إدخال الاسم الثلاثي</div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="university_id" class="form-label fw-bold">الرقم الجامعي</label>
                                        <input type="text" class="form-control" id="university_id" name="university_id" required>
                                        <div class="invalid-feedback">يرجى إدخال الرقم الجامعي</div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="grade" class="form-label fw-bold">الفرقة</label>
                                        <select class="form-select" id="grade" name="grade" required>
                                            <option value="" selected disabled>اختر الفرقة...</option>
                                            <option value="الأولى">الفرقة الأولى</option>
                                            <option value="الثانية">الفرقة الثانية</option>
                                            <option value="الثالثة">الفرقة الثالثة</option>
                                            <option value="الرابعة">الفرقة الرابعة</option>
                                        </select>
                                        <div class="invalid-feedback">يرجى اختيار الفرقة</div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="phone" class="form-label fw-bold">رقم الهاتف</label>
                                        <input type="tel" class="form-control" id="phone" name="phone" required>
                                        <div class="invalid-feedback">يرجى إدخال رقم الهاتف</div>
                                    </div>
                                </div>
                            </div>
                            
                            <hr class="my-4">
                            
                            <h4 class="mb-4 fw-bold"><i class="fas fa-running me-2"></i>الأنشطة المتاحة</h4>
                            
                            <div class="row g-4 mb-4" id="activities-container">
                                <?php foreach($activities as $activity): ?>
                                    <div class="col-md-6">
                                        <div class="activity-card p-3 h-100">
                                            <div class="d-flex align-items-center">
                                                <input type="checkbox" name="activities[]" value="<?= $activity['id'] ?>" id="activity-<?= $activity['id'] ?>" class="form-check-input me-3" style="width: 20px; height: 20px;">
                                                <div class="activity-icon me-3">
                                                    <i class="fas fa-<?= 
                                                        match($activity['name']) {
                                                            'OTU CPC club' => 'code',
                                                            'نشاط رياضي' => 'futbol',
                                                            'نشاط ثقافي' => 'book-open',
                                                            'نشاط علمي' => 'microscope',
                                                            'نشاط تطوعي' => 'hands-helping' ,
                                                            default => 'star'
                                                        } 
                                                    ?>"></i>
                                                </div>
                                                <div>
                                                    <h5 class="fw-bold mb-1"><?= $activity['name'] ?></h5>
                                                    <p class="text-muted mb-0 small"><?= $activity['description'] ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <div class="text-center mt-4">
                                <button type="submit" class="btn btn-primary btn-lg px-5 py-3">
                                    <i class="fas fa-paper-plane me-2"></i>تسجيل الأنشطة المختارة
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        (function () {
            'use strict'
            
            var forms = document.querySelectorAll('.needs-validation')
            
            Array.prototype.slice.call(forms)
                .forEach(function (form) {
                    form.addEventListener('submit', function (event) {
                        if (!form.checkValidity()) {
                            event.preventDefault()
                            event.stopPropagation()
                        }
                        
                        form.classList.add('was-validated')
                    }, false)
                })
        })()
        
        document.querySelectorAll('input[name="activities[]"]').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const card = this.closest('.activity-card');
                if (this.checked) {
                    card.classList.add('selected');
                } else {
                    card.classList.remove('selected');
                }
            });
        });
        
        document.querySelector('form').addEventListener('submit', function(e) {
            const checkedActivities = document.querySelectorAll('input[name="activities[]"]:checked');
            if (checkedActivities.length === 0) {
                e.preventDefault();
                alert('يجب اختيار نشاط واحد على الأقل');
                return false;
            }
            
            const btn = this.querySelector('button[type="submit"]');
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> جاري التسجيل...';
            btn.disabled = true;
        });
    </script>
</body>
</html>