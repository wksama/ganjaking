!function(r,a,e,o){"use strict";typeof Color.fn.toString!==o&&(Color.fn.toString=function(){if(this._alpha<1)return this.toCSS("rgba",this._alpha).replace(/\s+/g,"");var r=parseInt(this._color,10).toString(16);if(this.error)return"";if(r.length<6)for(var a=6-r.length-1;a>=0;a--)r="0"+r;return"#"+r}),r.cs_ParseColorValue=function(r){var a=r.replace(/\s+/g,""),e=-1!==a.indexOf("rgba")?parseFloat(100*a.replace(/^.*,(.+)\)/,"$1")):100,o=100>e?!0:!1;return{value:a,alpha:e,rgba:o}},r.fn.cs_wpColorPicker=function(){return this.each(function(){var a=r(this);if(a.data("rgba")!==!1){var e=r.cs_ParseColorValue(a.val());a.wpColorPicker({clear:function(){a.trigger("keyup")},change:function(r,e){var o=e.color.toString();a.closest(".wp-picker-container").find(".cs-alpha-slider-offset").css("background-color",o),a.val(o).trigger("change")},create:function(){var o=a.data("a8cIris"),c=a.closest(".wp-picker-container"),l=r('<div class="cs-alpha-wrap"><div class="cs-alpha-slider"></div><div class="cs-alpha-slider-offset"></div><div class="cs-alpha-text"></div></div>').appendTo(c.find(".wp-picker-holder")),i=l.find(".cs-alpha-slider"),t=l.find(".cs-alpha-text"),n=l.find(".cs-alpha-slider-offset");i.slider({slide:function(r,e){var c=parseFloat(e.value/100);o._color._alpha=c,a.wpColorPicker("color",o._color.toString()),t.text(1>c?c:"")},create:function(){var s=parseFloat(e.alpha/100),p=1>s?s:"";t.text(p),n.css("background-color",e.value),c.on("click",".wp-picker-clear",function(){o._color._alpha=1,t.text(""),i.slider("option","value",100).trigger("slide")}),c.on("click",".wp-picker-default",function(){var e=r.cs_ParseColorValue(a.data("default-color")),c=parseFloat(e.alpha/100),l=1>c?c:"";o._color._alpha=c,t.text(l),i.slider("option","value",e.alpha).trigger("slide")}),c.on("click",".wp-color-result",function(){l.toggle()}),r("body").on("click.wpcolorpicker",function(){l.hide()})},value:e.alpha,step:1,min:1,max:100})}})}else a.wpColorPicker({clear:function(){a.trigger("keyup")},change:function(r,e){a.val(e.color.toString()).trigger("change")}})})},r(e).ready(function(){r(".cs-wp-color-picker").cs_wpColorPicker()})}(jQuery,window,document);

;(function ( $, window, undefined ) {
  'use strict';
  $.cs_ParseColorValue = function( val ) {
    var value = val.replace(/\s+/g, ''),
        alpha = ( value.indexOf('rgba') !== -1 ) ? parseFloat( value.replace(/^.*,(.+)\)/, '$1') * 100 ) : 100,
        rgba  = ( alpha < 100 ) ? true : false;
    return { value: value, alpha: alpha, rgba: rgba };
  };
}(jQuery, window));

