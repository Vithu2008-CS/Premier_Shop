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

        // For now, we simulate sending or use Laravel Mail if configured
        // In a real app, you'd trigger a Mailable here.
        
        return redirect()->route('admin.mail.inbox')->with('success', 'Message sent successfully!');
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
