require(['jquery','datatables', 'sticky'],function($z){
    $z(window).load(function() {
        $z('#messages').dataTable({
            paging: true,
            "dom": "lifrtp",
            fixedHeader: true
        });
    })
});