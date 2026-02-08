<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        .card { border: 1px solid #e5e7eb; padding: 20px; border-radius: 8px; font-family: sans-serif; }
        .btn { background-color: #570df8; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; }
        .title { color: #1f2937; font-size: 18px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="card">
        <h2 class="title">Olá, {{ $user->name }}!</h2>
        <p>Boas notícias! O livro que estavas à espera já está disponível para requisição.</p>
        
        <div style="margin: 20px 0;">
            <strong>Livro:</strong> {{ $book->name }} <br>
            <strong>ISBN:</strong> {{ $book->isbn }}
        </div>

        <a href="{{ config('app.url') }}/books/{{ $book->id }}" class="btn">
            Reservar Agora
        </a>

        <p style="margin-top: 20px; font-size: 12px; color: #6b7280;">
            Inovcorp Library - Sistema Automático de Alertas
        </p>
    </div>
</body>
</html>