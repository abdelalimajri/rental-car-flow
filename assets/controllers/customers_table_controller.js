import BaseDataTableController from './generic/base_data_table_controller.js'

/* stimulusFetch: 'lazy' */
export default class extends BaseDataTableController {
    // Only provide a columns fallback when none are passed via data-customers-table-columns-value
    resolveColumns() {
        if (Array.isArray(this.columnsValue) && this.columnsValue.length > 0) return this.columnsValue
        return [
            { title: 'Nom complet', field: 'fullName' },
            { title: 'Email', field: 'email' },
            { title: 'Téléphone', field: 'phoneNumber' },
            { title: 'CIN', field: 'identityNumber' },
            { title: 'Actif', field: 'active', formatter: 'tickCross', hozAlign: 'center', sorter: 'boolean' },
        ]
    }
}
