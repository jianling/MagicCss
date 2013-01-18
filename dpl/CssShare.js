baidu(function(){
    String.prototype.isEmpty = function () {
        return !/\S/.test(this);
    }
    /**
     * @description 自动识别字符串开头的缩进，缩进最顶级到行首，同时去除首位空行
     */
    function fixTab(str) {
        if(!str) return;
        var arr, i, line, spaces, min_spaces;

        str = str.replace(/\t/g, '    ');
        arr = str.split('\n');

        while( arr[0].isEmpty() ) arr.shift();
        while( arr[arr.length-1].isEmpty() ) arr.pop();

        for ( i = 0; i < arr.length; ++i ) {
            line = arr[i];
            if( line.isEmpty() ) continue;
            spaces = /^ +/.exec(line);
            if( !spaces ) 
                min_spaces = 0;
            else if( min_spaces == undefined || spaces[0].length < min_spaces.length ) min_spaces = spaces[0];
        }

        if ( min_spaces ) for (i = 0; i < arr.length; i++) {
            arr[i] = arr[i].substr(min_spaces.length);
        }

        return arr.join('\n');
    }
    var $nav = baidu('<ul>').appendTo(baidu('<div class="nav">').appendTo('body'));
    var section_pos = [];
    var padding_top = function(){
        var pad = 30, header_height = baidu('div.header').height();
        var value = header_height - baidu(window).scrollTop();
        value < 0 && (value = 0);
        return value + pad;
    };
    function makeNavFor( $section ) {
        var section_top = $section.offset().top;
        var title = $section.children('h2').text();
        var $li = baidu('<li>').appendTo($nav);
        var $a = baidu('<a></a>')
            .text(title)
            .data('target', $section)
            .click(onNavClick)
            .appendTo($li);
        $section.data('nav', $a);
    }
    function onNavClick( e ) {
        var target = baidu(e.target).data('target');
        baidu(window).scrollTop(target.offset().top-padding_top());
        baidu(window).scrollTop(target.offset().top-padding_top()); //执行两次，才可以正确计算
        updateCurrentNav();
    }

    var $sections = baidu('div.section');
    $sections.each(function(index, section){
        var $section = baidu(section);
        var $link = $section.children('link');
        var $example = $section.children('div.example-html');
        if(!$example.length) return;
        // baidu.ajax({
        //     url: $link.attr('href'),
        //     type: 'GET',
        //     dataType: 'text',
        //     success: function( cssContent ) {
        //         var $code = baidu('<pre>').text(cssContent).addClass('prettyprint').addClass('css');
        //         $link.after($code);
        //         prettyPrint();
        //     }
        // });
        var html_code = fixTab( $example.html() );
        var $example_code = baidu('<pre>').text(html_code).addClass('prettyprint').addClass('html');
        $example.before($example_code);
        makeNavFor($section);
    });
    function updateCurrentNav (){
        var bt = baidu(window).scrollTop() + padding_top(), range = 10;
        $sections.each(function(index, section) {
            var $section = baidu(section);
            var st = $section.offset().top,
                sh = $section.height();
            if( bt > st - range && bt < st + sh ) {
                baidu('.nav a.current').removeClass('current');
                var $nav = $section.data('nav');
                $nav && $nav.addClass('current');
            }
        });
    }
    function fixNavPosition() {
        var top = baidu('div.header').height() - baidu(window).scrollTop();
        if(top < 0) top = 0;
        if(baidu.browser.ie && baidu.browser.ie < 7) {
            top += baidu(window).scrollTop();
        }
        baidu('div.nav').css('top', top + 30 + 'px');
    }
    baidu(window).scroll(updateCurrentNav);
    baidu(window).scroll(fixNavPosition).resize(fixNavPosition);
    updateCurrentNav();
    prettyPrint();
    baidu('a[href=#]').removeAttr('href').css('cursor', 'pointer'); //禁止导航
});