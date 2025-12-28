<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f7fa;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        .email-header {
            background: linear-gradient(135deg, #696cff 0%, #3f42ef 100%);
            padding: 40px 30px;
            text-align: center;
        }
        .email-header img {
            width: 80px;
            height: 80px;
            margin-bottom: 15px;
        }
        .email-header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        }
        .email-body {
            padding: 40px 30px;
            color: #333333;
            line-height: 1.6;
        }
        .email-body h2 {
            color: #696cff;
            font-size: 22px;
            margin-bottom: 20px;
        }
        .email-body p {
            margin-bottom: 15px;
            font-size: 15px;
        }
        .reset-button {
            display: inline-block;
            margin: 30px 0;
            padding: 15px 40px;
            background: linear-gradient(135deg, #696cff 0%, #3f42ef 100%);
            color: #ffffff;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            transition: transform 0.2s;
        }
        .reset-button:hover {
            transform: translateY(-2px);
        }
        .info-box {
            background-color: #f8f9fa;
            border-left: 4px solid #696cff;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .email-footer {
            background-color: #f8f9fa;
            padding: 25px 30px;
            text-align: center;
            color: #6c757d;
            font-size: 13px;
        }
        .email-footer a {
            color: #696cff;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1>🐄 Smart Dairy</h1>
            <p style="color: #ffffff; margin: 10px 0 0 0; font-size: 16px;">Farm Management System</p>
        </div>
        
        <div class="email-body">
            <h2>Password Reset Request</h2>
            
            <p>Hello <strong>{{ $user->name }}</strong>,</p>
            
            <p>We received a request to reset your password for your Smart Dairy account. If you didn't make this request, you can safely ignore this email.</p>
            
            <div style="text-align: center;">
                <a href="{{ $resetLink }}" class="reset-button">Reset Your Password</a>
            </div>
            
            <div class="info-box">
                <strong>⏰ Important:</strong> This password reset link will expire in <strong>1 hour</strong> for security reasons.
            </div>
            
            <p>If the button above doesn't work, copy and paste this link into your browser:</p>
            <p style="word-break: break-all; color: #696cff; font-size: 14px;">{{ $resetLink }}</p>
            
            <p style="margin-top: 30px;">If you have any questions or need assistance, please contact our support team.</p>
            
            <p style="margin-top: 20px;">
                Best regards,<br>
                <strong>Smart Dairy Team</strong>
            </p>
        </div>
        
        <div class="email-footer">
            <p>© {{ date('Y') }} Smart Dairy Farm Management System. All rights reserved.</p>
            <p>This is an automated email. Please do not reply to this message.</p>
        </div>
    </div>
</body>
</html>
