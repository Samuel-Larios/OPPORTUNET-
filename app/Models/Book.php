<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Book extends Model { protected $fillable=['title','slug','description','author','published_on','price','currency','cover_path','document_path','is_published']; protected function casts(): array { return ['published_on'=>'date','is_published'=>'boolean']; } public function orders(): HasMany { return $this->hasMany(BookOrder::class); } }
