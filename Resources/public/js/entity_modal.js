

modalEntityTreeExpandParentRecursive = function(elem) {

    if ($(elem).treegrid('isNode')) {
        if ($(elem).treegrid('getDepth') > 0) {
            var parent = $(elem).treegrid('getParentNode');
            $(parent).treegrid('expand');
            expandParentRecursive(parent);
        }
    }
};

modalEntityTreegrid = function(elem) {
    $(elem).treegrid({
        initialState: 'collapsed'
    });
    modalEntityTreeExpandParentRecursive($('.tree-activate', elem));
};

inputSet = function(event, elem, params) {

    $(params[0]).val(params[1]);

};

changeModalEntity = function() {
    var modal = $(this).closest('div.modal');

    $('#'+modal.data('id')).val($(this).data('id'));
    $('#'+modal.data('name')).val($(this).data('name'));
    $(modal).modal('hide')
};

$(document).ready(function() {
    $('.modalEntityTree').on('loaded.bs.modal', function (e) {
        modalEntityTreegrid($('.treegrid', this));
        $('.modalentityitem', this).on('click', changeModalEntity);
    })

});