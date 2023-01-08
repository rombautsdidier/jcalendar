---
layout: default
lang: fr_FR
---

# Plugin jCalendar

Plugin pour récupérer les informations du calendrier juif.

## Configuration du plugin jCalendar


Il faut renseigner plusieurs informations dans la configuration du plugin:

- Le nombre de minutes après le coucher du soleil pour l'événement allumage de bougie
- Le nombre de minutes avant le lever du soleil pour l'événement havdalah
- L'adresse du site web (https://www.hebcal.com)
- La langue d'affichage


## Configuration

Il est possible de créer plusieurs calendriers afin de répartir les informations reçues si on le veut.
Il y a 13 options au total

- La géolocalisation: Il faudra renseigner la géolocalisation dans Jeedom ou utiliser le plugin geotrav (obligatoire pour que les commandes soient créées)
- La récupération ou non du mode chabbat et yomtov
- La récupération ou non des vacances majeures
- La récupération ou non des vacances mineures
- La récupération ou non des vacances modernes
- La récupération ou non des vacances israël et Lectures Torah
- La récupération ou non des dates hébraïque avec la possibilité d'en récupérer les plus importantes ou toutes
- La récupération ou non des fêtes mineures
- La récupération ou non des Shabbatot spéciaux
- La récupération ou non des Parashat
- La récupération ou non des Jours du Omer
- Remplacer l'affichage de langue choisie en Hébreu


## Les Commandes

Une commande sera créée par options choisie dans la configuration de l'équipement.
Elle donneront une information texte de l'événement.

Pour l'option mode chabbat et yomtov, une commande mode Chabbat sera disponible ainsi que l'heure de début du Chabbat et l'heure de fin (Havdalah). Le mode Chabbat sera actif entre la date et heure du début du Chabbat et jusqu'à la date et heure du Havdalah.


