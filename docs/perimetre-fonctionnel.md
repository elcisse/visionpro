# Périmètre fonctionnel — Gestion de location d'engins TP/Bâtiment

Statut : validé avec l'utilisateur le 2026-07-25. Sert de base à la modélisation des entités (prochaine étape).

## 1. Acteurs

- **Utilisateurs internes GEOPARTNERS uniquement** pour cette version : pas de portail client, pas de portail propriétaire externe, pas de compte pour les chauffeurs.
- Rôles/permissions internes (admin, gestionnaire de parc, comptable...) à définir lors de la modélisation, via le système de rôles AdminLTE/Laravel.

## 2. Modules fonctionnels

### 2.1 Parc d'engins (Machines)
- Fiche engin : type/désignation, marque, référence/modèle, tarif horaire par défaut, statut (disponible, en location, en panne, en entretien, hors service), compteur horaire cumulé.
- Tous les engins appartiennent à GEOPARTNERS — pas de gestion multi-propriétaires ni de reversement de commission.
- Historique complet consultable par engin : contrats, pointages, pannes, entretiens.

### 2.2 Chauffeurs / Opérateurs
- Fiche chauffeur dédiée (nom, contact, permis/qualification).
- Affectation chauffeur ↔ engin dans le temps (un chauffeur peut être affecté à plusieurs engins successivement).
- Utilisée dans le pointage journalier ; sert de base au futur calcul de coût salarial dans la rentabilité par engin.

### 2.3 Clients (Contractants)
- Fiche client (raison sociale ou particulier, contact, adresse, identifiants légaux si entreprise).
- Historique des contrats par client.

### 2.4 Contrats de location
- **Un contrat = un engin + un client** (pas de contrats multi-engins).
- Champs clés : dates début/fin, tarif horaire applicable (par défaut celui de l'engin, modifiable au contrat), lieu du chantier, statut (en cours, terminé, résilié).
- Clause de panne héritée du contrat type existant : aucune facturation/indemnisation due pendant la période d'immobilisation pour panne.
- Génération du contrat en PDF à prévoir dans une itération ultérieure.

### 2.5 Pointage journalier (suivi d'utilisation)
- Saisie manuelle quotidienne par engin/contrat : date, heures travaillées, chauffeur affecté ce jour-là, panne éventuelle (avec heures d'arrêt), commentaire.
- Les heures de panne sont exclues des heures facturables (conforme à la clause contractuelle).

### 2.6 Facturation
- **Facturation périodique** (hebdomadaire ou mensuelle) sur les contrats en cours, basée sur le cumul des heures pointées facturables de la période.
- Facture de clôture en fin de contrat sur le reliquat non encore facturé.
- Statuts : brouillon, émise, partiellement payée, payée, en retard.

### 2.7 Paiements
- Enregistrement des règlements clients (montant, date, mode de paiement, référence).
- Suivi du solde restant dû par facture et par client.
- Alertes/relances sur les factures en retard.

### 2.8 Maintenance (pannes + entretien préventif)
- **Pannes** : déclarées via le pointage ou un module dédié ; dates d'immobilisation, coût de réparation, impact direct sur la disponibilité de l'engin et sur la facturation.
- **Entretien préventif** : planification de révisions périodiques (ex. tous les X heures de compteur ou à date fixe), avec suivi de réalisation.
- Historique de maintenance complet par engin.

### 2.9 Charges & Rentabilité par engin
- Enregistrement des charges par engin : carburant, réparations liées aux pannes, entretien préventif, éventuellement coût chauffeur.
- Calcul de rentabilité = recettes facturées − charges, par engin et par période.
- Comparaison recettes réelles vs. recettes prévisionnelles (référence directe au tableau tarifaire initial fourni par l'utilisateur).

### 2.10 Tableau de bord / Reporting
- Vue d'ensemble : taux d'utilisation du parc, recettes de la période, engins en panne/en entretien, factures impayées, alertes d'échéance.
- Rapports détaillés : par engin, par client, par période, comparaison prévisionnel/réel.

### 2.11 Administration
- Gestion des utilisateurs internes et de leurs rôles/permissions.
- Paramètres généraux : tarifs horaires par défaut, devise (FCFA), seuils d'entretien préventif, modèles de contrat/facture.

## 3. Hors périmètre (cette version)

- Portail client en self-service.
- Portail propriétaire tiers / gestion multi-propriétaires et reversement de commission (tous les engins appartiennent à GEOPARTNERS).
- Contrats regroupant plusieurs engins.

## 4. Décisions validées (résumé)

| Sujet | Décision |
|---|---|
| Acteurs/accès | Équipe interne GEOPARTNERS uniquement |
| Suivi horaire | Saisie manuelle quotidienne (fiche de pointage) |
| Propriété des engins | Tous propres à GEOPARTNERS |
| Facturation | Facture + suivi paiements/relances + rentabilité par engin |
| Cardinalité contrat/engin | Un engin par contrat |
| Fréquence de facturation | Périodique (hebdo/mensuelle) + clôture |
| Chauffeurs | Fiche dédiée, affectée à un ou plusieurs engins |
| Maintenance | Pannes + entretien préventif planifié |

## 5. Prochaine étape

Modélisation des entités (schéma de données) et des relations, en vue des migrations Laravel — à faire une fois ce périmètre validé définitivement.
