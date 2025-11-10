import { startStimulusApp } from '@symfony/stimulus-bundle';
import FiltersController from './controllers/generic/filters_controller.js';
import GlobalSearchController from './controllers/generic/global_search_controller.js';
import CustomersTableController from './controllers/customers_table_controller.js';
import CarsTableController from './controllers/cars_table_controller.js';

const app = startStimulusApp();
// register any custom, 3rd party controllers here
app.register('customers-table', CustomersTableController);
app.register('cars-table', CarsTableController);
app.register('filters', FiltersController);
app.register('global-search', GlobalSearchController);
