<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ContactCategoryEnum;
use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        $query = Contact::query()->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('subject', 'like', '%' . $request->search . '%');
            });
        }

        $contacts = $query->paginate(20);
        $categories = ContactCategoryEnum::options();

        return view('admin.contacts.index', compact('contacts', 'categories'));
    }

    public function show(Contact $contact)
    {
        return view('admin.contacts.show', compact('contact'));
    }

    public function update(Request $request, Contact $contact)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,replied,closed',
            'admin_reply' => 'nullable|string|max:5000',
        ]);

        $contact->update([
            'status' => $validated['status'],
            'admin_reply' => $validated['admin_reply'],
            'replied_at' => $validated['status'] === 'replied' ? now() : $contact->replied_at,
        ]);

        if ($validated['status'] === 'replied' && $validated['admin_reply']) {
            try {
                Mail::send('emails.contact-reply', [
                    'contact' => $contact
                ], function ($message) use ($contact) {
                    $message->to($contact->email)
                            ->subject('Re: ' . $contact->subject);
                });
            } catch (\Exception $e) {
                \Log::error('Contact reply email sending failed: ' . $e->getMessage());
            }
        }

        return redirect()
            ->route('admin.contacts.show', $contact)
            ->with('success', '문의가 성공적으로 업데이트되었습니다.');
    }

    public function destroy(Contact $contact)
    {
        $contact->delete();

        return redirect()
            ->route('admin.contacts.index')
            ->with('success', '문의가 삭제되었습니다.');
    }
}