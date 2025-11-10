import BaseDataTableController from './generic/base_data_table_controller.js'

/* stimulusFetch: 'lazy' */
export default class extends BaseDataTableController {
  resolveColumns() {
    if (Array.isArray(this.columnsValue) && this.columnsValue.length > 0) return this.columnsValue
    return [
      { title: 'Immatriculation', field: 'registrationNumber' },
      { title: 'Marque', field: 'brand' },
      { title: 'Modèle', field: 'model' },
      { title: 'Année', field: 'year', hozAlign: 'center', sorter: 'number' },
      { title: 'Carburant', field: 'fuelType' },
      { title: 'Boîte', field: 'transmission' },
      { title: 'Catégorie', field: 'category' },
      { title: 'Prix/Jour', field: 'dailyRentalPrice', hozAlign: 'right', formatter: (cell) => `${Number(cell.getValue() || 0).toFixed(2)} MAD` },
      { title: 'Statut', field: 'status' },
      { title: 'Actif', field: 'active', formatter: 'tickCross', hozAlign: 'center', sorter: 'boolean' },
    ]
  }
}

