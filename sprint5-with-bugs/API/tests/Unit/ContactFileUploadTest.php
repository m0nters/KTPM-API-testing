<?php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class ContactFileUploadTest extends TestCase
{
    use RefreshDatabase;

    public function testPdfFileUploadShouldBeAllowed()
    {
        // Create a fake PDF file
        $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

        $response = $this->postJson('/contact', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'subject' => 'Test Subject', 
            'message' => 'Test message',
            'attachment' => $file
        ]);

        // Should accept PDF files
        $response->assertStatus(ResponseAlias::HTTP_OK);
        $response->assertJsonMissing(['error' => 'The file extension is incorrect, we only accept txt files.']);
    }

    public function testJpgFileUploadShouldBeAllowed()
    {
        // Create a fake JPG file
        $file = UploadedFile::fake()->image('image.jpg', 100, 100);

        $response = $this->postJson('/contact', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'subject' => 'Test Subject',
            'message' => 'Test message',
            'attachment' => $file
        ]);

        // Should accept JPG files
        $response->assertStatus(ResponseAlias::HTTP_OK);
        $response->assertJsonMissing(['error' => 'The file extension is incorrect, we only accept txt files.']);
    }

    public function testZeroKbFileShouldBeRejected()
    {
        // Create a fake file with 0KB
        $file = UploadedFile::fake()->create('empty.txt', 0);

        $response = $this->postJson('/contact', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'subject' => 'Test Subject',
            'message' => 'Test message',
            'attachment' => $file
        ]);

        // Should reject 0KB files
        $response->assertStatus(ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors('attachment');
    }

    public function testFileLargerThan500KbShouldBeRejected()
    {
        // Create a fake file larger than 500KB
        $file = UploadedFile::fake()->create('large.txt', 501);

        $response = $this->postJson('/contact', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'subject' => 'Test Subject',
            'message' => 'Test message',
            'attachment' => $file
        ]);

        // Should reject files larger than 500KB
        $response->assertStatus(ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonFragment(['File should be smaller than 500KB.']);
    }
}