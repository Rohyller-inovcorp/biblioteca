<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Confirmação de Empréstimo</title>
</head>
<body>
    <h1>Olá {{ $user->name }},</h1>

    <p>O seu empréstimo do livro <strong>{{ $loan->book->name }}</strong> foi registado com sucesso!</p>

    <p>Data do empréstimo: {{ $loan->loan_date->format('d/m/Y') }}</p>
    <p>Data prevista de entrega: {{ $loan->expected_return_date->format('d/m/Y') }}</p>

    @if($isAdmin ?? false)
        <p>Este email é para administrador.</p>
    @endif

    <p>Obrigado, <br> Biblioteca 2</p>
</body>
</html>
