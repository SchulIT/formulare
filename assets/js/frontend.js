require('../css/frontend.scss');

import { Tooltip } from "bootstrap";

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('[title]').forEach(function (el) {
        new Tooltip(el, {
            placement: 'bottom'
        });
    });
});