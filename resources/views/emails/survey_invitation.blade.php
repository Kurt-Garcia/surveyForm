<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Survey Invitation</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #4e73df;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            padding: 20px;
            border: 1px solid #ddd;
            border-top: none;
            border-radius: 0 0 5px 5px;
        }
        .button {
            display: inline-block;
            background-color: #4e73df;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            font-size: 12px;
            color: #666;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>You're Invited to Complete a Survey</h2>
    </div>
    
    <div class="content">
        <p>Dear {{ $customer_name }},</p>
        
        <p>You have been invited to participate in the following survey:</p>
        
        <h3>{{ $survey_title }}</h3>
        
        <p>Your feedback is important to us. Please click the button below to start the survey:</p>
        
        <div style="text-align: center;">
            <a href="{{ $survey_url }}" class="button">Start Survey</a>
        </div>
        
        <p>If you're having trouble with the button above, you can copy and paste the following link into your browser:</p>
        
        <p style="word-break: break-all;">{{ $survey_url }}</p>
        
        <p>Thank you for your participation!</p>
    </div>
    
    <div class="footer">
        <p>This is an automated email. Please do not reply directly to this message.</p>
    </div>
</body>
</html>
