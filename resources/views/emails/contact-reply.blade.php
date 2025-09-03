<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>문의 답변</title>
    <style>
        body {
            font-family: 'Malgun Gothic', 'Apple SD Gothic Neo', sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #e9ecef;
        }
        .header h1 {
            color: #2563eb;
            margin: 0;
            font-size: 24px;
        }
        .original-inquiry {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 6px;
            margin-bottom: 20px;
        }
        .reply-content {
            background: #e3f2fd;
            border: 1px solid #bbdefb;
            border-radius: 6px;
            padding: 20px;
            margin-top: 20px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            color: #6c757d;
            font-size: 14px;
        }
        .info-row {
            display: flex;
            margin-bottom: 10px;
        }
        .info-label {
            font-weight: bold;
            width: 80px;
            color: #495057;
        }
        .info-value {
            flex: 1;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📧 문의 답변</h1>
            <p>안녕하세요, {{ $contact->name }}님. 문의해 주신 내용에 대한 답변입니다.</p>
        </div>

        <div class="original-inquiry">
            <h3 style="margin-top: 0; color: #495057;">원본 문의 내용</h3>
            <div class="info-row">
                <div class="info-label">제목:</div>
                <div class="info-value">{{ $contact->subject }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">접수일:</div>
                <div class="info-value">{{ $contact->created_at->format('Y년 m월 d일 H:i') }}</div>
            </div>
            <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #dee2e6;">
                <div style="white-space: pre-wrap; line-height: 1.6; color: #495057;">{{ $contact->message }}</div>
            </div>
        </div>

        <div class="reply-content">
            <h3 style="margin-top: 0; color: #1976d2;">📝 관리자 답변</h3>
            <div style="white-space: pre-wrap; line-height: 1.8; color: #333;">{{ $contact->admin_reply }}</div>
            <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #bbdefb; text-align: right;">
                <small style="color: #1976d2;">답변일: {{ $contact->replied_at->format('Y년 m월 d일 H:i') }}</small>
            </div>
        </div>

        <div class="footer">
            <p>추가 문의사항이 있으시면 언제든지 연락해 주세요.</p>
            <p style="margin-top: 10px;">
                <strong>북로그 팀</strong><br>
                <a href="{{ config('app.url') }}" style="color: #2563eb; text-decoration: none;">{{ config('app.url') }}</a>
            </p>
        </div>
    </div>
</body>
</html>