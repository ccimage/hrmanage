/**
 * Created by zhang on 2015/4/25.
 */

function insertDialog(){
    var htmlstr = '<div class="am-modal am-modal-confirm" tabindex="-1" id="my-confirm">';
    htmlstr += '<div class="am-modal-dialog">';
    htmlstr += '<div class="am-modal-hd">删除确认</div>';
    htmlstr += '<div class="am-modal-bd">';
    htmlstr += '确定要删除这条记录吗？';
    htmlstr += '</div>';
    htmlstr += '<div class="am-modal-footer">';
    htmlstr += '<span class="am-modal-btn" data-am-modal-cancel>取消</span>';
    htmlstr += '<span class="am-modal-btn" data-am-modal-confirm>确定</span>';
    htmlstr += '</div>';
    htmlstr += '</div>';
    htmlstr += '</div>';

    return htmlstr;
}

function comfirmDelete(id, deleteurl) {
    var $confirm = $('#my-confirm');
    if($confirm.length <= 0){
        var htmlstr = insertDialog();
        $("body").append(htmlstr);
    }
    $confirm = $('#my-confirm');
    $confirm.modal({
        relatedTarget: this,
        onConfirm: function (options) {
            window.location.href=deleteurl;
        },
        onCancel: function(){
            $("#dataitem-"+id).removeClass("am-danger");
            $("#my-confirm").removeData("amui.modal");
        }
    });

    $("#dataitem-"+id).addClass("am-danger");
}


