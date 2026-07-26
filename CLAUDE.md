# Vision Pro — Gestion de location d'engins TP/Bâtiment

## Statut

Projet initialisé le 2026-07-25 : squelette Laravel + Livewire + AdminLTE en place, base MySQL `vision_pro` créée et migrée. Intégration Livewire+AdminLTE vérifiée (page `/test-integration`, config `adminlte.livewire = true`).

Modélisation des entités métier terminée (migrations + modèles Eloquent, relations câblées) : `entreprises`, `engins`, `chauffeurs`, `affectations` (historique chauffeur↔engin), `clients`, `contrats`, `pointages`, `maintenances`, `factures`, `paiements`, `charges`. `entreprises` et `engins` sont peuplées via seeders avec les données réelles des documents fournis.

Rôles/permissions installés : `spatie/laravel-permission` avec 6 rôles (Super Admin, Gestionnaire de parc, Commercial, Superviseur de chantier, Comptable, Direction) et 48 permissions (12 modules × view/create/update/delete). Super Admin bypass tout via `Gate::before` dans `AppServiceProvider`. Seeder `RolePermissionSeeder`, appelé depuis `DatabaseSeeder`.

Authentification minimale en place (pas de laravel/ui, pas de registration — app interne) : `App\Http\Controllers\Auth\LoginController` (login/logout classiques), vue `adminlte::auth.login` du package réutilisée directement, routes `/login` (guest) et `/logout` + `/dashboard` (auth) dans `routes/web.php`. `config/adminlte.php` : `dashboard_url=dashboard`, `register_url`/`password_reset_url` désactivés (false), menu latéral remplacé par le vrai menu de l'app (Tableau de bord, Engins avec `'can' => 'engins.view'`).

Premier CRUD Livewire+AdminLTE construit et testé de bout en bout (login réel via curl, permissions vérifiées avec Gate::before et avec un rôle restreint) : **Engins** (`App\Livewire\Engins\Manager`, vue `resources/views/livewire/engins/manager.blade.php`, route `engins.index`). Sert de patron à reproduire pour les autres entités (liste paginée + recherche + modal create/edit + delete, gates `module.action`).

