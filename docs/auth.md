# Documentation de l'API Projet Djallonke

Cette documentation détaille les endpoints disponibles pour l'API du Projet Djallonke.

## Authentification

L'API utilise Laravel Sanctum pour l'authentification via des tokens Bearer.

### 1. Connexion (Login)

Authentifie un utilisateur et retourne un token d'accès.

-   **URL** : `/api/login`
-   **Méthode** : `POST`
-   **Auth** : Non requis

#### Paramètres du Body

| Champ      | Type   | Requis | Description                       |
| :--------- | :----- | :----- | :-------------------------------- |
| `email`    | string | Oui    | L'adresse email de l'utilisateur. |
| `password` | string | Oui    | Le mot de passe de l'utilisateur. |

#### Exemple de Requête

```json
{
    "email": "utilisateur@example.com",
    "password": "password123"
}
```

#### Exemple de Réponse (Succès - 200 OK)

```json
{
    "access_token": "1|R5...TokenString...",
    "token_type": "Bearer",
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "utilisateur@example.com",
        "email_verified_at": null,
        "created_at": "2025-12-15T10:00:00.000000Z",
        "updated_at": "2025-12-15T10:00:00.000000Z"
    }
}
```

#### Exemple de Réponse (Erreur - 422 Unprocessable Entity)

```json
{
    "message": "Les identifiants fournis sont incorrects.",
    "errors": {
        "email": ["Les identifiants fournis sont incorrects."]
    }
}
```

---

### 2. Déconnexion (Logout)

Révoque le token d'accès actuel de l'utilisateur.

-   **URL** : `/api/logout`
-   **Méthode** : `POST`
-   **Auth** : Requis (Bearer Token)

#### Headers

| Clé             | Valeur                 |
| :-------------- | :--------------------- |
| `Authorization` | `Bearer <votre_token>` |
| `Accept`        | `application/json`     |

#### Exemple de Réponse (Succès - 200 OK)

```json
{
    "message": "Déconnexion réussie"
}
```

---

### 3. Utilisateur Connecté (User Profile)

Récupère les informations de l'utilisateur actuellement authentifié.

-   **URL** : `/api/user`
-   **Méthode** : `GET`
-   **Auth** : Requis (Bearer Token)

#### Headers

| Clé             | Valeur                 |
| :-------------- | :--------------------- |
| `Authorization` | `Bearer <votre_token>` |
| `Accept`        | `application/json`     |

#### Exemple de Réponse (Succès - 200 OK)

```json
{
    "id": 1,
    "name": "John Doe",
    "email": "utilisateur@example.com",
    "email_verified_at": null,
    "created_at": "2025-12-15T10:00:00.000000Z",
    "updated_at": "2025-12-15T10:00:00.000000Z"
}
```

---

### 4. Mot de passe oublié (Forgot Password)

Envoie un lien de réinitialisation de mot de passe à l'adresse email fournie.

-   **URL** : `/api/forgot-password`
-   **Méthode** : `POST`
-   **Auth** : Non requis

#### Paramètres du Body

| Champ   | Type   | Requis | Description                       |
| :------ | :----- | :----- | :-------------------------------- |
| `email` | string | Oui    | L'adresse email de l'utilisateur. |

#### Exemple de Requête

```json
{
    "email": "utilisateur@example.com"
}
```

#### Exemple de Réponse (Succès - 200 OK)

```json
{
    "status": "Nous vous avons envoyé par email le lien de réinitialisation du mot de passe !"
}
```

#### Exemple de Réponse (Erreur - 400 Bad Request)

```json
{
    "email": "Nous ne pouvons pas trouver d'utilisateur avec cette adresse email."
}
```

---

### 5. Réinitialisation de mot de passe (Reset Password)

Réinitialise le mot de passe de l'utilisateur à l'aide du token reçu par email.

-   **URL** : `/api/reset-password`
-   **Méthode** : `POST`
-   **Auth** : Non requis

#### Paramètres du Body

| Champ                   | Type   | Requis | Description                                  |
| :---------------------- | :----- | :----- | :------------------------------------------- |
| `token`                 | string | Oui    | Le token de réinitialisation reçu par email. |
| `email`                 | string | Oui    | L'adresse email de l'utilisateur.            |
| `password`              | string | Oui    | Le nouveau mot de passe (min 8 caractères).  |
| `password_confirmation` | string | Oui    | Confirmation du nouveau mot de passe.        |

#### Exemple de Requête

```json
{
    "token": "e8f9a...",
    "email": "utilisateur@example.com",
    "password": "newpassword123",
    "password_confirmation": "newpassword123"
}
```

#### Exemple de Réponse (Succès - 200 OK)

```json
{
    "status": "Votre mot de passe a été réinitialisé !"
}
```
