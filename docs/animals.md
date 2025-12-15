# Documentation de l'API - Gestion des Animaux

Cette section détaille les endpoints disponibles pour la gestion des animaux.

## Animaux

L'accès à ces ressources nécessite une authentification via un token Bearer (Sanctum).

### 1. Liste des animaux (Paginée)

Récupère une liste paginée des animaux.

-   **URL** : `/api/animals`
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
            "uid": "ANIMAL-001",
            "created_by": 1,
            "premises_id": 1,
            "species": "Goat",
            "sex": "Male",
            "birth_date": "2023-01-01",
            "life_status": "Alive",
            "created_at": "2025-12-15T10:00:00.000000Z",
            "updated_at": "2025-12-15T10:00:00.000000Z"
        }
    ],
    "links": {
        "first": "http://localhost/api/animals?page=1",
        "last": "http://localhost/api/animals?page=1",
        "prev": null,
        "next": null
    },
    "meta": {
        "current_page": 1,
        "from": 1,
        "last_page": 1,
        "path": "http://localhost/api/animals",
        "per_page": 20,
        "to": 1,
        "total": 1
    }
}
```

---

### 2. Liste de tous les animaux (Non paginée)

Récupère la liste complète de tous les animaux sans pagination.

-   **URL** : `/api/get-all-animals`
-   **Méthode** : `GET`
-   **Auth** : Requis (Bearer Token)

#### Exemple de Réponse (Succès - 200 OK)

```json
[
    {
        "id": 1,
        "uid": "ANIMAL-001",
        "created_by": 1,
        "premises_id": 1,
        "species": "Goat",
        "sex": "Male",
        "birth_date": "2023-01-01",
        "life_status": "Alive",
        "created_at": "2025-12-15T10:00:00.000000Z",
        "updated_at": "2025-12-15T10:00:00.000000Z"
    }
]
```

---

### 3. Créer un animal

Crée un nouvel animal.

-   **URL** : `/api/animals`
-   **Méthode** : `POST`
-   **Auth** : Requis (Bearer Token)

#### Paramètres du Body

| Champ         | Type    | Requis | Description                       |
| :------------ | :------ | :----- | :-------------------------------- |
| `uid`         | string  | Oui    | L'identifiant unique de l'animal. |
| `created_by`  | integer | Oui    | L'ID de l'utilisateur créateur.   |
| `premises_id` | integer | Oui    | L'ID de l'exploitation (premise). |
| `species`     | string  | Oui    | L'espèce de l'animal.             |
| `sex`         | string  | Oui    | Le sexe de l'animal.              |
| `birth_date`  | date    | Non    | La date de naissance de l'animal. |
| `life_status` | string  | Oui    | Le statut de vie de l'animal.     |

#### Exemple de Requête

```json
{
    "uid": "ANIMAL-002",
    "created_by": 1,
    "premises_id": 1,
    "species": "Sheep",
    "sex": "Female",
    "birth_date": "2023-02-01",
    "life_status": "Alive"
}
```

#### Exemple de Réponse (Succès - 201 Created)

```json
{
    "id": 2,
    "uid": "ANIMAL-002",
    "created_by": 1,
    "premises_id": 1,
    "species": "Sheep",
    "sex": "Female",
    "birth_date": "2023-02-01",
    "life_status": "Alive",
    "created_at": "2025-12-15T12:00:00.000000Z",
    "updated_at": "2025-12-15T12:00:00.000000Z"
}
```

---

### 4. Afficher un animal

Récupère les détails d'un animal spécifique.

-   **URL** : `/api/animals/{id}`
-   **Méthode** : `GET`
-   **Auth** : Requis (Bearer Token)

#### Exemple de Réponse (Succès - 200 OK)

```json
{
    "id": 1,
    "uid": "ANIMAL-001",
    "created_by": 1,
    "premises_id": 1,
    "species": "Goat",
    "sex": "Male",
    "birth_date": "2023-01-01",
    "life_status": "Alive",
    "created_at": "2025-12-15T10:00:00.000000Z",
    "updated_at": "2025-12-15T10:00:00.000000Z"
}
```

---

### 5. Mettre à jour un animal

Met à jour les informations d'un animal existant.

-   **URL** : `/api/animals/{id}`
-   **Méthode** : `PUT` ou `PATCH`
-   **Auth** : Requis (Bearer Token)

#### Paramètres du Body

| Champ         | Type    | Requis | Description                       |
| :------------ | :------ | :----- | :-------------------------------- |
| `uid`         | string  | Oui    | L'identifiant unique de l'animal. |
| `created_by`  | integer | Oui    | L'ID de l'utilisateur créateur.   |
| `premises_id` | integer | Oui    | L'ID de l'exploitation (premise). |
| `species`     | string  | Oui    | L'espèce de l'animal.             |
| `sex`         | string  | Oui    | Le sexe de l'animal.              |
| `birth_date`  | date    | Non    | La date de naissance de l'animal. |
| `life_status` | string  | Oui    | Le statut de vie de l'animal.     |

#### Exemple de Requête

```json
{
    "uid": "ANIMAL-001",
    "created_by": 1,
    "premises_id": 1,
    "species": "Goat",
    "sex": "Male",
    "birth_date": "2023-01-01",
    "life_status": "Deceased"
}
```

#### Exemple de Réponse (Succès - 200 OK)

```json
{
    "id": 1,
    "uid": "ANIMAL-001",
    "created_by": 1,
    "premises_id": 1,
    "species": "Goat",
    "sex": "Male",
    "birth_date": "2023-01-01",
    "life_status": "Deceased",
    "created_at": "2025-12-15T10:00:00.000000Z",
    "updated_at": "2025-12-15T12:30:00.000000Z"
}
```

---

### 6. Supprimer un animal

Supprime un animal de la base de données.

-   **URL** : `/api/animals/{id}`
-   **Méthode** : `DELETE`
-   **Auth** : Requis (Bearer Token)

#### Exemple de Réponse (Succès - 204 No Content)

_(Pas de contenu dans la réponse)_
