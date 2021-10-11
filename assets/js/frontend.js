require('../css/frontend.scss');

let bsn = require('bootstrap.native');

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('[title]').forEach(function (el) {
        new bsn.Tooltip(el, {
            placement: 'bottom'
        });
    });
});