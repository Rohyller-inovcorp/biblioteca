<p>Olá Admin,</p>

<p>O cidadão <strong>{{ $review->user->name }}</strong> deixou um review para o livro <strong>{{ $review->book->name }}</strong>.</p>

<p>Comentário:</p>
<blockquote>{{ $review->comment }}</blockquote>

<p><a href="{{ route('reviews.index') }}">Ver reviews pendentes</a></p>
