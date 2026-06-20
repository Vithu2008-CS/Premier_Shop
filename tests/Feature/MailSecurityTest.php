<?php

namespace Tests\Feature;

use App\Models\ContactMessage;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * Admin-composed mail must not carry raw HTML into the outbound email or the
 * stored "sent" copy. Str::markdown() is configured to escape html_input, so
 * markdown still renders but embedded tags (e.g. <script>) are neutralised.
 */
class MailSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_mail_escapes_raw_html_in_body(): void
    {
        Mail::fake();

        $adminRole = Role::create(['name' => 'admin', 'display_name' => 'Administrator', 'is_staff' => true]);
        $admin = User::factory()->create(['role_id' => $adminRole->id]);

        $this->actingAs($admin)->post(route('admin.mail.send'), [
            'to'      => ['customer@example.com'],
            'subject' => 'Hello',
            'message' => "Hi **there** <script>alert('xss')</script>",
        ])->assertStatus(302);

        $sent = ContactMessage::where('folder', 'sent')->latest('id')->first();
        $this->assertNotNull($sent);

        // Markdown still rendered (bold), but the script tag is escaped, not raw.
        $this->assertStringContainsString('<strong>there</strong>', $sent->message);
        $this->assertStringNotContainsString("<script>alert('xss')</script>", $sent->message);
        $this->assertStringContainsString('&lt;script&gt;', $sent->message);
    }
}
