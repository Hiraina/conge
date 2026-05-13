# Checklist du projet – Système RH TechMada (CI4 + SQLite)

## Répartition des rôles

| Élève | Responsabilités principales |
|-------|----------------------------|
| **A** | Setup initial, Authentification, Espace employé (complet), Profil utilisateur |
| **B** | Migrations/Seeders, Espace RH, Espace admin, Logique métier transverse, Finitions |

---

## 1. Mise en place et organisation

### Tâches communes
- [ ] S’assurer que le dossier `writable/` est accessible en écriture
- [ ] S’accorder sur les conventions de code (PSR-12, nommage des routes, etc.)
- [ ] Créer le dépôt Git partagé et ajouter les deux membres

### 👤 Élève A
- [ ] Créer le projet CI4 : `composer create-project codeigniter4/appstarter gestion-conges`
- [ ] Configurer le fichier `.env` (URL du projet, connexion DB SQLite)
- [ ] Initialiser Git : `git init`, `git add .`, `git commit -m "Initial commit"`
- [ ] Pousser le projet sur GitHub/GitLab
- [ ] Créer et partager le lien du repo avec l'élève B

### 👤 Élève B
- [ ] Cloner le projet : `git clone <url>`
- [ ] Créer sa branche de travail : `git checkout -b dev-bdd`
- [ ] Préparer le schéma BDD sur papier/notes (colonnes, relations, contraintes)
- [ ] Réfléchir à la logique métier (solde calculé, déduction à l'approbation)

---

## 2. Base de données – Migrations & Seeders

### 👤 Élève B (responsable)
- [ ] Créer les 5 migrations dans l'ordre :
  - `departements`
  - `types_conge`
  - `employes`
  - `soldes`
  - `conges`
- [ ] Définir les contraintes :
  - `employes.email` UNIQUE
  - `employes.role` CHECK (role IN ('employe', 'rh', 'admin'))
  - `conges.statut` CHECK (statut IN ('en_attente', 'approuvee', 'refusee', 'annulee'))
  - Clés étrangères avec `ON DELETE RESTRICT` ou `CASCADE`
- [ ] Rédiger le seeder (`DatabaseSeeder.php`) avec :
  - 1 admin, 2 employés, 1 responsable RH
  - 3 types de congés (Congés payés, RTT, Sans solde)
  - Soldes initialisés (année courante, jours_attribues, jours_pris = 0)
- [ ] Exécuter `php spark migrate` et `php spark db:seed`
- [ ] Vérifier que le fichier `writable/database.db` est créé

### 👤 Élève A (en parallèle)
- [ ] Pendant que B fait les migrations, préparer la structure des contrôleurs
- [ ] Noter les routes nécessaires pour l'authentification

---

## 3. Authentification & contrôle d’accès (40 min)

### 👤 Élève A (responsable)
- [ ] Créer le **filtre personnalisé `AuthFilter`** : vérifier session + rôle
- [ ] Configurer les **routes protégées par groupe** : `/employee/*`, `/rh/*`, `/admin/*`
- [ ] Créer le **contrôleur `AuthController`** : login, logout, formulaire avec CSRF
- [ ] Implémenter les **mots de passe hashés avec `password_hash()`**
- [ ] Ajouter la **vérification du rôle dans chaque contrôleur** (redirection si mauvais rôle)
- [ ] Créer le **layout partagé** (`app.php`) avec sidebar dynamique selon rôle
- [ ] Ajouter les messages **flashdata** pour succès/erreur de connexion

### 👤 Élève B (en parallèle)
- [ ] Préparer les modèles (voir étape 4)
- [ ] Ajouter les relations et méthodes utiles dans les modèles

---

## 4. Modèles & méthodes métier (préparatoire)

### 👤 Élève B
- [ ] Créer les modèles :
  - `EmployeModel`
  - `DepartementModel`
  - `TypeCongeModel`
  - `SoldeModel`
  - `CongeModel`
- [ ] Configurer `$table` et `$allowedFields` pour chaque modèle
- [ ] Ajouter les méthodes utiles dans les modèles :
  - `getSoldeRestant($employe_id, $type_conge_id, $annee)`
  - `verifierSoldeSuffisant($employe_id, $type_conge_id, $annee, $nb_jours)`
  - `deduireSolde($employe_id, $type_conge_id, $annee, $nb_jours)`
  - `recrediterSolde($employe_id, $type_conge_id, $annee, $nb_jours)`
  - `verifierChevauchement($employe_id, $date_debut, $date_fin)`
  - `calculerNbJours($date_debut, $date_fin)`

### 👤 Élève A (en parallèle)
- [ ] Tester l’authentification avec les comptes créés par B
- [ ] Vérifier que les sessions fonctionnent correctement

---

## 5. Espace Employé (60 min)

### 👤 Élève A (responsable)
- [ ] **Tableau de bord employé** : afficher le solde restant par type de congé
- [ ] **Formulaire de soumission de demande** (type, dates, motif) avec validation CI4
  - [ ] Vérifier `date_debut <= date_fin`
  - [ ] Vérifier l’absence de chevauchement (utiliser la méthode du modèle)
  - [ ] Vérifier le solde suffisant (alerte, pas de débit)
  - [ ] Calculer le nombre de jours
- [ ] **Enregistrement** de la demande (`statut = 'en_attente'`)
- [ ] **Liste des propres demandes** avec statuts
- [ ] **Annulation d’une demande** (seulement si `statut = 'en_attente'`)
- [ ] **Modification du profil** (nom, mot de passe)
- [ ] Tester : un employé ne peut pas voir les demandes des autres

### 👤 Élève B (code review)
- [ ] Relire le code de l’espace employé
- [ ] Vérifier les appels aux modèles (méthodes métier)

---

## 6. Espace Responsable RH (50 min)

### 👤 Élève B (responsable)
- [ ] **Liste de toutes les demandes en attente** (avec filtre par département)
- [ ] **Détail d’une demande** : boutons Approuver / Refuser + commentaire optionnel
- [ ] **Logique d’approbation** (cœur métier) :
  - [ ] Vérifier à nouveau le solde suffisant
  - [ ] Mettre à jour `soldes.jours_pris = jours_pris + nb_jours`
  - [ ] Changer le statut en `approuvee`
- [ ] **Logique de refus** : statut `refusee`, pas de changement dans soldes
- [ ] **Annulation après approbation** : recréditer le solde si une demande approuvée est annulée
- [ ] **Filtre sur les demandes** (par statut, par département)
- [ ] **Consultation du solde de chaque employé** (lecture seule)
- [ ] Tester : solde insuffisant → refus avec message ; approbation → solde déduit

### 👤 Élève A (code review)
- [ ] Relire le code RH
- [ ] Vérifier la cohérence avec l’espace employé

---

## 7. Espace Administrateur (30 min)

### 👤 Élève B (responsable)
- [ ] **CRUD des employés** (créer, modifier, désactiver → `actif = 0`)
- [ ] **CRUD des départements** (nom, description)
- [ ] **CRUD des types de congés** (libelle, jours_annuels, deductible)
- [ ] **Tableau de bord admin** : absences du mois en cours (demandes approuvées)
- [ ] **Initialiser / ajuster le solde annuel** d’un employé
- [ ] **Historique complet** de toutes les demandes (paginé, triable)

### 👤 Élève A (code review)
- [ ] Relire le code admin
- [ ] Vérifier les permissions (seul l’admin accède)

---

## 8. Logique métier transverse – Points obligatoires (vérification commune)

- [ ] **Le solde n’est déduit qu’à l’approbation** (pas à la soumission)
- [ ] **Annulation après approbation** : recréditer le solde
- [ ] **Refus après approbation** : recréditer le solde
- [ ] **Empêcher les chevauchements** : pas deux demandes actives sur les mêmes dates
- [ ] **Empêcher la soumission si solde insuffisant**
- [ ] **Bloquer les dates invalides** (`date_debut > date_fin`)

---

## 9. Tests et validation (binôme ensemble)

- [ ] Tester le workflow complet :
  - Employé soumet → RH approuve → solde mis à jour
  - Employé soumet → RH refuse → solde inchangé
  - Employé annule avant approbation → solde inchangé
- [ ] Tester les permissions : URL directe par employé → redirection
- [ ] Vérifier CSRF sur tous les formulaires POST
- [ ] Vérifier `redirect()` après chaque POST (Pattern PRG)
- [ ] Vérifier les messages flashdata

---

## 10. Finitions & livrables (20 min)

### 👤 Élève A
- [ ] Rendre le code propre (indentation, pas de `var_dump`)
- [ ] Vérifier les fonctionnalités employé (4 points)

### 👤 Élève B
- [ ] Rédiger le `README.md` avec :
  - Instructions d’installation
  - Comptes de test
- [ ] Vérifier les fonctionnalités RH (3 points) et admin (3 points)

### 👤 A et B ensemble
- [ ] Fusionner la branche finale : `git checkout main`, `git merge dev-bdd`
- [ ] Tagger `v1.0` : `git tag -a v1.0 -m "Version finale du projet"`
- [ ] Déposer le projet (lien Git ou archive)

---

## 11. Bonus (si temps)

- [ ] Export CSV des demandes (RH ou admin) – 👤 B
- [ ] Graphique simple sur dashboard admin (stats par mois) – 👤 B
- [ ] Notification email lors d’une validation – 👤 A ou B
