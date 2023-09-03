require('../css/backend.scss');

import { Tooltip } from "bootstrap";

require('../../vendor/schulit/common-bundle/Resources/assets/js/polyfill');
require('../../vendor/schulit/common-bundle/Resources/assets/js/menu');
require('../../vendor/schulit/common-bundle/Resources/assets/js/icon-picker');

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('[title]').forEach(function (el) {
        new Tooltip(el, {
            placement: 'bottom'
        });
    });
});