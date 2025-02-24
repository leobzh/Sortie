# 📄 SYMFONY-COMMANDES.md
📌 **Fichier contenant les commandes Symfony essentielles pour gérer le projet.**

## 🛠️ Création et gestion du projet Symfony

### Créer un nouveau projet Symfony
```sh
symfony new nom-du-projet --webapp
```

### Lancer le serveur Symfony (mode dev)
```sh
symfony server:start
```

### Vérifier l'état du serveur
```sh
symfony server:status
```

### Arrêter le serveur
```sh
symfony server:stop
```

---

## 🗄️ Gestion des dépendances

### Installer les dépendances
```sh
symfony composer install
```

### Ajouter une dépendance
```sh
symfony composer require nom-du-paquet
```

### Supprimer une dépendance
```sh
symfony composer remove nom-du-paquet
```

---

## 🔄 Gestion du cache

### Vider le cache (mode dev)
```sh
symfony console cache:clear
```

### Vider le cache (mode prod)
```sh
symfony console cache:clear --env=prod
```

---

## 📂 Gestion des entités et de la base de données

### Créer une entité
```sh
symfony console make:entity
```

### Générer les migrations
```sh
symfony console make:migration
```

### Appliquer les migrations
```sh
symfony console doctrine:migrations:migrate
```

### Charger des fixtures (données de test)
```sh
symfony console doctrine:fixtures:load
```

---

## 🔗 Gestion des routes et contrôleurs

### Lister toutes les routes du projet
```sh
symfony console debug:router
```

### Créer un contrôleur
```sh
symfony console make:controller NomDuControleur
```

---

## 🔧 Gestion des utilisateurs et de la sécurité

### Créer un utilisateur (avec Symfony Security)
```sh
symfony console make:user
```

### Installer et configurer l'authentification
```sh
symfony console make:auth
```

### Vérifier les rôles et permissions des utilisateurs
```sh
symfony console security:check
```

---

## 🛠️ Autres commandes utiles

### Voir toutes les commandes disponibles
```sh
symfony console list
```

### Vérifier la configuration de Symfony
```sh
symfony console about
