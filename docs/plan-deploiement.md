# Plan de travail — Du développement au déploiement

Statut de référence : 2026-07-26. Complète `docs/perimetre-fonctionnel.md` (le quoi) avec le comment/quand.

## Où on en est

- Socle technique : Laravel 13 + Livewire 4 + AdminLTE + MySQL, opérationnel en local (`php artisan serve`).
- Modèle de données complet (11 tables métier) + rôles/permissions (Spatie, 6 rôles) + authentification minimale + compte admin.
- 1 seul CRUD construit à ce jour : **Engins** (sert de patron).
- Aucun commit git, aucun dépôt distant — le code n'existe qu'en local pour l'instant.
- 4 packages installés mais pas encore intégrés : dompdf, medialibrary, activitylog, simple-excel.

## Phase 0 — Filet de sécurité (à faire immédiatement, avant d'aller plus loin)

- Premier commit git + création d'un dépôt distant (GitHub/GitLab privé). Tant que ce n'est pas fait, tout le travail ne vit que sur ce poste.
- Décider d'un workflow de branches minimal (ex: `main` stable + branches de fonctionnalités, ou commits directs sur `main` si projet solo).

## Phase 1 — Compléter les CRUD métier

Reproduire le patron du module Engins (liste paginée + recherche, modal création/édition, suppression, gates `module.action`) pour :

1. Chauffeurs
2. Clients
3. Contrats (le plus structurant — dépend d'Engins et Clients)
4. Pointages (saisie quotidienne — dépend des Contrats et Chauffeurs)
5. Maintenances (pannes + entretien préventif)
6. Factures (génération à partir des pointages d'une période)
7. Paiements (rattachés à une facture)
8. Charges (rattachées à un engin, éventuellement une maintenance)
9. Page de paramétrage `Entreprise` (singleton, formulaire simple sans liste)
10. Gestion des utilisateurs internes + attribution des rôles (écran réservé à Super Admin)

Chaque module suit le même canevas, donc le rythme devrait s'accélérer après les 2-3 premiers.

## Phase 2 — Logique métier transverse

Une fois les CRUD en place, câbler les règles qui traversent plusieurs tables :

- Synchronisation du statut d'un engin (`disponible` / `en_location` / `en_panne` / `en_entretien`) en fonction des contrats actifs, pannes et entretiens en cours.
- Génération de facture à partir des pointages d'une période donnée (somme des heures facturables × tarif).
- Calcul automatique du statut de facture (`payee`, `partiellement_payee`, `en_retard`) à partir des paiements enregistrés.
- Calcul de rentabilité par engin (recettes facturées − charges) sur une période.
- Numérotation/validations de cohérence (ex: dates de contrat, chevauchement de pointages).

## Phase 3 — Tableau de bord & reporting

- Page d'accueil (dashboard) avec indicateurs clés : recettes du mois, taux d'utilisation du parc, engins en panne/entretien, factures impayées.
- Rapports : recettes réelles vs prévisionnelles (référence directe au tableau initial de l'utilisateur), rentabilité par engin, historique par client.

## Phase 4 — Intégration des packages complémentaires

Décidé précédemment de reporter après le CRUD — à réactiver ici :

- **dompdf** : génération PDF des contrats et factures.
- **medialibrary** : logo de l'entreprise, photos des engins.
- **activitylog** : audit des modifications sur contrats/factures/paiements.
- **simple-excel** : export des rapports (recettes, rentabilité).

## Phase 5 — Tests automatisés

- Tests Feature (Pest/PHPUnit, déjà scaffoldé) sur les points critiques : calcul de facturation, permissions par rôle, création/mise à jour des entités clés.
- Au minimum : un test qui vérifie qu'un rôle restreint (ex: Superviseur de chantier) ne peut pas accéder aux modules qui ne lui sont pas destinés.

## Phase 6 — Durcissement avant mise en prod

- Revue de code (`/code-review`) sur l'ensemble des modules.
- Vérification des validations de formulaires (messages d'erreur en français, contraintes métier).
- Nettoyage des seeders de démo (garder `EntrepriseSeeder`/`EnginSeeder` de données réelles, retirer toute donnée de test superflue).
- Vérification des permissions par défaut (aucun rôle trop permissif par erreur).

## Phase 7 — Préparation au déploiement

Décisions à prendre avec toi avant cette phase :

- **Hébergement** : l'environnement de prod doit supporter **PHP ≥ 8.4** (contrainte identifiée en local avec MAMP/PHP 8.3). À vérifier auprès de l'hébergeur choisi (VPS, mutualisé, ou plateforme comme Forge/Vapor) — beaucoup d'hébergeurs mutualisés au Sénégal n'ont pas encore PHP 8.4.
- **Domaine** et certificat HTTPS.
- **Base de données de production** : MySQL managé ou sur le même serveur, stratégie de sauvegarde régulière.
- **Variables d'environnement de prod** (`.env` de prod séparé, `APP_DEBUG=false`, clés/mots de passe forts — notamment remplacer le mot de passe admin actuel `admin123`).
- **Stockage des fichiers** (logos, photos d'engins, PDF générés) : disque local du serveur ou S3-compatible.

## Phase 8 — Déploiement & recette utilisateur

- Déploiement initial (manuel via SSH/Git pull + `composer install --no-dev` + `npm run build` + migrations, ou CI/CD si le dépôt distant est en place).
- Recette utilisateur (UAT) avec l'équipe GEOPARTNERS sur un jeu de données réel ou proche du réel.
- Formation rapide des utilisateurs internes selon leur rôle (Gestionnaire de parc, Commercial, Comptable, Direction...).

## Phase 9 — Après mise en production

- Sauvegardes automatiques régulières de la base.
- Suivi des logs d'erreurs (Laravel `storage/logs`, ou service externe si volume le justifie).
- Cycle d'itérations courtes pour ajustements post-lancement (retours terrain).

## Prochaine décision

Confirmer si on enchaîne directement sur la **Phase 0 (git) puis Phase 1 (CRUD Chauffeurs/Clients/Contrats)**, ou s'il y a un ordre différent à privilégier.
