<?php
header('Content-Type: text/html; charset=utf-8');

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'otu');
define('DB_CHARSET', 'utf8mb4');

function handleError($message) {
    header("Location: index.php?error=" . urlencode($message));
    exit();
}

try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    
    $conn = new PDO($dsn, DB_USER, DB_PASS, $options);

    $national_id = filter_input(INPUT_POST, 'national_id', FILTER_SANITIZE_STRING);
    $student_id = filter_input(INPUT_POST, 'student_id', FILTER_SANITIZE_STRING);

    if (empty($national_id) || empty($student_id)) {
        handleError("الرجاء إدخال الرقم القومي والرقم الجامعي");
    }

    $stmt = $conn->prepare("SELECT * FROM student_data WHERE national_id = ? AND student_id = ?");
    $stmt->execute([$national_id, $student_id]);
    $student = $stmt->fetch();

    if (!$student) {
        handleError("لا توجد بيانات مطابقة للرقم القومي والرقم الجامعي");
    }

    $stmt = $conn->prepare("SELECT * FROM results WHERE student_id = ?");
    $stmt->execute([$student_id]);
    $result = $stmt->fetch();

    if (!$result) {
        handleError("لا توجد نتائج مسجلة لهذا الطالب");
    }

    $subjects = ['CCNA', 'Java', 'Web', 'Capstone', 'DB', 'DS'];
    $passed_subjects = 0;
    foreach ($subjects as $subject) {
        if ($result[$subject] >= 50) $passed_subjects++;
    }
    $success_percentage = round(($passed_subjects / count($subjects)) * 100, 2);
    
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نتيجة الطالب - جامعة أكتوبر التكنولوجية</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <style>
        :root {
            --primary-color:rgb(12, 30, 48);
            --primary-light:rgb(52, 54, 110);
            --secondary-color: #e6f2ff;
            --accent-color:rgb(117, 45, 45);
            --success-color: #4CAF50;
            --danger-color: #F44336;
            --text-color: #333;
            --light-gray: #f8f9fa;
            --border-radius: 10px;
            --box-shadow: 0 5px 20px rgba(0, 51, 102, 0.15);
            --transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.1);
        }
        
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: 'Tajawal', sans-serif;
            background-color: var(--light-gray);
            color: var(--text-color);
            line-height: 1.6;
            overflow-x: hidden;
        }
        
        .container {
            max-width: 1000px;
            margin: 40px auto;
            padding: 40px;
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            position: relative;
            overflow: hidden;
            opacity: 0;
            transform: translateY(20px);
            animation: fadeInUp 0.8s forwards;
        }
        
        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .container::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, var(--primary-color), var(--primary-light));
            animation: progressBar 2s ease-in-out;
        }
        
        @keyframes progressBar {
            from { width: 0 }
            to { width: 100% }
        }
        
        h1, h2, h3 {
            color: var(--primary-color);
        }
        
        h1 {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            position: relative;
            font-size: 2rem;
        }
        
        h1::after {
            content: '';
            position: absolute;
            bottom: 0;
            right: 50%;
            transform: translateX(50%);
            width: 100px;
            height: 4px;
            background: linear-gradient(90deg, var(--accent-color), var(--primary-color));
            border-radius: 2px;
        }
        
        h2 {
            margin: 30px 0 20px;
            padding-right: 15px;
            position: relative;
            font-size: 1.5rem;
        }
        
        h2::after {
            content: '';
            position: absolute;
            bottom: -5px;
            right: 0;
            width: 50px;
            height: 3px;
            background-color: var(--accent-color);
            border-radius: 3px;
        }
        
        .info-card {
            background-color: white;
            border-radius: var(--border-radius);
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: var(--box-shadow);
            border-left: 5px solid var(--primary-color);
            transition: var(--transition);
            transform: scale(0.98);
            opacity: 0;
            animation: cardEntrance 0.6s forwards 0.3s;
        }
        
        @keyframes cardEntrance {
            to {
                transform: scale(1);
                opacity: 1;
            }
        }
        
        .info-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 51, 102, 0.2);
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
        }
        
        .info-item {
            margin-bottom: 15px;
            opacity: 0;
            transform: translateX(20px);
            animation: slideIn 0.5s forwards;
        }
        
        @keyframes slideIn {
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        .info-item:nth-child(1) { animation-delay: 0.4s; }
        .info-item:nth-child(2) { animation-delay: 0.5s; }
        .info-item:nth-child(3) { animation-delay: 0.6s; }
        .info-item:nth-child(4) { animation-delay: 0.7s; }
        .info-item:nth-child(5) { animation-delay: 0.8s; }
        .info-item:nth-child(6) { animation-delay: 0.9s; }
        
        .info-label {
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 8px;
            font-size: 0.95rem;
        }
        
        .info-value {
            font-size: 1.1rem;
            padding: 8px 12px;
            background-color: var(--secondary-color);
            border-radius: 6px;
            display: inline-block;
        }
        
        .results-container {
            margin: 40px 0;
            opacity: 0;
            animation: fadeIn 0.8s forwards 0.5s;
        }
        
        @keyframes fadeIn {
            to { opacity: 1 }
        }
        
        .results-table {
            width: 100%;
            border-collapse: collapse;
            margin: 30px 0;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.08);
            border-radius: var(--border-radius);
            overflow: hidden;
        }
        
        .results-table th {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            color: white;
            padding: 18px;
            text-align: right;
            font-weight: 500;
        }
        
        .results-table td {
            padding: 15px;
            border-bottom: 1px solid #eee;
            position: relative;
        }
        
        .results-table tr:not(:first-child) {
            transition: var(--transition);
        }
        
        .results-table tr:not(:first-child):hover {
            background-color: var(--secondary-color);
            transform: translateX(5px);
        }
        
        .results-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .grade-legend {
            display: flex;
            justify-content: center;
            gap: 25px;
            margin: 30px 0;
            flex-wrap: wrap;
            opacity: 0;
            animation: fadeIn 0.8s forwards 0.8s;
        }
        
        .legend-item {
            display: flex;
            align-items: center;
            font-size: 0.9rem;
            padding: 8px 15px;
            border-radius: 30px;
            background-color: white;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
        }
        
        .legend-color {
            width: 18px;
            height: 18px;
            border-radius: 50%;
            margin-left: 10px;
        }
        
        .summary-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 25px;
            margin-top: 40px;
            opacity: 0;
            animation: fadeIn 0.8s forwards 1s;
        }
        
        .summary-card {
            background: white;
            border-radius: var(--border-radius);
            padding: 25px;
            text-align: center;
            box-shadow: var(--box-shadow);
            border-top: 4px solid var(--primary-color);
            transition: var(--transition);
            transform: translateY(20px);
            opacity: 0;
        }
        
        .summary-card:nth-child(1) { animation: cardSlideUp 0.6s forwards 1s; }
        .summary-card:nth-child(2) { animation: cardSlideUp 0.6s forwards 1.1s; }
        .summary-card:nth-child(3) { animation: cardSlideUp 0.6s forwards 1.2s; }
        .summary-card:nth-child(4) { animation: cardSlideUp 0.6s forwards 1.3s; }
        
        @keyframes cardSlideUp {
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        
        .summary-card:hover {
            transform: translateY(-10px) !important;
            box-shadow: 0 15px 30px rgba(0, 51, 102, 0.15);
        }
        
        .summary-card h3 {
            margin-bottom: 15px;
            font-size: 1.1rem;
            color: var(--primary-light);
        }
        
        .summary-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-color);
            margin: 10px 0;
        }
        
        .progress-container {
            margin-top: 20px;
        }
        
        .progress-bar {
            height: 10px;
            background-color: #e0e0e0;
            border-radius: 5px;
            overflow: hidden;
            position: relative;
        }
        
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--primary-color), var(--accent-color));
            width: 0;
            border-radius: 5px;
            transition: width 1.5s ease-in-out 1.5s;
        }
        
        .progress-text {
            font-size: 0.85rem;
            margin-top: 8px;
            text-align: center;
            color: #666;
        }
        
        .btn-container {
            text-align: center;
            margin-top: 40px;
            opacity: 0;
            animation: fadeIn 0.8s forwards 1.5s;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            padding: 12px 30px;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            color: white;
            text-decoration: none;
            border-radius: 50px;
            transition: var(--transition);
            border: none;
            cursor: pointer;
            font-family: inherit;
            font-size: 1rem;
            font-weight: 500;
            box-shadow: 0 5px 15px rgba(0, 51, 102, 0.3);
            position: relative;
            overflow: hidden;
        }
        
        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 51, 102, 0.4);
        }
        
        .btn:active {
            transform: translateY(1px);
        }
        
        .btn::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(255,255,255,0.2), transparent);
            transform: translateX(-100%);
            transition: transform 0.6s ease;
        }
        
        .btn:hover::after {
            transform: translateX(100%);
        }
        
        .btn i {
            margin-left: 10px;
            transition: transform 0.3s ease;
        }
        
        .btn:hover i {
            transform: translateX(5px);
        }
        
        .university-header {
            text-align: center;
            margin-bottom: 30px;
            opacity: 0;
            animation: fadeIn 0.8s forwards 0.2s;
        }
        
        .university-logo {
            height: 80px;
            margin-bottom: 15px;
            transition: transform 0.5s ease;
        }
        
        .university-logo:hover {
            transform: rotateY(180deg);
        }
        
        .university-title {
            font-size: 1.8rem;
            color: var(--primary-color);
            margin-bottom: 5px;
        }
        
        .university-subtitle {
            color: #666;
            font-size: 1.1rem;
        }
        
        .watermark {
            position: absolute;
            bottom: 20px;
            left: 20px;
            font-size: 0.8rem;
            color: rgba(0, 0, 0, 0.1);
            z-index: -1;
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 25px;
                margin: 20px;
            }
            
            h1 {
                font-size: 1.6rem;
            }
            
            .info-grid {
                grid-template-columns: 1fr;
            }
            
            .summary-cards {
                grid-template-columns: 1fr;
            }
            
            .results-table {
                font-size: 0.9rem;
            }
            
            .results-table th, 
            .results-table td {
                padding: 12px 8px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="university-header animate__animated animate__fadeInDown">
            <img src="/OTU/assets_2/img/logo.png" alt="شعار الجامعة" class="university-logo">
            <h1 class="university-title">جامعة أكتوبر التكنولوجية</h1>
            <div class="university-subtitle">نظام نتائج الطلاب - الفصل الدراسي الأول 2025/2026</div>
        </div>
        
        <h1 class="animate__animated animate__fadeIn"><i class="fas fa-user-graduate"></i> كشف الدرجات</h1>
        
        <div class="info-card">
            <h2><i class="fas fa-id-card"></i> البيانات الشخصية</h2>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">الاسم الكامل</div>
                    <div class="info-value"><?= htmlspecialchars($student['full_name']) ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">الرقم الجامعي</div>
                    <div class="info-value"><?= htmlspecialchars($student['student_id']) ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">الكلية</div>
                    <div class="info-value"><?= htmlspecialchars($student['college']) ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">القسم</div>
                    <div class="info-value"><?= htmlspecialchars($student['department']) ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">الفرقة</div>
                    <div class="info-value"><?= htmlspecialchars($student['level'] ?? 'غير محدد') ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">الفصل الدراسي</div>
                    <div class="info-value"><?= htmlspecialchars($student['semester'] ?? 'غير محدد') ?></div>
                </div>
            </div>
        </div>
        
        <div class="results-container">
            <h2><i class="fas fa-poll"></i> النتائج الاكاديمية</h2>
            <table class="results-table">
                <thead>
                    <tr>
                        <th>المادة</th>
                        <th>الدرجة</th>
                        <th>التقدير</th>
                        <th>الحالة</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($subjects as $subject): 
                        $grade = getGradeLetter($result[$subject]);
                        $is_passed = $result[$subject] >= 50;
                    ?>
                    <tr class="animate__animated animate__fadeInRight">
                        <td><strong><?= $subject ?></strong></td>
                        <td><?= htmlspecialchars($result[$subject]) ?></td>
                        <td><span class="grade-badge" style="background-color: <?= getGradeColor($grade) ?>"><?= $grade ?></span></td>
                        <td>
                            <?php if ($is_passed): ?>
                                <span class="status-passed"><i class="fas fa-check-circle"></i> ناجح</span>
                            <?php else: ?>
                                <span class="status-failed"><i class="fas fa-times-circle"></i> راسب</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <div class="grade-legend">
            <div class="legend-item">
                <div class="legend-color" style="background-color: var(--success-color)"></div>
                <span>ناجح (50-100)</span>
            </div>
            <div class="legend-item">
                <div class="legend-color" style="background-color: var(--danger-color)"></div>
                <span>راسب (أقل من 50)</span>
            </div>
        </div>
        
        <div class="summary-cards">
            <div class="summary-card">
                <h3><i class="fas fa-calculator"></i> المجموع الكلي</h3>
                <div class="summary-value"><?= htmlspecialchars($result['total']) ?></div>
                <div class="progress-text">من <?= count($subjects) * 100 ?> درجة</div>
            </div>
            <div class="summary-card">
                <h3><i class="fas fa-chart-line"></i> المعدل التراكمي</h3>
                <div class="summary-value"><?= number_format($result['average'], 2) ?></div>
                <div class="progress-text"><?= getGradeDescription($result['average']) ?></div>
            </div>
            <div class="summary-card">
                <h3><i class="fas fa-medal"></i> التقدير العام</h3>
                <div class="summary-value" style="color: <?= getGradeColor($result['grade']) ?>"><?= htmlspecialchars($result['grade']) ?></div>
                <div class="progress-text"><?= getGradeDescriptionByLetter($result['grade']) ?></div>
            </div>
          
                </div>
            </div>
        </div>
        
        <div class="btn-container">
            <a href="http://localhost:8080/OTU/index.php" class="btn"><i class="fas fa-arrow-left"></i> العودة للصفحة الرئيسية</a>
        </div>
        
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                document.querySelector('.progress-fill').style.width = '<?= $success_percentage ?>%';
            }, 1500);
        });
    </script>
