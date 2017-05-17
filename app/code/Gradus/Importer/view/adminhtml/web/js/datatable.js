require(['jquery','datatables', 'sticky'],function($){
    $(window).load(function() {
        $('#messages').dataTable({
            paging: true,
            "dom": "lifrtp"
        });
        $('#tr_stick').stick_in_parent();
    })
});