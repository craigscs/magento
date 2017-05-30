require(['jquery'],function(q) {
    q('#copy_items').multiSelect();
    q('#save-button').click(function() {
        tinymce.triggerSave();
    });
});