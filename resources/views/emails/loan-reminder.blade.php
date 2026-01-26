<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Lembrete de Empréstimo</title>
</head>
<body>
    <h1>Olá {{ $user->name }},</h1>

    <p>O seu empréstimo do livro <strong>{{ $loan->book->name }}</strong> vence amanhã!</p>

    <p>Data prevista de entrega: {{ $loan->expected_return_date->format('d/m/Y') }}</p>

    <p>Por favor, assegure-se de devolver o livro a tempo.</p>

    <p>Obrigado, <br> Biblioteca</p>
</body>
</html>
