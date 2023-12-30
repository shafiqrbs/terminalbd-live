/**
 Core script to handle the entire theme and core functions
 **/
var GlobalScript = function () {

    var tableTable = function () {
        $('.dataTable').DataTable({
            iDisplayLength: 25,
            scrollY: '50vh',
            scrollX: false,
            scrollCollapse: true,
            paging: false,
            bInfo: false,
            orderable: true,
            bSort: true,
            aoColumnDefs: [
                {
                    bSortable: false,
                    aTargets: [-1]
                }
            ]
        });
    }
    var tableInlineNext = function () {
        $('body').on('keydown', '.td-inline-input', function(event) {
            var key = event.which || event.charCode || event.keyCode
            if (key === 13) {
                var self = $(this), form = self.parents('form:eq(0)'), focusable, next;
                focusable = form.find('.td-inline-input').filter(':visible');
                next = focusable.eq(focusable.index(this)+1);
                if (next.length) {
                    next.focus();
                }
                return false;
            }
        });
    }

    var tableScriptSearch = function(){

        $('#tableScriptSearch').keyup(function(){

            // Search text
            var text = $(this).val();
            // Hide all content class element
            $('.product-content').hide();
            // Search
            $('.product-content .cta-title').each(function(){
                if($(this).text().toLowerCase().indexOf(""+text+"") != -1 ){
                    $(this).closest('.product-content').show();
                }
            });

        });
    }

    return {
        //main function to initiate the module
        init: function () {
            tableTable();
            tableInlineNext();
            tableScriptSearch();
        }

    };

}();