ÉTAPE 1 — Création du projet
👤 A

Crée le projet CI4 :

composer create-project codeigniter4/appstarter gestion-conges

Configure :

.env
URL du projet
connexion DB
👤 B

Pendant ce temps :
Prépare déjà le schéma de la BDD sur papier/notes :

Tables :

departements
employes
types_conge
soldes
conges

Réfléchit aux colonnes.

Exemple :

employes:
id
nom
email
password
role
departement_id
ÉTAPE 2 — Git
👤 A

Initialise Git :

git init
git add .
git commit -m "Initial commit"

Crée le repo GitHub/GitLab.

👤 B

Clone le projet :

git clone ...

Puis crée sa branche :

git checkout -b dev-bdd
ÉTAPE 3 — Migrations
👤 B

Commence les migrations :

php spark make:migration CreateEmployesTable

Puis crée :

departements
types_conge
employes
soldes
conges

Ensuite :

php spark migrate
👤 A
Pendant ce temps :

Crée :

AuthController
vues login
routes login/logout

Exemple :

GET /login
POST /login
GET /logout
ÉTAPE 4 — Modèles & Auth
👤 B

Crée les modèles :

php spark make:model EmployeModel

etc.

Puis configure :

$table
$allowedFields
👤 A

Pendant ce temps :

Code :

formulaire login
récupération email/password
session utilisateur

Exemple :
ession()->set(...)
ÉTAPE 5 — Seeders
👤 B

Crée les données de test :

php spark make:seeder UserSeeder

Ajoute :

admin
RH
employés
types de congés
Puis :

php spark db:seed UserSeeder
👤 A

Pendant ce temps :

Teste l’auth avec les comptes créés.

Exemple :

admin@test.com
1234
ÉTAPE 6 — Rôles & sécurité
👤 A

Crée les filters :

php spark make:filter AdminFilter

Puis :

AdminFilter
RhFilter
EmployeFilter

Configure :
app/Config/Filters.php
👤 B

Pendant ce temps :

Ajoute dans les modèles :

relations utiles
méthodes utiles

Exemple :

getSoldeByEmploye()
ÉTAPE 7 — Fusion
👤 A et B

Quand tout marche :

git add .
git commit -m "Auth + BDD done"
git push

Puis merge.