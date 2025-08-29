# Touiter – API REST

Projet réalisé dans le cadre du TP REST 2025.  
Il s’agit d’une mini-API type “Twitter” permettant de publier des **touits** (160 caractères max), protégée par **JWT + Refresh Token**.

---

## Installation

### 1) Cloner le projet
```bash
git clone https://github.com/ton-user/touiter.git
cd touiter
```

### 2) Installer les dépendances
```bash
composer install
```

### 3) Générer les clés JWT
```bash
mkdir -p config/jwt
openssl genrsa -out config/jwt/private.pem -aes256 4096
openssl rsa -pubout -in config/jwt/private.pem -out config/jwt/public.pem
```

Le mot de passe saisi doit être reporté dans `.env.local` :

```env
JWT_PASSPHRASE="ton_passphrase"
```

### 4) Configurer la base dans le `.env.local`


```env
DATABASE_URL="sqlite:///%kernel.project_dir%/var/data.db"
```

Créer le schéma et charger les fixtures :

```bash
bin/console doctrine:database:create --if-not-exists
bin/console doctrine:migrations:migrate -n
bin/console doctrine:fixtures:load -n
```

### 5) Lancer le serveur
```bash
symfony serve
# ou : php -S 127.0.0.1:8000 -t public
```

---

## 📦 Fonctionnalités

- **Touits**
    - `GET /api/touits` → liste (publique)
    - `GET /api/touits/{id}` → détail (public)
    - `POST /api/touits` → création (protégée JWT)
    - `DELETE /api/touits/{id}` → suppression (protégée JWT)

- **Sécurité**
    - `POST /api/login_check` → login par email/password → renvoie `{ token, refresh_token }`
    - `POST /api/token/refresh` → rafraîchit le JWT via un `refresh_token`
    - (Optionnel) `GET /api/profile` → infos de l’utilisateur connecté

- **Validation**
    - `contenu` ≤ 160 caractères (sinon 400)
    - `author` obligatoire

---

## ✅ Tests

Les commandes cURL de test sont documentées dans [tests.md](./tests.md).  
Elles couvrent :
- Login OK/KO
- Refresh OK/KO
- GET /touits (liste/détail)
- POST /touits (création avec/sans token, validation)
- DELETE /touits (OK, 401, 404)
- (optionnel) /profile

---

## 👤 Utilisateur de test (fixtures)

```text
email    : user@example.com
password : password
roles    : ROLE_USER
```

---




