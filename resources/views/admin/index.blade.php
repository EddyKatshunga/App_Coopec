<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    @vite('resources/css/app.css')
</head>
<body>
    <div>
        <h1>Bienvenue, {{ Auth::user()->getRoleNames() }} {{ Auth::user()->name }}</h1>
    </div>
    <a href="{{ route('transaction.depot') }}">Ajouter un depot</a>
    <a href="{{ route('transaction.retrait') }}">Ajouter un retrait</a>
    <a href="{{ route('membre.index') }}">Liste des membres</a>
    <a href="{{ route('agents.index') }}">Liste des agents</a>
    <a href="{{ route('transaction.index') }}">Liste des transactions</a>
</body>
</html>