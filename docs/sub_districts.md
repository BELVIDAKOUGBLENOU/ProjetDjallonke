# Documentation de l'API - Gestion des Sous-Districts

Cette section détaille les endpoints disponibles pour la gestion des sous-districts.

## Sous-Districts

L'accès à ces ressources nécessite une authentification via un token Bearer (Sanctum).

### 1. Liste des sous-districts (Paginée)

Récupère une liste paginée des sous-districts.

-   **URL** : `/api/sub-districts`
-   **Méthode** : `GET`
-   **Auth** : Requis (Bearer Token)

#### Paramètres de requête (Query Parameters)

| Champ         | Type    | Description                                      |
| :------------ | :------ | :----------------------------------------------- |
| `page`        | integer | Le numéro de la page à récupérer (défaut 1).     |
| `district_id` | integer | (Optionnel) Filtrer par l'ID du district parent. |
| `imbriqued`   | boolean | Si `true` (ou `1`), inclut les villages liés.    |

#### Exemple de Réponse (Succès - 200 OK)

```json
{
    "data": [
        {
            "id": 1,
            "name": "Sous-District A",
            "district_id": 1,
            "created_at": "2025-12-15T10:00:00.000000Z",
            "updated_at": "2025-12-15T10:00:00.000000Z"
        }
    ],
    "links": {
        "first": "http://localhost/api/sub-districts?page=1",
        "last": "http://localhost/api/sub-districts?page=1",
        "prev": null,
        "next": null
    },
    "meta": {
        "current_page": 1,
        "from": 1,
        "last_page": 1,
        "path": "http://localhost/api/sub-districts",
        "per_page": 20,
        "to": 1,
        "total": 1
    }
}
```

---

### 2. Liste de tous les sous-districts (Non paginée)

Récupère la liste complète de tous les sous-districts sans pagination.

-   **URL** : `/api/get-all-sub-districts`
-   **Méthode** : `GET`
-   **Auth** : Requis (Bearer Token)

#### Paramètres de requête (Query Parameters)

| Champ       | Type    | Description                                   |
| :---------- | :------ | :-------------------------------------------- |
| `imbriqued` | boolean | Si `true` (ou `1`), inclut les villages liés. |

#### Exemple de Réponse (Succès - 200 OK)

```json
[
    {
        "id": 1,
        "name": "Sous-District A",
        "district_id": 1,
        "created_at": "2025-12-15T10:00:00.000000Z",
        "updated_at": "2025-12-15T10:00:00.000000Z"
    }
]
```

---

### 3. Créer un sous-district

Crée un nouveau sous-district.

-   **URL** : `/api/sub-districts`
-   **Méthode** : `POST`
-   **Auth** : Requis (Bearer Token)

#### Paramètres du Body

| Champ         | Type    | Requis | Description                                          |
| :------------ | :------ | :----- | :--------------------------------------------------- |
| `name`        | string  | Oui    | Le nom du sous-district.                             |
| `district_id` | integer | Oui    | L'ID du district auquel appartient le sous-district. |

#### Exemple de Requête

```json
{
    "name": "Nouveau Sous-District",
    "district_id": 1
}
```

#### Exemple de Réponse (Succès - 201 Created)

```json
{
    "id": 2,
    "name": "Nouveau Sous-District",
    "district_id": 1,
    "created_at": "2025-12-15T12:00:00.000000Z",
    "updated_at": "2025-12-15T12:00:00.000000Z"
}
```

---

### 4. Afficher un sous-district

Récupère les détails d'un sous-district spécifique.

-   **URL** : `/api/sub-districts/{id}`
-   **Méthode** : `GET`
-   **Auth** : Requis (Bearer Token)

#### Exemple de Réponse (Succès - 200 OK)

```json
{
    "id": 1,
    "name": "Sous-District A",
    "district_id": 1,
    "created_at": "2025-12-15T10:00:00.000000Z",
    "updated_at": "2025-12-15T10:00:00.000000Z"
}
```

---

### 5. Mettre à jour un sous-district

Met à jour les informations d'un sous-district existant.

-   **URL** : `/api/sub-districts/{id}`
-   **Méthode** : `PUT` ou `PATCH`
-   **Auth** : Requis (Bearer Token)

#### Paramètres du Body

| Champ         | Type    | Requis | Description                                          |
| :------------ | :------ | :----- | :--------------------------------------------------- |
| `name`        | string  | Oui    | Le nom du sous-district.                             |
| `district_id` | integer | Oui    | L'ID du district auquel appartient le sous-district. |

#### Exemple de Requête

```json
{
    "name": "Sous-District A Modifié",
    "district_id": 1
}
```

#### Exemple de Réponse (Succès - 200 OK)

```json
{
    "id": 1,
    "name": "Sous-District A Modifié",
    "district_id": 1,
    "created_at": "2025-12-15T10:00:00.000000Z",
    "updated_at": "2025-12-15T12:30:00.000000Z"
}
```

---

### 6. Supprimer un sous-district

Supprime un sous-district de la base de données.

-   **URL** : `/api/sub-districts/{id}`
-   **Méthode** : `DELETE`
-   **Auth** : Requis (Bearer Token)

#### Exemple de Réponse (Succès - 204 No Content)

_(Pas de contenu dans la réponse)_
