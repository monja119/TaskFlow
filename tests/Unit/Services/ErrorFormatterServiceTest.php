<?php

namespace Tests\Unit\Services;

use App\Services\Error\ErrorFormatterService;
use Exception;
use Tests\TestCase;

class ErrorFormatterServiceTest extends TestCase
{
    private ErrorFormatterService $formatter;

    protected function setUp(): void
    {
        parent::setUp();
        $this->formatter = new ErrorFormatterService;
    }

    /** @test */
    public function it_formats_404_error_correctly()
    {
        $error = $this->formatter->format(404);

        $this->assertEquals(404, $error['status']);
        $this->assertEquals('Page Non Trouvée', $error['title']);
        $this->assertEquals('🔍', $error['icon']);
        $this->assertNotEmpty($error['description']);
    }

    /** @test */
    public function it_formats_401_error_correctly()
    {
        $error = $this->formatter->format(401);

        $this->assertEquals(401, $error['status']);
        $this->assertEquals('Non Authentifié', $error['title']);
        $this->assertEquals('🔐', $error['icon']);
    }

    /** @test */
    public function it_formats_403_error_correctly()
    {
        $error = $this->formatter->format(403);

        $this->assertEquals(403, $error['status']);
        $this->assertEquals('Accès Refusé', $error['title']);
        $this->assertEquals('🚫', $error['icon']);
    }

    /** @test */
    public function it_formats_500_error_correctly()
    {
        $error = $this->formatter->format(500);

        $this->assertEquals(500, $error['status']);
        $this->assertEquals('Erreur Interne du Serveur', $error['title']);
        $this->assertEquals('⚡', $error['icon']);
    }

    /** @test */
    public function it_includes_exception_message_when_provided()
    {
        $exception = new Exception('Test error message');
        $error = $this->formatter->format(500, $exception);

        $this->assertEquals('Test error message', $error['message']);
    }

    /** @test */
    public function it_includes_debug_info_in_debug_mode()
    {
        config(['app.debug' => true]);

        $exception = new Exception('Test exception');
        $error = $this->formatter->format(500, $exception);

        $this->assertNotNull($error['debug']);
        $this->assertArrayHasKey('message', $error['debug']);
        $this->assertArrayHasKey('file', $error['debug']);
        $this->assertArrayHasKey('line', $error['debug']);
        $this->assertArrayHasKey('trace', $error['debug']);
    }

    /** @test */
    public function it_excludes_debug_info_in_production_mode()
    {
        config(['app.debug' => false]);

        $exception = new Exception('Test exception');
        $error = $this->formatter->format(500, $exception);

        $this->assertNull($error['debug']);
    }

    /** @test */
    public function it_provides_default_message_for_unknown_status_codes()
    {
        $error = $this->formatter->format(418); // I'm a teapot

        $this->assertEquals(418, $error['status']);
        $this->assertNotEmpty($error['title']);
        $this->assertNotEmpty($error['description']);
        $this->assertNotEmpty($error['icon']);
    }

    /** @test */
    public function it_handles_server_errors_properly()
    {
        $error = $this->formatter->format(502);

        $this->assertEquals(502, $error['status']);
        $this->assertEquals('Mauvaise Passerelle', $error['title']);
    }

    /** @test */
    public function it_handles_validation_errors()
    {
        $error = $this->formatter->format(422);

        $this->assertEquals(422, $error['status']);
        $this->assertEquals('Données Invalides', $error['title']);
    }

    /** @test */
    public function it_handles_rate_limiting_errors()
    {
        $error = $this->formatter->format(429);

        $this->assertEquals(429, $error['status']);
        $this->assertEquals('Trop de Requêtes', $error['title']);
    }

    /** @test */
    public function it_sanitizes_file_paths_in_debug_info()
    {
        config(['app.debug' => true]);

        $exception = new Exception('Test');
        $error = $this->formatter->format(500, $exception);

        // Vérifier que les chemins absolus ont été supprimés
        $this->assertStringNotContainsString(base_path(), $error['debug']['file']);
    }
}
