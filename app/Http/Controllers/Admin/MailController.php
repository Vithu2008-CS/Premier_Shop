<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\NewsletterSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

/**
 * Admin mail centre: inbox (contact form messages), sent, drafts, trash, starred, tags.
 * All mail records are ContactMessage rows distinguished by the `folder` column.
 * Outbound mail is sent via Laravel Mail and a copy is saved as a 'sent' ContactMessage.
 */
class MailController extends Controller
{
    public function inbox()
    {
        $messages = ContactMessage::where('folder', 'inbox')
            ->where('is_trash', false)
            ->orderBy('created_at', 'desc')->paginate(15);
        $unreadCount = ContactMessage::where('is_read', false)->where('folder', 'inbox')->count();

        return view('admin.mail.inbox', [
            'messages' => $messages,
            'unreadCount' => $unreadCount,
            'pageTitle' => 'Inbox',
            'pageIcon' => 'inbox',
        ]);
    }

    public function sent()
    {
        $messages = ContactMessage::where('folder', 'sent')
            ->where('is_trash', false)
            ->orderBy('created_at', 'desc')->paginate(15);
        $unreadCount = ContactMessage::where('is_read', false)->where('folder', 'inbox')->count();

        return view('admin.mail.inbox', [
            'messages' => $messages,
            'unreadCount' => $unreadCount,
            'pageTitle' => 'Sent Mail',
            'pageIcon' => 'mail',
        ]);
    }

    public function important()
    {
        $messages = ContactMessage::where('is_starred', true)
            ->where('is_trash', false)
            ->orderBy('created_at', 'desc')->paginate(15);
        $unreadCount = ContactMessage::where('is_read', false)->where('folder', 'inbox')->count();

        return view('admin.mail.inbox', [
            'messages' => $messages,
            'unreadCount' => $unreadCount,
            'pageTitle' => 'Important',
            'pageIcon' => 'briefcase',
        ]);
    }

    public function drafts()
    {
        $messages = ContactMessage::where('folder', 'draft')
            ->where('is_trash', false)
            ->orderBy('created_at', 'desc')->paginate(15);
        $unreadCount = ContactMessage::where('is_read', false)->where('folder', 'inbox')->count();

        return view('admin.mail.inbox', [
            'messages' => $messages,
            'unreadCount' => $unreadCount,
            'pageTitle' => 'Drafts',
            'pageIcon' => 'file',
        ]);
    }

    public function trash()
    {
        $messages = ContactMessage::where('is_trash', true)
            ->orderBy('created_at', 'desc')->paginate(15);
        $unreadCount = ContactMessage::where('is_read', false)->where('folder', 'inbox')->count();

        return view('admin.mail.inbox', [
            'messages' => $messages,
            'unreadCount' => $unreadCount,
            'pageTitle' => 'Trash',
            'pageIcon' => 'trash',
        ]);
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

        return view('admin.mail.inbox', [
            'messages' => $messages,
            'unreadCount' => $unreadCount,
            'pageTitle' => $tag ? ucfirst($tag) : 'Tags',
            'pageIcon' => 'tag',
        ]);
    }

