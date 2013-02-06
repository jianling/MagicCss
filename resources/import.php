<?php
    header("Content-type: text/css; charset=utf-8");
    
    $units_base_dir = '../resources/units/base/';
    $components_base_dir = '../resources/components/base/';

    $models = explode(",", $_GET['m']);
    $skins = explode(",", $_GET['skins']);

    if(!isset($_GET['m'])){
        $models = getAllModels();
    }
    if(!isset($_GET['skins'])){
        $skins = array('default');
    }

    // var_dump($models);return;

    $output = '';
    $IMPORTED = array();

    foreach ($models as $model) {
        if(preg_match('/magic/', $model)){
            $output .= getComponentCSS($model);
        }else{
            $output .= getUnitCSS($model);
        }
    }

    echo $output;

    function getAllModels(){
        global $units_base_dir, $components_base_dir;

        $dirs = array($units_base_dir, $components_base_dir);
        $models = array();

        foreach ($dirs as $dir) {
            $handler = opendir($dir);
            $filename = readdir($handler);
            while($filename){
                if(is_dir($dir.'/'.$filename) && $filename != '.' && $filename != '..'){
                    array_push($models, $filename);
                }
                $filename = readdir($handler);
            }
        }

        return $models;
    }

    function getComponentCSS($model){
        // echo $model."\n";
        global $units_base_dir, $components_base_dir, $IMPORTED, $skins;

        if(in_array($model, $IMPORTED)) return '';

        array_push($IMPORTED, $model);

        # 取base部分css
        $model_base = $components_base_dir.$model.'/'.$model.'.css';

        # 取依赖css
        $content = file_get_contents($model_base);
        # /** @import units: [button] **/
        $unit_pattern = "/\/\*\*\s*@import\s+units\s*:\s+\[(.+)\]\s+\*\*\//";
        preg_match_all($unit_pattern, $content, $units_matched);
        # /** @import components: [button] **/
        $components_pattern = "/\/\*\*\s*@import\s+components\s*:\s+\[(.+)\]\s+\*\*\//";
        preg_match_all($components_pattern, $content, $components_matched);

        $imported_units = explode(',', $units_matched[1][0]);
        $imported_components = explode(',', $components_matched[1][0]);

        foreach ($imported_units as $imported_unit) {
            $imported_unit = trim($imported_unit);
            if($imported_unit) $componentcss .= getUnitCSS($imported_unit);
        }

        foreach ($imported_components as $imported_component) {
            $imported_component = trim($imported_component);
            if($imported_component) $componentcss .= getComponentCSS($imported_component);
        }

        $componentcss .= file_get_contents($model_base)."\n";

        # 取skin部分css
        foreach ($skins as $skin) {
            $model_skin = '../resources/components/'.$skin.'/'.$model.'/'.$model.'.css';
            $componentcss .= file_get_contents($model_skin)."\n";
        }

        return $componentcss;
    }

    function getUnitCSS($model){
        global $units_base_dir, $components_base_dir, $IMPORTED, $skins;

        if(in_array($model, $IMPORTED)) return '';

        array_push($IMPORTED, $model);

        # 取base部分css
        $model_base = $units_base_dir.$model.'/'.$model.'.css';
        $unitcss .= file_get_contents($model_base)."\n";

        # 取skin部分css
        foreach ($skins as $skin) {
            $model_skin = '../resources/units/'.$skin.'/'.$model.'.css';
            $unitcss .= file_get_contents($model_skin)."\n";
        }

        return $unitcss;
    }
?>