<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContentSecurityPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_csp_header_uses_nonce_and_drops_unsafe_inline_for_scripts(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $csp = $response->headers->get('Content-Security-Policy');

        $this->assertNotNull($csp);

        preg_match('/script-src ([^;]+);/', $csp, $scriptSrc);
        $this->assertNotEmpty($scriptSrc, 'CSP must define script-src');
        $this->assertMatchesRegularExpression("/'nonce-[A-Za-z0-9+\/=]+'/", $scriptSrc[1]);
        $this->assertStringNotContainsString('unsafe-inline', $scriptSrc[1]);
        $this->assertStringNotContainsString('unsafe-eval', $scriptSrc[1]);
    }

    public function test_inline_scripts_carry_the_csp_nonce(): void
    {
        $response = $this->get('/');

        $csp = $response->headers->get('Content-Security-Policy');
        preg_match("/'nonce-([^']+)'/", $csp, $m);
        $nonce = $m[1] ?? null;

        $this->assertNotNull($nonce, 'CSP header must contain a nonce');
        $response->assertSee('nonce="'.$nonce.'"', false);
    }

    public function test_home_page_has_no_inline_event_handlers(): void
    {
        $html = $this->get('/')->getContent();

        $this->assertDoesNotMatchRegularExpression(
            '/\son(click|change|submit|error|load|input|keyup)\s*=\s*["\']/i',
            $html,
            'Inline event handlers are blocked by the nonce CSP — use csp-shim data attributes instead.'
        );
    }
}
