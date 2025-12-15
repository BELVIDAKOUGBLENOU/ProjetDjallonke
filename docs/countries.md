# Documentation de l'API - Gestion des Pays (Countries)

Cette section détaille les endpoints disponibles pour la gestion des pays.

## Pays

L'accès à ces ressources nécessite une authentification via un token Bearer (Sanctum).

### 1. Liste des pays (Paginée)

Récupère une liste paginée des pays.

-   **URL** : `/api/countries`
-   **Méthode** : `GET`
-   **Auth** : Requis (Bearer Token)

#### Paramètres de requête (Query Parameters)

| Champ       | Type    | Description                                                                |
| :---------- | :------ | :------------------------------------------------------------------------- |
| `page`      | integer | Le numéro de la page à récupérer (défaut 1).                               |
| `imbriqued` | boolean | Si `true` (ou `1`), inclut les districts, sous-districts et villages liés. |

#### Exemple de Réponse (Succès - 200 OK)

```json
{
    "data": [
        {
            "id": 1,
            "name": "Bénin",
            "code_iso": "BJ",
            "created_at": "2025-12-15T10:00:00.000000Z",
            "updated_at": "2025-12-15T10:00:00.000000Z"
        },
        {
            "id": 2,
            "name": "Togo",
            "code_iso": "TG",
            "created_at": "2025-12-15T10:00:00.000000Z",
            "updated_at": "2025-12-15T10:00:00.000000Z"
        }
    ],
    "links": {
        "first": "http://localhost/api/countries?page=1",
        "last": "http://localhost/api/countries?page=1",
        "prev": null,
        "next": null
    },
    "meta": {
        "current_page": 1,
        "from": 1,
        "last_page": 1,
        "links": [
            {
                "url": null,
                "label": "&laquo; Previous",
                "active": false
            },
            {
                "url": "http://localhost/api/countries?page=1",
                "label": "1",
                "active": true
            },
            {
                "url": null,
                "label": "Next &raquo;",
                "active": false
            }
        ],
        "path": "http://localhost/api/countries",
        "per_page": 20,
        "to": 2,
        "total": 2
    }
}
```

---

### 2. Liste de tous les pays (Non paginée)

Récupère la liste complète de tous les pays sans pagination.

-   **URL** : `/api/get-all-countries`
-   **Méthode** : `GET`
-   **Auth** : Requis (Bearer Token)

#### Paramètres de requête (Query Parameters)

| Champ       | Type    | Description                                                                |
| :---------- | :------ | :------------------------------------------------------------------------- |
| `imbriqued` | boolean | Si `true` (ou `1`), inclut les districts, sous-districts et villages liés. |

#### Exemple de Réponse (Succès - 200 OK)

```json
[
    {
        "id": 1,
        "name": "Bénin",
        "code_iso": "BJ",
        "created_at": "2025-12-15T10:00:00.000000Z",
        "updated_at": "2025-12-15T10:00:00.000000Z"
    },
    {
        "id": 2,
        "name": "Togo",
        "code_iso": "TG",
        "created_at": "2025-12-15T10:00:00.000000Z",
        "updated_at": "2025-12-15T10:00:00.000000Z"
    }
]
```

---

### 3. Créer un pays

Crée un nouveau pays.

-   **URL** : `/api/countries`
-   **Méthode** : `POST`
-   **Auth** : Requis (Bearer Token)

#### Paramètres du Body

| Champ      | Type   | Requis | Description                       |
| :--------- | :----- | :----- | :-------------------------------- |
| `name`     | string | Oui    | Le nom du pays.                   |
| `code_iso` | string | Oui    | Le code ISO du pays (ex: BJ, FR). |

#### Exemple de Requête

```json
{
    "name": "Sénégal",
    "code_iso": "SN"
}
```

#### Exemple de Réponse (Succès - 201 Created)

```json
{
    "id": 3,
    "name": "Sénégal",
    "code_iso": "SN",
    "created_at": "2025-12-15T12:00:00.000000Z",
    "updated_at": "2025-12-15T12:00:00.000000Z"
}
```

---

### 4. Afficher un pays

Récupère les détails d'un pays spécifique.

-   **URL** : `/api/countries/{id}`
-   **Méthode** : `GET`
-   **Auth** : Requis (Bearer Token)

#### Exemple de Réponse (Succès - 200 OK)

```json
{
    "id": 1,
    "name": "Bénin",
    "code_iso": "BJ",
    "created_at": "2025-12-15T10:00:00.000000Z",
    "updated_at": "2025-12-15T10:00:00.000000Z"
}
```

---

### 5. Mettre à jour un pays

Met à jour les informations d'un pays existant.

-   **URL** : `/api/countries/{id}`
-   **Méthode** : `PUT` ou `PATCH`
-   **Auth** : Requis (Bearer Token)

#### Paramètres du Body

| Champ      | Type   | Requis | Description                       |
| :--------- | :----- | :----- | :-------------------------------- |
| `name`     | string | Oui    | Le nom du pays.                   |
| `code_iso` | string | Oui    | Le code ISO du pays (ex: BJ, FR). |

#### Exemple de Requête

```json
{
    "name": "République du Bénin",
    "code_iso": "BJ"
}
```

#### Exemple de Réponse (Succès - 200 OK)

```json
{
    "id": 1,
    "name": "République du Bénin",
    "code_iso": "BJ",
    "created_at": "2025-12-15T10:00:00.000000Z",
    "updated_at": "2025-12-15T12:30:00.000000Z"
}
```

---

### 6. Supprimer un pays

Supprime un pays de la base de données.

-   **URL** : `/api/countries/{id}`
-   **Méthode** : `DELETE`
-   **Auth** : Requis (Bearer Token)

#### Exemple de Réponse (Succès - 204 No Content)

_(Pas de contenu dans la réponse)_
