/**
 * Admin UI Components - Icon Picker, Star Rating, Toggle Switch
 */

// ============================================
// ICON PICKER
// ============================================
var FA_ICONS = [
    'fas fa-hospital','fas fa-clinic-medical','fas fa-stethoscope','fas fa-heartbeat','fas fa-paw',
    'fas fa-dog','fas fa-cat','fas fa-horse','fas fa-dove','fas fa-kiwi-bird','fas fa-fish','fas fa-spider',
    'fas fa-bone','fas fa-syringe','fas fa-pills','fas fa-capsules','fas fa-prescription-bottle',
    'fas fa-briefcase-medical','fas fa-first-aid','fas fa-ambulance','fas fa-medkit','fas fa-band-aid',
    'fas fa-teeth','fas fa-tooth','fas fa-x-ray','fas fa-microscope','fas fa-vial','fas fa-vials',
    'fas fa-dna','fas fa-flask','fas fa-thermometer-half','fas fa-weight','fas fa-eye','fas fa-ear-listen',
    'fas fa-hand-holding-heart','fas fa-hands-holding','fas fa-handshake','fas fa-house',
    'fas fa-store','fas fa-shop','fas fa-building','fas fa-city','fas fa-warehouse',
    'fas fa-truck','fas fa-car','fas fa-location-dot','fas fa-map-marker-alt','fas fa-phone',
    'fas fa-envelope','fas fa-globe','fas fa-wifi','fas fa-shield-halved','fas fa-lock',
    'fas fa-star','fas fa-heart','fas fa-circle-check','fas fa-award','fas fa-trophy','fas fa-medal',
    'fas fa-crown','fas fa-gem','fas fa-certificate','fas fa-ribbon','fas fa-thumbs-up',
    'fas fa-users','fas fa-user','fas fa-user-doctor','fas fa-user-nurse','fas fa-people-group',
    'fas fa-scissors','fas fa-shower','fas fa-bath','fas fa-soap','fas fa-spray-can',
    'fas fa-basket-shopping','fas fa-cart-shopping','fas fa-bag-shopping','fas fa-box','fas fa-boxes-stacked',
    'fas fa-leaf','fas fa-seedling','fas fa-tree','fas fa-sun','fas fa-moon','fas fa-cloud',
    'fas fa-droplet','fas fa-fire','fas fa-bolt','fas fa-snowflake',
    'fas fa-utensils','fas fa-mug-hot','fas fa-wheat-awn','fas fa-apple-whole','fas fa-carrot',
    'fas fa-camera','fas fa-image','fas fa-video','fas fa-music','fas fa-palette',
    'fas fa-graduation-cap','fas fa-book','fas fa-chalkboard-user','fas fa-school',
    'fas fa-wrench','fas fa-screwdriver-wrench','fas fa-gear','fas fa-gears','fas fa-hammer',
    'fas fa-cross','fas fa-plus','fas fa-circle-plus','fas fa-square-plus',
    'fas fa-chart-line','fas fa-chart-bar','fas fa-chart-pie',
    'fas fa-money-bill','fas fa-coins','fas fa-credit-card','fas fa-wallet',
    'fas fa-clock','fas fa-calendar','fas fa-bell','fas fa-flag','fas fa-bookmark',
    'fas fa-comment','fas fa-comments','fas fa-quote-left','fas fa-bullhorn',
    'fas fa-link','fas fa-paperclip','fas fa-file','fas fa-folder','fas fa-database',
    'fas fa-code','fas fa-terminal','fas fa-laptop','fas fa-desktop','fas fa-mobile-screen',
    'fas fa-print','fas fa-fax','fas fa-headset','fas fa-satellite-dish',
    'fas fa-plane','fas fa-ship','fas fa-bicycle','fas fa-motorcycle',
    'fas fa-puzzle-piece','fas fa-dice','fas fa-gamepad','fas fa-futbol','fas fa-baseball',
    'fas fa-recycle','fas fa-trash','fas fa-broom','fas fa-filter',
    'fas fa-circle-info','fas fa-circle-question','fas fa-circle-exclamation','fas fa-triangle-exclamation',
    'fab fa-instagram','fab fa-facebook','fab fa-whatsapp','fab fa-youtube','fab fa-tiktok','fab fa-twitter'
];

function initIconPicker(inputId, previewId, gridId) {
    var input = document.getElementById(inputId);
    var preview = document.getElementById(previewId);
    var grid = document.getElementById(gridId);
    if (!input || !grid) return;

    var current = input.value;

    function render(filter) {
        grid.innerHTML = '';
        var f = (filter || '').toLowerCase().replace(/fas |fab |fa-/g, '');
        FA_ICONS.forEach(function(ic) {
            if (f && ic.toLowerCase().replace(/fas |fab |fa-/g, '').indexOf(f) === -1) return;
            var btn = document.createElement('button');
            btn.type = 'button';
            btn.title = ic;
            btn.className = 'icon-pick-btn' + (ic === current ? ' selected' : '');
            btn.innerHTML = '<i class="' + ic + '"></i>';
            btn.onclick = function() {
                current = ic;
                input.value = ic;
                if (preview) preview.innerHTML = '<i class="' + ic + '"></i>';
                render(f);
            };
            grid.appendChild(btn);
        });
    }

    input.addEventListener('input', function() {
        current = this.value;
        if (preview) preview.innerHTML = '<i class="' + this.value + '"></i>';
        render(this.value);
    });

    render('');
}

// ============================================
// STAR RATING PICKER
// ============================================
function initStarRating(inputId, containerId) {
    var input = document.getElementById(inputId);
    var container = document.getElementById(containerId);
    if (!input || !container) return;

    var current = parseFloat(input.value) || 5;

    function render() {
        container.innerHTML = '';
        for (var i = 1; i <= 5; i++) {
            var star = document.createElement('button');
            star.type = 'button';
            star.className = 'star-btn' + (i <= current ? ' active' : '');
            star.innerHTML = '<i class="fas fa-star"></i>';
            star.setAttribute('data-val', i);
            star.onclick = function() {
                current = parseInt(this.getAttribute('data-val'));
                input.value = current;
                render();
            };
            container.appendChild(star);
        }
    }

    render();
}

// ============================================
// AUTO-INIT ON DOM READY
// ============================================
document.addEventListener('DOMContentLoaded', function() {
    // Auto-init icon pickers
    document.querySelectorAll('[data-icon-picker]').forEach(function(el) {
        var id = el.getAttribute('data-icon-picker');
        initIconPicker(id + 'Input', id + 'Preview', id + 'Grid');
    });

    // Auto-init star ratings
    document.querySelectorAll('[data-star-rating]').forEach(function(el) {
        var id = el.getAttribute('data-star-rating');
        initStarRating(id + 'Input', id + 'Stars');
    });
});