;(function ( $, window, undefined ) {
    'use strict';
    function get_values_from_hidden_field(t) {
        var mv = t.find(".porto-bs-result-value").val() || null;
        if (mv != null) {
          var vals = mv.split("|"),
            has_style = false;
          $.each(vals, function(i, vl) {
              if (vl != '') {
                  var splitval = vl.split(":");

                  switch(splitval[0]) {
                    case 'color':   if(splitval[1]!='transparent') {
                                      t.find('.porto-bs-colorpicker').val(splitval[1]).trigger('change');
                                      //   set alpha value
                                      var picker = $.cs_ParseColorValue( splitval[1] );
                                      var sl_value = parseFloat( picker.alpha / 100 );
                                      var alpha_val = sl_value < 1 ? sl_value : '';
                                      t.find('.cs-alpha-text').text( alpha_val );
                                      //  drag position
                                      if(alpha_val == '') {
                                        t.find('.cs-alpha-slider .ui-slider-handle').css('left', '100%');
                                      } else {
                                        alpha_val = parseFloat(alpha_val * 100);
                                        t.find('.cs-alpha-slider .ui-slider-handle').css('left', alpha_val+'%');
                                      }
                                      t.find('.wp-picker-container').find('.cs-alpha-slider-offset').css('background-color', splitval[1]);
                                      t.find('.iris-strip').css('background-image', '-webkit-linear-gradient(top, '+splitval[1]+', rgb(197, 197, 197))');
                                    }
                          break;
                    case 'style':   t.find(".porto-bs-select").find("option[value=" + splitval[1] + "]").attr("selected", true);
                                    if(splitval[1]==='none' || splitval[1]==='inherit' || ! splitval[1] ) {
                                      t.find('.porto-bs-input-block, .porto-bs-colorpicker-block').hide();
                                    }
                                    has_style = true;
                          break;
                    default:
                              t.find('.porto-bs-input').each(function(input_index, elem) {
                                var dataid = $(elem).attr("data-id");
                                if( dataid==splitval[0] ) {
                                  var unit = $(elem).attr("data-unit"),
                                    mval = splitval[1].split(unit);
                                  $(elem).val(mval[0]);
                                }
                             });
                      break;
                  }
              }
          });
          if ( ! has_style ) {
            t.find('.porto-bs-input-block, .porto-bs-colorpicker-block').hide();
          }
        } else {
          var s = t.find('.porto-bs-select option:selected').val();
          if( s==='none' || s==='inherit' || ! s ) {
            t.find('.porto-bs-input-block, .porto-bs-colorpicker-block').hide();
          }
        }
      }
      function set_values_from_hidden_field(t, hxcolor) {
        var nval = "";
        t.find(".porto-bs-input").each(function(index, elm) {
          var unit = t.find(".porto-bs-result-value").attr("data-unit");
          var ival = $(elm).val();
          
		  var item = $(elm).attr("data-id") + ":" + $(elm).val() + unit + "|";
		  nval += item;
        });

        //  Set Color
        if(typeof hxcolor != 'undefined' || hxcolor != null) {
          var nval = nval + 'color:' +hxcolor+ '|';
        } else {
          var va = t.find('.porto-bs-colorpicker').val() || t.find('a.wp-color-result').css('background-color') || '';
          if(va!='') {
            var nval = nval + "color:" +va+ "|";
          }
        }

        var sv = t.find(".porto-bs-select option:selected").val() || '';
        if(typeof sv != 'undefined' && sv != '') {
           var nval = nval + "style:" +sv+ "|";
        }
        t.find(".porto-bs-result-value").val(nval);
      }
  $(document).ready(function($) {
        $(".porto-boxshadow").each(function(index, element) {
        var t = $(element);

          //  Add dependency for none box shadow'
          t.find('.porto-bs-select').on('change', function(event) {
            var s = $(this).val();
            
            if(typeof s != 'undefined' && s != null) {
              if( s==='none' || s==='inherit' || ! s ) {

                t.find('.porto-bs-input-block, .porto-bs-colorpicker-block').hide();
              } else {
                t.find('.porto-bs-input-block').show();
                t.find('.porto-bs-colorpicker-block').show();
              }
            }
          });
          
          get_values_from_hidden_field(t);
          set_values_from_hidden_field(t);

          //  On change - input / select
          t.find(".porto-bs-input, .porto-bs-select, .porto-bs-colorpicker").on('change', function(e){
            set_values_from_hidden_field(t);
          });

          //  Color
          var options = {
              change: function(event, ui){
                var hxcolor = $( this ).wpColorPicker( 'color' );
                //   set alpha value
                var picker = $.cs_ParseColorValue( hxcolor );
                var sl_value = parseFloat( picker.alpha / 100 );
                var alpha_val = sl_value < 1 ? sl_value : '';
                t.find('.cs-alpha-text').text( alpha_val );
                //  drag position
                if(alpha_val == '') {
                  t.find('.cs-alpha-slider .ui-slider-handle').css('left', '100%');
                } else {
                  alpha_val = parseFloat(alpha_val * 100);
                  t.find('.cs-alpha-slider .ui-slider-handle').css('left', alpha_val+'%');
                }
                t.find('.wp-picker-container').find('.cs-alpha-slider-offset').css('background-color', hxcolor);
                t.find('.iris-strip').css('background-image', '-webkit-linear-gradient(top, '+hxcolor+', rgb(197, 197, 197))');
                set_values_from_hidden_field(t, hxcolor);
              },
              clear: function (event, ui) {
                var hxcolor = 'transparent';
                set_values_from_hidden_field(t, hxcolor);
              },
          };
          t.find(".porto-bs-colorpicker").wpColorPicker(options);

      });
  });
}(jQuery, window));