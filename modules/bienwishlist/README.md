# Module BienWishlist — Favoris pour biens immobiliers

Module **compagnon** de `gestionbien` pour PrestaShop 8.2.6.

## Fonctionnalités

- Bouton cœur ❤ injecté automatiquement sur :
  - Chaque card de bien dans la liste publique (`/biens`)
  - La page détail d'un bien (`/bien/{id}`)
- **Visiteurs non connectés** : un clic sur le cœur ouvre une **modale** invitant à se connecter, avec redirection vers la page de connexion et retour automatique à la page d'origine après login.
- **Clients connectés** : ajout/retrait en AJAX sans rechargement de page, avec feedback toast.
- Page dédiée `/mes-favoris` listant tous les biens favoris du client.
- Compteur de favoris dans le compte client.
- Synchronisation automatique sur les recherches AJAX du module `gestionbien` (les boutons sont injectés sur les nouvelles cards via MutationObserver).

## Pré-requis

Le module `gestionbien` doit être **installé et activé** avant `bienwishlist` (dépendance déclarée).

## Installation

1. Décompresser le zip dans `modules/` ou téléverser via le BO PrestaShop.
2. Modules → Gestionnaire de modules → installer **« Favoris Biens Immobiliers »**.
3. Régénérer le `.htaccess` (Préférences → SEO & URL) pour activer la route `/mes-favoris`.

## URLs

| URL | Rôle |
|---|---|
| `/mes-favoris` | Page des favoris (auth requise, redirige sinon) |
| `/module/bienwishlist/ajax` | Endpoint AJAX (toggle / add / remove / get_ids) |

## Base de données

Table créée : `ps_real_estate_wishlist`

| Colonne | Type | Description |
|---|---|---|
| id_wishlist | INT PK | Auto |
| id_customer | INT | Référence client PrestaShop |
| id_property | INT | Référence bien (gestionbien) |
| date_add | DATETIME | Date d'ajout aux favoris |

Index unique `(id_customer, id_property)` qui rend l'opération `add` idempotente.

## Flux de connexion

1. Visiteur non connecté clique le cœur sur `/biens` ou `/bien/123`.
2. Modale s'ouvre : « Connectez-vous pour ajouter ce bien à vos favoris ».
3. Bouton **Se connecter** → redirection vers `/connexion?back=<url courante>`.
4. Après connexion, PrestaShop renvoie automatiquement à la page d'origine.
5. Au prochain chargement, `window.BW_CONFIG.wishlistIds` contient les IDs déjà en wishlist → les cœurs sont préremplis.

## Sécurité

- Le contrôleur AJAX retourne **401** pour toute action de modification si le client n'est pas connecté (impossible de manipuler la wishlist d'un autre client).
- La page `/mes-favoris` exige l'authentification (`$auth = true`) et redirige automatiquement vers la page de connexion sinon.
- Toutes les requêtes SQL utilisent des `(int)` casts pour éviter l'injection.
- L'index unique en base empêche les doublons.

## Personnalisation

- **Couleurs** : variables CSS en haut de `views/css/wishlist.css` (`--bw-primary`, `--bw-dark`, etc. — alignées avec gestionbien).
- **Textes** : modifier les chaînes dans la méthode `hookDisplayHeader()` de `bienwishlist.php` (clé `texts`).
- **Position du bouton sur la card** : variable CSS `top` / `right` de `.bw-heart-btn.bw-on-card`.

## Structure du module

```
bienwishlist/
├── bienwishlist.php          # Classe principale
├── config.xml
├── README.md
├── classes/
│   └── WishlistManager.php      # Logique métier (add/remove/list)
├── controllers/front/
│   ├── ajax.php              # Endpoint AJAX
│   └── wishlist.php          # Page /mes-favoris
├── sql/
│   ├── install.sql
│   └── uninstall.sql
└── views/
    ├── css/wishlist.css
    ├── js/wishlist.js        # Injection auto, modale login, AJAX
    └── templates/
        ├── front/wishlist.tpl
        └── hook/customer-account.tpl
```

## Ce qui se passe quand l'utilisateur clique

### Scénario 1 — Visiteur anonyme
```
Click cœur → JS détecte cfg.isLogged === false
          → Affiche la modale BW
          → Clic « Se connecter » → redirige vers /connexion?back=<URL courante>
```

### Scénario 2 — Client connecté
```
Click cœur → POST /module/bienwishlist/ajax (action=toggle, id_property=N)
          → Le contrôleur vérifie l'auth + l'existence du bien actif
          → Insertion ou suppression dans real_estate_wishlist
          → Retour JSON { success, in_wishlist, count, message }
          → JS met à jour tous les cœurs liés à ce bien + affiche un toast
```