</body>
</html>
<?php

} catch(PDOException $e) {
    handleError("حدث خطأ في النظام: " . $e->getMessage());
}

function getGradeLetter($score) {
    if ($score >= 90) return 'A+';
    if ($score >= 85) return 'A';
    if ($score >= 80) return 'B+';
    if ($score >= 75) return 'B';
    if ($score >= 70) return 'C+';
    if ($score >= 65) return 'C';
    if ($score >= 60) return 'D+';
    if ($score >= 50) return 'D';
    return 'F';
}

function getGradeColor($grade) {
    switch ($grade) {
        case 'A+': return '#4CAF50';
        case 'A': return '#8BC34A';
        case 'B+': return '#CDDC39';
        case 'B': return '#FFC107';
        case 'C+': return '#FF9800';
        case 'C': return '#FF5722';
        case 'D+': return '#F44336';
        case 'D': return '#E91E63';
        case 'F': return '#9C27B0';
        default: return '#607D8B';
    }
}

function getGradeDescription($average) {
    if ($average >= 90) return 'امتياز مع مرتبة الشرف';
    if ($average >= 80) return 'امتياز';
    if ($average >= 70) return 'جيد جداً';
    if ($average >= 60) return 'جيد';
    if ($average >= 50) return 'مقبول';
    return 'راسب';
}

function getGradeDescriptionByLetter($grade) {
    switch ($grade) {
        case 'A+': return 'امتياز مع مرتبة الشرف';
        case 'A': return 'امتياز';
        case 'B+': return 'جيد جداً';
        case 'B': return 'جيد';
        case 'C+': return 'مقبول';
        case 'C': 
        case 'D+': 
        case 'D': return 'ناجح';
        case 'F': return 'راسب';
        default: return '-';
    }
}
?>