# Documentation de l'API - Gestion des Enregistrements de Poids

Cette section détaille les endpoints disponibles pour la gestion des enregistrements de poids.

## Enregistrements de Poids

L'accès à ces ressources nécessite une authentification via un token Bearer (Sanctum).

### 1. Liste des enregistrements (Paginée)

Récupère une liste paginée des enregistrements de poids.

-   **URL** : `/api/weight-records`
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
            "uid": "WEIGHT-001",
            "event_id": 1,
            "weight": 25.5,
            "age_days": 100,
            "measure_method": "Scale",
            "created_at": null,
            "updated_at": null
        }
    ],
    "links": {
        "first": "http://localhost/api/weight-records?page=1",
        "last": "http://localhost/api/weight-records?page=1",
        "prev": null,
        "next": null
    },
    "meta": {
        "current_page": 1,
        "from": 1,
        "last_page": 1,
        "path": "http://localhost/api/weight-records",
        "per_page": 20,
        "to": 1,
        "total": 1
    }
}
```

---

### 2. Liste de tous les enregistrements (Non paginée)

Récupère la liste complète de tous les enregistrements de poids sans pagination.

-   **URL** : `/api/get-all-weight-records`
-   **Méthode** : `GET`
-   **Auth** : Requis (Bearer Token)

#### Exemple de Réponse (Succès - 200 OK)

```json
[
    {
        "id": 1,
        "uid": "WEIGHT-001",
        "event_id": 1,
        "weight": 25.5,
        "age_days": 100,
        "measure_method": "Scale",
        "created_at": null,
        "updated_at": null
    }
]
```

---

### 3. Créer un enregistrement

Crée un nouvel enregistrement de poids.

-   **URL** : `/api/weight-records`
-   **Méthode** : `POST`
-   **Auth** : Requis (Bearer Token)

#### Paramètres du Body

| Champ            | Type    | Requis | Description                               |
| :--------------- | :------ | :----- | :---------------------------------------- |
| `uid`            | string  | Oui    | L'identifiant unique de l'enregistrement. |
| `event_id`       | integer | Oui    | L'ID de l'événement associé.              |
| `weight`         | numeric | Oui    | Le poids enregistré.                      |
| `age_days`       | integer | Non    | L'âge en jours au moment de la pesée.     |
| `measure_method` | string  | Non    | La méthode de mesure utilisée.            |

#### Exemple de Requête

```json
{
    "uid": "WEIGHT-002",
    "event_id": 1,
    "weight": 30.0,
    "age_days": 120,
    "measure_method": "Scale"
}
```

#### Exemple de Réponse (Succès - 201 Created)

```json
{
    "id": 2,
    "uid": "WEIGHT-002",
    "event_id": 1,
    "weight": 30.0,
    "age_days": 120,
    "measure_method": "Scale",
    "created_at": null,
    "updated_at": null
}
```

---

### 4. Afficher un enregistrement

Récupère les détails d'un enregistrement spécifique.

-   **URL** : `/api/weight-records/{id}`
-   **Méthode** : `GET`
-   **Auth** : Requis (Bearer Token)

#### Exemple de Réponse (Succès - 200 OK)

```json
{
    "id": 1,
    "uid": "WEIGHT-001",
    "event_id": 1,
    "weight": 25.5,
    "age_days": 100,
    "measure_method": "Scale",
    "created_at": null,
    "updated_at": null
}
```

---

### 5. Mettre à jour un enregistrement

Met à jour les informations d'un enregistrement existant.

-   **URL** : `/api/weight-records/{id}`
-   **Méthode** : `PUT` ou `PATCH`
-   **Auth** : Requis (Bearer Token)

#### Paramètres du Body

| Champ            | Type    | Requis | Description                               |
| :--------------- | :------ | :----- | :---------------------------------------- |
| `uid`            | string  | Oui    | L'identifiant unique de l'enregistrement. |
| `event_id`       | integer | Oui    | L'ID de l'événement associé.              |
| `weight`         | numeric | Oui    | Le poids enregistré.                      |
| `age_days`       | integer | Non    | L'âge en jours au moment de la pesée.     |
| `measure_method` | string  | Non    | La méthode de mesure utilisée.            |

#### Exemple de Requête

```json
{
    "uid": "WEIGHT-001",
    "event_id": 1,
    "weight": 26.0,
    "age_days": 100,
    "measure_method": "Scale"
}
```

#### Exemple de Réponse (Succès - 200 OK)

```json
{
    "id": 1,
    "uid": "WEIGHT-001",
    "event_id": 1,
    "weight": 26.0,
    "age_days": 100,
    "measure_method": "Scale",
    "created_at": null,
    "updated_at": null
}
```

---

### 6. Supprimer un enregistrement

Supprime un enregistrement de la base de données.

-   **URL** : `/api/weight-records/{id}`
-   **Méthode** : `DELETE`
-   **Auth** : Requis (Bearer Token)

#### Exemple de Réponse (Succès - 204 No Content)

_(Pas de contenu dans la réponse)_
