<?php

namespace App\Http\Controllers;

use App\Enums\ContactCategoryEnum;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class ContactController extends Controller
{
    public function create()
    {
        return view('contact', [
            'categories' => ContactCategoryEnum::options()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'category' => ['required', Rule::enum(ContactCategoryEnum::class)],
            'message' => 'required|string|max:5000',
            'privacy' => 'required|accepted',
        ], [
            'name.required' => '이름을 입력해 주세요.',
            'email.required' => '이메일을 입력해 주세요.',
            'email.email' => '올바른 이메일 형식을 입력해 주세요.',
            'subject.required' => '제목을 입력해 주세요.',
            'message.required' => '문의 내용을 입력해 주세요.',
            'message.max' => '문의 내용은 5000자를 초과할 수 없습니다.',
            'privacy.required' => '개인정보 수집 및 이용에 동의해 주세요.',
        ]);

        $contact = Contact::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'subject' => $validated['subject'],
            'category' => $validated['category'],
            'message' => $validated['message'],
        ]);

        try {
            Mail::send('emails.contact', [
                'contact' => $contact
            ], function ($message) use ($contact) {
                $message->to(config('mail.admin_email', 'admin@booklog.com'))
                        ->subject('[북로그 문의] ' . $contact->subject);
            });
        } catch (\Exception $e) {
            \Log::error('Contact email sending failed: ' . $e->getMessage());
        }

        return redirect()
            ->route('contact.create')
            ->with('success', '문의가 성공적으로 전송되었습니다. 빠른 시일 내에 답변드리겠습니다.');
    }
}
