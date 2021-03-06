<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>角色管理</title>
</head>
<link href="//cdn.bootcss.com/bootstrap/3.3.5/css/bootstrap.min.css" rel="stylesheet">
<script src="//cdn.bootcss.com/jquery/1.11.3/jquery.min.js"></script>
<!--<script src="/lvwei/Public/Js/bootstrap/bootstrap.min.js"></script>-->
<script src="//cdn.bootcss.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
<body>
<?php echo ($head); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-2">
            <ul class="nav nav-pills nav-stacked nav-pills-stacked-example">
                <li role="presentation"><a href="<?php echo U("Rbac/Rbac/actionOperationUser") ?>">管理员管理</a></li>
                <li role="presentation" class="active"><a href="#">增加角色</a></li>
                <li role="presentation"><a href="#">增加权限</a></li>
            </ul>
        </div>
        <div class="col-md-10">
            <table class="table table-hover">
                <tr>
                    <th>编号</th>
                    <th>名称</th>
                    <th>备注</th>
                    <th>状态</th>
                    <th>操作</th>
                </tr>
                <?php if(is_array($roleInfoDataDB)): foreach($roleInfoDataDB as $k=>$v): ?><tr>
                        <td><?php echo ($v["id"]); ?></td>
                        <td><?php echo ($v["name"]); ?></td>
                        <td><?php echo ($v["remark"]); ?></td>
                        <td><?php if($v["status"] == 0): ?>正常<?php else: ?>停用<?php endif; ?></td>
                        <td>
                            <button data-id="<?php echo ($v["id"]); ?>" type="button" class="btn btn-warning btn-xs"><?php if($v["status"] == 1): ?>启用<?php else: ?>停用<?php endif; ?></button>
                            <button data-id="<?php echo ($v["id"]); ?>" type="button" class="btn btn-danger btn-xs">删除</button>
                            <button data-id="<?php echo ($v["id"]); ?>" data-opertype="operationnode" type="button" class="btn btn-danger btn-xs">分配权限</button>
                        </td>
                    </tr><?php endforeach; endif; ?>
            </table>
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal" data-whatever="@mdo">角色</button>
        </div>
    </div>
    <div class="modal fade" id="operationnode" role="dialog" aria-labelledby="gridSystemModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="gridSystemModalLabel">选择修改项</h4>
                    <input type="hidden" value="0" name="id">
                </div>
                <div class="modal-body">
                    <table class="table table-hover">
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">保存</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
</div>
<script>
    var OperationAdmin = function(){

        var checkTreeControl = function(obj){
            var trObj = obj.parentNode.parentNode.parentNode.parentNode;
            var level = obj.getAttribute('data-level');
            var saveObj = [];
            while(trObj.nextSibling.tagName == 'TR'){
                trObj = trObj.nextSibling;
                if(level >= trObj.getAttribute('data-level')){
                    break;
                }
                saveObj.push(trObj);
                trObj.querySelectorAll('input')[0].checked=true;
            }
        };

        var _clickTrOperation = function(event){
            if(event.target.tagName != 'BUTTON') return false;

            if(event.target.getAttribute('data-opertype') == 'operationnode'){
                var id = event.target.getAttribute('data-id');
                $.get('<?php echo U('Rbac/Rbac/actionAjaxOperationFactory') ?>' , {'mode' : 'operationnode' , 'id' : id } ,function(responseData){

                    $('#operationnode input[name="id"]').val(id);
                    $('#operationnode .modal-body .table').html(responseData);
                    $('#operationnode').modal('show');
                });
            }
        };

        var _initi = function(){
            $('.table-hover tr').on('click' , _clickTrOperation);

            $('#operationnode .modal-footer .btn-primary').on('click' , function(){
                var id = $('#operationnode input[name="id"]').val();
                var obj = $("input[name='nodeid[]']:checked");
                var checkedArr = [];
                for(var i = 0 ; i < obj.length ; i++){
                    checkedArr.push($("input[name='nodeid[]']:checked")[i].value);
                }
                var arrStr = checkedArr.join(',');
                $.get('<?php echo U('Rbac/Rbac/actionAjaxOperationFactory') ?>' , {'mode' : 'modifyusernode' , 'id' : id , 'content' : arrStr } ,function(responseData){
                    $('#operationnode').modal('hide');
                });
            });

        }();


        return {

            checkTreeControl:checkTreeControl

        }

    }();



</script>



</body>
</html>