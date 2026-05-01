# 🚗 Car Rental API (Symfony)

API REST pour la gestion de location de voitures.

---

## 📌 Fonctionnalités

- Authentification JWT
- Liste des voitures
- Réservation de voiture
- Modification / suppression de réservation

---

## ⚙️ Installation

```bash
composer install
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
php bin/console doctrine:fixtures:load
symfony server:start

👤 Utilisateur de test

Email: admin@mail.com

Password: 123456

🔐 Authentification
Login

POST /api/login

{
  "username": "admin@mail.com",
  "password": "123456"
}

Response
{
  "token": "JWT_TOKEN"
}

📡 Endpoints
🔹 GET /api/cars

Liste des voitures

[
  {
    "id": 1,
    "brand": "BMW",
    "model": "X5"
  }
]

🔹 GET /api/cars/{id}

Détails d'une voiture

{
  "id": 1,
  "brand": "BMW",
  "model": "X5"
}

🔹 POST /api/reservations

Créer une réservation

{
  "car_id": 1,
  "start_date": "2026-05-01",
  "end_date": "2026-05-05"
}
🔹 GET /api/users/{id}/reservations

Réservations d’un utilisateur

[
  {
    "id": 1,
    "start_date": "2026-05-01",
    "end_date": "2026-05-05",
    "car_id": 1,
    "brand": "BMW",
    "model": "X5"
  }
]

🔹 PUT /api/reservations/{id}

Modifier une réservation

{
  "start_date": "2026-05-02",
  "end_date": "2026-05-06"
}
🔹 DELETE /api/reservations/{id}

Supprimer une réservation

{
  "message": "Reservation Deleted Successfully !"
}

🧪 Tests
php bin/phpunit