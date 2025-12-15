# Documentation de l'API - Gestion des Villages

Cette section détaille les endpoints disponibles pour la gestion des villages.

## Villages

L'accès à ces ressources nécessite une authentification via un token Bearer (Sanctum).

### 1. Liste des villages (Paginée)

Récupère une liste paginée des villages.

-   **URL** : `/api/villages`
-   **Méthode** : `GET`
-   **Auth** : Requis (Bearer Token)

#### Paramètres de requête (Query Parameters)

| Champ             | Type    | Description                                           |
| :---------------- | :------ | :---------------------------------------------------- |
| `page`            | integer | Le numéro de la page à récupérer (défaut 1).          |
| `sub_district_id` | integer | (Optionnel) Filtrer par l'ID du sous-district parent. |

#### Exemple de Réponse (Succès - 200 OK)

```json
{
    "data": [
        {
            "id": 1,
            "name": "Village A",
            "local_code": "V001",
            "sub_district_id": 1,
            "created_at": "2025-12-15T10:00:00.000000Z",
            "updated_at": "2025-12-15T10:00:00.000000Z"
        }
    ],
    "links": {
        "first": "http://localhost/api/villages?page=1",
        "last": "http://localhost/api/villages?page=1",
        "prev": null,
        "next": null
    },
    "meta": {
        "current_page": 1,
        "from": 1,
        "last_page": 1,
        "path": "http://localhost/api/villages",
        "per_page": 20,
        "to": 1,
        "total": 1
    }
}
```

---

### 2. Liste de tous les villages (Non paginée)

Récupère la liste complète de tous les villages sans pagination.

-   **URL** : `/api/get-all-villages`
-   **Méthode** : `GET`
-   **Auth** : Requis (Bearer Token)

#### Exemple de Réponse (Succès - 200 OK)

```json
[
    {
        "id": 1,
        "name": "Village A",
        "local_code": "V001",
        "sub_district_id": 1,
        "created_at": "2025-12-15T10:00:00.000000Z",
        "updated_at": "2025-12-15T10:00:00.000000Z"
    }
]
```

---

### 3. Créer un village

Crée un nouveau village.

-   **URL** : `/api/villages`
-   **Méthode** : `POST`
-   **Auth** : Requis (Bearer Token)

#### Paramètres du Body

| Champ             | Type    | Requis | Description                                         |
| :---------------- | :------ | :----- | :-------------------------------------------------- |
| `name`            | string  | Oui    | Le nom du village.                                  |
| `local_code`      | string  | Oui    | Le code local du village.                           |
| `sub_district_id` | integer | Oui    | L'ID du sous-district auquel appartient le village. |

#### Exemple de Requête

```json
{
    "name": "Nouveau Village",
    "local_code": "V002",
    "sub_district_id": 1
}
```

#### Exemple de Réponse (Succès - 201 Created)

```json
{
    "id": 2,
    "name": "Nouveau Village",
    "local_code": "V002",
    "sub_district_id": 1,
    "created_at": "2025-12-15T12:00:00.000000Z",
    "updated_at": "2025-12-15T12:00:00.000000Z"
}
```

---

### 4. Afficher un village

Récupère les détails d'un village spécifique.

-   **URL** : `/api/villages/{id}`
-   **Méthode** : `GET`
-   **Auth** : Requis (Bearer Token)

#### Exemple de Réponse (Succès - 200 OK)

```json
{
    "id": 1,
    "name": "Village A",
    "local_code": "V001",
    "sub_district_id": 1,
    "created_at": "2025-12-15T10:00:00.000000Z",
    "updated_at": "2025-12-15T10:00:00.000000Z"
}
```

---

### 5. Mettre à jour un village

Met à jour les informations d'un village existant.

-   **URL** : `/api/villages/{id}`
-   **Méthode** : `PUT` ou `PATCH`
-   **Auth** : Requis (Bearer Token)

#### Paramètres du Body

| Champ             | Type    | Requis | Description                                         |
| :---------------- | :------ | :----- | :-------------------------------------------------- |
| `name`            | string  | Oui    | Le nom du village.                                  |
| `local_code`      | string  | Oui    | Le code local du village.                           |
| `sub_district_id` | integer | Oui    | L'ID du sous-district auquel appartient le village. |

#### Exemple de Requête

```json
{
    "name": "Village A Modifié",
    "local_code": "V001-MOD",
    "sub_district_id": 1
}
```

#### Exemple de Réponse (Succès - 200 OK)

```json
{
    "id": 1,
    "name": "Village A Modifié",
    "local_code": "V001-MOD",
    "sub_district_id": 1,
    "created_at": "2025-12-15T10:00:00.000000Z",
    "updated_at": "2025-12-15T12:30:00.000000Z"
}
```

---

### 6. Supprimer un village

Supprime un village de la base de données.

-   **URL** : `/api/villages/{id}`
-   **Méthode** : `DELETE`
-   **Auth** : Requis (Bearer Token)

#### Exemple de Réponse (Succès - 204 No Content)

_(Pas de contenu dans la réponse)_
