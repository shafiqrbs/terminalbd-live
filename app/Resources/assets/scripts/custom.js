
var CloneDiv = function(){

    return {

        //main function to initiate the theme
        init: function () {
            cloneDivHomeBlock();
            getPurchaseOrderCharts();
        }
    }

    function cloneDivHomeBlock(){

        var count = 0;
        $('#addmore').click(function(){

            var $clone = $('.clone:eq(0)').clone();
            $clone.find('[id]').each(function(){this.id+='someotherpart'});
            $clone.find(':text,textarea,:hidden' ).val("");
            $clone.attr('id', "added"+(++count));
            $('.clone:eq(0)').after($clone);
            $('#rmvbtn').removeAttr('disabled');

        });

        $('#rmvbtn').click(function(){
            $('#added'+(count--)).remove();
            if (count>0)  $('#rmvbtn').removeAttr('disabled');
            else $('#rmvbtn').attr('disabled', 'disabled');
        });
    }

    function getPurchaseOrderCharts(URL){
        console.log(URL);
        $.ajax({
            url: URL,
            dataType: 'json',
            async:false,
            success: function (data) {

                var date = data.date;
                var value = data.prquisition;
                Index.initCharts();
            }
        });

    }


}();