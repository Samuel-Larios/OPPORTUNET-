<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class BookOrder extends Model { protected $fillable=['reference','book_id','user_id','amount','status','provider_transaction_id','checkout_url','unlock_code','secured_document_path','paid_at','delivered_at']; protected function casts(): array { return ['paid_at'=>'datetime','delivered_at'=>'datetime']; } public function getRouteKeyName(): string{return 'reference';} public function book(): BelongsTo{return $this->belongsTo(Book::class);} public function user(): BelongsTo{return $this->belongsTo(User::class);} }
