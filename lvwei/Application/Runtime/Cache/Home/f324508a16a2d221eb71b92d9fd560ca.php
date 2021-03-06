<?php if (!defined('THINK_PATH')) exit();?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
    <title>郁生源中秋礼券兑换</title>
</head>
<script src="http://apps.bdimg.com/libs/zepto/1.1.4/zepto.min.js"></script>


<script type="text/javascript">
;(function($){
  var touch = {},
    touchTimeout, tapTimeout, swipeTimeout, longTapTimeout,
    longTapDelay = 750,
    gesture

  function swipeDirection(x1, x2, y1, y2) {
    return Math.abs(x1 - x2) >=
      Math.abs(y1 - y2) ? (x1 - x2 > 0 ? 'Left' : 'Right') : (y1 - y2 > 0 ? 'Up' : 'Down')
  }

  function longTap() {
    longTapTimeout = null
    if (touch.last) {
      touch.el.trigger('longTap')
      touch = {}
    }
  }

  function cancelLongTap() {
    if (longTapTimeout) clearTimeout(longTapTimeout)
    longTapTimeout = null
  }

  function cancelAll() {
    if (touchTimeout) clearTimeout(touchTimeout)
    if (tapTimeout) clearTimeout(tapTimeout)
    if (swipeTimeout) clearTimeout(swipeTimeout)
    if (longTapTimeout) clearTimeout(longTapTimeout)
    touchTimeout = tapTimeout = swipeTimeout = longTapTimeout = null
    touch = {}
  }

  function isPrimaryTouch(event){
    return (event.pointerType == 'touch' ||
      event.pointerType == event.MSPOINTER_TYPE_TOUCH)
      && event.isPrimary
  }

  function isPointerEventType(e, type){
    return (e.type == 'pointer'+type ||
      e.type.toLowerCase() == 'mspointer'+type)
  }

  $(document).ready(function(){
    var now, delta, deltaX = 0, deltaY = 0, firstTouch, _isPointerType

    if ('MSGesture' in window) {
      gesture = new MSGesture()
      gesture.target = document.body
    }

    $(document)
      .bind('MSGestureEnd', function(e){
        var swipeDirectionFromVelocity =
          e.velocityX > 1 ? 'Right' : e.velocityX < -1 ? 'Left' : e.velocityY > 1 ? 'Down' : e.velocityY < -1 ? 'Up' : null
        if (swipeDirectionFromVelocity) {
          touch.el.trigger('swipe')
          touch.el.trigger('swipe'+ swipeDirectionFromVelocity)
        }
      })
      .on('touchstart MSPointerDown pointerdown', function(e){
        if((_isPointerType = isPointerEventType(e, 'down')) &&
          !isPrimaryTouch(e)) return
        firstTouch = _isPointerType ? e : e.touches[0]
        if (e.touches && e.touches.length === 1 && touch.x2) {
          // Clear out touch movement data if we have it sticking around
          // This can occur if touchcancel doesn't fire due to preventDefault, etc.
          touch.x2 = undefined
          touch.y2 = undefined
        }
        now = Date.now()
        delta = now - (touch.last || now)
        touch.el = $('tagName' in firstTouch.target ?
          firstTouch.target : firstTouch.target.parentNode)
        touchTimeout && clearTimeout(touchTimeout)
        touch.x1 = firstTouch.pageX
        touch.y1 = firstTouch.pageY
        if (delta > 0 && delta <= 250) touch.isDoubleTap = true
        touch.last = now
        longTapTimeout = setTimeout(longTap, longTapDelay)
        // adds the current touch contact for IE gesture recognition
        if (gesture && _isPointerType) gesture.addPointer(e.pointerId)
      })
      .on('touchmove MSPointerMove pointermove', function(e){
        if((_isPointerType = isPointerEventType(e, 'move')) &&
          !isPrimaryTouch(e)) return
        firstTouch = _isPointerType ? e : e.touches[0]
        cancelLongTap()
        touch.x2 = firstTouch.pageX
        touch.y2 = firstTouch.pageY

        deltaX += Math.abs(touch.x1 - touch.x2)
        deltaY += Math.abs(touch.y1 - touch.y2)
      })
      .on('touchend MSPointerUp pointerup', function(e){
        if((_isPointerType = isPointerEventType(e, 'up')) &&
          !isPrimaryTouch(e)) return
        cancelLongTap()

        // swipe
        if ((touch.x2 && Math.abs(touch.x1 - touch.x2) > 30) ||
            (touch.y2 && Math.abs(touch.y1 - touch.y2) > 30))

          swipeTimeout = setTimeout(function() {
            if (touch.el){
              touch.el.trigger('swipe')
              touch.el.trigger('swipe' + (swipeDirection(touch.x1, touch.x2, touch.y1, touch.y2)))
            }
            touch = {}
          }, 0)

        // normal tap
        else if ('last' in touch)
          // don't fire tap when delta position changed by more than 30 pixels,
          // for instance when moving to a point and back to origin
          if (deltaX < 30 && deltaY < 30) {
            // delay by one tick so we can cancel the 'tap' event if 'scroll' fires
            // ('tap' fires before 'scroll')
            tapTimeout = setTimeout(function() {

              // trigger universal 'tap' with the option to cancelTouch()
              // (cancelTouch cancels processing of single vs double taps for faster 'tap' response)
              var event = $.Event('tap')
              event.cancelTouch = cancelAll
              // [by paper] fix -> "TypeError: 'undefined' is not an object (evaluating 'touch.el.trigger'), when double tap
              if (touch.el) touch.el.trigger(event)

              // trigger double tap immediately
              if (touch.isDoubleTap) {
                if (touch.el) touch.el.trigger('doubleTap')
                touch = {}
              }

              // trigger single tap after 250ms of inactivity
              else {
                touchTimeout = setTimeout(function(){
                  touchTimeout = null
                  if (touch.el) touch.el.trigger('singleTap')
                  touch = {}
                }, 250)
              }
            }, 0)
          } else {
            touch = {}
          }
          deltaX = deltaY = 0

      })
      // when the browser window loses focus,
      // for example when a modal dialog is shown,
      // cancel all ongoing events
      .on('touchcancel MSPointerCancel pointercancel', cancelAll)

    // scrolling the window indicates intention of the user
    // to scroll, not tap or swipe, so cancel all ongoing events
    $(window).on('scroll', cancelAll)
  })

  ;['swipe', 'swipeLeft', 'swipeRight', 'swipeUp', 'swipeDown',
    'doubleTap', 'tap', 'singleTap', 'longTap'].forEach(function(eventName){
    $.fn[eventName] = function(callback){ return this.on(eventName, callback) }
  })
})(Zepto)    


