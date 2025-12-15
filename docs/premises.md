# Documentation de l'API - Gestion des Exploitations (Premises)

Cette section détaille les endpoints disponibles pour la gestion des exploitations.

## Exploitations (Premises)

L'accès à ces ressources nécessite une authentification via un token Bearer (Sanctum).

### 1. Liste des exploitations (Paginée)

Récupère une liste paginée des exploitations.

-   **URL** : `/api/premises`
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
            "village_id": 1,
            "created_by": 1,
            "community_id": 1,
            "code": "PREM-001",
            "address": "123 Farm Lane",
            "gps_coordinates": "12.345, 67.890",
            "type": "Farm",
            "health_status": "Good",
            "created_at": "2025-12-15T10:00:00.000000Z",
            "updated_at": "2025-12-15T10:00:00.000000Z"
        }
    ],
    "links": {
        "first": "http://localhost/api/premises?page=1",
        "last": "http://localhost/api/premises?page=1",
        "prev": null,
        "next": null
    },
    "meta": {
        "current_page": 1,
        "from": 1,
        "last_page": 1,
        "path": "http://localhost/api/premises",
        "per_page": 20,
        "to": 1,
        "total": 1
    }
}
```

---

### 2. Liste de toutes les exploitations (Non paginée)

Récupère la liste complète de toutes les exploitations sans pagination.

-   **URL** : `/api/get-all-premises`
-   **Méthode** : `GET`
-   **Auth** : Requis (Bearer Token)

#### Exemple de Réponse (Succès - 200 OK)

```json
[
    {
        "id": 1,
        "village_id": 1,
        "created_by": 1,
        "community_id": 1,
        "code": "PREM-001",
        "address": "123 Farm Lane",
        "gps_coordinates": "12.345, 67.890",
        "type": "Farm",
        "health_status": "Good",
        "created_at": "2025-12-15T10:00:00.000000Z",
        "updated_at": "2025-12-15T10:00:00.000000Z"
    }
]
```

---

### 3. Créer une exploitation

Crée une nouvelle exploitation.

-   **URL** : `/api/premises`
-   **Méthode** : `POST`
-   **Auth** : Requis (Bearer Token)

#### Paramètres du Body

| Champ             | Type    | Requis | Description                       |
| :---------------- | :------ | :----- | :-------------------------------- |
| `village_id`      | integer | Oui    | L'ID du village.                  |
| `created_by`      | integer | Oui    | L'ID de l'utilisateur créateur.   |
| `community_id`    | integer | Non    | L'ID de la communauté.            |
| `code`            | string  | Oui    | Le code unique de l'exploitation. |
| `address`         | string  | Non    | L'adresse de l'exploitation.      |
| `gps_coordinates` | string  | Non    | Les coordonnées GPS.              |
| `type`            | string  | Oui    | Le type d'exploitation.           |
| `health_status`   | string  | Non    | Le statut sanitaire.              |

#### Exemple de Requête

```json
{
    "village_id": 1,
    "created_by": 1,
    "community_id": 1,
    "code": "PREM-002",
    "address": "456 Ranch Road",
    "gps_coordinates": "12.346, 67.891",
    "type": "Ranch",
    "health_status": "Excellent"
}
```

#### Exemple de Réponse (Succès - 201 Created)

```json
{
    "id": 2,
    "village_id": 1,
    "created_by": 1,
    "community_id": 1,
    "code": "PREM-002",
    "address": "456 Ranch Road",
    "gps_coordinates": "12.346, 67.891",
    "type": "Ranch",
    "health_status": "Excellent",
    "created_at": "2025-12-15T12:00:00.000000Z",
    "updated_at": "2025-12-15T12:00:00.000000Z"
}
```

---

### 4. Afficher une exploitation

Récupère les détails d'une exploitation spécifique.

-   **URL** : `/api/premises/{id}`
-   **Méthode** : `GET`
-   **Auth** : Requis (Bearer Token)

#### Exemple de Réponse (Succès - 200 OK)

```json
{
    "id": 1,
    "village_id": 1,
    "created_by": 1,
    "community_id": 1,
    "code": "PREM-001",
    "address": "123 Farm Lane",
    "gps_coordinates": "12.345, 67.890",
    "type": "Farm",
    "health_status": "Good",
    "created_at": "2025-12-15T10:00:00.000000Z",
    "updated_at": "2025-12-15T10:00:00.000000Z"
}
```

---

### 5. Mettre à jour une exploitation

Met à jour les informations d'une exploitation existante.

-   **URL** : `/api/premises/{id}`
-   **Méthode** : `PUT` ou `PATCH`
-   **Auth** : Requis (Bearer Token)

#### Paramètres du Body

| Champ             | Type    | Requis | Description                       |
| :---------------- | :------ | :----- | :-------------------------------- |
| `village_id`      | integer | Oui    | L'ID du village.                  |
| `created_by`      | integer | Oui    | L'ID de l'utilisateur créateur.   |
| `community_id`    | integer | Non    | L'ID de la communauté.            |
| `code`            | string  | Oui    | Le code unique de l'exploitation. |
| `address`         | string  | Non    | L'adresse de l'exploitation.      |
| `gps_coordinates` | string  | Non    | Les coordonnées GPS.              |
| `type`            | string  | Oui    | Le type d'exploitation.           |
| `health_status`   | string  | Non    | Le statut sanitaire.              |

#### Exemple de Requête

```json
{
    "village_id": 1,
    "created_by": 1,
    "community_id": 1,
    "code": "PREM-001",
    "address": "123 Farm Lane",
    "gps_coordinates": "12.345, 67.890",
    "type": "Farm",
    "health_status": "Quarantine"
}
```

#### Exemple de Réponse (Succès - 200 OK)

```json
{
    "id": 1,
    "village_id": 1,
    "created_by": 1,
    "community_id": 1,
    "code": "PREM-001",
    "address": "123 Farm Lane",
    "gps_coordinates": "12.345, 67.890",
    "type": "Farm",
    "health_status": "Quarantine",
    "created_at": "2025-12-15T10:00:00.000000Z",
    "updated_at": "2025-12-15T12:30:00.000000Z"
}
```

---

### 6. Supprimer une exploitation

Supprime une exploitation de la base de données.

-   **URL** : `/api/premises/{id}`
-   **Méthode** : `DELETE`
-   **Auth** : Requis (Bearer Token)

#### Exemple de Réponse (Succès - 204 No Content)

_(Pas de contenu dans la réponse)_
