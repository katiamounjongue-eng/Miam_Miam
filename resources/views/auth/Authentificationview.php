<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Authentification</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        form { margin-bottom: 20px; padding: 20px; border: 1px solid #ccc; width: 300px; }
        input { display: block; margin-bottom: 10px; width: 100%; padding: 5px; }
        button { padding: 5px 10px; }
        .message { margin-bottom: 20px; color: green; }
        .error { color: red; }
    </style>
</head>
<body>

<h2>Test Authentification</h2>

@if(session('message'))
    <div class="message">{{ session('message') }}</div>
@endif

@if(session('error'))
    <div class="error">{{ session('error') }}</div>
@endif

<h3>Inscription</h3>
<form method="POST" action="{{ route('auth.register') }}">
    @csrf
    <input type="text" name="name" placeholder="Nom" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Mot de passe" required>
    <input type="password" name="password_confirmation" placeholder="Confirmer le mot de passe" required>
    <button type="submit">S’inscrire</button>
</form>

<h3>Connexion</h3>
<form method="POST" action="{{ route('auth.login') }}">
    @csrf
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Mot de passe" required>
    <button type="submit">Se connecter</button>
</form>

<h3>Déconnexion</h3>
<form method="POST" action="{{ route('auth.logout') }}">
    @csrf
    <button type="submit">Se déconnecter</button>
</form>

</body>
</html>
