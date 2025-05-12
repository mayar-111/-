<?php 
$db_host = 'localhost';
$db_name = 'registration';
$db_user = 'root';  
$db_pass = '';      

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
    mysqli_set_charset($conn, "utf8mb4");
} catch (mysqli_sql_exception $e) {
    die("Database connection failed: " . $e->getMessage());
}

function getAcademicStats($conn) {
    $query = "
        SELECT 
            (SELECT COUNT(*) FROM university_admin) AS total_admin,
            (SELECT COUNT(*) FROM academic_staff) AS total_faculty,
            (SELECT COUNT(*) FROM affairs) AS total_affairs,
            (SELECT COUNT(*) FROM students) AS total_students
    ";
    
    $result = mysqli_query($conn, $query);
    return mysqli_fetch_assoc($result);
}

$academicStats = null;
$error_message = null;

try {
    $academicStats = getAcademicStats($conn);
} catch (mysqli_sql_exception $e) {
    $error_message = "Error fetching academic statistics: " . $e->getMessage();
}
?>

<section id="stats" class="stats py-5">
    <div class="container" data-aos="fade-up" data-aos-delay="100">
        <div class="row gy-4 align-items-center">
            <div class="col-lg-5 order-lg-1">
                <img src="../OTU/assets_2/img/student.jpg" alt="Students" class="img-fluid stats-image">
            </div>
            
            <div class="col-lg-6 order-lg-2">
                <h2 class="stats-title">&ensp;&ensp;&ensp;&ensp;&ensp;&ensp;&ensp;&ensp;Academic Community</h2>
                <p class="stats-description" style="direction: rtl;">
                    يتكون المجتمع الأكاديمي من أعضاء هيئة التدريس والهيئة المعاونة والطلاب، حيث يعمل الجميع معًا في بيئة تعليمية تهدف إلى تحقيق التميز الأكاديمي والبحثي. 
                    يساهم أعضاء هيئة التدريس في نقل المعرفة وإجراء الأبحاث المبتكرة، بينما تقدم الهيئة المعاونة الدعم الأكاديمي والإداري لضمان جودة العملية التعليمية. 
                    أما الطلاب، فهم محور العملية التعليمية، حيث يتم تزويدهم بالمهارات والمعرفة اللازمة لتحقيق النجاح في مجالاتهم المختلفة.
                </p>
                
                <div class="row gy-4">
                    <?php if ($error_message): ?>
                        <div class="alert alert-danger">
                            <?php echo $error_message; ?>
                        </div>
                    <?php elseif ($academicStats): ?>
                        <div class="row">
    <div class="col-md-6 mb-4">
        <div class="stats-item" style="display: flex; align-items: center; direction: ltr;">
            <i class="bi bi-emoji-smile" style="font-size: 3rem; color: #4c3518; margin-right: 15px;"></i>
            <div style="display: flex; flex-direction: column;">
                <span data-purecounter-start="0" 
                      data-purecounter-end="<?php echo $academicStats['total_admin']; ?>" 
                      data-purecounter-duration="1" 
                      class="purecounter" 
                      style="font-size: 2rem; font-weight: bold; color: #4c3518;">
                    0
                </span>
                <p style="margin: 0; font-size: 1rem; color: #495057;">
                    <strong>University Administration</strong>
                </p>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 mb-4">
        <div class="stats-item" style="display: flex; align-items: center; direction: ltr;">
            <i class="bi bi-journal-richtext" style="font-size: 3rem; color: #4c3518; margin-right: 15px;"></i>
            <div style="display: flex; flex-direction: column;">
                <span data-purecounter-start="0" 
                      data-purecounter-end="<?php echo $academicStats['total_faculty']; ?>" 
                      data-purecounter-duration="1" 
                      class="purecounter" 
                      style="font-size: 2rem; font-weight: bold; color: #4c3518;">
                    0
                </span>
                <p style="margin: 0; font-size: 1rem; color: #495057;">
                    <strong>Academic Staff</strong>
                </p>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 mb-4">
        <div class="stats-item" style="display: flex; align-items: center; direction: ltr;">
            <i class="bi bi-headset" style="font-size: 3rem; color: #4c3518; margin-right: 15px;"></i>
            <div style="display: flex; flex-direction: column;">
                <span data-purecounter-start="0" 
                      data-purecounter-end="<?php echo $academicStats['total_affairs']; ?>" 
                      data-purecounter-duration="1" 
                      class="purecounter" 
                      style="font-size: 2rem; font-weight: bold; color: #4c3518;">
                    0
                </span>
                <p style="margin: 0; font-size: 1rem; color: #495057;">
                    <strong>Affairs</strong>
                </p>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 mb-4">
        <div class="stats-item" style="display: flex; align-items: center; direction: ltr;">
            <i class="bi bi-people" style="font-size: 3rem; color: #4c3518; margin-right: 15px;"></i>
            <div style="display: flex; flex-direction: column;">
                <span data-purecounter-start="0" 
                      data-purecounter-end="<?php echo $academicStats['total_students']; ?>" 
                      data-purecounter-duration="1" 
                      class="purecounter" 
                      style="font-size: 2rem; font-weight: bold; color: #4c3518;">
                    0
                </span>
                <p style="margin: 0; font-size: 1rem; color: #495057;">
                    <strong>Students</strong>
                </p>
            </div>
        </div>
    </div>
</div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            No academic statistics available.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

<script>
    AOS.init({
        duration: 800,
        easing: 'ease-in-out'
    });
</script>