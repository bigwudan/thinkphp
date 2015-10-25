(function ($) {
    var chancetext = {};

    var Chance = function(obj) {
        var base = this;

        base.init = function(){
            base.start('in');
        }    


        base.start = function(mode){
            var setting_json = (obj.data('setting'))[mode],
                obj_arr = ($.makeArray(obj.children('span')));
            if( setting_json.mode === 'sequence'){
                base.animate(obj_arr , setting_json , mode);
            }else if(setting_json.mode === 'reverse'){
                obj_arr = obj_arr.reverse();
                base.animate(obj_arr , setting_json , mode);
            }else if(setting_json.mode === 'shuffle'){
                for(var j , x , i = obj_arr.length ; i ; j = parseInt(Math.random() * i) , x = obj_arr[--i] , obj_arr[i] = obj_arr[j] , obj_arr[j] =  x);
                base.animate(obj_arr , setting_json , mode);
            }
        }

        base.animate = function(obj_arr , setting_json , mode){
            obj.trigger("startEven-"+mode);
            setTimeout(function(){
                $(obj_arr).each(function(i , _this){
                    var delay = setting_json.delay * i * setting_json.scale;
                    //$(_this).css('visibility' , 'hidden');//兼容chrome！重影
                    setTimeout(function(){
                        $(_this).css('visibility' , 'visible');
                        $(_this).addClass('animated ' + setting_json.effect);
                        $(_this).one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function () {
                            $(_this).removeClass('animated ' + setting_json.effect);
                            if(obj.children('span').hasClass('animated') == false) base[mode + "CB"]();
                        });
                    } , delay);
                });
            } , setting_json.startDelay)
        }


        base.outCB = function(){
            obj.trigger("endEven-out");
            obj.children('span').css('visibility' , 'hidden');

        }

        base.inCB = function(){
            obj.trigger("endEven-in");
            base.start('out');
        }
        
        base.init();
    };

    chancetext.init = function(objs){
        objs.each(function(i,obj){
            var setting_str = $(obj).data('setting') || {},
                setting_tmp = {};

            if(typeof setting_str != 'object'){
                alert('data-setting Error!');
                return false;
            }
            $(obj).data('setting' , $.extend({} , chancetext.setting ,setting_str ));
            var chance_obj = new Chance($(obj) , 2);
        });
    }

    chancetext.setting = {
        loop: false,
        in:{
            effect: "flash",
            startDelay : "1000",
            scale : "1.5",
            delay: 50,
            mode: "sequence"
        },
        out:{
            effect: "flash",
            startDelay : "1000",
            scale : "1.5",
            delay: 50,
            mode: "sequence"
        }
    } ;

    window['chancetext'] = chancetext;

}(jQuery));
