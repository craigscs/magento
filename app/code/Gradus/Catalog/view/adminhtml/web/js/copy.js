require(['jquery', 'Magento_Ui/js/modal/modal'],function(q, modal) {
    q('#save-button').click(function() {
        tinymce.triggerSave();
    });
    var options = {
        type: 'popup',
        responsive: true,
        innerScroll: true,
        buttons: [{
            text: q.mage.__('Continue'),
            class: '',
            click: function () {
                this.closeModal();
            }
        }]
    };
        var popup = modal(options, q('#copy-mpdal'));
    q("#copy").on('click',function(){
        q("#copy-mpdal").modal("openModal");
    });
});