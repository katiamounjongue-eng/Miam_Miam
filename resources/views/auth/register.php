<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
</head>
<body>
    <h1>Inscription</h1>
    <form action="{{ route('register') }}" method="POST">
        @csrf
        <label for="user_type_id">Type d'utilisateur:</label>
        <input type="text" name="user_type_id" required><br>

        <label for="first_name">Prénom:</label>
        <input type="text" name="first_name" required><br>

        <label for="last_name">Nom:</label>
        <input type="text" name="last_name" required><br>

        <label for="user_password">Mot de passe:</label>
        <input type="password" name="user_password" required><br>

        <label for="mail_adress">Email:</label>
        <input type="email" name="mail_adress"><br>

        <label for="phone_number">Numéro de téléphone:</label>
        <input type="text" name="phone_number"><br>

        <label for="inscription_date">Date d'inscription:</label>
        <input type="date" name="inscription_date" required><br>

        <label for="account_statut">Statut du compte:</label>
        <input type="checkbox" name="account_statut" value="1"><br>

        <button type="submit">S'inscrire</button>
    </form>
</body>
</html>