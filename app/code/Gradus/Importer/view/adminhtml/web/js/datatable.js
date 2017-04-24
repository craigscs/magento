require(['jquery','datatables'],function($){
        $(window).load(function() {
            $('#messages').dataTable({
                paging: false
            });
        })
        });