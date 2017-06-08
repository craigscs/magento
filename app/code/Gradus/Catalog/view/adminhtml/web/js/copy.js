require(['jquery', 'Magento_Ui/js/modal/modal', 'select2'],function(q, modal) {
    q('#save-button').click(function() {
        tinymce.triggerSave();
    });
    q( document ).ajaxComplete(function() {
        q( "#copy_items" ).select2({ });
    });

    q( "#highlight_search" ).select2({
        ajax: {
            url: "/admin/catalog/product/search/",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term, // search term
                    page: params.page
                };
            },
            processResults: function (data, params) {
                params.page = params.page || 1;

                return {
                    results: data.items,
                    pagination: {
                        more: (params.page * 30) < data.total_count
                    }
                };
            },
            cache: true
        },
        escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
        minimumInputLength: 2,
        templateResult: formatRepo, // omitted for brevity, see the source of this page
        templateSelection: formatRepoSelection // omitted for brevity, see the source of this page
    });

    function formatRepo (repo) {
        if (repo.loading) return repo.text;

        var markup = "<div class='select2-result-repository clearfix'>" +
            "<div class='select2-result-repository__meta'>" +
            "<div class='select2-result-repository__title'>" + repo.mfr_num + " | " + repo.product_name+"</div>";
        "</div></div>";

        return markup;
    }

    function formatRepoSelection (repo) {
        return repo.mfr_num;
    }
});