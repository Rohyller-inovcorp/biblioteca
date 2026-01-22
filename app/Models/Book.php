<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Publisher;
use App\Models\Author;
class Book extends Model
{

    use HasFactory;
    protected $fillable = ['isbn', 'name', 'publisher_id', 'bibliography', 'cover_image', 'price'];
    protected $casts = ['bibliography' => 'encrypted', 'cover_image' => 'encrypted', 'price' => 'decimal:2',];
    public function publisher()
    {
        return $this->belongsTo(Publisher::class);
    }

    public function authors()
    {
        return $this->belongsToMany(Author::class);
    }
}
