# Documentation de l'API - Gestion des Communautés

Cette section détaille les endpoints disponibles pour la gestion des communautés.

## Communautés

L'accès à ces ressources nécessite une authentification via un token Bearer (Sanctum).

### 1. Liste des communautés (Paginée)

Récupère une liste paginée des communautés.

-   **URL** : `/api/communities`
-   **Méthode** : `GET`
-   **Auth** : Requis (Bearer Token)

#### Paramètres de requête (Query Parameters)

| Champ  | Type    | Description                                  |
| :----- | :------ | :------------------------------------------- |
| `page` | integer | Le numéro de la page à récupérer (défaut 1). |

#### Exemple de Réponse (Succès - 200 OK)

```json
{
    "data": [
        {
            "id": 1,
            "name": "Communauté Agricole",
            "creation_date": "2020-01-01",
            "created_by": 1,
            "country_id": 1,
            "created_at": "2025-12-15T10:00:00.000000Z",
            "updated_at": "2025-12-15T10:00:00.000000Z"
        }
    ],
    "links": {
        "first": "http://localhost/api/communities?page=1",
        "last": "http://localhost/api/communities?page=1",
        "prev": null,
        "next": null
    },
    "meta": {
        "current_page": 1,
        "from": 1,
        "last_page": 1,
        "path": "http://localhost/api/communities",
        "per_page": 20,
        "to": 1,
        "total": 1
    }
}
```

---

### 2. Liste de toutes les communautés (Non paginée)

Récupère la liste complète de toutes les communautés sans pagination.

-   **URL** : `/api/get-all-communities`
-   **Méthode** : `GET`
-   **Auth** : Requis (Bearer Token)

#### Exemple de Réponse (Succès - 200 OK)

```json
[
    {
        "id": 1,
        "name": "Communauté Agricole",
        "creation_date": "2020-01-01",
        "created_by": 1,
        "country_id": 1,
        "created_at": "2025-12-15T10:00:00.000000Z",
        "updated_at": "2025-12-15T10:00:00.000000Z"
    }
]
```

---

### 3. Créer une communauté

Crée une nouvelle communauté.

-   **URL** : `/api/communities`
-   **Méthode** : `POST`
-   **Auth** : Requis (Bearer Token)

#### Paramètres du Body

| Champ           | Type    | Requis | Description                                        |
| :-------------- | :------ | :----- | :------------------------------------------------- |
| `name`          | string  | Oui    | Le nom de la communauté.                           |
| `creation_date` | date    | Oui    | La date de création de la communauté (YYYY-MM-DD). |
| `created_by`    | integer | Oui    | L'ID de l'utilisateur créateur.                    |
| `country_id`    | integer | Oui    | L'ID du pays de la communauté.                     |

#### Exemple de Requête

```json
{
    "name": "Nouvelle Communauté",
    "creation_date": "2025-01-01",
    "created_by": 1,
    "country_id": 1
}
```

#### Exemple de Réponse (Succès - 201 Created)

```json
{
    "id": 2,
    "name": "Nouvelle Communauté",
    "creation_date": "2025-01-01",
    "created_by": 1,
    "country_id": 1,
    "created_at": "2025-12-15T12:00:00.000000Z",
    "updated_at": "2025-12-15T12:00:00.000000Z"
}
```

---

### 4. Afficher une communauté

Récupère les détails d'une communauté spécifique.

-   **URL** : `/api/communities/{id}`
-   **Méthode** : `GET`
-   **Auth** : Requis (Bearer Token)

#### Exemple de Réponse (Succès - 200 OK)

```json
{
    "id": 1,
    "name": "Communauté Agricole",
    "creation_date": "2020-01-01",
    "created_by": 1,
    "country_id": 1,
    "created_at": "2025-12-15T10:00:00.000000Z",
    "updated_at": "2025-12-15T10:00:00.000000Z"
}
```

---

### 5. Mettre à jour une communauté

Met à jour les informations d'une communauté existante.

-   **URL** : `/api/communities/{id}`
-   **Méthode** : `PUT` ou `PATCH`
-   **Auth** : Requis (Bearer Token)

#### Paramètres du Body

| Champ           | Type    | Requis | Description                                        |
| :-------------- | :------ | :----- | :------------------------------------------------- |
| `name`          | string  | Oui    | Le nom de la communauté.                           |
| `creation_date` | date    | Oui    | La date de création de la communauté (YYYY-MM-DD). |
| `created_by`    | integer | Oui    | L'ID de l'utilisateur créateur.                    |
| `country_id`    | integer | Oui    | L'ID du pays de la communauté.                     |

#### Exemple de Requête

```json
{
    "name": "Communauté Agricole Modifiée",
    "creation_date": "2020-01-01",
    "created_by": 1,
    "country_id": 1
}
```

#### Exemple de Réponse (Succès - 200 OK)

```json
{
    "id": 1,
    "name": "Communauté Agricole Modifiée",
    "creation_date": "2020-01-01",
    "created_by": 1,
    "country_id": 1,
    "created_at": "2025-12-15T10:00:00.000000Z",
    "updated_at": "2025-12-15T12:30:00.000000Z"
}
```

---

### 6. Supprimer une communauté

Supprime une communauté de la base de données.

-   **URL** : `/api/communities/{id}`
-   **Méthode** : `DELETE`
-   **Auth** : Requis (Bearer Token)

#### Exemple de Réponse (Succès - 204 No Content)

_(Pas de contenu dans la réponse)_
