# spip_mots_automatiques
Un plugin pour extraire automatiquement les mots-clefs des articles

**Toute aide bienvenue pour finaliser ce plugin !**

**Ça fonctionne comme ça :**
- À l’installation ça crée une catégorie de mots-clés dédiés avec des paramètres par défaut.
- La page config permet de choisir le nombres de mots-clés voulus et la méthode d’extraction (on a que l’api Yake pour l’instant, mais c’est facile d’en rajouter).
- Quand on enregistre un article ça vérifie si les mots-clés auto on déjà été recherchés et sinon ça lance l’extraction, ajoute les nouveaux mots et fais les associations.

## Carte routière

**Il y a deux choses sur lesquels je bloque :**
- Je n’arrive pas à utiliser le pipeline post_edition, je suis toujours sur pre_edition et si je change ça ne marche plus.
- J’aimerais lancer la recherche sur tous les articles déjà existant et vu la taille j’aimerais mieux éviter de le faire à la main ! Est-ce qu’il y a une fonction qui permettrait de réenregistrer tous les articles ?
