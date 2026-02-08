<p>Olá {{ $review->user->name }},</p>

<p>O seu review do livro <strong>{{ $review->book->name }}</strong> foi atualizado.</p>

<p>Status: <strong>{{ ucfirst($review->status) }}</strong></p>

@if($review->status === 'rejected')
    <p>Motivo da rejeição: {{ $review->rejection_reason }}</p>
@endif

<p>Comentário:</p>
<blockquote>{{ $review->comment }}</blockquote>
