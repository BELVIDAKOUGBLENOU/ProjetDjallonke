# Documentation de l'API - Gestion des Personnes

Cette section détaille les endpoints disponibles pour la gestion des personnes.

## Personnes

L'accès à ces ressources nécessite une authentification via un token Bearer (Sanctum).

### 1. Liste des personnes (Paginée)

Récupère une liste paginée des personnes.

-   **URL** : `/api/persons`
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
            "name": "Jean Dupont",
            "address": "123 Rue Principale",
            "phone": "+229 97 00 00 00",
            "nationalId": "ID123456789",
            "created_at": "2025-12-15T10:00:00.000000Z",
            "updated_at": "2025-12-15T10:00:00.000000Z"
        }
    ],
    "links": {
        "first": "http://localhost/api/persons?page=1",
        "last": "http://localhost/api/persons?page=1",
        "prev": null,
        "next": null
    },
    "meta": {
        "current_page": 1,
        "from": 1,
        "last_page": 1,
        "path": "http://localhost/api/persons",
        "per_page": 20,
        "to": 1,
        "total": 1
    }
}
```

---

### 2. Liste de toutes les personnes (Non paginée)

Récupère la liste complète de toutes les personnes sans pagination.

-   **URL** : `/api/get-all-persons`
-   **Méthode** : `GET`
-   **Auth** : Requis (Bearer Token)

#### Exemple de Réponse (Succès - 200 OK)

```json
[
    {
        "id": 1,
        "name": "Jean Dupont",
        "address": "123 Rue Principale",
        "phone": "+229 97 00 00 00",
        "nationalId": "ID123456789",
        "created_at": "2025-12-15T10:00:00.000000Z",
        "updated_at": "2025-12-15T10:00:00.000000Z"
    }
]
```

---

### 3. Créer une personne

Crée une nouvelle personne.

-   **URL** : `/api/persons`
-   **Méthode** : `POST`
-   **Auth** : Requis (Bearer Token)

#### Paramètres du Body

| Champ        | Type   | Requis | Description                                |
| :----------- | :----- | :----- | :----------------------------------------- |
| `name`       | string | Oui    | Le nom complet de la personne.             |
| `address`    | string | Non    | L'adresse de la personne.                  |
| `phone`      | string | Non    | Le numéro de téléphone de la personne.     |
| `nationalId` | string | Non    | L'identifiant national (doit être unique). |

#### Exemple de Requête

```json
{
    "name": "Marie Curie",
    "address": "456 Avenue de la Science",
    "phone": "+229 96 00 00 00",
    "nationalId": "ID987654321"
}
```

#### Exemple de Réponse (Succès - 201 Created)

```json
{
    "id": 2,
    "name": "Marie Curie",
    "address": "456 Avenue de la Science",
    "phone": "+229 96 00 00 00",
    "nationalId": "ID987654321",
    "created_at": "2025-12-15T12:00:00.000000Z",
    "updated_at": "2025-12-15T12:00:00.000000Z"
}
```

---

### 4. Afficher une personne

Récupère les détails d'une personne spécifique.

-   **URL** : `/api/persons/{id}`
-   **Méthode** : `GET`
-   **Auth** : Requis (Bearer Token)

#### Exemple de Réponse (Succès - 200 OK)

```json
{
    "id": 1,
    "name": "Jean Dupont",
    "address": "123 Rue Principale",
    "phone": "+229 97 00 00 00",
    "nationalId": "ID123456789",
    "created_at": "2025-12-15T10:00:00.000000Z",
    "updated_at": "2025-12-15T10:00:00.000000Z"
}
```

---

### 5. Mettre à jour une personne

Met à jour les informations d'une personne existante.

-   **URL** : `/api/persons/{id}`
-   **Méthode** : `PUT` ou `PATCH`
-   **Auth** : Requis (Bearer Token)

#### Paramètres du Body

| Champ        | Type   | Requis | Description                                |
| :----------- | :----- | :----- | :----------------------------------------- |
| `name`       | string | Oui    | Le nom complet de la personne.             |
| `address`    | string | Non    | L'adresse de la personne.                  |
| `phone`      | string | Non    | Le numéro de téléphone de la personne.     |
| `nationalId` | string | Non    | L'identifiant national (doit être unique). |

#### Exemple de Requête

```json
{
    "name": "Jean Dupont Modifié",
    "address": "789 Nouvelle Adresse",
    "phone": "+229 97 11 11 11",
    "nationalId": "ID123456789"
}
```

#### Exemple de Réponse (Succès - 200 OK)

```json
{
    "id": 1,
    "name": "Jean Dupont Modifié",
    "address": "789 Nouvelle Adresse",
    "phone": "+229 97 11 11 11",
    "nationalId": "ID123456789",
    "created_at": "2025-12-15T10:00:00.000000Z",
    "updated_at": "2025-12-15T12:30:00.000000Z"
}
```

---

### 6. Supprimer une personne

Supprime une personne de la base de données.

-   **URL** : `/api/persons/{id}`
-   **Méthode** : `DELETE`
-   **Auth** : Requis (Bearer Token)

#### Exemple de Réponse (Succès - 204 No Content)

_(Pas de contenu dans la réponse)_
