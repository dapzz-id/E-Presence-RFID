<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kode Verifikasi</title>
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
        
        .code-container {
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 20px;
            margin: 30px 0;
            text-align: center;
        }
        
        .code {
            font-size: 32px;
            font-weight: 700;
            letter-spacing: 8px;
            color: #2c3e50;
            padding: 10px;
            background-color: #ffffff;
            border-radius: 6px;
            border: 1px dashed #dee2e6;
            display: inline-block;
            margin: 10px 0;
        }
        
        .warning {
            background-color: #fff8e1;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            font-size: 14px;
            color: #856404;
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
            
            .code {
                font-size: 28px;
                letter-spacing: 6px;
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
            <h1>Kode Verifikasi Akun Siswa</h1>
            
            <p>Halo,</p>
            
            <p>Terima kasih telah mendaftar. Untuk menyelesaikan proses pembuatan akun siswa, silakan gunakan kode verifikasi berikut:</p>
            
            <div class="code-container">
                <div class="code">{{ $code }}</div>
                <p>Kode ini berlaku selama <strong>{{ $expires_in }}</strong></p>
            </div>
            
            <div class="warning">
                <strong>Perhatian:</strong> Jangan berikan kode ini kepada siapapun. Tim kami tidak akan pernah meminta kode verifikasi Anda.
            </div>
            
            <p>Jika Anda tidak merasa meminta kode verifikasi ini, silakan abaikan email ini atau hubungi tim dukungan kami.</p>
            
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