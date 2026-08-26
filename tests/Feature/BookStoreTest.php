<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\BookOrder;
use App\Models\User;
use App\Services\BookPdfDeliveryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BookStoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_published_book_is_public_and_each_order_gets_an_encrypted_pdf(): void
    {
        Storage::fake('local');
        $source = new \FPDF();
        $source->AddPage();
        $source->SetFont('Arial', '', 14);
        $source->Cell(40, 10, 'Livre de test');
        Storage::disk('local')->put('books/source/test.pdf', $source->Output('S'));

        $book = Book::query()->create([
            'title' => 'Livre de test', 'slug' => 'livre-de-test', 'description' => 'Description du livre.',
            'author' => 'Auteur test', 'published_on' => now()->toDateString(), 'price' => 2500,
            'document_path' => 'books/source/test.pdf', 'is_published' => true,
        ]);
        $user = User::factory()->create();
        $order = BookOrder::query()->create([
            'reference' => 'book-test-reference', 'book_id' => $book->id, 'user_id' => $user->id,
            'amount' => 2500, 'status' => 'paid', 'unlock_code' => 'CODE-UNIQUE-123',
        ]);

        $this->get(route('books.index'))->assertOk()->assertSee('Livre de test');
        $this->get(route('books.show', $book))->assertOk()->assertSee('Auteur test');

        $path = app(BookPdfDeliveryService::class)->secure($order->fresh('book'));

        Storage::disk('local')->assertExists($path);
        $securedPdf = Storage::disk('local')->get($path);
        $this->assertStringStartsWith('%PDF', $securedPdf);
        $this->assertStringContainsString('/Encrypt', $securedPdf);
        $this->assertSame($path, app(BookPdfDeliveryService::class)->secure($order->fresh('book')));
    }
}
