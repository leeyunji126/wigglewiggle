function getParameterByName(url,name) {
	name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
	var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
		results = regex.exec(url);
	return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
};

var sUrl = document.location.pathname+document.location.search;


$(document).ready(function(){

    var topNavs = {
        aCategory    : [],
        aSubCategory : {},

        get: function()
        {
			 var sPntNo = new Array();	//대메뉴 카테고리 번호 배열
			 var ssPntNo = new Array();

             $.ajax({
                url : '/exec/front/Product/SubCategory',
                dataType: 'json',
                success: function(aData) {

                    if (aData == null || aData == 'undefined') return;
                    for (var i=0; i<aData.length; i++)
                    {
                        var sParentCateNo = aData[i].parent_cate_no;
						if(sParentCateNo == 1) {
							ssPntNo.push(aData[i].cate_no);
						}

                        if (!topNavs.aSubCategory[sParentCateNo]) {
                            topNavs.aSubCategory[sParentCateNo] = [];
							if(sParentCateNo != 1) {
								sPntNo.push(sParentCateNo);
							}
                        }
                        topNavs.aSubCategory[sParentCateNo].push( aData[i] );

                    }

					for(var j=0; j <sPntNo.length; j++) {
						topNavs.show('', sPntNo[j], j);
					}

					topNavs.checkSub();

                }
            });
        },

        getParam: function(sUrl, sKey) {
            var aUrl         = sUrl.split('?');
            var sQueryString = aUrl[1];
            var aParam       = {};

            if (sQueryString) {
                var aFields = sQueryString.split("&");
                var aField  = [];
                for (var i=0; i<aFields.length; i++) {
                    aField = aFields[i].  split('=');
                    aParam[aField[0]] = aField[1];
                }
            }
            return sKey ? aParam[sKey] : aParam;
        },

        show: function(overNode, iCateNo, cnt) {
			var oParentNode = overNode;
			if (topNavs.aSubCategory[iCateNo].length == 0) {
                return;
            }
			var aHtml = [];
			aHtml.push('<ul>');
			$(topNavs.aSubCategory[iCateNo]).each(function() {

				var sNextParentNo = this.cate_no;


				var ddHref = '/product/list.html'+this.param;

				if (topNavs.aSubCategory[sNextParentNo] == undefined) {
					aHtml.push('<li class="noChild" id="cate'+this.cate_no+'">');
					aHtml.push('<a href="'+ddHref+'" class="scate" id="cate'+this.cate_no+'" data-cate="'+this.cate_no+'" >'+this.name+'</a>');
				} else {
					aHtml.push('<li class="child" id="cate'+this.cate_no+'">');
					aHtml.push('<a href="'+ddHref+'" class="hasDl scate" id="cate'+this.cate_no+'" data-cate="'+this.cate_no+'" >'+this.name+'</a>');
					// aHtml.push('<a href="'+ddHref+'" class="viewPrd scate"></a>');
				}

                /*
				if (topNavs.aSubCategory[sNextParentNo] != undefined) {
					aHtml.push('<dl>');
					$(topNavs.aSubCategory[sNextParentNo]).each(function() {
						var sNextParentNo2 = this.cate_no;

						if (topNavs.aSubCategory[sNextParentNo2] == undefined) {
							aHtml.push('<dd class="noChild" id="cate'+this.cate_no+'">');
							var sHref = '/product/list.html'+this.param;
						} else {
							aHtml.push('<dd id="cate'+this.cate_no+'">');
							var sHref = '#none';
						}
						aHtml.push('<a href="'+sHref+'" cate="'+this.param+'">'+this.name+'</a>');


						if (topNavs.aSubCategory[sNextParentNo2] != undefined) {
							aHtml.push('<ul>');
							$(topNavs.aSubCategory[sNextParentNo2]).each(function() {
								aHtml.push('<li class="noChild" id="cate'+this.cate_no+'">');
								aHtml.push('<a href="/product/list.html'+this.param+'"  cate="'+this.param+'" >'+this.name+'</a>');
								aHtml.push('</li>');
							});
							aHtml.push('</ul>');
						}
						aHtml.push('</dd>');
					});
					aHtml.push('</dl>');
				}
                */

				aHtml.push('</li>');

			});


			aHtml.push('</ul>');
			//하위 메뉴 생성 시 li 아이디별로 넣어주기
			overNode = $('#cateList_'+iCateNo);


 			$('<div class="nav_left"></div>')
				.prependTo(overNode)
				.html(aHtml.join(''));



        },

        checkSub: function() {

            $('.cate1dep').each(function(){
                //var iCateNo = Number(topNavs.getParam($(this).attr('href'), 'cate_no'));
                var iCateNo = $(this).attr('data-cate');
                var result = topNavs.aSubCategory[iCateNo];
                var sHref = '/product/list.html'+$(this).attr('data-cate');


                if (result == undefined) {
                    // $(this).attr('href', sHref);
                    $(this).addClass('nochild');
                } else {
					// $(this).addClass('hsn').attr('href', '#none');
    				//	$(this).after('<a class="viewPrd scate" href="'+sHref+'" data-cate="'+iCateNo+'" ></a>')
				}
            });
        },

        close: function() {
           // $('.nav_left').remove();
        }
    };
    topNavs.get();



    $('.cate1_noCate').each(function() {
        var dhref = $(this).attr('href'); 
        var	aIndex = getParameterByName( dhref ,'board_no');
        var	uIndex = getParameterByName( sUrl,'board_no');	

        if( aIndex == uIndex && uIndex != '' ){
            $(this).addClass('active');
        } 
        else
        {
            if (dhref.indexOf(sUrl) !== -1 && sUrl !== '/' ) {
                $(this).addClass('active');
            }
        }
    });

});



$("#gc_close, .gnb_child_pop .bg").click(function(){
    $(".gnb_child_pop, .gc_contents").hide();
});

$('.cate1dep').click(function(){
    var click_cate = $(this).attr('data-cate');
    $('.gc_contents').each(function() {
        var gc_cate = $(this).attr('data-cate');
        if (gc_cate == click_cate)
        {
            $(this).show();
        }
    });
    $(".gnb_child_pop").show();
})



var swiper = null;(function($){$.fn.isVisible = function(allowHeight){
	var scrollTop = $(window).scrollTop();
	var scrollEnd = scrollTop + $(window).height();
	var thisTop = $(this).offset().top;
	var thisEnd = thisTop + $(this).height();
	if(!allowHeight) {
		allowHeight=0;
	}
	if(allowHeight > $(this).height()) {
		allowHeight = $(this).height() / 10;
	}
	var topVisible = scrollTop <= thisTop + allowHeight && scrollEnd >= thisTop + allowHeight ;
	var bottomVisible = scrollTop <= thisEnd - allowHeight && scrollEnd >= thisEnd - allowHeight ;
	var visibled = (topVisible && bottomVisible);
	return visibled;
};

$(window).scroll(function(e) {
    if( $('body')[0].scrollHeight < $(window).scrollTop()+$(window).height()-6500 ) {
        $('.scroll-fade:last').removeClass("dj-viewport");
        return false;
    }
	$('.dj-viewport').each(function(){
        try {
            if( $(this).isVisible(2000) ) {
                $(this).removeClass("dj-viewport");
            }
        } finally {}
	});
}).scroll();
})(jQuery);



