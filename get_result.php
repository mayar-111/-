<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نظام النتائج الإلكتروني | جامعة تكنولوجيا الصناعة والطاقة</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary:rgb(3, 5, 16);
            --primary-light:rgb(3, 5, 16);
            --secondary:rgb(3, 5, 16);
            --dark: #1b263b;
            --light: #f8f9fa;
            --success: #4cc9f0;
            --white: #ffffff;
            --gradient: linear-gradient(135deg,rgb(91, 7, 7) 0%,rgb(31, 2, 24) 100%);
            --shadow: 0 10px 30px rgb(91, 7, 7) 0%;
            --transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Tajawal', sans-serif;
            background-color: #f0f2f5;
            background-image: url('https://images.unsplash.com/photo-1576091160550-2173dba999ef?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            color: var(--dark);
            position: relative;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            background: rgba(0, 0, 0, 0.6);
            z-index: 0;
        }

        .container {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 500px;
            margin: 2rem;
            animation: fadeIn 0.8s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: var(--transition);
            transform-style: preserve-3d;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgb(91, 7, 7) 0%;
        }

        .card-header {
            background: var(--gradient);
            color: var(--white);
            padding: 2rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .card-header::after {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 70%);
            transform: rotate(30deg);
            animation: shine 3s infinite;
        }

        @keyframes shine {
            0% { transform: rotate(30deg) translate(-10%, -10%); }
            100% { transform: rotate(30deg) translate(10%, 10%); }
        }

        .logo {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 1rem;
        }

        .logo-icon {
            font-size: 2.8rem;
            margin-bottom: 1rem;
            color: var(--white);
            background: rgba(255, 255, 255, 0.2);
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 5px 15px rgba(166, 34, 34, 0.1);
        }

        .logo-text h1 {
            font-size: 1.6rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .logo-text p {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        .card-body {
            padding: 2rem;
        }

        .form-title {
            text-align: center;
            margin-bottom: 2rem;
            position: relative;
        }

        .form-title h2 {
            font-size: 1.4rem;
            color: var(--dark);
            display: inline-block;
        }

        .form-title h2::after {
            content: '';
            position: absolute;
            bottom: -10px;
            right: 50%;
            transform: translateX(50%);
            width: 60px;
            height: 3px;
            background: var(--primary);
            border-radius: 3px;
        }

        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--dark);
            font-size: 0.95rem;
        }

        .input-field {
            position: relative;
        }

        .input-icon {
            position: absolute;
            top: 50%;
            right: 15px;
            transform: translateY(-50%);
            color: var(--primary);
            font-size: 1.1rem;
        }

        .form-control {
            width: 100%;
            padding: 15px 15px 15px 50px;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            font-size: 1rem;
            transition: var(--transition);
            background-color: var(--light);
            color:  rgb(91, 7, 7) 50%;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.2);
        }

        .form-control::placeholder {
            color: #adb5bd;
            font-size: 0.9rem;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: 100%;
            padding: 15px;
            background: var(--gradient);
            color: var(--white);
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: rgb(91, 7, 7) 0%;
            margin-top: 0.5rem;
            box-shadow: 0 4px 15px rgb(91, 7, 7) 0%;
        }

        .btn:hover {
            background: linear-gradient(135deg,rgb(91, 7, 7) 50%);
            transform: translateY(-2px);
            box-shadow: 0 7px 20px rgb(91, 7, 7) 0%;
        }

        .btn:active {
            transform: translateY(0);
        }

        .btn i {
            font-size: 1.1rem;
        }

        .footer {
            text-align: center;
            margin-top: 2rem;
            font-size: 0.8rem;
            color: #6c757d;
        }

        .floating {
            animation: floating 3s ease-in-out infinite;
        }

        @keyframes floating {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }

        @media (max-width: 576px) {
            .container {
                margin: 1rem;
            }
            
            .card-header {
                padding: 1.5rem;
            }
            
            .card-body {
                padding: 1.5rem;
            }
            
            .logo-text h1 {
                font-size: 1.4rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <div class="logo">
                    <div class="logo-icon floating">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <div class="logo-text">
                        <h1>جامعة تكنولوجيا الصناعة والطاقة</h1>
                        <p>نظام الاستعلام الإلكتروني للنتائج</p>
                    </div>
                </div>
            </div>
            
            <div class="card-body">
                <div class="form-title">
                    <h2>استعلام عن النتائج</h2>
                </div>
                
                <form action="result.php" method="post">
                    <div class="form-group">
                        <label for="student_id" class="form-label">الرقم الجامعي</label>
                        <div class="input-field">
                            <i class="fas fa-user-graduate input-icon"></i>
                            <input type="text" id="student_id" name="student_id" class="form-control"  required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="national_id" class="form-label">الرقم القومي</label>
                        <div class="input-field">
                            <i class="fas fa-id-card input-icon"></i>
                            <input type="text" id="national_id" name="national_id" class="form-control" required>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn">
                        <i class="fas fa-search"></i>
                        عرض النتيجة
                    </button>
                </form>
                
                <div class="footer">
                    <p> جميع الحقوق محفوظة لجامعة أكتوبر التكنولوجية  &copy; 2025 - نظام إدارة النتائج</p>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const inputs = document.querySelectorAll('.form-control');
        
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.querySelector('.input-icon').style.opacity = '0';
            });
            
            input.addEventListener('input', function() {
                if (this.value.length > 0) {
                    this.parentElement.querySelector('.input-icon').style.opacity = '0';
                } else {
                    this.parentElement.querySelector('.input-icon').style.opacity = '1';
                }
            });
            
            input.addEventListener('blur', function() {
                if (this.value.length === 0) {
                    this.parentElement.querySelector('.input-icon').style.opacity = '1';
                }
            });
        });
    });
</script>
</body>
</html>