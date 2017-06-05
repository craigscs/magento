require(['jquery'],function(q) {
    q( document ).ready(function() {
        q('[name="product[in_the_box]"]').parent().parent().hide();
        q('[name="product[features]"]').parent().parent().hide();
        q('[name="product[techspecs]"]').parent().parent().hide();
        q('[name="product[highlights]"]').parent().parent().hide();
    })
});
