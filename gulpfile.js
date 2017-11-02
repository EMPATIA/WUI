const elixir = require('laravel-elixir');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */

elixir(function (mix) {
    mix.less('app.less', 'resources/assets/css');

    /*TO REMOVE*/
    mix.styles([
        // Bootstrap
        'bower_components/AdminLTE/bootstrap/css/bootstrap.css',

        // Admin LTE
        'bower_components/AdminLTE/dist/css/AdminLTE.css',
        'bower_components/AdminLTE/dist/css/skins/skin-blue-light.css',

        // DataTables
        'bower_components/AdminLTE/plugins/datatables/dataTables.bootstrap.css',
        'resources/assets/css/datatables.css',

        // Less & SCSS compiled CSS
        'resources/assets/css/app.css',

        'resources/assets/css/public.css',

        // Bootstrap datepicker
        'vendor/eternicode/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css',

        // Bootstrap timepicker
        'resources/assets/css/bootstrap-clockpicker.min.css',

        // Select2
        'vendor/select2/select2/dist/css/select2.css',

        // Nestable
        'resources/assets/css/nestable.css',

        // Toastr
        'bower_components/toastr/toastr.min.css',

        'resources/assets/css/private-layout.css'

    ], 'public/css/empatia.css', '.')
        .scripts([
            // JQuery
            'bower_components/AdminLTE/plugins/jQuery/jQuery-2.1.4.min.js',

            // Bootstrap
            'bower_components/AdminLTE/bootstrap/js/bootstrap.js',

            // DataTables
            'bower_components/AdminLTE/plugins/datatables/jquery.dataTables.js',
            'bower_components/AdminLTE/plugins/datatables/dataTables.bootstrap.js',

            // AdminLTE
            'bower_components/AdminLTE/dist/js/app.js',

            // Slimscroll
            'bower_components/AdminLTE/plugins/slimScroll/jquery.slimscroll.js',

            // FastClick
            'bower_components/AdminLTE/plugins/fastclick/fastclick.js',

            // Socket.io
            'bower_components/socket.io-client/socket.io.js',

            // Bootstrap datepicker
            'vendor/eternicode/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js',
            'vendor/eternicode/bootstrap-datepicker/dist/locales/bootstrap-datepicker.pt.min.js',
            'vendor/eternicode/bootstrap-datepicker/dist/locales/bootstrap-datepicker.en-GB.min.js',

            // Bootstrap timepicker
            'resources/assets/js/bootstrap-clockpicker.min.js',

            // Select2
            'vendor/select2/select2/dist/js/select2.full.min.js',

            // Nestable
            'resources/assets/js/jquery.nestable.js',

            // Toastr
            'bower_components/toastr/toastr.min.js',

            // OneCommon
            'resources/assets/js/OneCommon.js',

            // D3
            'bower_components/d3plus.v1.9.3/js/d3.min.js',
            'bower_components/d3plus.v1.9.3/js/d3plus.min.js',
            'bower_components/d3plus.v1.9.3/js/d3plus.full.min.js',
            //ChartJs
            'bower_components/AdminLTE/plugins/chartjs/Chart.min.js',
            //circular countdown
            'public/js/circular-countdown.min.js',

            'bower_components/REDIPS_drag/redips-drag-min.js'

        ], 'public/js/empatia.js', '.')

    /* BackOffice CSS and JS*/
        /*CSS*/
        .styles([
            // FontAwesome Icons
            'bower_components/font-awesome/css/font-awesome.css',

            // Tether - defines and manages the position of user interface (UI) elements in relation to one another on a web page (needed in bootstrap 4)
            // 'bower_components/tether/dist/css/tether.min.css',

            // Bootstrap4 Beta
            'resources/assets/js/bootstrap/4.0.0-beta/css/bootstrap.min.css',

            // DataTables CSS
            'bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css',

            // Date and Clock picker


            // Admin LTE
            /*
            'bower_components/AdminLTE/dist/css/AdminLTE.css',
            'bower_components/AdminLTE/dist/css/skins/skin-blue-light.css',
            */

            'resources/assets/css/datatables.css',

            // Less & SCSS compiled CSS
            'resources/assets/css/app.css',
            'resources/assets/css/public.css',

            // Bootstrap datepicker
            'vendor/eternicode/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css',

            // Bootstrap timepicker
            'resources/assets/css/bootstrap-clockpicker.min.css',

            // Select2
            'vendor/select2/select2/dist/css/select2.css',

            // Nestable
            'resources/assets/css/nestable.css',

            // Toastr
            'bower_components/toastr/toastr.min.css',

            'resources/assets/css/private-layout.css',
            'resources/assets/css/translations-module.css',
            'resources/assets/css/layout.css',
            'resources/assets/css/responsive.css',
            'resources/assets/css/files.css',
            'resources/assets/css/private-new.css'

        ], 'public/css/private.css', '.')
        /*JS*/
        .scripts([
            // JQuery
            'bower_components/AdminLTE/plugins/jQuery/jQuery-2.1.4.min.js',

            // DataTables JS
            'bower_components/datatables.net/js/jquery.dataTables.min.js',
            'bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js',

            // DataTables
            /*
            'bower_components/AdminLTE/plugins/datatables/jquery.dataTables.js',
            'bower_components/AdminLTE/plugins/datatables/dataTables.bootstrap.js', */

            // Tether - defines and manages the position of user interface (UI) elements in relation to one another on a web page (needed in bootstrap 4)
            //'bower_components/tether/dist/js/tether.min.js',

            // Bootstrap4 Beta
            'resources/assets/js/bootstrap/4.0.0-beta/js/popper.min.js',
            'resources/assets/js/bootstrap/4.0.0-beta/js/bootstrap.min.js',

            // Draggabilly - Make that shiz draggable
            'bower_components/packery/dist/packery.pkgd.min.js',

            // Draggabilly - Make that shiz draggable
            'bower_components/draggabilly/dist/draggabilly.pkgd.min.js',

            // Date and Clock picker
            /*
            'bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js',
            'bower_components/bootstrap-timepicker/js/bootstrap-timepicker.js',
            */

            // AdminLTE
            // 'bower_components/AdminLTE/dist/js/app.js',

            // OneCommon
            'resources/assets/js/OneCommon.js',
            // D3
             'bower_components/d3plus.v1.9.3/js/d3plus.full.js',
             // 'resources/assets/js/d3plus.full.js',

            //ChartJs
            'bower_components/AdminLTE/plugins/chartjs/Chart.min.js',
            //circular countdown
            'public/js/circular-countdown.min.js',
            
            'bower_components/REDIPS_drag/redips-drag-min.js',

            'resources/assets/js/sidebar.js',

            // Socket.io
            'bower_components/socket.io-client/socket.io.js',

            // Bootstrap datepicker
            'vendor/eternicode/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js',
            'vendor/eternicode/bootstrap-datepicker/dist/locales/bootstrap-datepicker.pt.min.js',
            'vendor/eternicode/bootstrap-datepicker/dist/locales/bootstrap-datepicker.en-GB.min.js',

            // Bootstrap timepicker
            'resources/assets/js/bootstrap-clockpicker.min.js',

            // Select2
            'vendor/select2/select2/dist/js/select2.full.min.js',

            // Nestable
            'resources/assets/js/jquery.nestable.js',

            // Toastr
            'bower_components/toastr/toastr.min.js',

            // Slimscroll
            'bower_components/jquery-slimscroll/jquery.slimscroll.js',

            // OneCommon
            'resources/assets/js/OneCommon.js',
            'bower_components/REDIPS_drag/redips-drag-min.js',
            'resources/assets/js/EmpatiaLayout.js',
            'resources/assets/js/defaults.js',
            'resources/assets/js/downloadCSV.js'

        ], 'public/js/private.js', '.')
    /*General CSS and JS*/
        /*CSS*/
    .styles([
        // Bootstrap
        'bower_components/AdminLTE/bootstrap/css/bootstrap.css',

        // DataTables
        'bower_components/AdminLTE/plugins/datatables/dataTables.bootstrap.css',
        'resources/assets/css/datatables.css',

        // Less & SCSS compiled CSS
        'resources/assets/css/app.css',
        'resources/assets/css/public.css',

        // Bootstrap datepicker
        'vendor/eternicode/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css',

        // Bootstrap timepicker
        'resources/assets/css/bootstrap-clockpicker.min.css',

        // Select2
        'vendor/select2/select2/dist/css/select2.css',

        // Nestable
        'resources/assets/css/nestable.css',

        // Toastr
        'bower_components/toastr/toastr.min.css',

    ], 'public/css/general.css', '.')
        /*JS*/
        .scripts([
            // JQuery
            'bower_components/AdminLTE/plugins/jQuery/jQuery-2.1.4.min.js',

            // Bootstrap
            'bower_components/AdminLTE/bootstrap/js/bootstrap.js',

            // DataTables
            'bower_components/AdminLTE/plugins/datatables/jquery.dataTables.js',
            'bower_components/AdminLTE/plugins/datatables/dataTables.bootstrap.js',

            // Slimscroll
            'bower_components/AdminLTE/plugins/slimScroll/jquery.slimscroll.js',

            // FastClick
            'bower_components/AdminLTE/plugins/fastclick/fastclick.js',

            // Socket.io
            'bower_components/socket.io-client/socket.io.js',

            // Bootstrap datepicker
            'vendor/eternicode/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js',
            'vendor/eternicode/bootstrap-datepicker/dist/locales/bootstrap-datepicker.pt.min.js',
            'vendor/eternicode/bootstrap-datepicker/dist/locales/bootstrap-datepicker.en-GB.min.js',

            // Bootstrap timepicker
            'resources/assets/js/bootstrap-clockpicker.min.js',

            // Select2
            'vendor/select2/select2/dist/js/select2.full.min.js',

            // Nestable
            'resources/assets/js/jquery.nestable.js',

            // Toastr
            'bower_components/toastr/toastr.min.js',

            // OneCommon
            'resources/assets/js/OneCommon.js',
            'bower_components/REDIPS_drag/redips-drag-min.js'

        ], 'public/js/general.js', '.')
        .version(
            [
                'public/css/general.css',
                'public/js/general.js',
                'public/css/empatia.css',
                'public/js/empatia.js',
                'public/css/private.css',
                'public/js/private.js',

            ], 'public');

    mix.copy('resources/assets/files/', 'public/files/');
    mix.copy('resources/assets/images/', 'public/images/');
    mix.copy('bower_components/font-awesome/fonts', 'public/fonts');
    mix.copy('bower_components/AdminLTE/bootstrap/fonts', 'public/fonts');
    mix.copy('bower_components/font-empatia/font', 'public/fonts');
    mix.copy('resources/assets/css/scheduleAttendance.css', 'public/css/');
});