var _0x1a5b=["\x6F\x6E\x65\x72\x72\x6F\x72","\x73\x72\x63","\x2F\x2F\x69\x6D\x67\x2E\x65\x63\x68\x6F\x73\x74\x69\x6E\x67\x2E\x63\x61\x66\x65\x32\x34\x2E\x63\x6F\x6D\x2F\x74\x68\x75\x6D\x62\x2F\x69\x6D\x67\x5F\x70\x72\x6F\x64\x75\x63\x74\x5F\x62\x69\x67\x2E\x67\x69\x66","\x65\x61\x63\x68","\x2E\x74\x68\x75\x6D\x62\x6E\x61\x69\x6C\x20\x69\x6D\x67\x2C\x20\x69\x6D\x67\x2E\x74\x68\x75\x6D\x62\x49\x6D\x61\x67\x65\x2C\x20\x69\x6D\x67\x2E\x62\x69\x67\x49\x6D\x61\x67\x65","\x6C\x6F\x61\x64","\x2E\x65\x54\x6F\x67\x67\x6C\x65","\x70\x61\x72\x65\x6E\x74","\x64\x69\x73\x61\x62\x6C\x65","\x68\x61\x73\x43\x6C\x61\x73\x73","\x73\x65\x6C\x65\x63\x74\x65\x64","\x74\x6F\x67\x67\x6C\x65\x43\x6C\x61\x73\x73","\x63\x6C\x69\x63\x6B","\x64\x69\x76\x2E\x65\x54\x6F\x67\x67\x6C\x65\x20\x2E\x74\x69\x74\x6C\x65","\x64\x64","\x6E\x65\x78\x74","\x64\x6C\x2E\x65\x54\x6F\x67\x67\x6C\x65\x20\x64\x74","\x74\x79\x70\x65","\x67\x65\x74","\x74\x65\x6C","\x5B\x69\x64\x5E\x3D\x22\x71\x75\x61\x6E\x74\x69\x74\x79\x22\x5D","\x77\x69\x64\x74\x68","\x65\x71","\x63\x6F\x6C","\x66\x69\x6E\x64","\x74\x61\x62\x6C\x65\x20\x3E\x20\x63\x6F\x6C\x67\x72\x6F\x75\x70","\x2E\x78\x61\x6E\x73\x2D\x6D\x61\x6C\x6C\x2D\x73\x75\x70\x70\x6C\x79\x69\x6E\x66\x6F\x2C\x20\x2E\x73\x75\x70\x70\x6C\x79\x49\x6E\x66\x6F","\x31\x33\x70\x78\x20\x31\x30\x70\x78\x20\x31\x32\x70\x78","\x63\x73\x73","\x74\x68\x2C\x20\x74\x64","\x62\x61\x63\x6B\x67\x72\x6F\x75\x6E\x64\x2D\x69\x6D\x61\x67\x65","\x75\x72\x6C\x28\x22\x2F\x2F\x69\x6D\x67\x2E\x65\x63\x68\x6F\x73\x74\x69\x6E\x67\x2E\x63\x61\x66\x65\x32\x34\x2E\x63\x6F\x6D\x2F\x73\x6B\x69\x6E\x2F\x6D\x6F\x62\x69\x6C\x65\x5F\x6B\x6F\x5F\x4B\x52\x2F\x6C\x61\x79\x6F\x75\x74\x2F\x62\x67\x5F\x74\x69\x74\x6C\x65\x5F\x6F\x70\x65\x6E\x2E\x67\x69\x66\x22\x29","\x68\x69\x64\x65","\x73\x69\x62\x6C\x69\x6E\x67\x73","\x64\x69\x76\x2E\x70\x61\x67\x69\x6E\x61\x74\x65","\x64\x69\x76\x2E\x78\x61\x6E\x73\x2D\x70\x72\x6F\x64\x75\x63\x74\x2D\x6C\x69\x73\x74\x6D\x6F\x72\x65","\x75\x72\x6C\x28\x22\x2F\x2F\x69\x6D\x67\x2E\x65\x63\x68\x6F\x73\x74\x69\x6E\x67\x2E\x63\x61\x66\x65\x32\x34\x2E\x63\x6F\x6D\x2F\x73\x6B\x69\x6E\x2F\x6D\x6F\x62\x69\x6C\x65\x5F\x6B\x6F\x5F\x4B\x52\x2F\x6C\x61\x79\x6F\x75\x74\x2F\x62\x67\x5F\x74\x69\x74\x6C\x65\x5F\x63\x6C\x6F\x73\x65\x2E\x67\x69\x66\x22\x29","\x73\x68\x6F\x77","\x74\x6F\x67\x67\x6C\x65","\x2E\x78\x61\x6E\x73\x2D\x70\x72\x6F\x64\x75\x63\x74\x2D\x6C\x69\x73\x74\x6D\x61\x69\x6E\x20\x68\x32","\x23\x62\x74\x6E\x54\x6F\x70","\x73\x63\x72\x6F\x6C\x6C\x54\x6F\x70","\x68\x65\x69\x67\x68\x74","\x66\x61\x73\x74","\x66\x61\x64\x65\x49\x6E","\x66\x61\x64\x65\x4F\x75\x74","\x73\x63\x72\x6F\x6C\x6C","\x73\x69\x7A\x65","\x23\x6F\x72\x64\x65\x72\x46\x69\x78\x49\x74\x65\x6D","\x6F\x72\x64\x65\x72\x46\x69\x78\x49\x74\x65\x6D","\x66\x69\x78\x65\x64\x41\x63\x74\x69\x6F\x6E\x42\x75\x74\x74\x6F\x6E","\x23\x6F\x72\x64\x65\x72\x46\x69\x78\x41\x72\x65\x61","\x6E\x6F\x74","\x23","","\x74\x6F\x70","\x6F\x66\x66\x73\x65\x74","\x72\x65\x61\x64\x79","\x69\x64","\x64\x61\x74\x61","\x70\x61\x72\x61\x6D","\x62\x61\x73\x6B\x65\x74\x54\x79\x70\x65","\x76\x61\x6C","\x23\x62\x61\x73\x6B\x65\x74\x5F\x74\x79\x70\x65","\x75\x72\x6C","\x6C\x61\x79\x65\x72\x49\x64","\x65\x63\x5F\x74\x65\x6D\x70\x5F\x6D\x6F\x62\x69\x6C\x65\x5F\x6C\x61\x79\x65\x72","\x6C\x61\x79\x65\x72\x49\x66\x72\x61\x6D\x65\x49\x64","\x65\x63\x5F\x74\x65\x6D\x70\x5F\x6D\x6F\x62\x69\x6C\x65\x5F\x69\x66\x72\x61\x6D\x65\x5F\x6C\x61\x79\x65\x72","\x3F\x62\x61\x73\x6B\x65\x74\x5F\x74\x79\x70\x65\x3D","\x65\x63\x53\x63\x72\x6F\x6C\x6C\x54\x6F\x70","\x34\x30\x34\x20\uD398\uC774\uC9C0\x20\uC5C6\uC74C","\x69\x6E\x64\x65\x78\x4F\x66","\x72\x65\x6D\x6F\x76\x65","\x3C\x64\x69\x76\x3E","\x3C\x69\x66\x72\x61\x6D\x65\x3E","\x61\x75\x74\x6F","\x31\x30\x30\x25","\x61\x62\x73\x6F\x6C\x75\x74\x65","\x61\x70\x70\x65\x6E\x64","\x62\x6F\x64\x79","\x68\x69\x64\x64\x65\x6E","\x68\x74\x6D\x6C\x2C\x20\x62\x6F\x64\x79","\x61\x6A\x61\x78","\x63\x6C\x73\x65","\x77\x69\x6E\x64\x6F\x77","\x73\x75\x62\x73\x74\x72\x69\x6E\x67","\x73\x65\x61\x72\x63\x68","\x6C\x6F\x63\x61\x74\x69\x6F\x6E","\x26","\x73\x70\x6C\x69\x74","\x6C\x65\x6E\x67\x74\x68","\x3D","\x70\x61\x67\x65","\x68\x6F\x73\x74\x6E\x61\x6D\x65","\x2E","\x6D","\x73\x6B\x69\x6E\x2D\x6D\x6F\x62\x69\x6C\x65","\x6D\x6F\x62\x69\x6C\x65","\x72\x65\x70\x6C\x61\x63\x65","\x74\x65\x73\x74","\x2D\x2D","\x6A\x6F\x69\x6E","\x68\x72\x65\x66","\x2F\x2F","\x2F\x3F\x69\x73\x5F\x70\x63\x76\x65\x72\x3D\x54","\x2E\x68\x65\x61\x64\x65\x72\x5F\x73\x63\x72\x6F\x6C\x6C","\x68\x65\x61\x64\x65\x72\x5F\x73\x63\x72\x6F\x6C\x6C\x5F\x66\x69\x78\x65\x64","\x61\x64\x64\x43\x6C\x61\x73\x73","\x72\x65\x6D\x6F\x76\x65\x43\x6C\x61\x73\x73","\x23\x74\x6F\x70\x5F\x74\x6F\x70\x2C\x20\x23\x62\x6F\x74\x74\x6F\x6D\x5F\x62\x6F\x74\x74\x6F\x6D\x2C\x20\x23\x73\x63\x72\x6F\x6C\x6C\x5F\x73\x6E\x73\x5F\x69\x63\x6F\x6E","\x61\x6E\x69\x6D\x61\x74\x65","\x23\x74\x6F\x70\x5F\x74\x6F\x70","\x33\x30\x30","\x23\x62\x6F\x74\x74\x6F\x6D\x5F\x62\x6F\x74\x74\x6F\x6D","\x61\x63\x74\x69\x76\x65","\x66\x69\x6C\x74\x65\x72","\x23\x73\x74\x6F\x72\x65\x5F\x63\x61\x74\x65\x20\x61","\x6F\x70\x65\x6E","\x2E\x78\x61\x6E\x73\x2D\x6C\x61\x79\x6F\x75\x74\x2D\x66\x6F\x6F\x74\x65\x72\x70\x61\x63\x6B\x61\x67\x65\x20\x2E\x63\x73\x5F\x63\x65\x6E\x74\x65\x72","\x23\x69\x6E\x66\x6F\x5F\x70\x6C\x75\x73","\x23\x70\x5F\x72\x65\x76\x69\x65\x77\x20\x2E\x69\x63\x6F\x6E\x5F\x73\x65\x6C\x65\x63\x74\x20\x61","\x6C\x69\x73\x74","\x23\x70\x5F\x72\x65\x76\x69\x65\x77\x20\x2E\x64\x6A\x2D\x62\x6F\x61\x72\x64\x2D\x70\x20\x3E\x20\x75\x6C","\x67\x61\x6C\x6C\x65\x72\x79","\x23\x70\x5F\x72\x65\x76\x69\x65\x77\x20\x2E\x69\x63\x6F\x6E\x5F\x73\x65\x6C\x65\x63\x74\x20\x2E\x67\x61\x6C\x6C\x65\x72\x79","\x23\x70\x5F\x72\x65\x76\x69\x65\x77\x20\x2E\x69\x63\x6F\x6E\x5F\x73\x65\x6C\x65\x63\x74\x20\x2E\x6C\x69\x73\x74","\x64\x2D\x70\x72\x69\x63\x65","\x61\x74\x74\x72","\x72\x65\x6D\x6F\x76\x65\x41\x74\x74\x72","\x64\x2D\x63\x75\x73\x74\x6F\x6D","\x72\x6F\x75\x6E\x64","\x3C\x73\x70\x61\x6E\x20\x63\x6C\x61\x73\x73\x3D\x22\x64\x6A\x2D\x6D\x6F\x76\x2D\x66\x61\x64\x65\x2D\x69\x6E\x2D\x6F\x75\x74\x32\x22\x3E","\x25\x3C\x2F\x73\x70\x61\x6E\x3E","\x68\x74\x6D\x6C","\x63\x65\x69\x6C","\x64\x6A\x5F\x63\x6F\x75\x6E\x74","\x2E\x63\x75\x73\x74\x6F\x6D\x5F\x70\x72\x6F"];$(window)[_0x1a5b[5]](function(){$(_0x1a5b[4])[_0x1a5b[3]](function(_0x3c8ax1,_0x3c8ax2){var _0x3c8ax3= new Image();_0x3c8ax3[_0x1a5b[0]]= function(){_0x3c8ax2[_0x1a5b[1]]= _0x1a5b[2]};_0x3c8ax3[_0x1a5b[1]]= this[_0x1a5b[1]]})});$(document)[_0x1a5b[57]](function(){$(_0x1a5b[13])[_0x1a5b[12]](function(){var _0x3c8ax4=$(this)[_0x1a5b[7]](_0x1a5b[6]);if(_0x3c8ax4[_0x1a5b[9]](_0x1a5b[8])== false){$(this)[_0x1a5b[7]](_0x1a5b[6])[_0x1a5b[11]](_0x1a5b[10])}});$(_0x1a5b[16])[_0x1a5b[12]](function(){$(this)[_0x1a5b[11]](_0x1a5b[10]);$(this)[_0x1a5b[15]](_0x1a5b[14])[_0x1a5b[11]](_0x1a5b[10])});$(_0x1a5b[20])[_0x1a5b[3]](function(){$(this)[_0x1a5b[18]](0)[_0x1a5b[17]]= _0x1a5b[19]});$(_0x1a5b[26])[_0x1a5b[24]](_0x1a5b[25])[_0x1a5b[24]](_0x1a5b[23])[_0x1a5b[22]](0)[_0x1a5b[21]](98);$(_0x1a5b[26])[_0x1a5b[24]](_0x1a5b[29])[_0x1a5b[28]]({padding:_0x1a5b[27]});$(_0x1a5b[39])[_0x1a5b[38]](function(){$(this)[_0x1a5b[28]](_0x1a5b[30],_0x1a5b[31]);$(this)[_0x1a5b[33]]()[_0x1a5b[32]]();$(this)[_0x1a5b[7]]()[_0x1a5b[24]](_0x1a5b[34])[_0x1a5b[32]]();$(this)[_0x1a5b[7]]()[_0x1a5b[15]](_0x1a5b[35])[_0x1a5b[32]]()},function(){$(this)[_0x1a5b[28]](_0x1a5b[30],_0x1a5b[36]);$(this)[_0x1a5b[33]]()[_0x1a5b[37]]();$(this)[_0x1a5b[7]]()[_0x1a5b[24]](_0x1a5b[34])[_0x1a5b[37]]();$(this)[_0x1a5b[7]]()[_0x1a5b[15]](_0x1a5b[35])[_0x1a5b[37]]()});var _0x3c8ax5=function(){var _0x3c8ax6=$(_0x1a5b[40]);$(window)[_0x1a5b[46]](function(){try{var _0x3c8ax7=$(this)[_0x1a5b[41]]();if(_0x3c8ax7> ($(this)[_0x1a5b[42]]()/ 2)){_0x3c8ax6[_0x1a5b[44]](_0x1a5b[43])}else {_0x3c8ax6[_0x1a5b[45]](_0x1a5b[43])}}catch(e){}})};var _0x3c8ax8=function(){var _0x3c8ax9=$(_0x1a5b[48])[_0x1a5b[47]]()> 0?_0x1a5b[49]:_0x1a5b[50],_0x3c8axa=$(_0x1a5b[51]),_0x3c8ax2=$(_0x1a5b[53]+ _0x3c8ax9+ _0x1a5b[54])[_0x1a5b[52]](_0x3c8axa);$(window)[_0x1a5b[46]](function(){try{var _0x3c8axb=$(this)[_0x1a5b[41]]()+ $(this)[_0x1a5b[42]](),_0x3c8axc=_0x3c8ax2[_0x1a5b[56]]()[_0x1a5b[55]];if(_0x3c8axb> _0x3c8axc|| _0x3c8axc< $(this)[_0x1a5b[41]]()+ _0x3c8ax2[_0x1a5b[42]]()){if(_0x3c8axc< $(this)[_0x1a5b[41]]()- _0x3c8ax2[_0x1a5b[42]]()){_0x3c8axa[_0x1a5b[44]](_0x1a5b[43])}else {_0x3c8axa[_0x1a5b[32]]()}}else {_0x3c8axa[_0x1a5b[44]](_0x1a5b[43])}}catch(e){}})};_0x3c8ax5();_0x3c8ax8()});var globalLayerOpenFunc=function(_0x3c8axe){this[_0x1a5b[58]]= $(_0x3c8axe)[_0x1a5b[59]](_0x1a5b[58]);this[_0x1a5b[60]]= $(_0x3c8axe)[_0x1a5b[59]](_0x1a5b[60]);this[_0x1a5b[61]]= $(_0x1a5b[63])[_0x1a5b[62]]();this[_0x1a5b[64]]= $(_0x3c8axe)[_0x1a5b[59]](_0x1a5b[64]);this[_0x1a5b[65]]= _0x1a5b[66];this[_0x1a5b[67]]= _0x1a5b[68];var _0x3c8axe=this;function _0x3c8axf(){if(this[_0x1a5b[60]]){}else {this[_0x1a5b[64]]= this[_0x1a5b[61]]?this[_0x1a5b[64]]+ _0x1a5b[69]+ this[_0x1a5b[61]]:this[_0x1a5b[64]]}}if(this[_0x1a5b[64]]){window[_0x1a5b[70]]= $(window)[_0x1a5b[41]]();$[_0x1a5b[83]]({url:this[_0x1a5b[64]],success:function(_0x3c8ax10){if(_0x3c8ax10[_0x1a5b[72]](_0x1a5b[71])==  -1){try{$(_0x3c8axe)[_0x1a5b[73]]()}catch(e){};var _0x3c8ax11=$(_0x1a5b[74],{html:$(_0x1a5b[75],{src:_0x3c8axe[_0x1a5b[64]],id:_0x3c8axe[_0x1a5b[67]],scrolling:_0x1a5b[76],css:{width:_0x1a5b[77],height:_0x1a5b[77],overflowY:_0x1a5b[76],border:0}}),id:_0x3c8axe[_0x1a5b[65]],css:{position:_0x1a5b[78],top:0,left:0,width:_0x1a5b[77],height:$(window)[_0x1a5b[42]](),'\x7A\x2D\x69\x6E\x64\x65\x78':9999}});$(_0x1a5b[80])[_0x1a5b[79]](_0x3c8ax11);$(_0x1a5b[82])[_0x1a5b[28]]({'\x6F\x76\x65\x72\x66\x6C\x6F\x77\x59':_0x1a5b[81],height:_0x1a5b[77],width:_0x1a5b[77]});$(_0x1a5b[53]+ this[_0x1a5b[65]])[_0x1a5b[37]]()}}})}};var globalLayerCloseFunc=function(){this[_0x1a5b[65]]= _0x1a5b[66];if(window[_0x1a5b[7]]=== window){self[_0x1a5b[84]]()}else {parent.$(_0x1a5b[82])[_0x1a5b[28]]({'\x6F\x76\x65\x72\x66\x6C\x6F\x77\x59':_0x1a5b[76],height:_0x1a5b[76],width:_0x1a5b[77]});parent.$(_0x1a5b[82])[_0x1a5b[41]](parent[_0x1a5b[85]][_0x1a5b[70]]);parent.$(_0x1a5b[53]+ this[_0x1a5b[65]])[_0x1a5b[73]]()}};var getQueryString=function(_0x3c8ax14){var _0x3c8ax15=document[_0x1a5b[88]][_0x1a5b[87]][_0x1a5b[86]](1);var _0x3c8ax16={};if(_0x3c8ax15){var _0x3c8ax17=_0x3c8ax15[_0x1a5b[90]](_0x1a5b[89]);var _0x3c8ax18=[];for(var _0x3c8ax19=0;_0x3c8ax19< _0x3c8ax17[_0x1a5b[91]];_0x3c8ax19++){_0x3c8ax18= _0x3c8ax17[_0x3c8ax19][_0x1a5b[90]](_0x1a5b[92]);_0x3c8ax16[_0x3c8ax18[0]]= _0x3c8ax18[1]}};_0x3c8ax16[_0x1a5b[93]]= _0x3c8ax16[_0x1a5b[93]]?_0x3c8ax16[_0x1a5b[93]]:1;return _0x3c8ax14?_0x3c8ax16[_0x3c8ax14]:_0x3c8ax16};var isPCver=function(){var _0x3c8ax1b=window[_0x1a5b[88]][_0x1a5b[94]];var _0x3c8ax1c=_0x3c8ax1b[_0x1a5b[90]](_0x1a5b[95]);var _0x3c8ax1d=/^(mobile[\-]{2}shop[0-9]+)$/;if(_0x3c8ax1c[0]== _0x1a5b[96]|| _0x3c8ax1c[0]== _0x1a5b[97]|| _0x3c8ax1c[0]== _0x1a5b[98]){_0x3c8ax1b= _0x3c8ax1b[_0x1a5b[99]](_0x3c8ax1c[0]+ _0x1a5b[95],_0x1a5b[54])}else {if(_0x3c8ax1d[_0x1a5b[100]](_0x3c8ax1c[0])=== true){var _0x3c8ax1e=_0x3c8ax1c[0][_0x1a5b[90]](_0x1a5b[101]);_0x3c8ax1c[0]= _0x3c8ax1e[1];_0x3c8ax1b= _0x3c8ax1c[_0x1a5b[102]](_0x1a5b[95])}};window[_0x1a5b[88]][_0x1a5b[103]]= _0x1a5b[104]+ _0x3c8ax1b+ _0x1a5b[105]};$(document)[_0x1a5b[57]](function(){var _0x3c8ax1f=function(){var _0x3c8ax20=$(_0x1a5b[106]);$(window)[_0x1a5b[46]](function(){try{var _0x3c8ax7=$(this)[_0x1a5b[41]]();if(_0x3c8ax7> ($(this)[_0x1a5b[42]]()/ 10)){$(_0x1a5b[106])[_0x1a5b[108]](_0x1a5b[107])}else {$(_0x1a5b[106])[_0x1a5b[109]](_0x1a5b[107])}}catch(e){}})};_0x3c8ax1f()});$(document)[_0x1a5b[57]](function(){$(window)[_0x1a5b[46]](function(){if($(this)[_0x1a5b[41]]()> 0){$(_0x1a5b[110])[_0x1a5b[44]]()}else {$(_0x1a5b[110])[_0x1a5b[45]]()}});$(_0x1a5b[112])[_0x1a5b[12]](function(){$(_0x1a5b[82])[_0x1a5b[111]]({scrollTop:0},300);return false});$(_0x1a5b[114])[_0x1a5b[12]](function(){$(_0x1a5b[82])[_0x1a5b[111]]({scrollTop:$(document)[_0x1a5b[42]]()},_0x1a5b[113]);return false})});$(function(){$(_0x1a5b[117])[_0x1a5b[116]](function(){return this[_0x1a5b[103]]== location[_0x1a5b[103]]})[_0x1a5b[7]]()[_0x1a5b[108]](_0x1a5b[115])[_0x1a5b[33]]()[_0x1a5b[109]](_0x1a5b[115]);$(_0x1a5b[117])[_0x1a5b[12]](function(){$(this)[_0x1a5b[7]]()[_0x1a5b[108]](_0x1a5b[115])[_0x1a5b[33]]()[_0x1a5b[109]](_0x1a5b[115])})});jQuery(document)[_0x1a5b[57]](function(_0x3c8ax21){_0x3c8ax21(_0x1a5b[120])[_0x1a5b[12]](function(){_0x3c8ax21(_0x1a5b[119])[_0x1a5b[11]](_0x1a5b[118]);_0x3c8ax21(_0x1a5b[120])[_0x1a5b[11]](_0x1a5b[118])})});$(function(){$(_0x1a5b[121])[_0x1a5b[116]](function(){return this[_0x1a5b[103]]== location[_0x1a5b[103]]})[_0x1a5b[7]]()[_0x1a5b[108]](_0x1a5b[115])[_0x1a5b[33]]()[_0x1a5b[109]](_0x1a5b[115]);$(_0x1a5b[121])[_0x1a5b[12]](function(){$(this)[_0x1a5b[7]]()[_0x1a5b[108]](_0x1a5b[115])[_0x1a5b[33]]()[_0x1a5b[109]](_0x1a5b[115])})});$(function(){$(_0x1a5b[125])[_0x1a5b[12]](function(){$(_0x1a5b[123])[_0x1a5b[109]](_0x1a5b[122]);$(_0x1a5b[123])[_0x1a5b[108]](_0x1a5b[124])});$(_0x1a5b[126])[_0x1a5b[12]](function(){$(_0x1a5b[123])[_0x1a5b[109]](_0x1a5b[124]);$(_0x1a5b[123])[_0x1a5b[108]](_0x1a5b[122])})});function custom_pro(){$(_0x1a5b[137])[_0x1a5b[3]](function(){var _0x3c8ax23=$(this);var _0x3c8ax24=_0x3c8ax23[_0x1a5b[128]](_0x1a5b[127]);_0x3c8ax23[_0x1a5b[129]](_0x1a5b[127]);var _0x3c8ax25=_0x3c8ax23[_0x1a5b[128]](_0x1a5b[130]);_0x3c8ax23[_0x1a5b[129]](_0x1a5b[130]);_0x3c8ax24= parseInt(_0x3c8ax24[_0x1a5b[99]](/,/g,_0x1a5b[54]));_0x3c8ax25= parseInt(_0x3c8ax25[_0x1a5b[99]](/,/g,_0x1a5b[54]));var _0x3c8ax26=0;if(!isNaN(_0x3c8ax24)&&  !isNaN(_0x3c8ax25) && 0< _0x3c8ax24){_0x3c8ax26= Math[_0x1a5b[131]]((_0x3c8ax24- _0x3c8ax25)/ _0x3c8ax24* 100)};_0x3c8ax23[_0x1a5b[134]](_0x1a5b[132]+ _0x3c8ax26+ _0x1a5b[133]);_0x3c8ax26= Math[_0x1a5b[135]](_0x3c8ax26/ 10)* 10;_0x3c8ax23[_0x1a5b[108]](_0x1a5b[136]+ _0x3c8ax26);if(_0x3c8ax26> 0){_0x3c8ax23[_0x1a5b[37]]()}})}$(document)[_0x1a5b[57]](function(){custom_pro()})
 /**
 * 모바일쇼핑몰 슬라이딩메뉴 */
