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
        $messages = ContactMessage::where('folder', 'inbox')
            ->where('is_trash', false)
            ->orderBy('created_at', 'desc')->paginate(15);
        $unreadCount = ContactMessage::where('is_read', false)->where('folder', 'inbox')->count();
        
        $pageTitle = 'Inbox';
        $pageIcon = 'inbox';
        return view('admin.mail.inbox', compact('messages', 'unreadCount', 'pageTitle', 'pageIcon'));
    }

    public function sent()
    {
        $messages = ContactMessage::where('folder', 'sent')
            ->where('is_trash', false)
            ->orderBy('created_at', 'desc')->paginate(15);
        $unreadCount = ContactMessage::where('is_read', false)->where('folder', 'inbox')->count();
        
        $pageTitle = 'Sent Mail';
        $pageIcon = 'mail';
        return view('admin.mail.inbox', compact('messages', 'unreadCount', 'pageTitle', 'pageIcon'));
    }

    public function important()
    {
        $messages = ContactMessage::where('is_starred', true)
            ->where('is_trash', false)
            ->orderBy('created_at', 'desc')->paginate(15);
        $unreadCount = ContactMessage::where('is_read', false)->where('folder', 'inbox')->count();
        
        $pageTitle = 'Important';
        $pageIcon = 'briefcase';
        return view('admin.mail.inbox', compact('messages', 'unreadCount', 'pageTitle', 'pageIcon'));
    }

    public function drafts()
    {
        $messages = ContactMessage::where('folder', 'draft')
            ->where('is_trash', false)
            ->orderBy('created_at', 'desc')->paginate(15);
        $unreadCount = ContactMessage::where('is_read', false)->where('folder', 'inbox')->count();
        
        $pageTitle = 'Drafts';
        $pageIcon = 'file';
        return view('admin.mail.inbox', compact('messages', 'unreadCount', 'pageTitle', 'pageIcon'));
    }

    public function trash()
    {
        $messages = ContactMessage::where('is_trash', true)
            ->orderBy('created_at', 'desc')->paginate(15);
        $unreadCount = ContactMessage::where('is_read', false)->where('folder', 'inbox')->count();
        
        $pageTitle = 'Trash';
        $pageIcon = 'trash';
        return view('admin.mail.inbox', compact('messages', 'unreadCount', 'pageTitle', 'pageIcon'));
    }

    public function tags($tag = null)
    {
        $query = ContactMessage::where('is_trash', false);
        if ($tag) {
            $query->where('tags', 'like', "%{$tag}%");
        } else {
            $query->whereNotNull('tags');
        }
        $messages = $query->orderBy('created_at', 'desc')->paginate(15);
        $unreadCount = ContactMessage::where('is_read', false)->where('folder', 'inbox')->count();
        
        $pageTitle = $tag ? ucfirst($tag) : 'Tags';
        $pageIcon = 'tag';
        return view('admin.mail.inbox', compact('messages', 'unreadCount', 'pageTitle', 'pageIcon'));
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

        $isDraft = $request->has('save_draft');

        if (!$isDraft) {
            foreach ($emails as $email) {
                Mail::to($email)->send(new \App\Mail\AdminCustomMail($request->subject, $request->message));
            }
            \Log::info("Admin mail sent successfully to recipients: " . implode(', ', $emails));
        }

        // Save to DB
        ContactMessage::create([
            'name' => 'Admin (' . auth()->user()->name . ')',
            'email' => implode(', ', $emails), // store all recipients in email column
            'subject' => $request->subject,
            'message' => $request->message,
            'is_read' => true, // Outbound messages are inherently read
            'folder' => $isDraft ? 'draft' : 'sent',
        ]);
        
        if ($isDraft) {
            return redirect()->route('admin.mail.drafts')->with('success', 'Draft saved successfully!');
        }
        return redirect()->route('admin.mail.sent')->with('success', 'Message sent successfully to ' . count($emails) . ' recipient(s)!');
    }

    public function toggleStar($id)
    {
        $message = ContactMessage::findOrFail($id);
        $message->is_starred = !$message->is_starred;
        $message->save();
        
        return back()->with('success', 'Message marked as ' . ($message->is_starred ? 'important' : 'unimportant') . '.');
    }

    /**
     * Delete a message.
     */
    public function destroy($id)
    {
        $message = ContactMessage::findOrFail($id);
        if ($message->is_trash) {
            $message->delete();
            return redirect()->route('admin.mail.trash')->with('success', 'Message permanently deleted!');
        } else {
            $message->is_trash = true;
            $message->save();
            return back()->with('success', 'Message moved to trash!');
        }
    }
}
