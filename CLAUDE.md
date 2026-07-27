# Vision Pro — Gestion de location d'engins TP/Bâtiment

## Statut

Projet initialisé le 2026-07-25 : squelette Laravel + Livewire en place, base MySQL `vision_pro` créée et migrée.

**2026-07-27 : le package `jeroennoten/laravel-adminlte` a été retiré.** Cause : ses liens de menu provoquaient un rechargement complet de page à chaque clic ; on a voulu activer `wire:navigate` (navigation SPA native de Livewire) pour corriger ça, mais le JS jQuery du package (sidebar, widgets) n'est pas conçu pour survivre à une navigation sans rechargement — résultat : pages blanches. Solution retenue (voir section dédiée ci-dessous) : garder l'apparence AdminLTE (sa CSS compilée, déjà vendue dans `public/vendor/adminlte/dist/css/`) mais écrire notre propre layout/sidebar/navbar, sans jQuery, avec Alpine.js (fourni nativement par Livewire) pour l'interactivité. **Confirmé fonctionnel par l'utilisateur.**

Modélisation des entités métier terminée (migrations + modèles Eloquent, relations câblées) : `entreprises`, `engins`, `chauffeurs`, `affectations` (historique chauffeur↔engin), `clients`, `contrats`, `pointages`, `maintenances`, `factures`, `paiements`, `charges`. `entreprises` et `engins` sont peuplées via seeders avec les données réelles des documents fournis.

Rôles/permissions installés : `spatie/laravel-permission` avec 6 rôles (Super Admin, Gestionnaire de parc, Commercial, Superviseur de chantier, Comptable, Direction) et 48 permissions (12 modules × view/create/update/delete). Super Admin bypass tout via `Gate::before` dans `AppServiceProvider`. Seeder `RolePermissionSeeder`, appelé depuis `DatabaseSeeder`.

