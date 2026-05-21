# Projet E-Commerce Symfony

Chemin local du projet :

```text
C:\Users\oujdq\Documents\Codex\2026-05-21\hello\ecommerce-symfony
```

## Lancement

```powershell
cd C:\Users\oujdq\Documents\Codex\2026-05-21\hello\ecommerce-symfony
composer install
php bin\console doctrine:migrations:migrate
php bin\console app:seed-demo
php -S 127.0.0.1:8002 -t public public/dev-router.php
```

Ouvrir ensuite :

```text
http://127.0.0.1:8002/
```

Compte demo :

```text
demo@example.com
password
```

## Fonctionnalites realisees

- Page d'accueil avec les produits TOP.
- Liste des categories.
- Liste des produits par categorie.
- Page details produit avec formulaire d'ajout au panier.
- Panier stocke en session avec modification et suppression de ligne.
- Enregistrement du panier en commande si l'utilisateur est connecte.
- Redirection vers la connexion si l'utilisateur n'est pas connecte.
- Inscription avec mot de passe confirme et hache.
- Connexion Symfony `form_login`.
- Page profil securisee avec informations personnelles et commandes passees.
- Architecture panier avec `CartInterface`, `SessionCart`, `ApiCart` et `CartHandler`.
