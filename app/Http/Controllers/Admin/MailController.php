<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\NewsletterSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class MailController extends Controller
{
    /**
     * Display the inbox (Contact Messages).
     */
    public function inbox()
    {
        $messages = ContactMessage::orderBy('created_at', 'desc')->paginate(15);
        $unreadCount = ContactMessage::where('is_read', false)->count();
        
        return view('admin.mail.inbox', compact('messages', 'unreadCount'));
    }

    /**
     * Display a specific message.
     */
    public function read($id)
    {
        $message = ContactMessage::findOrFail($id);
        
        // Mark as read if it wasn't
        if (!$message->is_read) {
            $message->update(['is_read' => true]);
        }
        
        $unreadCount = ContactMessage::where('is_read', false)->count();
        
        return view('admin.mail.read', compact('message', 'unreadCount'));
    }

    /**
     * Show the compose form.
     */
    public function compose(Request $request)
    {
        $to = $request->get('to', '');
        $subject = $request->get('subject', '');
        $unreadCount = ContactMessage::where('is_read', false)->count();
        
        return view('admin.mail.compose', compact('to', 'subject', 'unreadCount'));
    }

    /**
     * Handle sending a message.
     */
    public function send(Request $request)
    {
        $request->validate([
            'to' => 'required',
            'subject' => 'required|string|max:255',
            'message' => 'required',
        ]);

        $recipients = $request->to;
        if (!is_array($recipients)) {
            $recipients = explode(',', $recipients);
        }

        $emails = [];
        foreach ($recipients as $recipient) {
            if ($recipient === 'newsletter') {
                $subscriberEmails = NewsletterSubscription::pluck('email')->toArray();
                $emails = array_merge($emails, $subscriberEmails);
            } else {
                $emails[] = trim($recipient);
            }
        }

        $emails = array_unique($emails);

        foreach ($emails as $email) {
            Mail::to($email)->send(new \App\Mail\AdminCustomMail($request->subject, $request->message));
        }

        \Log::info("Admin mail sent successfully to recipients: " . implode(', ', $emails));
        
        return redirect()->route('admin.mail.inbox')->with('success', 'Message sent successfully to ' . count($emails) . ' recipient(s)!');
    }

    /**
     * Delete a message.
     */
    public function destroy($id)
    {
        $message = ContactMessage::findOrFail($id);
        $message->delete();
        
        return redirect()->route('admin.mail.inbox')->with('success', 'Message deleted successfully!');
    }
}
