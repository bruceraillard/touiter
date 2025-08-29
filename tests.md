# Tests Touiter – Commandes cURL

Ce fichier regroupe uniquement les **tests** à exécuter sur l’API Touiter.  
Chaque commande cURL est accompagnée du **statut HTTP attendu**.

---

## 1) Authentification

### 1.1 Login OK
```bash
curl -v -X POST http://127.0.0.1:8000/api/login_check   -H "Content-Type: application/json"   -d '{"email":"user@example.com","password":"password"}'
```
**Attendu** : 200 OK + `{"token":"...","refresh_token":"..."}`

### 1.2 Login KO (mauvais mot de passe)
```bash
curl -v -X POST http://127.0.0.1:8000/api/login_check   -H "Content-Type: application/json"   -d '{"email":"user@example.com","password":"wrong"}'
```
**Attendu** : 401 Unauthorized

---

## 2) Refresh Token

### 2.1 Refresh OK
```bash
curl -v -X POST http://127.0.0.1:8000/api/token/refresh   -H "Content-Type: application/json"   -d '{"refresh_token":"COLLER_ICI_LE_REFRESH_TOKEN"}'
```
**Attendu** : 200 OK + `{"token":"...","refresh_token":"..."}`

### 2.2 Refresh KO – manquant
```bash
curl -v -X POST http://127.0.0.1:8000/api/token/refresh   -H "Content-Type: application/json"   -d '{}'
```
**Attendu** : 400 Bad Request

### 2.3 Refresh KO – invalide
```bash
curl -v -X POST http://127.0.0.1:8000/api/token/refresh   -H "Content-Type: application/json"   -d '{"refresh_token":"INVALIDE"}'
```
**Attendu** : 401 Unauthorized

### 2.4 Refresh KO – réutilisé (single-use)
```bash
curl -v -X POST http://127.0.0.1:8000/api/token/refresh   -H "Content-Type: application/json"   -d '{"refresh_token":"ANCIEN_REFRESH_DEJA_UTILISE"}'
```
**Attendu** : 401 Unauthorized

---

## 3) Endpoints Touits

### 3.1 Liste (public)
```bash
curl -v http://127.0.0.1:8000/api/touits
```
**Attendu** : 200 OK + tableau JSON

### 3.2 Détail (public)
```bash
curl -v http://127.0.0.1:8000/api/touits/1
```
**Attendu** : 200 OK si existe, sinon 404 Not Found

### 3.3 Création (protégée) – OK
```bash
curl -v -X POST http://127.0.0.1:8000/api/touits   -H "Authorization: Bearer COLLER_ICI_LE_TOKEN"   -H "Content-Type: application/json"   -d '{"contenu":"Touit via JWT","author":"User"}'
```
**Attendu** : 201 Created

### 3.4 Création (protégée) – sans token
```bash
curl -v -X POST http://127.0.0.1:8000/api/touits   -H "Content-Type: application/json"   -d '{"contenu":"Pas de token","author":"User"}'
```
**Attendu** : 401 Unauthorized

### 3.5 Création (protégée) – validation (contenu >160)
```bash
curl -v -X POST http://127.0.0.1:8000/api/touits   -H "Authorization: Bearer COLLER_ICI_LE_TOKEN"   -H "Content-Type: application/json"   -d '{"contenu":"XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX","author":"User"}'
```
**Attendu** : 400 Bad Request

### 3.6 Suppression (protégée) – OK
```bash
curl -v -X DELETE http://127.0.0.1:8000/api/touits/1   -H "Authorization: Bearer COLLER_ICI_LE_TOKEN"
```
**Attendu** : 204 No Content

### 3.7 Suppression (protégée) – sans token
```bash
curl -v -X DELETE http://127.0.0.1:8000/api/touits/1
```
**Attendu** : 401 Unauthorized

### 3.8 Suppression (protégée) – 404
```bash
curl -v -X DELETE http://127.0.0.1:8000/api/touits/999999   -H "Authorization: Bearer COLLER_ICI_LE_TOKEN"
```
**Attendu** : 404 Not Found

---

## 4) (Optionnel) Profil

### 4.1 Profil – OK
```bash
curl -v http://127.0.0.1:8000/api/profile   -H "Authorization: Bearer COLLER_ICI_LE_TOKEN"
```
**Attendu** : 200 OK + `{"id":...,"email":"user@example.com","roles":[...]}`

### 4.2 Profil – sans token
```bash
curl -v http://127.0.0.1:8000/api/profile
```
**Attendu** : 401 Unauthorized
