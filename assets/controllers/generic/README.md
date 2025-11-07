# Contrôleurs génériques Stimulus

Ce dossier regroupe les contrôleurs réutilisables pour les pages listées (tableaux avec filtrage et recherche globale).

## Vue d'ensemble
- **base_data_table_controller.js**: Classe de base abstraite pour gérer une table distante (API Platform + Tabulator).
- **filters_controller.js**: Gestion des filtres dynamiques (génère un évènement `channel:filters-changed`).
- **global_search_controller.js**: Barre de recherche globale (évènement `channel:global-search`).

## Évènements émis
| Évènement | Détail | Description |
|-----------|--------|-------------|
| `channel:filters-changed` | `{ filters: {...}, reset?: true }` | Émis à chaque modification d'un champ de filtre. Le flag `reset` force la purge totale des filtres. |
| `channel:global-search` | `{ q: string }` | Recherche globale (champ unique) ajoutée aux filtres fusionnés. |

## Intégration dans une page
```html
<!-- Filtres -->
<div
  data-controller="filters"
  data-filters-channel-value="customers"
  data-action="input->filters#onInput change->filters#onInput"
  data-filters-open-value="false">
  <!-- Champs avec attribut name -->
  <input name="lastName" />
  <input name="firstName" />
  <select name="active"><option value="">Tous</option><option value="1">Actifs</option></select>
  <button type="button" data-action="filters#clear">Réinitialiser</button>
</div>

<!-- Barre de recherche globale (ex: dans la navbar) -->
<input
  data-controller="global-search"
  data-global-search-channel-value="customers"
  data-action="input->global-search#onInput"
  placeholder="Rechercher..." />

<!-- Tableau spécifique (ex: clients) -->
<div
  data-controller="customers-table"
  data-customers-table-endpoint-value="/api/customers"
  data-customers-table-channel-value="customers"
  data-customers-table-placeholder-value="Aucun client"
  data-customers-table-order-mapping-value='{"fullName":"lastName"}'>
</div>
```

## Surcharger pour une autre ressource
Crée un contrôleur dédié qui étend la base :
```javascript
// assets/controllers/cars_table_controller.js
import BaseDataTableController from './generic/base_data_table_controller.js'

export default class extends BaseDataTableController {
  resolveColumns() {
    return [
      { title: 'Marque', field: 'brand' },
      { title: 'Modèle', field: 'model' },
      { title: 'Immatriculation', field: 'registrationNumber' },
      { title: 'Disponible', field: 'available', formatter: 'tickCross', hozAlign: 'center', sorter: 'boolean' },
    ]
  }
}
```
Puis enregistrement Stimulus dans `assets/bootstrap.js` :
```javascript
import CarsTableController from './controllers/cars_table_controller.js'
app.register('cars-table', CarsTableController)
```
Et dans le template :
```html
<div
  data-controller="cars-table"
  data-cars-table-endpoint-value="/api/cars"
  data-cars-table-channel-value="cars"
  data-cars-table-placeholder-value="Aucune voiture">
</div>
```

## Reset des filtres
Le bouton `filters#clear` émet `reset: true`; la table purge tous les paramètres de filtre et recharge la page 1. Si vous souhaitez également vider la recherche globale, écoutez cet évènement pour réinitialiser le champ d'entrée (ou dispatchez un évènement `channel:global-search` avec `{ q: '' }`).

## Bonnes pratiques
- Toujours définir `data-*-channel-value` de façon cohérente entre filtres, tableau et barre de recherche.
- Utiliser des noms d'attribut `name="..."` qui correspondent aux clés de filtre acceptées par l'API Platform (SearchFilter, BooleanFilter, etc.).
- Pour modifier le chemin de l'API (versioning), surcharger `resolveEndpoint()` dans la sous-classe.
- Fournir des colonnes personnalisées via `data-*-columns-value='[ {"title":"...","field":"..."} ]'` ou surcharger `resolveColumns()`.

## Dépendances
- Stimulus via `@symfony/stimulus-bundle`
- Tabulator (chargé par asset mapper) : utilisation de `TabulatorFull`.
- API Platform (format Hydra JSON-LD).

## Migration
L'ancien contrôleur générique `data-table` a été retiré. Utilisez soit:
- Une sous-classe dédiée (ex: customers-table, cars-table).
- Ou ajoutez un nouveau contrôleur spécifique qui étend la base abstraite.

## Licence
Libre d'utilisation dans ce projet.

