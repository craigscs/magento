require(['jquery','chosen'],function(q){
var val = [];
q( document ).ready(function() {
    q('#page_entity_sku').chosen({width: "100%"});
    var vals =  q('#page_require_skus').val();
    if (vals != '') {
        val = vals.split(',');
    }
});
q(document).on('click', ':checkbox', function(){
    var vall = q(this).val();
    var ch = q(this).is(':checked');
    if (ch) {
        if (val.indexOf(q(this).val()) == -1) {
            val.push(vall);
        }
    } else {
        if (val.indexOf(q(this).val()) != -1) {
            var index = val.indexOf(vall);
            if (index >= 0) {
                val.splice( index, 1 );
            }
        }
    }
    val.sort(sortNumber);
    q('#page_require_skus').val(val.toString());
});

q(document).ajaxStop(function () {
    var vals =  q('#page_require_skus').val();
    var ar = vals.split(',');

    setTimeout(ar.forEach(function(element) {
        q("input:checkbox[value="+element+"]").attr("checked", true);
    }), 6000);
});

function sortNumber(a,b) {
    return a - b;
}

function waitforChecked(selector, time) {
    if(document.querySelector(selector)!=null) {
        alert("The element is displayed, you can put your code instead of this alert.")
        return;
    }
    else {
        setTimeout(function() {
            waitforChecked(selector, time);
        }, time);
    }
}
});