/*
Template Name: Tapeli - Responsive Laravel Admin Dashboard
Author: Zoyothemes
Version: 1.0.0
Website: https://zoyothemes.com/
File: Datatable init Js
*/

import 'datatables.net/js/jquery.dataTables';
// DataTable().
import 'datatables.net-bs5/js/dataTables.bootstrap5';

import 'datatables.net-keytable/js/dataTables.keyTable';
import 'datatables.net-keytable-bs5/js/keyTable.bootstrap5';

$(document).ready(function () {

    // Default Datatable
    $('#datatable').DataTable();

    //Buttons examples
    var table = $('#datatable-buttons').DataTable({
        lengthChange: false,
        buttons: ['copy', 'print']
    });

    // Key Tables
    $("#key-table").DataTable({ 
        keys: true 
    });

    // Responsive Datatable
    console.log('Inisialisasi DataTables untuk #responsive-datatable dimulai');
    if ($("#responsive-datatable").length) {
        try {
            var table = $("#responsive-datatable").DataTable({
                scrollX: true,
                responsive: false,
                keys: false, // Disable keyTable to prevent errors with empty tables
                language: {
                    emptyTable: "Tidak ada data exit meeting."
                }
            });
            
            // Prevent keyTable from initializing on this table
            if (table && table.keys) {
                table.keys.disable();
            }
            console.log('Inisialisasi DataTables untuk #responsive-datatable selesai');
        } catch (e) {
            console.error('Error initializing DataTable:', e);
        }
    }

    // Multi Selection Datatable
    $('#selection-datatable').DataTable({
        select: {
            style: 'multi'
        }
    });

    // Alternative Pagination Datatable
    $("#alternative-page-datatable").DataTable({ 
        "pagingType": "full_numbers", 
    });

    // Scroll Vertical Datatable
    $("#scroll-vertical-datatable").DataTable({ 
        scrollY: "350px", 
        scrollCollapse: true, 
        paging: false 
    });

    // Scroll Horizontal Datatable
    $('#scroll-horizontal-datatable').DataTable({ 
        scrollX: true
    });

    // Complex headers with column visibility Datatable
    $("#complex-header-datatable").DataTable({ 
        "columnDefs": [ {
            "visible": false,
            "targets": -1
        } ]
    });

    // Row created callback Datatable
    $("#row-callback-datatable").DataTable({ 
        "createdRow": function ( row, data, index ) {
            if ( data[5].replace(/[\$,]/g, '') * 1 > 150000 ) {
                $('td', row).eq(5).addClass('text-danger');
            }
        }
    }),

    // State Saving Datatable
    $("#state-saving-datatable").DataTable({ 
        stateSave: true
    });

    // Fixed Columns Datatable
    $("#fixed-columns-datatable").DataTable({ 
        scrollY: 300, 
        scrollX: true, 
        scrollCollapse: true, 
        paging: false, 
        fixedColumns: true 
    });

    // Fixed Header Database
    $('#fixed-header-datatable').DataTable( {
        responsive: true,
    });

    // table.buttons().container()
    //     .appendTo('#datatable-buttons_wrapper .col-md-6:eq(0)');

    $("#datatable_length select[name*='datatable_length']").addClass('form-select form-select-sm');
    $("#datatable_length select[name*='datatable_length']").removeClass('custom-select custom-select-sm');
    $(".dataTables_length label").addClass('form-label');
});


// $(document).ready(function() {
//     var table = $('#fixed-header-datatable').DataTable( {
//         responsive: true,
//     } );
 
//     new $.fn.dataTable.FixedHeader( table );
// } );
    