# Instructions pour les agents

## Projet

- Ce depot contient le theme WordPress autonome `Foldery` du site `sebastienj.com`.
- Conserver le theme autonome : ne pas reintroduire de dependance obligatoire envers un theme parent ou un ancien plugin.
- Les styles principaux sont dans `style.css` et `assets/css/`. Les blocs dynamiques et leurs rendus PHP sont dans `inc/`.
- Preserver le comportement desktop lors d'une adaptation mobile, sauf demande explicite contraire.

## Modifications et validations

- Respecter les modifications locales existantes et ne pas inclure de fichiers sans rapport avec la demande.
- Apres une modification de CSS, JavaScript ou PHP chargee par WordPress, incrementer legerement `FOLDERY_VERSION` dans `functions.php` pour invalider le cache.
- Executer les controles pertinents avant livraison :
  - `php -l` sur les fichiers PHP modifies ;
  - `node --check` sur les fichiers JavaScript modifies ;
  - `git diff --check`.
- Ne pas conserver dans le depot les captures d'ecran ou artefacts temporaires produits pendant les tests.

## Git obligatoire apres modification

- Apres chaque modification terminee et validee, creer un commit descriptif puis pousser la branche courante vers `origin`.
- Verifier le contenu du commit avec `git status` et ne committer que les fichiers lies a la demande.
- Ne pas modifier un commit existant avec `--amend` et ne pas forcer le push, sauf demande explicite.
- Si le commit ou le push echoue, conserver les changements locaux et signaler clairement le blocage.

## Production

- Un push Git ne publie pas automatiquement le theme en production.
- Ne lancer le deploiement que sur demande explicite. La commande locale est `update sebastienj`.
- La cible distante attendue est `www/sebastienj.com/wp-content/themes/foldery`.
