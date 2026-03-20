<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index()
    {
        return view('contact');
    }

    public function send(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:150',
            'phone' => 'nullable|string|max:20',
            'subject' => 'required|string|max:200',
            'message' => 'required|string|max:2000',
        ]);

        ContactMessage::create($validated);

        return redirect()->route('contact')->with('success', 'Thank you for your message! We\'ll get back to you shortly.');
    }
}