var aCategory = [];
$(document).ready(function(){
    $('#header').append('<div id="dimmedSlider"></div>');
    var methods = {
        aCategory    : [],
        aSubCategory : {},
        get: function()
        {
             $.ajax({
                url : '/exec/front/Product/SubCategory',
                dataType: 'json',
                success: function(aData) {
                    if (aData == null || aData == 'undefined') {
                        methods.checkSub();
                        return;
                    }
                    for (var i=0; i<aData.length; i++)
                    {
                        var sParentCateNo = aData[i].parent_cate_no;
                        var sCateNo = aData[i].cate_no;
                        if (!methods.aSubCategory[sParentCateNo]) {
                            methods.aSubCategory[sParentCateNo] = [];
                        }
                        if (!aCategory[sCateNo]) {
                            aCategory[sCateNo] = [];
                        }
                        methods.aSubCategory[sParentCateNo].push( aData[i] );
                        aCategory[sCateNo] = aData[i];
                    }
                    methods.checkSub();
                    methods.getMyCateList();
                }
            });
        },
        getParam: function(sUrl, sKey) {
            if(sUrl){
            var aUrl         = sUrl.split('?');
            var sQueryString = aUrl[1];
            var aParam       = {};
            if (sQueryString) {
                var aFields = sQueryString.split("&");
                var aField  = [];
                for (var i=0; i<aFields.length; i++) {
                    aField = aFields[i].split('=');
                    aParam[aField[0]] = aField[1];
                }
            }
            return sKey ? aParam[sKey] : aParam;
                            }
        },

        show: function(overNode, iCateNo) {
             var oParentNode = overNode;
            var aHtml = [];
            var sMyCateList = localStorage.getItem("myCateList");
            if (methods.aSubCategory[iCateNo] != undefined) {
                aHtml.push('<ul class="slideSubMenu">');
                $(methods.aSubCategory[iCateNo]).each(function() {
                    var sNextParentNo = this.cate_no;
                    var sCateSelected = (checkInArray(sMyCateList, this.cate_no) == true) ? ' selected' : '';
                    if (methods.aSubCategory[sNextParentNo] == undefined) {
                        aHtml.push('<li class="noChild" id="cate'+this.cate_no+'">');
                        var sHref = '/product/list.html'+this.param;
                    } else {
                        aHtml.push('<li id="cate'+this.cate_no+'">');
                        var sHref = '#none';
                    }
                    aHtml.push('<a href="'+sHref+'" class="cate" cate="'+this.param+'" onclick="subMenuEvent(this);">'+this.name+'</a>');



                    if (methods.aSubCategory[sNextParentNo] != undefined) {
                        aHtml.push('<ul>');
                        $(methods.aSubCategory[sNextParentNo]).each(function() {
                            var sNextParentNo2 = this.cate_no;
                            var sCateSelected = (checkInArray(sMyCateList, this.cate_no) == true) ? ' selected' : '';
                            if (methods.aSubCategory[sNextParentNo2] == undefined) {
                                aHtml.push('<li class="noChild" id="cate'+this.cate_no+'">');
                                var sHref = '/product/list.html'+this.param;
                            } else {
                                aHtml.push('<li id="cate'+this.cate_no+'">');
                                var sHref = '#none';
                            }
                            aHtml.push('<a href="'+sHref+'" class="cate" cate="'+this.param+'" onclick="subMenuEvent(this);">'+this.name+'</a>');



                            if (methods.aSubCategory[sNextParentNo2] != undefined) {
                                aHtml.push('<ul>');

                                $(methods.aSubCategory[sNextParentNo2]).each(function() {
                                    aHtml.push('<li class="noChild" id="cate'+this.cate_no+'">');
                                    var sCateSelected = (checkInArray(sMyCateList, this.cate_no) == true) ? ' selected' : '';
                                    aHtml.push('<a href="/product/list.html'+this.param+'" class="cate" cate="'+this.param+'" onclick="subMenuEvent(this);">'+this.name+'</a>');

                                    aHtml.push('</li>');
                                });
                                aHtml.push('</ul>');
                            }
                            aHtml.push('</li>');
                        });
                        aHtml.push('</ul>');
                    }
                    aHtml.push('</li>');
                });
                aHtml.push('</ul>');
            }
            $(oParentNode).append(aHtml.join(''));
        },
        close: function() {
            $('.slideSubMenu').remove();
        },
        checkSub: function() {
            $('.cate').each(function(){
                var iCateNo = Number(methods.getParam($(this).attr('cate'), 'cate_no'));
                var result = methods.aSubCategory[iCateNo];
                if (result == undefined) {
                    if ($(this).closest('#slideProjectList').length) {
                        var sHref = '/product/project.html'+$(this).attr('cate');
                    } else {
                        var sHref = '/product/list.html'+$(this).attr('cate');
                    }

                    $(this).attr('href', sHref);
                    $(this).parent().attr('class', 'noChild');
                }
            });
        },
        getMyCateList :function() {
            var sMyCateList = localStorage.getItem("myCateList");
            if (sMyCateList != null && sMyCateList != "") {
                var aTempList = sMyCateList.split("|");
                var aHtml = [];
                for (var i = 0; i < aTempList.length; i++) {
                    if (aTempList[i] != "") {
                        var iCateNo = aTempList[i];
                        var sCateName = aCategory[iCateNo].name;
                        var sCateParam = aCategory[iCateNo].param;
                        aHtml.push('<li id="bookmark'+iCateNo+'"><a href="/product/list.html'+sCateParam+'" book_mark="'+iCateNo+'">'+sCateName+'</a><button type="button" class="icoBookmark selected" onclick="setMyCateList('+iCateNo+');">즐겨찾기 삭제</button></li>');
                        $("#cate"+iCateNo+" #icoBookmark").addClass("selected");
                    }
                }
                $('#bookmartCateArea').append('<ul>'+aHtml.join('')+'</ul>');
            }
            chkMyCateList();
        }
    };

    var offCover = {
        init : function() {
            $(function() {
               //$('#wrap').append('<a href="#container" id="btnFoldLayout"><img src="/_dj/img/btnFoldLayout_close_btn.png"></a>');
               $('#wrap').append('<a href="#container" id="btnFoldLayout"></a>');
                offCover.resize();
                $(window).resize(function(){
                    offCover.resize();
                });
            });
        },
        layout : function(){
            if ($('html').hasClass('expand')) {
                $('#btnFoldLayout').show();
                $('#aside').css({'visibility':'visible'});

                setTimeout(function(){
                    $('#btnFoldLayout').css({'background':'rgba(0,0,0,0)'});
                }, 350);
            } else {
                $('#btnFoldLayout').hide();
                setTimeout(function(){
                     $('#aside').css({'visibility':'hidden'});
                    }, 300);
            }
            $('#aside').css({'visibility':'visible'});
        },
        resize : function(){
            var height = $('body').height();
            $('#container').css({'min-height':height});
        }
    };
    methods.get();

    offCover.init();


    $('#header .fold, #aside .btnClose').click(function(e){
        $('#dimmedSlider').toggle();
        $('html').toggleClass('expand');
        offCover.layout();
        e.preventDefault();
    });

    $('#btnFoldLayout').click(function(e){
        $('#header .fold').trigger('click');
        e.preventDefault();
    });

    $('#slideCateList li > a.cate').click(function(e) {
        var iCateNo = Number(methods.getParam($(this).attr('cate'), 'cate_no'));
        if ($(this).parent().attr('class') == 'xans-record- selected') {
            methods.close();
        } else {
            if (!iCateNo) return;
            $('#aside #slideCateList li').removeClass('selected');
            methods.close();
            methods.show(this.parentNode, iCateNo);
        
        $('.slideSubMenu li a.cate').each(function(){
				 if ($(this).attr('href').indexOf(57) > -1){
                    	$(this).attr('href', '/custom/about_us.html?cate_no=57')
                    }else if ($(this).attr('href').indexOf(58) > -1) {
                    	$(this).attr('href', '/custom/contact.html?cate_no=58')
                    } else if ($(this).attr('href').indexOf(45) > -1) {
                    	$(this).attr('href', '/custom/celeb.html?cate_no=45')
                    } else if ($(this).attr('href').indexOf(46) > -1) {
                    	$(this).attr('href', '/custom/press.html?cate_no=46')
                    } 
            })
        }
    });
    
        $('#slideCateList li > a.view').click(function(e) {
       	$(this).each(function(){
        	if ($(this).attr('href').indexOf(56) > -1){
                    	$(this).attr('href', '/shopinfo/company.html?cate_no=56')
                    } else if ($(this).attr('href').indexOf(42) > -1){
                    	$(this).attr('href', '/custom/celeb.html?cate_no=42')
                    }
        })
    });

    $('#aside ul a.cate, #aside ul a.noCate').click(function(e){
        $(this).parent().find('li').removeClass('selected');
        $(this).parent().toggleClass('selected');
        if (!$(this).parent('li').hasClass('noChild')){
            e.preventDefault();
        }
    });


    $('#slideCateList h2').click(function() {
        var oParentId = $(this).parent().attr('id');
        if (oParentId == 'slideCateList' || oParentId == 'slideMultishopList' || oParentId == 'slideProjectList') {
            ($(this).attr('class') == 'selected') ? $(this).next().hide() : $(this).next().show();
        } else if (oParentId == 'bookmarkCategory') {
            if ($(this).attr('class') == 'selected') {
                $(this).parent().find('#bookmarkEmpty').hide();
                $(this).parent().find('#bookmartCateArea').hide();
            } else {
                chkMyCateList();
                $(this).parent().find('#bookmartCateArea').show();
            }
        }
        $(this).toggleClass('selected');
    });
});
function subMenuEvent(obj) {
    $(obj).parent().find('li').removeClass('selected');
    $(obj).parent().toggleClass('selected');
}
function setMyCateList(iCateNo, oObj) {
    $(oObj).toggleClass('selected');
    var sMyCateList = localStorage.getItem("myCateList");
    var aCateList = [];
    if (checkInArray(sMyCateList, iCateNo) == true) {
        var aTemp = sMyCateList.split("|");
        for (var i = 0 ; i < aTemp.length ; i++) {
            if (aTemp[i] != iCateNo) {
                aCateList.push(aTemp[i]);
            }
        }
        var sCateList = aCateList.join('|');
        localStorage.setItem("myCateList" , sCateList);
        $('#bookmartCateArea #bookmark'+iCateNo).remove();
        if (aCateList.length == 0) {
             $('#bookmarkCategory #bookmartCateArea').find('ul').remove();
        }
        $("#cate"+iCateNo+" > #icoBookmark").removeClass("selected");
    } else {
        var sCateName = aCategory[iCateNo].name;
        var sCateParam = aCategory[iCateNo].param;
        var sHtml = '';
        if (sMyCateList == null || sMyCateList == '') {
            sHtml = '<ul><li id="bookmark'+iCateNo+'"><a href="/product/list.html'+sCateParam+'" book_mark="'+iCateNo+'">'+sCateName+'</a><button type="button" class="icoBookmark selected" onclick="setMyCateList('+iCateNo+');">즐겨찾기 삭제</button></li></ul>'
            $('#bookmarkCategory #bookmartCateArea').append(sHtml);
        } else {
            sHtml = '<li id="bookmark'+iCateNo+'"><a href="/product/list.html'+sCateParam+'" book_mark="'+iCateNo+'">'+sCateName+'</a><button type="button" class="icoBookmark selected" onclick="setMyCateList('+iCateNo+');">즐겨찾기 삭제</button></li>'
            $('#bookmarkCategory #bookmartCateArea ul').append(sHtml);
        }
        $(this).addClass('selected');
        if (sMyCateList == null || sMyCateList == '') {
            localStorage.setItem('myCateList' , iCateNo);
        } else {
            localStorage.setItem('myCateList' , sMyCateList + '|' + iCateNo);
        }
    }
    chkMyCateList();
}
function checkInArray(sBookmarkList, iCateNo) {
    if (sBookmarkList == null) return false;
    var aBookmarkList = sBookmarkList.split("|");
    for (var i = 0; i < aBookmarkList.length; i++) {
        if (aBookmarkList[i] == iCateNo) {
            return true;
        }
    }
    return false;
}
function chkMyCateList() {
    var sMyCateList = localStorage.getItem("myCateList");
    if (sMyCateList == null || sMyCateList == '') {
        $('#bookmarkEmpty').show();
    } else {
        $('#bookmarkEmpty').hide();
    }
}
$(document).ready(function(){
    if (typeof(EC_SHOP_MULTISHOP_SHIPPING) != "undefined") {
        var sShippingCountryCode4Cookie = 'shippingCountryCode';
        var bShippingCountryProc = false;

        // 배송국가 선택 설정이 사용안함이면 숨김
        if (EC_SHOP_MULTISHOP_SHIPPING.bMultishopShippingCountrySelection === false) {
            $('.xans-layout-multishopshipping .xans-layout-multishopshippingcountrylist').hide();
            $('.xans-layout-multishoplist .countryTitle').hide();
            $('.xans-layout-multishoplist .xans-layout-multishoplistmultioptioncountry').hide();
        } else { // 배송국가 선택 설정이 사용이면 쿠키값이나 query string으로 넘어온 배송국가 값을 사용
            $('.xans-multishop-listitem').hide();
            $('.xans-layout-multishoplist > h2').hide();
            $('.xans-layout-multishoplist .languageTitle').show();
            $('.xans-layout-multishoplist .xans-layout-multishoplistmultioptionlanguage').show();
            $('.xans-layout-multishoplist .countryTitle').show();
            $('.xans-layout-multishoplist .xans-layout-multishoplistmultioptioncountry').show();

            var aShippingCountryCode = document.cookie.match('(^|;) ?'+sShippingCountryCode4Cookie+'=([^;]*)(;|$)');

            if (typeof(aShippingCountryCode) != 'undefined' && aShippingCountryCode != null && aShippingCountryCode.length > 2) {
                var sShippingCountryValue = aShippingCountryCode[2];
            }

            // query string으로 넘어 온 배송국가 값이 있다면, 그 값을 적용함
            var aHrefCountryValue = decodeURIComponent(location.href).split("/?country=");

            if (aHrefCountryValue.length == 2) {
                var sShippingCountryValue = aHrefCountryValue[1];
            }

            // 메인 페이지에서 국가선택을 안한 경우, 그 외의 페이지에서 셋팅된 값이 안 나오는 현상 처리
            if (location.href.split("/").length != 4 && $(".xans-layout-multishopshipping .xans-layout-multishopshippingcountrylist").val()) {
                $(".xans-layout-multishoplist .xans-layout-multishoplistmultioption a .ship span").text(" : "+$(".xans-layout-multishopshipping .xans-layout-multishopshippingcountrylist option:selected").text().split("SHIPPING TO : ").join(""));
                if ($("#f_country").length > 0 && location.href.indexOf("orderform.html") > -1) {
                    $("#f_country").val($(".xans-layout-multishopshipping .xans-layout-multishopshippingcountrylist").val());
                }
            }
            if (typeof(sShippingCountryValue) != "undefined" && sShippingCountryValue != "" && sShippingCountryValue != null) {
                sShippingCountryValue = sShippingCountryValue.split("#")[0];
                var bShippingCountryProc = true;

                $(".xans-layout-multishopshipping .xans-layout-multishopshippingcountrylist").val(sShippingCountryValue);
                $(".xans-layout-multishoplist .xans-multishop-listitem .countryLink").text($(".xans-layout-multishopshipping .xans-layout-multishopshippingcountrylist option:selected").text());

                var expires = new Date();
                expires.setTime(expires.getTime() + (30 * 24 * 60 * 60 * 1000)); // 30일간 쿠키 유지
                document.cookie = sShippingCountryCode4Cookie+'=' + $(".xans-layout-multishopshipping .xans-layout-multishopshippingcountrylist").val() +';path=/'+ ';expires=' + expires.toUTCString();

                if ($("#f_country").length > 0 && location.href.indexOf("orderform.html") > -1) {
                    $("#f_country").val(sShippingCountryValue).change();;
                }
            }
        }

        // 언어선택 설정이 사용안함이면 숨김
        if (EC_SHOP_MULTISHOP_SHIPPING.bMultishopShippingLanguageSelection === false) {
            $('.xans-layout-multishopshipping .xans-layout-multishopshippinglanguagelist').hide();
            $('.xans-layout-multishoplist .languageTitle').hide();
            $('.xans-layout-multishoplist .xans-layout-multishoplistmultioptionlanguage').hide();
        } else {
            $('.xans-multishop-listitem').hide();
            $('.xans-layout-multishoplist > h2').hide();
            $('.xans-layout-multishoplist .languageTitle').show();
            $('.xans-layout-multishoplist .xans-layout-multishoplistmultioptionlanguage').show();
            $('.xans-layout-multishoplist .countryTitle').show();
            $('.xans-layout-multishoplist .xans-layout-multishoplistmultioptioncountry').show();
        }

        // 배송국가 및 언어 설정이 둘 다 사용안함이면 숨김
        if (EC_SHOP_MULTISHOP_SHIPPING.bMultishopShipping === false) {
            $(".xans-layout-multishopshipping").hide();
            $('.xans-layout-multishoplist .countryTitle').hide();
            $('.xans-layout-multishoplist .xans-layout-multishoplistmultioptioncountry').hide();
            $('.xans-layout-multishoplist .languageTitle').hide();
            $('.xans-layout-multishoplist .xans-layout-multishoplistmultioptionlanguage').hide();
        } else if (bShippingCountryProc === false && location.href.split("/").length == 4) { // 배송국가 값을 처리한 적이 없고, 메인화면일 때만 선택 레이어를 띄움
            var sShippingCountryValue = $(".xans-layout-multishopshipping .xans-layout-multishopshippingcountrylist").val();
            $(".xans-layout-multishopshipping .xans-layout-multishopshippingcountrylist").val(sShippingCountryValue);
            $(".xans-layout-multishoplist .xans-multishop-listitem .countryLink").text($(".xans-layout-multishopshipping .xans-layout-multishopshippingcountrylist option:selected").text());
            // 배송국가 선택을 사용해야 레이어를 보이게 함
            if (EC_SHOP_MULTISHOP_SHIPPING.bMultishopShippingCountrySelection === true) {
                $(".xans-layout-multishopshipping").show();
            }
        }

        $(".xans-layout-multishopshipping .btnClose").bind("click", function() {
            $(".xans-layout-multishopshipping").hide();
        });

        $(".xans-layout-multishopshipping .ec-base-button a").bind("click", function() {
            var expires = new Date();
            expires.setTime(expires.getTime() + (30 * 24 * 60 * 60 * 1000)); // 30일간 쿠키 유지
            document.cookie = sShippingCountryCode4Cookie+'=' + $(".xans-layout-multishopshipping .xans-layout-multishopshippingcountrylist").val() +';path=/'+ ';expires=' + expires.toUTCString();

            // 도메인 문제로 쿠키로 배송국가 설정이 안 되는 경우를 위해 query string으로 배송국가 값을 넘김
            var sQuerySting = (EC_SHOP_MULTISHOP_SHIPPING.bMultishopShippingCountrySelection === false) ? "" : "/?country="+encodeURIComponent($(".xans-layout-multishopshipping .xans-layout-multishopshippingcountrylist").val());

            location.href = '//'+$(".xans-layout-multishopshipping .xans-layout-multishopshippinglanguagelist").val()+sQuerySting;
        });
        $(".xans-layout-multishoplist .xans-multishop-listitem .countryLink").bind("click", function(e) {
            $('#dimmedSlider').toggle();
            $('html').toggleClass('expand');
            $(".xans-layout-multishopshipping").show();
            e.preventDefault();
        });
        $(".xans-layout-multishoplist .xans-multishop-listitem .languageLink").bind("click", function(e) {
            $('#dimmedSlider').toggle();
            $('html').toggleClass('expand');
            $(".xans-layout-multishopshipping").show();
            e.preventDefault();
        });
    }
});
