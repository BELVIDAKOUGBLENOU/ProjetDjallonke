# Documentation de l'API - Gestion des Districts

Cette section détaille les endpoints disponibles pour la gestion des districts.

## Districts

L'accès à ces ressources nécessite une authentification via un token Bearer (Sanctum).

### 1. Liste des districts (Paginée)

Récupère une liste paginée des districts.

-   **URL** : `/api/districts`
-   **Méthode** : `GET`
-   **Auth** : Requis (Bearer Token)

#### Paramètres de requête (Query Parameters)

| Champ        | Type    | Description                                                     |
| :----------- | :------ | :-------------------------------------------------------------- |
| `page`       | integer | Le numéro de la page à récupérer (défaut 1).                    |
| `country_id` | integer | Filtrer les districts par ID de pays.                           |
| `imbriqued`  | boolean | Si `true` (ou `1`), inclut les sous-districts et villages liés. |

#### Exemple de Réponse (Succès - 200 OK)

```json
{
    "data": [
        {
            "id": 1,
            "name": "District A",
            "country_id": 1,
            "created_at": "2025-12-15T10:00:00.000000Z",
            "updated_at": "2025-12-15T10:00:00.000000Z"
        }
    ],
    "links": {
        "first": "http://localhost/api/districts?page=1",
        "last": "http://localhost/api/districts?page=1",
        "prev": null,
        "next": null
    },
    "meta": {
        "current_page": 1,
        "from": 1,
        "last_page": 1,
        "path": "http://localhost/api/districts",
        "per_page": 20,
        "to": 1,
        "total": 1
    }
}
```

---

### 2. Liste de tous les districts (Non paginée)

Récupère la liste complète de tous les districts sans pagination.

-   **URL** : `/api/get-all-districts`
-   **Méthode** : `GET`
-   **Auth** : Requis (Bearer Token)

#### Paramètres de requête (Query Parameters)

| Champ       | Type    | Description                                                     |
| :---------- | :------ | :-------------------------------------------------------------- |
| `imbriqued` | boolean | Si `true` (ou `1`), inclut les sous-districts et villages liés. |

#### Exemple de Réponse (Succès - 200 OK)

```json
[
    {
        "id": 1,
        "name": "District A",
        "country_id": 1,
        "created_at": "2025-12-15T10:00:00.000000Z",
        "updated_at": "2025-12-15T10:00:00.000000Z"
    }
]
```

---

### 3. Créer un district

Crée un nouveau district.

-   **URL** : `/api/districts`
-   **Méthode** : `POST`
-   **Auth** : Requis (Bearer Token)

#### Paramètres du Body

| Champ        | Type    | Requis | Description                                 |
| :----------- | :------ | :----- | :------------------------------------------ |
| `name`       | string  | Oui    | Le nom du district.                         |
| `country_id` | integer | Oui    | L'ID du pays auquel appartient le district. |

#### Exemple de Requête

```json
{
    "name": "Nouveau District",
    "country_id": 1
}
```

#### Exemple de Réponse (Succès - 201 Created)

```json
{
    "id": 2,
    "name": "Nouveau District",
    "country_id": 1,
    "created_at": "2025-12-15T12:00:00.000000Z",
    "updated_at": "2025-12-15T12:00:00.000000Z"
}
```

---

### 4. Afficher un district

Récupère les détails d'un district spécifique.

-   **URL** : `/api/districts/{id}`
-   **Méthode** : `GET`
-   **Auth** : Requis (Bearer Token)

#### Exemple de Réponse (Succès - 200 OK)

```json
{
    "id": 1,
    "name": "District A",
    "country_id": 1,
    "created_at": "2025-12-15T10:00:00.000000Z",
    "updated_at": "2025-12-15T10:00:00.000000Z"
}
```

---

### 5. Mettre à jour un district

Met à jour les informations d'un district existant.

-   **URL** : `/api/districts/{id}`
-   **Méthode** : `PUT` ou `PATCH`
-   **Auth** : Requis (Bearer Token)

#### Paramètres du Body

| Champ        | Type    | Requis | Description                                 |
| :----------- | :------ | :----- | :------------------------------------------ |
| `name`       | string  | Oui    | Le nom du district.                         |
| `country_id` | integer | Oui    | L'ID du pays auquel appartient le district. |

#### Exemple de Requête

```json
{
    "name": "District A Modifié",
    "country_id": 1
}
```

#### Exemple de Réponse (Succès - 200 OK)

```json
{
    "id": 1,
    "name": "District A Modifié",
    "country_id": 1,
    "created_at": "2025-12-15T10:00:00.000000Z",
    "updated_at": "2025-12-15T12:30:00.000000Z"
}
```

---

### 6. Supprimer un district

Supprime un district de la base de données.

-   **URL** : `/api/districts/{id}`
-   **Méthode** : `DELETE`
-   **Auth** : Requis (Bearer Token)

#### Exemple de Réponse (Succès - 204 No Content)

_(Pas de contenu dans la réponse)_
