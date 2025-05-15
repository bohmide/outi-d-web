🧠 Outi-D - Plateforme Web Symfony
**Outi-D** est une plateforme d'apprentissage interactive orientée jeux et quiz, conçue pour les enfants, les élèves et les étudiants. Cette version Web offre un back office complet et une API REST sécurisée pour communiquer avec l'application desktop.
## 🌐 Fonctionnalités principales
- Authentification via JWT (LexikJWTAuthenticationBundle)
- Gestion des utilisateurs, cours, quiz, forums, événements, etc.
- Back office d'administration avec vues Twig
- Téléversement de fichiers (images, documents) – VichUploaderBundle
- Génération de QR codes – EndroidQrCodeBundle
- Exportation de contenus en PDF – KnpSnappyBundle
- Modération des contenus et vérification d'email
- API REST sécurisée (API Platform ou contrôleurs personnalisés)
- Pagination et tri – KnpPaginatorBundle
## ⚙️ Stack Technique
- Symfony 6
- Doctrine ORM
- API Platform ou REST personnalisée
- MySQL / PostgreSQL
- JWT (LexikJWTAuthenticationBundle)
### Bundles utilisés
- `FrameworkBundle`, `MakerBundle`, `TwigBundle`, `TwigExtraBundle`
- `DoctrineBundle`, `DoctrineMigrationsBundle`
- `SecurityBundle`
- `SymfonyCastsResetPasswordBundle`, `SymfonyCastsVerifyEmailBundle`
- `VichUploaderBundle`, `KnpPaginatorBundle`
- `EndroidQrCodeBundle`, `KnpSnappyBundle`
- `NelmioCorsBundle`
## 🧪 Tests & Sécurité
- Tests unitaires avec PHPUnit
- Protection CSRF
- Validation des données avec Symfony Validator
## 🚧 Évolutions futures
- Notifications en temps réel (WebSocket)
- Module de visioconférence
- Application mobile (Flutter ou React Native)
- Gamification : badges, niveaux
hedha grp of brain't poeple
