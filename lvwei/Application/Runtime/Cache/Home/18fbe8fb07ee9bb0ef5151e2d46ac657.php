<?php if (!defined('THINK_PATH')) exit();?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no"/>

    <title>郁生源中秋礼券兑换</title>

</head>
<script src="http://apps.bdimg.com/libs/zepto/1.1.4/zepto.min.js"></script>
<style>
    *{
        padding: 0;
        margin: 0;
        border: none;
        font-family: 'Microsoft YaHei';
    }

    body{
        background-color: #D6DBC5;
        width: 100%;
        height:100%;


    }

    .container{
        width: 100%;
        height:100%;
    }
    .header img{
        margin: 0 auto;
        display: block;



    }
.container .jumbotron, .container-fluid .jumbotron {
    padding-right: 60px;
    padding-left: 60px;
}
.container .jumbotron, .container-fluid .jumbotron {
    border-radius: 6px;
}
@media screen and (min-width: 768px)
.jumbotron {
    padding-top: 48px;
    padding-bottom: 48px;
}
.jumbotron {
    padding-top: 30px;
    padding-bottom: 30px;
    margin-bottom: 30px;
    color: inherit;
    background-color: #D6DBC5;
}

p {
    display: block;
    -webkit-margin-before: 1em;
    -webkit-margin-after: 1em;
    -webkit-margin-start: 0px;
    -webkit-margin-end: 0px;
}

</style>
<body>
    <div class="container">
        <div class="header">
            <img src="/lvwei/Public/Img/loge.png">


        </div>
        <div class="body">
            
            <div class="jumbotron">
                  <h1 style="color:#a94442;text-align:center">提交成功</h1>
                  <p style="color:#8a6d3b;text-align:center">我们将次日发货</p>
                <p style="color:#8a6d3b;text-align:center">2秒钟后自动跳转....</p>
            </div>




        </div>
    </div>

<script type="text/javascript">
    window.setTimeout(function(){
        window.location.href="http://weidian.com/?userid=342020454";
    },1500);
</script>
</body>
</html>