Authentification minimale en place (pas de laravel/ui, pas de registration — app interne) : `App\Http\Controllers\Auth\LoginController` (login/logout classiques), vue autonome `resources/views/auth/login.blade.php` (ne dépend d'aucun package), routes `/login` (guest) et `/logout` + `/dashboard` (auth) dans `routes/web.php`.

**Phase 0 (git) et Phase 1 (CRUD) terminées.** Dépôt : https://github.com/elcisse/visionpro (branche `main`, tout committé et poussé au fil de l'eau).

Tous les CRUD Livewire+AdminLTE sont construits, testés de bout en bout (login réel via curl/tinker, permissions vérifiées) et poussés :
- `App\Livewire\Engins\Manager` — patron initial (liste + recherche + modal create/edit + delete, gates `module.action`).
- `App\Livewire\Chauffeurs\Manager`
- `App\Livewire\Clients\Manager`
- `App\Livewire\Contrats\Manager` — inclut l'upload du contrat en PDF (`document_pdf`, validation `mimes:pdf`, 10 Mo max, ancien fichier supprimé au remplacement).
- `App\Livewire\Pointages\Manager` — contrainte d'unicité (contrat, date) validée côté formulaire.
- `App\Livewire\Maintenances\Manager`
- `App\Livewire\Factures\Manager` — **calcule automatiquement** heures facturées et montant depuis les pointages de la période choisie (× tarif horaire du contrat), modifiable ensuite.
- `App\Livewire\Paiements\Manager` — affiche le solde restant dû et **resynchronise automatiquement** le statut de la facture (émise/partiellement payée/payée) à chaque paiement créé ou supprimé.
- `App\Livewire\Charges\Manager` — maintenance liée optionnelle, pré-remplit type/montant depuis celle-ci.
- `App\Livewire\Entreprise\Settings` — formulaire singleton (pas de liste), upload logo.
- `App\Livewire\Utilisateurs\Manager` — gestion des comptes + rôles multiples (`syncRoles`), protection anti-auto-suppression.

Bug corrigé au passage : `edit()` plantait (TypeError) sur Contrats/Pointages/Maintenances quand une date nullable (ex: `date_fin`) était `null` en base, à cause de propriétés Livewire typées `string` non-nullable alimentées via `optional()->format()`. Remplacé par `?->format() ?? ''`.

Pas encore fait : logique métier transverse restante (synchronisation du statut d'un engin selon contrat/panne/entretien, calcul de rentabilité par engin), dashboard/reporting, intégration des packages ci-dessous, tests automatisés, préparation déploiement.

## Packages ajoutés par l'utilisateur (hors scaffolding initial) — Phase 4

- ~~`barryvdh/laravel-dompdf`~~ **Intégré** : PDF contrats (`ContratPdfController`, `pdf/contrat.blade.php`) et factures (`FacturePdfController`, `pdf/facture.blade.php`), boutons sur les listes Contrats/Factures.
- ~~`spatie/laravel-medialibrary`~~ **Intégré** : `Engin` collection `photos` (galerie, conversion `thumb` 300×200 en `nonQueued`), `Entreprise` collection `logo` (`singleFile`, affiché dans la sidebar). Colonne `entreprises.logo` supprimée (remplacée par MediaLibrary).
- ~~`spatie/laravel-activitylog`~~ **Intégré** : `Contrat`/`Facture`/`Paiement` avec `LogsActivity`, page `App\Livewire\Audit\Manager` (route `audit.index`, permission `audit.view` — Super Admin + Direction). **Piège de cette version du package** : le détail des changements est dans la colonne `attribute_changes` (clés `attributes`/`old`), pas dans `properties` comme documenté pour les anciennes versions — à savoir si on ajoute `LogsActivity` à d'autres modèles plus tard.
- ~~`spatie/simple-excel`~~ **Intégré** : export `.xlsx` du rapport Recettes prévisionnelles (`RecettesPrevisionnellesExportController`, `SimpleExcelWriter::streamDownload()`).

**Phase 4 terminée** : les 4 packages ajoutés par l'utilisateur sont maintenant tous intégrés.

## Stack technique

- Backend : Laravel 13 (PHP 8.4.13 en CLI)
- Interface réactive : Livewire 4 (navigation SPA via `wire:navigate` sur tout le menu)
- Apparence : CSS AdminLTE (`public/vendor/adminlte/dist/css/adminlte.min.css`) + FontAwesome, **sans le package** `jeroennoten/laravel-adminlte` ni jQuery — layout/sidebar/navbar maison, interactivité en Alpine.js
- Base de données : MySQL (`vision_pro`, via MAMP)

## Layout maison (remplace AdminLTE package)

- `resources/views/layouts/admin.blade.php` : document HTML complet (plus de `@extends('adminlte::page')`). Reçoit `$slot` (contenu) et `$title` via `#[Layout('layouts.admin', ['title' => '...'])]` sur chaque composant Livewire — **contrat inchangé**, donc aucun des 11 modules CRUD n'a eu besoin d'être modifié lors de ce remplacement.
- `resources/views/layouts/partials/sidebar-menu.blade.php` : menu latéral piloté par un tableau PHP (section → items avec `route`/`icon`/`can`), filtré par permission (`auth()->user()->can(...)`). Pour ajouter une entrée de menu : éditer ce fichier (remplace l'ancien `config/adminlte.php`).
- Toggle sidebar en Alpine (`x-data="{ sidebarCollapsed: false }"` sur `<body>`), pas de jQuery/PushMenu.
- `resources/views/auth/login.blade.php` : page de connexion autonome, mêmes classes CSS AdminLTE (`login-page`, `login-box`, `card`...).
- Assets conservés : `public/vendor/adminlte/` (CSS uniquement, pas son JS), `public/vendor/fontawesome-free/`. Supprimés : jQuery, Bootstrap JS, Popper, OverlayScrollbars (plus utilisés, tout tourne en Alpine/Livewire).
- **Piège à ne pas reproduire** : un thème d'admin basé sur jQuery (widgets initialisés au `DOMContentLoaded`) est structurellement incompatible avec `wire:navigate`, qui évite justement le rechargement complet (donc plus de `DOMContentLoaded` entre deux pages). Toute future lib JS ajoutée à cette appli doit être compatible SPA (Alpine, ou init idempotente via `livewire:navigated`), sinon prévoir de la garder hors des pages naviguées en SPA.

## Environnement local — points d'attention

- **Ne pas servir l'app via Apache/MAMP** (`http://localhost:81/vision_pro/public`) : le module Apache de MAMP est en PHP 8.3.1, alors que ce projet (Symfony 8 / Livewire 4) exige PHP ≥ 8.4.1. La CLI `C:\php\php.exe` est en 8.4.13 mais en build NTS, incompatible avec mod_php.
  - **Utiliser `php artisan serve` pour le développement local** (tourne sur PHP 8.4.13 CLI, testé et fonctionnel).
  - Si on veut absolument passer par Apache/MAMP plus tard, il faudra installer une build PHP 8.4 Thread-Safe compatible Apache (non fournie avec cette install MAMP).
- Identifiants MySQL dans `.env` (fichier gitignoré, ne pas committer) : utilisateur applicatif `elcisse` avec tous les droits sur la base `vision_pro`.
- `APP_URL=http://127.0.0.1:8000` (corrigé le 2026-07-27, était resté sur l'ancienne config MAMP jamais utilisée) — génère les URLs de fichiers/médias. **À mettre à jour avec le vrai domaine lors du déploiement.**

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

- ~~**Phase 0** : premier commit git + dépôt distant.~~ Fait.
- ~~**Phase 1** : CRUD pour toutes les entités.~~ Fait.
- ~~**Phase 2** : logique métier transverse.~~ Fait — `App\Services\EnginStatutService` synchronise le statut engin (disponible/en_location/en_panne/en_entretien, priorité panne > entretien > location, `hors_service` jamais touché automatiquement) après chaque save/delete de Contrat ou Maintenance ; `Engin::rentabilite()` (recettes via `factures()` hasManyThrough, − charges) affiché sur la liste des Engins.
- ~~**Phase 3** : dashboard & reporting.~~ Fait — `App\Livewire\Dashboard\Show` affiche recettes réelles vs prévisionnelles (mois), taux d'utilisation du parc, engins en panne/entretien, factures impayées (montant restant dû) et en retard. `APP_LOCALE=fr` pour l'affichage des dates. Complété par `App\Livewire\Rapports\RecettesPrevisionnelles` (route `rapports.recettes-previsionnelles`) : détail par engin façon document tarifaire initial (jour/semaine/mois/année × taux = total), + export Excel.
- ~~**Phase 4** : intégration dompdf/medialibrary/activitylog/simple-excel.~~ Fait (voir section dédiée ci-dessus).
- **Phase 5-6** (prochaine) : tests automatisés, durcissement/revue de code.
- **Phase 7-9** : préparation déploiement (PHP ≥ 8.4 requis chez l'hébergeur, mot de passe admin `admin123` à changer), déploiement, recette utilisateur, suivi post-lancement.
