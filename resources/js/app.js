/*
Template Name: Tapeli - Responsive Laravel Admin Dashboard
Author: Zoyothemes
Version: 1.0.0
Website: https://zoyothemes.com/
File: Main Js File
*/

import $ from 'jquery'

window.jQuery = window.$ = $


import bootstrap from 'bootstrap/dist/js/bootstrap.min';

window.bootstrap = bootstrap;

import 'simplebar'
import Waves from 'node-waves'
import feather from 'feather-icons'
import 'datatables.net'
import 'datatables.net-bs5'
import 'datatables.net-responsive'
import 'datatables.net-responsive-bs5'
import 'datatables.net-buttons'
import 'datatables.net-buttons-bs5'
import 'datatables.net-buttons/js/buttons.html5.min'
import 'datatables.net-buttons/js/buttons.flash.min'
import 'datatables.net-buttons/js/buttons.print.min'
import 'datatables.net-keytable'
import 'datatables.net-select'
// import 'waypoints'
// import 'jquery.counterup'

// Auto-hide alert function
function autoHideAlerts() {
    // Auto-hide success alerts setelah 5 detik
    const successAlerts = document.querySelectorAll('.alert-success');
    successAlerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000); // 5 detik
    });

    // Auto-hide error alerts setelah 8 detik
    const errorAlerts = document.querySelectorAll('.alert-danger');
    errorAlerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 8000); // 8 detik
    });

    // Auto-hide warning alerts setelah 6 detik
    const warningAlerts = document.querySelectorAll('.alert-warning');
    warningAlerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 6000); // 6 detik
    });

    // Auto-hide info alerts setelah 4 detik
    const infoAlerts = document.querySelectorAll('.alert-info');
    infoAlerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 4000); // 4 detik
    });
}

// Jalankan auto-hide alerts ketika DOM ready
document.addEventListener('DOMContentLoaded', function() {
    autoHideAlerts();
});

// Export function untuk digunakan di file lain
window.autoHideAlerts = autoHideAlerts;


class App {

    initComponents() {

        // Waves Effect
        Waves.init();

        // Feather Icons
        feather.replace()

        // Popovers
        var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
        var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
            return new bootstrap.Popover(popoverTriggerEl)
        })

        // Tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })

        // Toasts
        var toastElList = [].slice.call(document.querySelectorAll('.toast'))
        var toastList = toastElList.map(function (toastEl) {
            return new bootstrap.Toast(toastEl)
        })

        // Toasts Placement
        var toastPlacement = document.getElementById("toastPlacement");
        if (toastPlacement) {
            document.getElementById("selectToastPlacement").addEventListener("change", function () {
                if (!toastPlacement.dataset.originalClass) {
                    toastPlacement.dataset.originalClass = toastPlacement.className;
                }
                toastPlacement.className = toastPlacement.dataset.originalClass + " " + this.value;
            });
        }

        // liveAlert
        var alertPlaceholder = document.getElementById('liveAlertPlaceholder')
        var alertTrigger = document.getElementById('liveAlertBtn')

        function alert(message, type) {
            var wrapper = document.createElement('div')
            wrapper.innerHTML = '<div class="alert alert-' + type + ' alert-dismissible" role="alert">' + message + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>'

            alertPlaceholder.append(wrapper)
        }

        if (alertTrigger) {
            alertTrigger.addEventListener('click', function () {
                alert('Nice, you triggered this alert message!', 'primary')
            })
        }
    }

    initControls = function () {

        //  Full Screen Controls
        $('[data-toggle="fullscreen"]').on("click", function (e) {
            e.preventDefault();
            $('body').toggleClass('fullscreen-enable');
            if (!document.fullscreenElement && /* alternative standard method */ !document.mozFullScreenElement && !document.webkitFullscreenElement) {  // current working methods
                if (document.documentElement.requestFullscreen) {
                    document.documentElement.requestFullscreen();
                } else if (document.documentElement.mozRequestFullScreen) {
                    document.documentElement.mozRequestFullScreen();
                } else if (document.documentElement.webkitRequestFullscreen) {
                    document.documentElement.webkitRequestFullscreen(Element.ALLOW_KEYBOARD_INPUT);
                }
            } else {
                if (document.cancelFullScreen) {
                    document.cancelFullScreen();
                } else if (document.mozCancelFullScreen) {
                    document.mozCancelFullScreen();
                } else if (document.webkitCancelFullScreen) {
                    document.webkitCancelFullScreen();
                }
            }
        });
        document.addEventListener('fullscreenchange', exitHandler);
        document.addEventListener("webkitfullscreenchange", exitHandler);
        document.addEventListener("mozfullscreenchange", exitHandler);

        function exitHandler() {
            if (!document.webkitIsFullScreen && !document.mozFullScreen && !document.msFullscreenElement) {
                $('body').removeClass('fullscreen-enable');
            }
        }
    }

    initMenu() {
        var self = this;
        var body = document.body;

        // Menu Toggle Button ( Placed in Topbar) (Left menu collapse)
        var menuToggleBtn = document.querySelector('.button-toggle-menu');
        if (menuToggleBtn) {
            menuToggleBtn.addEventListener('click', function () {

                if (body.getAttribute('data-sidebar') == 'default') {
                    body.setAttribute('data-sidebar', 'hidden');
                } else {
                    body.setAttribute('data-sidebar', 'default');
                }
            });
        }

        const updateOnWindowResize = () => {
            if (window.innerWidth < 1040) {
                body.setAttribute('data-sidebar', 'hidden');
            } else {
                body.setAttribute('data-sidebar', 'default');
            }
        }

        updateOnWindowResize();
        window.addEventListener('resize', updateOnWindowResize)

        // sidebar - main menu
        if ($("#side-menu").length) {
            var navCollapse = $('#side-menu li .collapse');

            // open one menu at a time only
            navCollapse.on({
                'show.bs.collapse': function (event) {
                    var parent = $(event.target).parents('.collapse.show');
                    // $('#side-menu .collapse.show').not(parent).collapse('hide');
                }
            });

            // activate the menu in left side bar (Vertical Menu) based on url
            $("#side-menu a").each(function () {
                var pageUrl = window.location.href.split(/[?#]/)[0];
                if (this.href == pageUrl) {
                    $(this).addClass("active");
                    $(this).parent().addClass("menuitem-active");
                    $(this).parent().parent().parent().addClass("show");
                    $(this).parent().parent().parent().parent().addClass("menuitem-active");

                    var firstLevelParent = $(this).parent().parent().parent().parent().parent().parent();
                    if (firstLevelParent.attr('id') !== 'sidebar-menu') firstLevelParent.addClass("show");

                    $(this).parent().parent().parent().parent().parent().parent().parent().addClass("menuitem-active");

                    var secondLevelParent = $(this).parent().parent().parent().parent().parent().parent().parent().parent().parent();
                    if (secondLevelParent.attr('id') !== 'wrapper') secondLevelParent.addClass("show");

                    var upperLevelParent = $(this).parent().parent().parent().parent().parent().parent().parent().parent().parent().parent();
                    if (!upperLevelParent.is('body')) upperLevelParent.addClass("menuitem-active");
                }
            });
        }
    }

    init() {
        this.initComponents();
        this.initMenu();
        this.initControls();
    }
}

new App().init();
