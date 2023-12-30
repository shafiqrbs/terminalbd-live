var EditableInit = function () {

    var initEditables = function () {
        //global settings
        $.fn.editable.defaults.inputclass = 'span12 m-wrap';
        $.fn.editable.defaults.url = '/post';
        $.fn.editableform.buttons = '<button type="submit" class="btn blue editable-submit"><i class="icon-ok"></i></button>';
        $.fn.editableform.buttons += '<button type="button" class="btn editable-cancel"><i class="icon-remove"></i></button>';
    }
    $(".editable").editable(initEditables());
    $(document).on("click", ".editable-submit", function() {
        setTimeout(pageReload, 1000);
    });
    function pageReload() {
        location.reload();
    }
}

var EditableWithOutReloadInit = function () {

    var initEditables = function () {
        //global settings
        $.fn.editable.defaults.inputclass = 'span12 m-wrap';
        $.fn.editable.defaults.placement = 'right';
        $.fn.editable.defaults.url = '/post';
        $.fn.editableform.buttons = '<button type="submit" class="btn blue editable-submit"><i class="icon-ok"></i></button>';
        $.fn.editableform.buttons += '<button type="button" class="btn editable-cancel"><i class="icon-remove"></i></button>';
    }
    $(".editable").editable(initEditables());

};