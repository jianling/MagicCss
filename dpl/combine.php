<?php

    $units_base = '../resources/units/base/';
    $components_base = '../resources/components/base/';

    $content = file_get_contents($units_base.'button/button.html');

    function get_dpl_name($content){
        preg_match('/<h2>(.*)<\/h2>/', $content, $match);
        if(!$match){
            return '';
        }else{
            return $match[1];
        }
    }

    function content_to_source($content){
        if(preg_match('/demo\s*start/', $content)){
            $content = preg_replace(array('/<!--\s*demo\s*start\s*-->/', '/<!--\s*demo\s*end\s*-->/'), 
                                    array('<pre class="prettyprint html">'.htmlentities(content_to_tpl($content), ENT_NOQUOTES, "utf-8").'</pre><div class="example-html">', '</div>'), $content);
        }else{
            $content = preg_replace(array('/<!--\s*tpl\s*start\s*-->/', '/<!--\s*tpl\s*end\s*-->/'), 
                                    array('<pre class="prettyprint html">'.htmlentities(content_to_tpl($content), ENT_NOQUOTES, "utf-8").'</pre><div class="example-html">', '</div>'), $content);
        }

        return $content;
    }

    function content_to_tpl($content){
        preg_match('/<!--\s*tpl\s*start\s*-->([\s\S]*)<!--\s*tpl\s*end\s*-->/', $content, $match);

        if(!$match){
            return '';
        }else{
            return $match[1];
        }
    }

    $dpls = array();
    $dirs = array($units_base, $components_base);

    foreach ($dirs as $dir) {
        $handler = opendir($dir);
        $filename = readdir($handler);
        while($filename){
            if(is_dir($dir.'/'.$filename) && $filename != '.' && $filename != '..'){
                $tpl_file = $dir.'/'.$filename.'/'.$filename.'.html';
                $content = file_get_contents($tpl_file);
                $name = get_dpl_name($content);
                $html_source = content_to_source($content);
                $tpl = content_to_tpl($content);
                array_push($dpls, array('html_source' => $html_source, 'tpl' => $tpl));
            }

            $filename = readdir($handler);
        }
    }
?>

<!DOCTYPE html>
<html>
<head>    
    <meta charset="utf8">
    <title>Magic Base CSS</title>
    <script type="text/javascript" src="http://fe.bdimg.com/tangram/2.0.1.3.js"></script>
    <script type="text/javascript" src="pretty/prettify.js"></script>
    <link rel="stylesheet" href="pretty/prettify.css">
    <script type="text/javascript" src="CssShare.js"></script>
    <link rel="stylesheet" href="css/reset.css" />
    <link rel="stylesheet" href="CssShare.css" />
    <link rel="stylesheet" href="../resources/import.php" />
    <link rel="stylesheet" href="../resources/units/default/common.css" />
</head>
<body>
    <div class="header">        
        <h1>Magic Base CSS</h1>
    </div>
    <?php
        foreach ($dpls as $dpl) {
            echo '<div class="section">'.$dpl['html_source'].'</div>';
        }
    ?>
</body>
</html>