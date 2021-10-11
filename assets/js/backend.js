require('../css/backend.scss');

let bsn = require('bootstrap.native');

require('../../vendor/schulit/common-bundle/Resources/assets/js/polyfill');
require('../../vendor/schulit/common-bundle/Resources/assets/js/menu');
require('../../vendor/schulit/common-bundle/Resources/assets/js/icon-picker');

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('[title]').forEach(function (el) {
        new bsn.Tooltip(el, {
            placement: 'bottom'
        });
    });
});