Pas encore fait : CRUD pour les 10 autres entités (chauffeurs, affectations, clients, contrats, pointages, maintenances, factures, paiements, charges, entreprise), logique métier transverse (synchronisation du statut d'un engin selon contrat/panne/entretien, calcul du solde et du statut de facture selon paiements, calcul de rentabilité), intégration des packages ci-dessous.

## Packages ajoutés par l'utilisateur (hors scaffolding initial)

Ajoutés directement par l'utilisateur dans `composer.json`, pas encore intégrés dans le code métier :
- `barryvdh/laravel-dompdf` — pressenti pour générer les PDF de contrats/factures.
- `spatie/laravel-medialibrary` — pressenti pour les photos d'engins / logo entreprise.
- `spatie/laravel-activitylog` — pressenti pour un journal d'audit sur les entités sensibles (contrats, factures, paiements).
- `spatie/simple-excel` — pressenti pour l'export Excel des rapports (recettes, rentabilité).

À confirmer avec l'utilisateur avant intégration : quel module en premier, et sur quelles entités précisément.

## Stack technique

- Backend : Laravel 13 (PHP 8.4.13 en CLI)
- Interface réactive : Livewire 4
- Thème d'administration : AdminLTE (`jeroennoten/laravel-adminlte`, assets/config publiés)
- Base de données : MySQL (`vision_pro`, via MAMP)

## Environnement local — points d'attention

- **Ne pas servir l'app via Apache/MAMP** (`http://localhost:81/vision_pro/public`) : le module Apache de MAMP est en PHP 8.3.1, alors que ce projet (Symfony 8 / Livewire 4) exige PHP ≥ 8.4.1. La CLI `C:\php\php.exe` est en 8.4.13 mais en build NTS, incompatible avec mod_php.
  - **Utiliser `php artisan serve` pour le développement local** (tourne sur PHP 8.4.13 CLI, testé et fonctionnel).
  - Si on veut absolument passer par Apache/MAMP plus tard, il faudra installer une build PHP 8.4 Thread-Safe compatible Apache (non fournie avec cette install MAMP).
- Identifiants MySQL dans `.env` (fichier gitignoré, ne pas committer) : utilisateur applicatif `elcisse` avec tous les droits sur la base `vision_pro`.

## Contexte métier

Application de gestion de location de machines/engins de travaux publics (TP) et de bâtiment, pour le compte de **GEOPARTNERS CONSULTING** (Cité Lobath Fall, rond-point EDK villa N°89, Pikine, Dakar — Sénégal). Devise : FCFA.

Éléments identifiés à partir des documents fournis (grille tarifaire + extrait de contrat) :

- **Engins** : chaque engin a un type, une quantité, et un tarif horaire de location.
  - Exemples relevés : Bulldozer D8 GC (45 000 FCFA/h), Pelle excavatrice Caterpillar CAT333-GC (30 000 FCFA/h), Pelle excavatrice Caterpillar CAT330-GC (30 000 FCFA/h), Gradeur CAT140K (35 000 FCFA/h), Tractopelle CAT426 (15 000 FCFA/h).
- **Temps de travail** : conversions standards utilisées pour les projections de recettes — jour = 20h, semaine = 120h, mois = 480h, année = 5760h.
- **Recettes prévisionnelles** : calculées par engin (taux horaire × temps de travail), avec un total global.
- **Clause contractuelle clé** : en cas de panne, le **propriétaire** de la machine reste seul responsable de celle-ci et des frais de réparation/remise en état ; la période d'immobilisation pour panne n'ouvre droit à aucun paiement ni indemnisation de la part du **Contractant** (client locataire).

Cette dernière clause suggère un modèle à trois acteurs :
- **Propriétaires** des engins (peuvent être des tiers, pas forcément l'entreprise elle-même)
- **GEOPARTNERS CONSULTING** en tant que gestionnaire/loueur intermédiaire
- **Clients/Contractants** qui louent les engins pour leurs chantiers

Entités probables à approfondir avant modélisation : Engins, Propriétaires, Clients, Contrats de location, Suivi d'utilisation (heures/jours travaillés), Facturation, Pannes/Maintenance.

## Documents sources

- Captures d'écran fournies le 24/07/2026 : tableau des recettes prévisionnelles (tarifs horaires par engin) et extrait d'un contrat de location (clause de panne, tableau des tarifs de location).

## Périmètre fonctionnel

Le périmètre fonctionnel a été approfondi et validé avec l'utilisateur : voir `docs/perimetre-fonctionnel.md` pour le détail des modules (parc d'engins, chauffeurs, clients, contrats, pointage, facturation, paiements, maintenance, rentabilité, reporting).

Décisions clés :
- Utilisateurs internes GEOPARTNERS uniquement (pas de portail client/propriétaire).
- Pointage manuel quotidien des heures travaillées par engin.
- Tous les engins appartiennent à GEOPARTNERS (pas de multi-propriétaires).
- Un contrat = un engin + un client.
- Facturation périodique (hebdo/mensuelle) + facture de clôture, avec suivi des paiements/relances et calcul de rentabilité par engin (charges vs recettes).
- Fiche chauffeur dédiée, affectée à un ou plusieurs engins.
- Maintenance : pannes + entretien préventif planifié.

## Modèle de données

| Table | Rôle |
|---|---|
| `entreprises` | Infos de GEOPARTNERS (singleton, en-tête contrats/factures) |
| `engins` | Parc de machines (statut, tarif horaire, compteur horaire) |
| `chauffeurs` | Fiches chauffeurs/opérateurs |
| `affectations` | Historique chauffeur ↔ engin (dates début/fin) |
| `clients` | Clients/contractants (particulier ou entreprise) |
| `contrats` | Un engin + un client, numéro auto (`CTR-YYYY-NNNN`) |
| `pointages` | Pointage journalier par contrat (heures travaillées, panne, chauffeur du jour) |
| `maintenances` | Pannes et entretiens préventifs par engin |
| `factures` | Facturation périodique/clôture par contrat, numéro auto (`FACT-YYYY-NNNN`) |
| `paiements` | Règlements reçus par facture |
| `charges` | Charges par engin (carburant, réparation, entretien...), liables à une `maintenance` |

## Prochaines étapes

Plan complet (dev → déploiement) dans `docs/plan-deploiement.md`. Résumé :

- **Phase 0** (urgent, pas encore fait) : premier commit git + dépôt distant — le code n'existe qu'en local pour l'instant.
- **Phase 1** : reproduire le patron CRUD des Engins pour chauffeurs, clients, contrats, pointages, maintenances, factures, paiements, charges, page entreprise (singleton), gestion utilisateurs/rôles.
- **Phase 2** : logique métier transverse (statut engin auto, statut/solde facture, rentabilité par engin).
- **Phase 3** : dashboard & reporting.
- **Phase 4** : intégration dompdf/medialibrary/activitylog/simple-excel.
- **Phase 5-6** : tests automatisés, durcissement/revue de code.
- **Phase 7-9** : préparation déploiement (PHP ≥ 8.4 requis chez l'hébergeur, mot de passe admin à changer), déploiement, recette utilisateur, suivi post-lancement.
