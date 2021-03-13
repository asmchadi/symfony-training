**SQLI - Formation Symfony 4** 
--
# Contenu du projet
## _Les fonctionnalités incluses dans le repo_ :
_TWIG_
- Intégration de la template de base du projet. 
- Ajout de 2 extensions Twig :
    - **src/Twig/CartExtension.php**: Cette extension comporte 2 fonctions `totalCart` et `totalOrderLine` et 2 filtres `minimize` et `minimizeLabel`
    - **src/Twig/CartExtensionRuntime.php**: Un exemple d'extension Twig permettant d'ajouter des extensions LAZY
- Personnalisation des templates pour les CollectionType et les erreurs etc.
- Personnalisation de la page d'erreur 404
- Ajout des messages `flash`
- Inclusion des sous templates (include)
- Appel et affichage des controllers avec twig `{{render(controller())}}`
- Generation des urls grâce à `url` et `path`
- Affichage de la liste des produits (aussi par categories + affichage de nombre de produits par catégorie)
- Un formulaire de recherche

_REQEST ET RESPONSE_
- La gestion des routes: Création des différentes page du projet
- Transformation d'un slug en objet grâce au ParamConverter lors de l'affichage des pages Produit et Catégorie
- Utilisation des wildcard : les routes paramétrées 
- Utilisation des sesssions.

_DOCTRINE_
- Gestion des requêtes complexes: Rechercher avec des `OR` et `AND` et utilisation de la méthode `expr()`
- Découverte des différentes fonctions `find()`, `findOneBy()`, `findBy()`
- Manipuler l'EntityManager (persist / flush)
- Utilisation des requêtes SQL raw (partie des *related products*)

_SERVICES_
- Injection de dépendences : Création des services et injecter d'autres services via le constructeur et des méthodes (par exemple `setters`) 
- Injection des paramètres dans des services
- Utilisation de `bind`
- Injection des services via des interfaces
- Rendre les services non partagés (`shared: false`)
- Le fichier `services.yaml`


_FORMULAIRES_
- Création des formulaires
- Validation des formulaires : les champs / vérification de la quantité
- Découverte des différents types de champs: TextType, SubmitType, CollectionType...
- Envoi de mail grâce à Swift_Mailer

_CONSOLE COMMANDE_
- Une commande symfony permettant de lister les paniers enregistrés 
- Possibilité d'afficher les détails d'une commande / modifier le statut
- Gestion des erreurs
- Découverte de Lifecycle d'une commande
- Styler les commandes symfony

## _Les fonctionnalités vues lors de la formation mais non incluses dans le repo_ :
- Comment utiliser les *Factory* pour injecter les services 
- Ajout de la wishlist (similaire à l'ajout de panier)

## _Fonctionnalités non incluses_ :
- L'internationalisation
- Les events et les events listeners / event subscribers
- Les events sur les formulaires

# Commandes à exécuter au lancement du projet
- Pour installer le projet, lancer la commande **composer install**
- Pour installer la base de données **php bin/console doctrine:database:create**
- Pour charger les migrations **php bin/console doctrine:migrations:migrate**
- Pour charger les fausses données **php bin/console doctrine:fixtures:load**
- Repo GIT pour les différents [exemples][git-repo]


   [git-repo]: <https://github.com/issamkhadiri1989/formation_symfony>
  