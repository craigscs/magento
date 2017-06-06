require(['jquery'],function(q) {
    q( document ).ajaxComplete(function() {
        q('[name="product[in_the_box]"]').parent().parent().hide();
        q('[name="product[features]"]').parent().parent().hide();
        q('[name="product[tech_specs]"]').parent().parent().hide();
        q('[name="product[highlights]"]').parent().parent().hide();
    })
});
