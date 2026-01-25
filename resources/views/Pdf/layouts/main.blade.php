<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        header { text-align: center; margin-bottom: 15px; }
        footer { position: fixed; bottom: 0; font-size: 10px; width: 100%; text-align: center; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 5px; }
    </style>
</head>
<body>

<header>
    <h3>COOPÉRATIVE D’ÉPARGNE</h3>
    <p>Document officiel</p>
</header>

@yield('content')

<footer>
    Document généré le {{ now()->format('d/m/Y H:i') }}
</footer>

</body>
</html>
