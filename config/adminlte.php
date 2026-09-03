<?php

use ColorlibHQ\AdminLte\Menu\Filters\ActiveFilter;
use ColorlibHQ\AdminLte\Menu\Filters\GateFilter;
use ColorlibHQ\AdminLte\Menu\Filters\HrefFilter;
use ColorlibHQ\AdminLte\Menu\Filters\SearchFilter;

return [

    /*
    |--------------------------------------------------------------------------
    | Title
    |--------------------------------------------------------------------------
    |
    | The default page title, and an optional prefix/postfix applied to every
    | page title set with @section('title', ...).
    |
    */

    'title' => 'AfyaPRO',
    'title_prefix' => '',
    'title_postfix' => ' · AfyaPRO',

    /*
    |--------------------------------------------------------------------------
    | Favicon
    |--------------------------------------------------------------------------
    */

    'use_ico_only' => false,
    'use_full_favicon' => false,

    /*
    |--------------------------------------------------------------------------
    | Google Fonts
    |--------------------------------------------------------------------------
    |
    | AdminLTE 4 uses Source Sans 3. Set to false to self-host or skip.
    |
    */

    'google_fonts' => [
        'allowed' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Logo
    |--------------------------------------------------------------------------
    |
    | The brand logo shown in the sidebar. `logo` accepts HTML and is
    | rendered UNESCAPED ({!! !!}) — only ever put trusted, hardcoded
    | markup here, never user-supplied or database-driven content.
    |
    */

    'logo' => '<b>Afya</b>PRO',
    'logo_img' => false,
    'logo_img_class' => 'brand-image opacity-75 shadow',
    'logo_img_alt' => 'AfyaPRO Logo',

    /*
    |--------------------------------------------------------------------------
    | Authentication logo
    |--------------------------------------------------------------------------
    */

    'auth_logo' => [
        'enabled' => false,
        'img' => [
            'path' => 'vendor/adminlte/img/AdminLTELogo.png',
            'alt' => 'Auth Logo',
            'class' => '',
            'width' => 50,
            'height' => 50,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User menu (topbar dropdown)
    |--------------------------------------------------------------------------
    |
    | `usermenu_profile_url` is passed through `url()`, so set it to a path or
    | an absolute URL — not a route name. `adminlte:scaffold` prefixes its
    | routes with `admin`, so use 'admin/profile' once the profile section is
    | scaffolded. `false` hides the "Profile" button and lets "Sign out" fill
    | the footer.
    |
    */

    'usermenu_enabled' => true,
    'usermenu_header' => false,
    'usermenu_header_class' => 'text-bg-primary',
    'usermenu_image' => false,
    'usermenu_desc' => false,
    'usermenu_profile_url' => 'profile',

    /*
    |--------------------------------------------------------------------------
    | Navbar notifications & messages
    |--------------------------------------------------------------------------
    |
    | The package falls back to sample "Brad Diesel" style demo data for these
    | dropdowns until the notifications/mailbox scaffolding is wired up. Keep
    | them empty for now rather than showing fake activity to real staff.
    |
    */

    'navbar_notifications' => [],
    'navbar_messages' => [],

    /*
    |--------------------------------------------------------------------------
    | Layout
    |--------------------------------------------------------------------------
    |
    | Body-level layout switches. These map directly to AdminLTE 4 body classes.
    |
    */

    'layout_topnav' => null,
    'layout_boxed' => null,
    'layout_fixed_sidebar' => true,   // .layout-fixed
    'layout_fixed_navbar' => true,    // .fixed-header
    'layout_fixed_footer' => null,    // .fixed-footer
    'layout_dark_mode' => null,       // null = respect system / user toggle
    'layout_rtl' => false,            // Enable right-to-left layout

    /*
    |--------------------------------------------------------------------------
    | Footer & Preloader
    |--------------------------------------------------------------------------
    |
    | `footer_left` / `footer_right` accept HTML and are rendered UNESCAPED
    | ({!! !!}) — only ever put trusted, hardcoded markup here, never
    | user-supplied or database-driven content.
    |
    */

    'footer_left' => 'Copyright &copy; 2014-'.date('Y').' <a href="https://adminlte.io" class="text-decoration-none">AdminLTE.io</a>. All rights reserved.',
    'footer_right' => 'Anything you want',
    'preloader' => false,
    'control_sidebar' => false,
    'control_sidebar_theme' => 'dark',

    // Documentation URL used by the navbar "Documentation" link and the sidebar
    // "View documentation" CTA (false to hide the CTA). Defaults to the in-app
    // docs viewer served at /docs (see the `docs` keys below).
    'sidebar_docs_url' => false,

    // Bundled demo/showcase pages (Dashboard v2/v3, Widgets, UI, Forms, Tables,
    // Layout Options, Theme Generate, auth variants, error pages). Set false to
    // skip registering their routes in production.
    'demo' => false,
    'demo_middleware' => ['web', 'auth'],

    // In-app documentation viewer: renders this package's docs/*.md files at
    // /docs and /docs/{page}. Set 'docs' => false to disable the route.
    'docs' => false,
    'docs_middleware' => ['web'],

    'sidebar_breakpoint' => 'lg',     // sidebar-expand-{breakpoint}
    'sidebar_mini' => true,           // .sidebar-mini
    'sidebar_collapse' => false,      // start collapsed
    'sidebar_collapse_auto_size' => false,
    'sidebar_scrollbar_theme' => 'os-theme-light',
    'sidebar_scrollbar_auto_hide' => 'leave',

    /*
    |--------------------------------------------------------------------------
    | Color theme
    |--------------------------------------------------------------------------
    |
    | The sidebar uses data-bs-theme="dark" by default (dark sidebar on a
    | light page, matching the AdminLTE 4 demos). Set to 'light' for a light
    | sidebar.
    |
    | The four *_color keys repaint the chrome without touching SCSS: each one
    | is injected into the layout <head> as a CSS custom-property override
    | (see ColorlibHQ\AdminLte\Support\ThemeColors). Leave a key null to keep
    | the stock AdminLTE colour. `primary_color` also recolours links and the
    | primary button variants. Values must be hex — '#rgb' or '#rrggbb';
    | anything else is ignored. The /demo/theme-generator page previews these
    | live and writes the snippet for you.
    |
    */

    'sidebar_theme' => 'dark',  // 'dark' | 'light'

    'primary_color' => '#0f766e',    // brand colour: links, .btn-primary, --bs-primary
    'sidebar_color' => null,    // .app-sidebar background
    'navbar_color' => null,     // .app-header background
    'footer_color' => null,     // .app-footer background

    /*
    |--------------------------------------------------------------------------
    | Custom body / element classes
    |--------------------------------------------------------------------------
    */

    'classes_body' => '',
    'classes_brand' => '',
    'classes_brand_text' => 'fw-light',
    'classes_content_wrapper' => '',
    'classes_content_header' => '',
    'classes_content' => '',
    'classes_sidebar' => 'bg-body-secondary shadow',
    'classes_sidebar_nav' => '',
    'classes_topnav' => 'navbar-expand bg-body',
    'classes_topnav_nav' => 'navbar',
    'classes_topnav_container' => 'container-fluid',

    /*
    |--------------------------------------------------------------------------
    | Color mode toggle
    |--------------------------------------------------------------------------
    |
    | Shows the Light/Dark/Auto dropdown in the topbar (AdminLTE 4 feature).
    |
    */

    'color_mode_toggle' => true,

    /*
    |--------------------------------------------------------------------------
    | Menu
    |--------------------------------------------------------------------------
    |
    | The sidebar (and optional top-nav) menu. Each item is an array. Supported
    | keys:
    |
    |   'header'      => 'SECTION LABEL'            // a section header
    |   'text'        => 'Dashboard'               // link label (required for links)
    |   'route'       => 'dashboard'               // named route  -> url
    |   'url'         => 'admin/users'             // raw url (relative or absolute)
    |   'icon'        => 'bi bi-speedometer'       // Bootstrap Icons class
    |   'icon_color'  => 'primary'                 // optional text-{color}
    |   'label'       => 5                         // badge value
    |   'label_color' => 'primary'                 // badge color
    |   'active'      => ['admin/users*']          // url patterns that mark active
    |   'target'      => '_blank'                  // anchor target
    |   'can'         => 'view-users'              // gate/permission to show item
    |   'submenu'     => [ ...child items... ]     // nested items (treeview)
    |
    */

    'menu' => [
        ['header' => 'MAIN'],
        [
            'text' => 'Dashboard',
            'route' => 'dashboard',
            'icon' => 'bi bi-speedometer2',
        ],
        [
            'text' => 'Patients',
            'route' => 'patients.index',
            'icon' => 'bi bi-person-vcard',
            'can' => 'patients.manage',
            'active' => ['patients*'],
        ],
        [
            'text' => 'Appointments',
            'route' => 'appointments.index',
            'icon' => 'bi bi-calendar2-check',
            'can' => 'appointments.manage',
            'active' => ['appointments*'],
        ],
        [
            'text' => 'Live Queue',
            'route' => 'queue.board',
            'icon' => 'bi bi-list-ol',
            'can' => 'queue.manage',
        ],
        [
            'text' => 'OPD Consultation',
            'route' => 'opd.consultation',
            'icon' => 'bi bi-heart-pulse',
            'can' => 'opd.manage',
        ],
        [
            'text' => 'Diagnostics',
            'route' => 'diagnostics.pending',
            'icon' => 'bi bi-clipboard2-pulse',
            'can' => 'diagnostics.manage',
            'active' => ['diagnostics*'],
        ],
        [
            'text' => 'Billing',
            'route' => 'billing.pending',
            'icon' => 'bi bi-receipt',
            'can' => 'billing.manage',
            'active' => ['billing*'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Menu filters
    |--------------------------------------------------------------------------
    |
    | Filters transform each menu item before rendering. Add your own classes
    | here (must implement ColorlibHQ\AdminLte\Menu\Filters\FilterInterface).
    | The defaults handle gates, active state, hrefs, and search items.
    |
    */

    'filters' => [
        GateFilter::class,
        HrefFilter::class,
        ActiveFilter::class,
        SearchFilter::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Plugins
    |--------------------------------------------------------------------------
    |
    | Optional JavaScript libraries integrated with AdminLTE 4. Disable plugins
    | you don't use to avoid loading unnecessary assets.
    |
    */

    'plugins' => [
        'flatpickr' => [
            'enabled' => false,
            'css' => 'vendor/flatpickr/flatpickr.min.css',
            'js' => 'vendor/flatpickr/flatpickr.min.js',
        ],
        'tom_select' => [
            'enabled' => false,
            'css' => 'vendor/tom-select/tom-select.bootstrap5.min.css',
            'js' => 'vendor/tom-select/tom-select.complete.min.js',
        ],
        'tabulator' => [
            'enabled' => false,
            'css' => 'vendor/tabulator-tables/tabulator.min.css',
            'js' => 'vendor/tabulator-tables/tabulator.min.js',
        ],
        'quill' => [
            'enabled' => false,
            'css' => 'vendor/quill/quill.snow.css',
            'js' => 'vendor/quill/quill.min.js',
        ],
        'apexcharts' => [
            'enabled' => false,
            'js' => 'vendor/apexcharts/apexcharts.min.js',
        ],
        'jsvectormap' => [
            'enabled' => false,
            'css' => 'vendor/jsvectormap/jsvectormap.min.css',
            // The library first, then the world map data (registers the 'world' map).
            'js' => [
                'vendor/jsvectormap/jsvectormap.min.js',
                'vendor/jsvectormap/maps/world.js',
            ],
        ],
        'fullcalendar' => [
            'enabled' => false,
            'css' => 'vendor/fullcalendar/index.global.min.css',
            'js' => 'vendor/fullcalendar/index.global.min.js',
        ],
        'sortablejs' => [
            'enabled' => false,
            'js' => 'vendor/sortablejs/sortablejs.min.js',
        ],
    ],

];
