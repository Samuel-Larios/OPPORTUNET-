<?php

namespace Tests\Feature;

use Livewire\Mechanisms\HandleRequests\EndpointResolver;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Tests\TestCase;

class LivewireCspTest extends TestCase
{
    public function test_livewire_csp_mode_is_enabled(): void
    {
        $this->assertTrue(config('livewire.csp_safe'));
    }

    public function test_livewire_serves_the_csp_safe_javascript_bundle(): void
    {
        $scriptPath = EndpointResolver::scriptPath(minified: ! config('app.debug'));

        $response = $this->get($scriptPath)->assertOk();
        $servedFile = realpath($response->baseResponse->getFile()->getPathname()) ?: $response->baseResponse->getFile()->getPathname();

        $this->assertInstanceOf(BinaryFileResponse::class, $response->baseResponse);
        $this->assertStringEndsWith(
            'vendor\\livewire\\livewire\\dist\\livewire.csp.js',
            str_replace('/', '\\', $servedFile)
        );
    }
}
