# [DigiBoard] [23.2.0] - Liste simplifiée des risques

Description : Cette version ajoute une liste simplifiée des risques, qui rassemble sur un seul écran les risques de tous les établissements avec leur cotation et l'avancement de leur plan d'action. Elle corrige aussi l'ouverture des documents Digirisk d'une autre entité sous Dolibarr 24, et dote le module d'une traduction anglaise.

**Cette version demande Saturne 23.2.1 ou supérieur.**

## Nouvelles fonctionnalités et innovations

### Liste simplifiée des risques

<!-- 📸 Ajouter une screenshot ici -->

* Une nouvelle page **Listing simplifié des risques**, accessible depuis le menu DigiBoard et depuis le menu gauche de Digirisk, présente les risques sous forme de liste lisible d'un coup d'œil : référence, pictogramme de la catégorie de danger, description, établissement, groupement ou unité de travail, cotation et plan d'action.
* Deux onglets séparent les **risques professionnels** et les **risques environnementaux**.
* Une barre de compteurs donne le nombre de risques par niveau — faible, à planifier, à traiter, inacceptable, non évalué — et filtre la liste d'un clic. Le niveau « non évalué » n'apparaît que s'il compte au moins un risque.
* Filtres par **établissement**, par **groupement ou unité de travail** et par **catégorie de danger**, plus une recherche libre sur le risque, l'unité ou la tâche.
* Pour chaque risque, le plan d'action affiche ses tâches, leur responsable, leur échéance et une **barre d'avancement** (tâches terminées sur le total, pourcentage déclaré).
* Sur une installation multi-entités, la liste réunit les risques des entités partagées ; un clic sur un risque ou une tâche d'une autre entité **bascule dans cette entité** et ouvre directement la fiche.

La page demande que Digirisk soit activé. **Les entrées de menu n'apparaissent qu'après une désactivation puis une réactivation de DigiBoard**, Dolibarr n'enregistrant les menus d'un module qu'à son activation.

## Améliorations & corrections

### Documents Digirisk

* **Sous Dolibarr 24, un document Digirisk d'une autre entité ouvert depuis le tableau de bord renvoyait « Le fichier n'existe pas ».** Dolibarr 24 remplace l'entité demandée par celle de la session avant d'appeler le module ; DigiBoard relit désormais l'entité dans la requête, quel que soit le cœur.

### Traductions

* Le module dispose désormais d'une **traduction anglaise**.
* Le bloc de partage des statistiques DigiRisk de la fiche entité Multicompany affichait ses clés brutes (`DIGIRISKSTATSSharing`) ; il est traduit.
* Les textes du module s'affichent dans la **langue de l'utilisateur**, et non dans celle de son navigateur quand les deux diffèrent.

## Comparaison des versions [23.1.1](https://github.com/Evarisk/digiboard/compare/23.1.1...23.2.0) et 23.2.0

* #85 [Lang] fix: charger la langue du module après celle de l'utilisateur [`e2d75de`](https://github.com/Evarisk/digiboard/commit/e2d75de)
* #83 [Risk] fix: ouvrir un risque ou une tâche d'une autre entité depuis la liste simplifiée [`d4479f5`](https://github.com/Evarisk/digiboard/commit/d4479f5)
* #81 [Risk] fix: update cotation pill colors (grey for low, white text on unacceptable) and hide unassessed when empty [`cc2f277`](https://github.com/Evarisk/digiboard/commit/cc2f277)
* #79 [Risk] add: simplified interactive risk listing page in Notion style [`66b4f8a`](https://github.com/Evarisk/digiboard/commit/66b4f8a)
* #77 [Lang] fix: add missing English translations and load lang file for MultiCompany sharing [`e635426`](https://github.com/Evarisk/digiboard/commit/e635426)
* #71 [Hook] fix: read requested entity from the request in checkSecureAccess [`888b1c2`](https://github.com/Evarisk/digiboard/commit/888b1c2)
