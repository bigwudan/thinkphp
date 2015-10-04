(function ($) {
    var chancetext = {};

    var Chance = function(obj) {
        var base = this,
            obj_arr = ($.makeArray(obj.children('span'))),
            scale = 1.5;



        base.out = function(obj_arr , setting_json ){
        	var _obj_arr = obj_arr;

            if( setting_json.out.mode === 'sequence'){
                _obj_arr = obj_arr;
            }else if(setting_json.out.mode === 'reverse'){
                _obj_arr = _obj_arr.reverse();
                
            }else if(setting_json.out.mode === 'shuffle'){
                for(var j , x , i = _obj_arr.length ; i ; j = parseInt(Math.random() * i) , x = _obj_arr[--i] , _obj_arr[i] = _obj_arr[j] , _obj_arr[j] =  x);
                
            }


            $(_obj_arr).each(function(i , _this){
                var delay = 50 * i * scale;
                $(_this).css('visibility' , 'hidden');
                setTimeout(function(){
                    $(_this).css('visibility' , 'visible');
                    $(_this).addClass('animated ' + setting_json.out.effect);
                    $(_this).one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function () {
                        $(_this).removeClass('animated ' + setting_json.out.effect);
                        if(obj.children('span').hasClass('animated') == false){
                            obj.children('span').css('visibility','hidden');
                        }

                    });
                } , delay);
            });
        }



        base.start = function(){
            var setting_json = obj.data('setting');
                //animName = setting_json.in.effect;

            if( setting_json.in.mode === 'sequence'){
                base.animate(obj_arr , setting_json , base.out);
            }else if(setting_json.in.mode === 'reverse'){
                obj_arr = obj_arr.reverse();
                base.animate(obj_arr , setting_json , base.out);
            }else if(setting_json.in.mode === 'shuffle'){
                for(var j , x , i = obj_arr.length ; i ; j = parseInt(Math.random() * i) , x = obj_arr[--i] , obj_arr[i] = obj_arr[j] , obj_arr[j] =  x);
                base.animate(obj_arr , setting_json , base.out);
            }
        }


        base.animate = function(obj_arr , setting_json , cb){
            $(obj_arr).each(function(i , _this){
                var delay = 50 * i * scale;
                setTimeout(function(){
                    $(_this).css('visibility','visible');
                    $(_this).addClass('animated ' + setting_json.in.effect);
                    $(_this).one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function () {
                        $(_this).removeClass('animated ' + setting_json.in.effect);
                        if(obj.children('span').hasClass('animated') == false){
                            cb(obj_arr , setting_json );
                        }

                    });
                } , delay);
            });
        }


        base.start();

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
            mode: "sequence"
        },
        out:{
            effect: "flash",
            mode: "sequence"
        }
    } ;

    window['chancetext'] = chancetext;

}(jQuery));
