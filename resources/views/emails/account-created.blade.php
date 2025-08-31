<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akun Berhasil Dibuat</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }
        
        .email-wrapper {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }
        
        .header {
            background-color: #2c3e50;
            padding: 20px 0;
            text-align: center;
        }
        
        .logo {
            max-width: 180px;
            max-height: 180px;
        }
        
        .content {
            padding: 40px 30px;
        }
        
        h1 {
            color: #2c3e50;
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 20px;
        }
        
        p {
            color: #555;
            font-size: 16px;
            margin-bottom: 20px;
        }
        
        .success-icon {
            text-align: center;
            margin: 20px 0;
        }
        
        .account-info {
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 25px;
            margin: 25px 0;
        }
        
        .account-info p {
            margin-bottom: 12px;
        }
        
        .account-info p:last-child {
            margin-bottom: 0;
        }
        
        .account-info strong {
            color: #2c3e50;
            font-weight: 600;
            display: inline-block;
            width: 100px;
        }
        
        .next-steps {
            background-color: #e8f4fc;
            border-left: 4px solid #3498db;
            padding: 15px;
            margin: 25px 0;
        }
        
        .next-steps h2 {
            color: #2c3e50;
            font-size: 18px;
            margin-bottom: 10px;
        }
        
        .divider {
            height: 1px;
            background-color: #e9ecef;
            margin: 30px 0;
        }
        
        .signature {
            margin-top: 30px;
        }
        
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #6c757d;
            border-top: 1px solid #e9ecef;
        }
        
        .social-links {
            margin: 15px 0;
        }
        
        .social-link {
            display: inline-block;
            margin: 0 10px;
            color: #6c757d;
            text-decoration: none;
        }
        
        @media only screen and (max-width: 600px) {
            .content {
                padding: 30px 20px;
            }
            
            h1 {
                font-size: 22px;
            }
            
            .account-info {
                padding: 20px;
            }
            
            .account-info strong {
                width: 80px;
            }
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="header">
            <img src="{{ asset('src/logo_raadeveloperz.png') }}" style="max-width: 180px; max-height: 180px;" alt="raadeveloperz Logo" class="logo">
        </div>
        
        <div class="content">
            <h1>Akun Siswa Berhasil Dibuat</h1>            
            <p>Halo, <strong>{{ $name }}</strong>!</p>
            
            <div class="success-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#2ecc71" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
            </div>
            
            <p>Akun Anda telah berhasil dibuat. Berikut adalah informasi akun Anda:</p>
            
            <div class="account-info">
                <p><strong>Username:</strong> {{ $username }}</p>
                <p><strong>Email:</strong> {{ $email }}</p>
            </div>
            
            <div class="next-steps">
                <h2>Langkah Selanjutnya</h2>
                <p>Anda dapat login menggunakan username dan password yang telah Anda buat.</p>
                <p>Jika Anda memiliki pertanyaan atau masalah dengan akun Anda, silakan hubungi administrator.</p>
            </div>
            
            <div class="divider"></div>
            
            <div class="signature">
                <p style="margin-bottom: 0 !important">Salam hormat,</p>
                <p><strong>raadeveloperz Team</strong></p>
            </div>
        </div>
        
        <div class="footer">
            <div class="social-links">
                <!-- Replace with your actual social media links -->
                <a href="https://raadeveloperz.web.id" class="social-link">Website</a> |
                <a href="https://instagram.com/raadeveloperz" class="social-link">Instagram</a> |
                <a href="https://youtube.com/@DitzzTechID" class="social-link">Youtube</a>
            </div>
            <p>&copy; {{ date('Y') }} raadeveloperz. Seluruh hak cipta dilindungi.</p>
            <p>Jika Anda memiliki pertanyaan, silakan hubungi kami di <a href="mailto:raadeveloperz@gmail.com">raadeveloperz@gmail.com</a> | <a href="https://wa.me/+62895383107479">+62 895-3831-07479</a></p>
        </div>
    </div>
</body>
</html>