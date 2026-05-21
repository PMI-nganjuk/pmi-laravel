<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that security headers are injected in HTTP responses.
     */
    public function test_security_headers_are_present_in_responses(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-XSS-Protection', '1; mode=block');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->assertHeader('Content-Security-Policy');
        
        $csp = $response->headers->get('Content-Security-Policy');
        $this->assertStringContainsString("default-src 'self'", $csp);
    }

    /**
     * Test that login attempts are rate limited after 5 failures.
     */
    public function test_login_endpoint_is_rate_limited(): void
    {
        // Hit the login route 5 times (which is the limit per minute)
        for ($i = 0; $i < 5; $i++) {
            $response = $this->post('/login', [
                'email' => 'hacker@pmi-nganjuk.or.id',
                'password' => 'wrong-password',
            ]);
            
            // Should get redirected back with validation errors (session error)
            $response->assertStatus(302);
            $response->assertSessionHasErrors('email');
        }

        // The 6th attempt should be blocked with 429 Too Many Requests
        $response = $this->post('/login', [
            'email' => 'hacker@pmi-nganjuk.or.id',
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(429);
    }
}
