<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>새로운 문의가 도착했습니다</title>
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
        .contact-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 6px;
            margin-bottom: 20px;
        }
        .info-row {
            display: flex;
            margin-bottom: 10px;
        }
        .info-label {
            font-weight: bold;
            width: 100px;
            color: #495057;
        }
        .info-value {
            flex: 1;
        }
        .message-content {
            background: white;
            border: 1px solid #dee2e6;
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
        .category-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .category-general { background: #e3f2fd; color: #1976d2; }
        .category-technical { background: #f3e5f5; color: #7b1fa2; }
        .category-partnership { background: #e8f5e8; color: #388e3c; }
        .category-bug { background: #ffebee; color: #d32f2f; }
        .category-other { background: #f5f5f5; color: #616161; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔔 새로운 문의가 도착했습니다</h1>
            <p>북로그 웹사이트에서 새로운 문의가 접수되었습니다.</p>
        </div>

        <div class="contact-info">
            <div class="info-row">
                <div class="info-label">이름:</div>
                <div class="info-value">{{ $contact->name }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">이메일:</div>
                <div class="info-value">{{ $contact->email }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">제목:</div>
                <div class="info-value">{{ $contact->subject }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">문의 유형:</div>
                <div class="info-value">
                    @if($contact->category)
                        @php
                            $categoryLabels = [
                                'general' => '일반 문의',
                                'technical' => '기술 문의', 
                                'partnership' => '제휴 문의',
                                'bug' => '버그 신고',
                                'other' => '기타'
                            ];
                            $label = $categoryLabels[$contact->category] ?? $contact->category;
                        @endphp
                        <span class="category-badge category-{{ $contact->category }}">{{ $label }}</span>
                    @else
                        <span class="category-badge category-other">미분류</span>
                    @endif
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">접수 시간:</div>
                <div class="info-value">{{ $contact->created_at->format('Y년 m월 d일 H:i') }}</div>
            </div>
        </div>

        <div class="message-content">
            <h3 style="margin-top: 0; color: #495057;">문의 내용:</h3>
            <div style="white-space: pre-wrap; line-height: 1.8;">{{ $contact->message }}</div>
        </div>

        <div class="footer">
            <p>이 메일은 북로그 문의 시스템에서 자동으로 발송되었습니다.</p>
            <p>문의에 대한 답변은 관리자 페이지에서 직접 처리해 주세요.</p>
        </div>
    </div>
</body>
</html>