    public function search(Request $request)
    {
        $q = trim($request->get('q', ''));
        $messages = ContactMessage::where('is_trash', false)
            ->where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                    ->orWhere('subject', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('message', 'like', "%{$q}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $unreadCount = ContactMessage::where('is_read', false)->where('folder', 'inbox')->count();

        return view('admin.mail.inbox', [
            'messages' => $messages,
            'unreadCount' => $unreadCount,
            'pageTitle' => 'Search: '.e($q),
            'pageIcon' => 'search',
        ]);
    }

    public function read($id)
    {
        $message = ContactMessage::findOrFail($id);

        if (! $message->is_read) {
            $message->update(['is_read' => true]);
        }

        $unreadCount = ContactMessage::where('is_read', false)->count();

        return view('admin.mail.read', compact('message', 'unreadCount'));
    }

    public function compose(Request $request)
    {
        $to = $request->get('to', '');
        $subject = $request->get('subject', '');
        $body = '';
        $draftId = null;

        if ($draftIdParam = $request->get('draft_id')) {
            $draft = ContactMessage::where('folder', 'draft')->findOrFail($draftIdParam);
            $to = $draft->email ?? '';
            $subject = $draft->subject;
            $body = $draft->message;
            $draftId = $draft->id;
        }

        $unreadCount = ContactMessage::where('is_read', false)->count();

        return view('admin.mail.compose', compact('to', 'subject', 'body', 'draftId', 'unreadCount'));
    }

    public function send(Request $request)
    {
        $isDraft = $request->has('save_draft');

        $request->validate([
            'to' => $isDraft ? 'nullable|array' : 'required|array|min:1',
            'to.*' => 'string|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $recipients = $request->to ?? [];
        $emails = [];
        foreach ($recipients as $recipient) {
            if ($recipient === 'newsletter') {
                $subscriberEmails = NewsletterSubscription::pluck('email')->toArray();
                $emails = array_merge($emails, $subscriberEmails);
            } else {
                $email = trim($recipient);
                if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    return back()->withErrors(['to' => "Invalid email address: {$email}"])->withInput();
                }
                $emails[] = $email;
            }
        }
        $emails = array_unique(array_filter($emails));

        // Escape any raw HTML in the admin's input so markdown formatting still
        // renders but embedded tags (e.g. <script>) cannot be injected into the
        // outbound customer email or the stored "sent" copy.
        $parsedMessage = \Illuminate\Support\Str::markdown($request->message, [
            'html_input' => 'escape',
            'allow_unsafe_links' => false,
        ]);
        $htmlContent = view('emails.admin_custom', ['mailMessage' => $parsedMessage])->render();

        if (! $isDraft) {
            if (empty($emails)) {
                return back()->withErrors(['to' => 'Add at least one recipient.'])->withInput();
            }
            foreach ($emails as $email) {
                Mail::to($email)->send(new \App\Mail\AdminCustomMail($request->subject, $parsedMessage));
            }
            \Log::info('Admin mail sent to: '.implode(', ', $emails));
        }

        // If editing a draft, delete the old one
        if ($request->filled('draft_id')) {
            ContactMessage::where('folder', 'draft')->where('id', $request->draft_id)->delete();
        }

        ContactMessage::create([
            'name' => 'Admin ('.auth()->user()->name.')',
            'email' => implode(', ', $emails),
            'subject' => $request->subject,
            'message' => $isDraft ? $request->message : $htmlContent,
            'is_read' => true,
            'folder' => $isDraft ? 'draft' : 'sent',
        ]);

        if ($isDraft) {
            return redirect()->route('admin.mail.drafts')->with('success', 'Draft saved.');
        }

        return redirect()->route('admin.mail.sent')
            ->with('success', 'Message sent to '.count($emails).' recipient(s).');
    }

    public function toggleStar($id)
    {
        $message = ContactMessage::findOrFail($id);
        $message->is_starred = ! $message->is_starred;
        $message->save();

        return back()->with('success', 'Message '.($message->is_starred ? 'starred' : 'unstarred').'.');
    }

    public function markUnread($id)
    {
        ContactMessage::findOrFail($id)->update(['is_read' => false]);

        return back()->with('success', 'Marked as unread.');
    }

    public function restore($id)
    {
        ContactMessage::findOrFail($id)->update(['is_trash' => false, 'folder' => 'inbox']);

        return back()->with('success', 'Message restored to inbox.');
    }

    public function destroy($id)
    {
        $message = ContactMessage::findOrFail($id);
        if ($message->is_trash) {
            $message->delete();

            return redirect()->route('admin.mail.trash')->with('success', 'Message permanently deleted.');
        } else {
            $message->update(['is_trash' => true]);

            return back()->with('success', 'Message moved to trash.');
        }
    }
}