</script>

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
.input-lg {
    height: 46px;
    padding: 10px 16px;
    font-size: 18px;
    line-height: 1.3333333;
    border-radius: 6px;
}

.form-control {
    margin: 10px auto;
    display: block;
    width: 80%;
    height: 34px;
    padding: 6px 12px;
    font-size: 14px;
    line-height: 1.42857143;
    color: #555;
    background-color: #fff;
    background-image: none;
    border: 1px solid #ccc;
    border-radius: 4px;
    -webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,.075);
    box-shadow: inset 0 1px 1px rgba(0,0,0,.075);
    -webkit-transition: border-color ease-in-out .15s,-webkit-box-shadow ease-in-out .15s;
    -o-transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;
    transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;
}

.btn-success {
    color: #fff;
    background-color: #5cb85c;
    border-color: #4cae4c;
}

.btn {
    display: inline-block;
    padding: 6px 12px;
    margin-bottom: 0;
    font-size: 14px;
    font-weight: 400;
    line-height: 1.42857143;
    text-align: center;
    white-space: nowrap;
    vertical-align: middle;
    -ms-touch-action: manipulation;
    touch-action: manipulation;
    cursor: pointer;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
    background-image: none;
    border: 1px solid transparent;
    border-radius: 4px;
}

.btn-danger {
    color: #fff;
    background-color: #d9534f;
    border-color: #d43f3a;
}

.button-container{
    margin-top: 25px;
    margin-bottom: 25px;
    text-align: center;


}

.button-container button{

    



}

.foot{
    margin: 10px;


}

.foot p  {

    font-size: 12px;




}
.foot img{
	width: 100%;
	height: 100%;


}
.alert-danger {
    color: #a94442;
    background-color: #f2dede;
    border-color: #ebccd1;
}
.alert {
    margin: 10px auto;
    width: 80%;
    padding: 10px;

    border: 1px solid transparent;
    border-radius: 4px;
}

