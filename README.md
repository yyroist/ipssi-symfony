# Commandes à lancer pour le projet

- création du .env à partir du .env.example
- composer install 
- npm install (ou yarn install)
- npm run dev (ou yarn encore dev)
- php bin/console doctrine:database:create
- php bin/console doctrine:migrations:migrate
- php bin/console doctrine:fixtures:load

# Utilisateurs administrateur/super administrateur

- 1 utilisateur avec ROLE_ADMIN :
  - mail : admin@mail.fr
  - pwd : admin123
- 1 utilisateur avec ROLE_SUPER_ADMIN
  - mail : super.admin@mail.fr
  - pwd : admin123