# Documentation de l'API - Gestion des Enregistrements de Performance

Cette section détaille les endpoints disponibles pour la gestion des enregistrements de performance.

## Enregistrements de Performance

L'accès à ces ressources nécessite une authentification via un token Bearer (Sanctum).

### 1. Liste des enregistrements (Paginée)

Récupère une liste paginée des enregistrements de performance.

-   **URL** : `/api/performance-records`
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
            "uid": "PERF-001",
            "created_by": 1,
            "animal_id": 1,
            "recorded_date": "2025-01-01",
            "context": "Routine Checkup",
            "created_at": "2025-12-15T10:00:00.000000Z",
            "updated_at": "2025-12-15T10:00:00.000000Z"
        }
    ],
    "links": {
        "first": "http://localhost/api/performance-records?page=1",
        "last": "http://localhost/api/performance-records?page=1",
        "prev": null,
        "next": null
    },
    "meta": {
        "current_page": 1,
        "from": 1,
        "last_page": 1,
        "path": "http://localhost/api/performance-records",
        "per_page": 20,
        "to": 1,
        "total": 1
    }
}
```

---

### 2. Liste de tous les enregistrements (Non paginée)

Récupère la liste complète de tous les enregistrements de performance sans pagination.

-   **URL** : `/api/get-all-performance-records`
-   **Méthode** : `GET`
-   **Auth** : Requis (Bearer Token)

#### Exemple de Réponse (Succès - 200 OK)

```json
[
    {
        "id": 1,
        "uid": "PERF-001",
        "created_by": 1,
        "animal_id": 1,
        "recorded_date": "2025-01-01",
        "context": "Routine Checkup",
        "created_at": "2025-12-15T10:00:00.000000Z",
        "updated_at": "2025-12-15T10:00:00.000000Z"
    }
]
```

---

### 3. Créer un enregistrement

Crée un nouvel enregistrement de performance.

-   **URL** : `/api/performance-records`
-   **Méthode** : `POST`
-   **Auth** : Requis (Bearer Token)

#### Paramètres du Body

| Champ           | Type    | Requis | Description                               |
| :-------------- | :------ | :----- | :---------------------------------------- |
| `uid`           | string  | Oui    | L'identifiant unique de l'enregistrement. |
| `created_by`    | integer | Oui    | L'ID de l'utilisateur créateur.           |
| `animal_id`     | integer | Oui    | L'ID de l'animal concerné.                |
| `recorded_date` | date    | Oui    | La date de l'enregistrement.              |
| `context`       | string  | Non    | Le contexte de l'enregistrement.          |

#### Exemple de Requête

```json
{
    "uid": "PERF-002",
    "created_by": 1,
    "animal_id": 1,
    "recorded_date": "2025-02-01",
    "context": "Vaccination"
}
```

#### Exemple de Réponse (Succès - 201 Created)

```json
{
    "id": 2,
    "uid": "PERF-002",
    "created_by": 1,
    "animal_id": 1,
    "recorded_date": "2025-02-01",
    "context": "Vaccination",
    "created_at": "2025-12-15T12:00:00.000000Z",
    "updated_at": "2025-12-15T12:00:00.000000Z"
}
```

---

### 4. Afficher un enregistrement

Récupère les détails d'un enregistrement spécifique.

-   **URL** : `/api/performance-records/{id}`
-   **Méthode** : `GET`
-   **Auth** : Requis (Bearer Token)

#### Exemple de Réponse (Succès - 200 OK)

```json
{
    "id": 1,
    "uid": "PERF-001",
    "created_by": 1,
    "animal_id": 1,
    "recorded_date": "2025-01-01",
    "context": "Routine Checkup",
    "created_at": "2025-12-15T10:00:00.000000Z",
    "updated_at": "2025-12-15T10:00:00.000000Z"
}
```

---

### 5. Mettre à jour un enregistrement

Met à jour les informations d'un enregistrement existant.

-   **URL** : `/api/performance-records/{id}`
-   **Méthode** : `PUT` ou `PATCH`
-   **Auth** : Requis (Bearer Token)

#### Paramètres du Body

| Champ           | Type    | Requis | Description                               |
| :-------------- | :------ | :----- | :---------------------------------------- |
| `uid`           | string  | Oui    | L'identifiant unique de l'enregistrement. |
| `created_by`    | integer | Oui    | L'ID de l'utilisateur créateur.           |
| `animal_id`     | integer | Oui    | L'ID de l'animal concerné.                |
| `recorded_date` | date    | Oui    | La date de l'enregistrement.              |
| `context`       | string  | Non    | Le contexte de l'enregistrement.          |

#### Exemple de Requête

```json
{
    "uid": "PERF-001",
    "created_by": 1,
    "animal_id": 1,
    "recorded_date": "2025-01-01",
    "context": "Routine Checkup - Follow up"
}
```

#### Exemple de Réponse (Succès - 200 OK)

```json
{
    "id": 1,
    "uid": "PERF-001",
    "created_by": 1,
    "animal_id": 1,
    "recorded_date": "2025-01-01",
    "context": "Routine Checkup - Follow up",
    "created_at": "2025-12-15T10:00:00.000000Z",
    "updated_at": "2025-12-15T12:30:00.000000Z"
}
```

---

### 6. Supprimer un enregistrement

Supprime un enregistrement de la base de données.

-   **URL** : `/api/performance-records/{id}`
-   **Méthode** : `DELETE`
-   **Auth** : Requis (Bearer Token)

#### Exemple de Réponse (Succès - 204 No Content)

_(Pas de contenu dans la réponse)_