.cover{

    z-index: 22;
    top: 0px;
    left: 0px;
    bottom: 0px;
    right: 0px;

    background-color: #000000;
    opacity: 0.5;
    position: absolute;
    display: none;



}

    .spinner {
        z-index: 99999;
        top: 50%;
        left: 50%;
        width: 50px;
        height: 60px;
        text-align: center;
        font-size: 10px;
        position: absolute;
        display: none;
    }

    .spinner > div {
        background-color: #67CF22;
        height: 100%;
        width: 6px;
        display: inline-block;

        -webkit-animation: stretchdelay 1.2s infinite ease-in-out;
        animation: stretchdelay 1.2s infinite ease-in-out;
    }

    .spinner .rect2 {
        -webkit-animation-delay: -1.1s;
        animation-delay: -1.1s;
    }

    .spinner .rect3 {
        -webkit-animation-delay: -1.0s;
        animation-delay: -1.0s;
    }

    .spinner .rect4 {
        -webkit-animation-delay: -0.9s;
        animation-delay: -0.9s;
    }

    .spinner .rect5 {
        -webkit-animation-delay: -0.8s;
        animation-delay: -0.8s;
    }

    @-webkit-keyframes stretchdelay {
        0%, 40%, 100% { -webkit-transform: scaleY(0.4) }
        20% { -webkit-transform: scaleY(1.0) }
    }

    @keyframes stretchdelay {
        0%, 40%, 100% {
            transform: scaleY(0.4);
            -webkit-transform: scaleY(0.4);
        }  20% {
               transform: scaleY(1.0);
               -webkit-transform: scaleY(1.0);
           }
    }


</style>
<body>

    <div class="spinner">
        <div class="rect1"></div>
        <div class="rect2"></div>
        <div class="rect3"></div>
        <div class="rect4"></div>
        <div class="rect5"></div>
    </div>

    <div class="container">
        <div class="header">
            <img src="/lvwei/Public/Img/loge.png">


        </div>
        <form method="post" action="<?php echo U('/Home/Index/actionSubmit') ?>">
	        <div class="body">
                <div class="alert alert-danger" role="alert" style="display: none">
                    <strong>错误:</strong><span>提货卷和验证码有错</span>
                </div>
	            <input name="number" class="form-control input-lg" type="number" placeholder="提货卷号">
	            <input name="checknum" class="form-control input-lg" type="number" placeholder="验证码">
	            <div class="button-container">
	                <button type="button" class="btn btn-success" style="margin-right: 40px;">提交</button>
	                <button type="button" class="btn btn btn-danger">重设</button>
	            </div>
	        </div>
		</form>

        <div class="foot">
			<img src="/lvwei/Public/Img/alarm.png">
        </div>
    </div>

    <div class="cover"></div>

<script type="text/javascript">

    var Index = function(){

        var _check = function(){
            var num = $('input[name="number"]').val();
            var checkNum = $('input[name="checknum"]').val();
            if(!num || !checkNum){
                $('.alert-danger').show();
                $('.alert-danger span').html('提货卷和验证码不能为空');
                return false;
            }
            return true;
        };
        var _initi = function(){

            $('.btn-danger').tap(function(){
                $('input[name="number"]').val('');
                $('input[name="checknum"]').val('');
            });

            $('.btn-success').tap(function(){
                var status = {
                    1:'填写正确卷号或验证码',
                    2:'填写正确卷号或验证码',
                    3:'该卷号已经消费',
                    4:'正确',
                };

                var flag = _check();
                if(flag){
                    var num = $('input[name="number"]').val();
                    var checkNum = $('input[name="checknum"]').val();

                    $('.cover').show();
                    $('.spinner').show();
                    $.get('index.php?a=actionCheckNum',{num : num , checknum : checkNum},function(data){

                        var statNum = parseInt(data);

                        if(statNum == 4){

                            $('form').submit();
                        }


                        $('.cover').hide();
                        $('.spinner').hide();




                        $('.alert-danger span').html(status[statNum]);
                        $('.alert-danger').show();



                    });
                }

               // $("form").submit();
            });
        }();
    }();




</script>

</body>
</html>