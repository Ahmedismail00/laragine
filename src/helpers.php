<?php

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

if (!function_exists('dummy_function')) {
    /**
     * get the dummy function.
     *
     * @param string $dummy
     * @return string
     */
    function dummy_function($dummy = '')
    {
        return $dummy;
    }
}

if (!function_exists('client_validation_response')) {
    /**
     * get the validation message formatted for client side.
     *
     * @param array  $validations
     * @param int    $start_code
     * @return array
     */
    function client_validation_response($validations, &$start_code = 4101)
    {
        $array = [];
        
        foreach ($validations as $key => $validation) {
            
            if ($key == 'custom' || $key == 'attributes') {
                continue;
            }
            
            if (is_array($validation)) {
                $array[$key] = client_validation_response($validation, $start_code);
            } else {
                $array[$key] = [
                    config('laragine.validation.message') => $validation,
                    config('laragine.validation.code')    => $start_code
                ];
                
                $start_code++;
            }
        }
        
        return $array;
    }
}

if (!function_exists('create_config_folder')) {

    /**
     * Create new Config folder
     * Key for know if this folder creates for base or other module
     * @param string $key
     * @param string $path
     * @return string
     */
    function create_folders(string $key, array $names = [])
    {

    }
}

if (!function_exists('folder_exist')) {

    /**
     * Check if folder exist or not
     * Key refer to main paths helper function in laravel
        * app_path - resource_path - config_path - ....
        *  (app - resource - migration -...)
     * @param string $key
     * @param string $path
     * @return string
     */
    function folder_exist(string $key, string $path = ''): string
    {
        return File::isDirectory($key($path));
    }
}


if (!function_exists('getStub')) {
    function getStub(string $path): string
    {
        return file_get_contents($path);
    }
}

if (!function_exists('makeModule')) {
    function makeModules($name): string
    {
        $main_files = config('laragine.module.main_files');
        $unit_folders = config('laragine.module.unit_folders');

        if (!createModule($name)) {
            return 'created before';
        }

        createModuleFiles($main_files, $name);

        createModuleFolders($unit_folders, $name);

        return 'done';
    }
}



if (!function_exists('createModule')) {
    /**
     * === Create module folder ===
     * @param $name
     * @param string $main_path
     */
    function createModule($name, string $main_path = 'Core\\') {
        $module_name = Str::studly($name);
        if(!folder_exist('base_path', "Core/$module_name")) {
            // create a module folder
            mkdir(base_path() . "\\$main_path/$module_name", 0777, true);
        } else {
            return false;
        }
        return true;
    }
}

/**

 */
if (!function_exists('createModuleFiles')) {
    /**
     * == Create main module files ==
     * - Config/main.php
     * - routes/api.php
     * - routes/web.php
     * @param $main_files
     * @param $name
     * @param string $main_path
     */
    function createModuleFiles($main_files, $name, string $main_path = 'Core\\') {
        $plural_name_lower_case = Str::plural(Str::lower($name));
        $module_name = Str::studly($name);

        foreach ($main_files as $key => $file) {
            $folder = substr($file, 0,strrpos($file, '/'));
            if(!folder_exist('base_path', "Core/$module_name/$folder")) {
                mkdir(base_path("Core/$module_name/$folder"), 0777, true);
            }
            $temp = str_replace (
                [
                    '#UNIT_NAME#',
                    '#UNIT_NAME_PLURAL_LOWER_CASE#',
                    '#MODULE_NAME#',
                ],
                [
                    $module_name,
                    $plural_name_lower_case,
                    $module_name
                ],
                getStub(__DIR__ . '\\' . $main_path. '\\' . "Module". '\\' .$key)
            );
            file_put_contents(base_path() . '\\Core'."\\$module_name\\$file", $temp);
        }
    }
}



/**
 * == Create main module folders
 */

if (!function_exists('createModuleFolders')) {
    function createModuleFolders($unit_folders, $name) {
        $module_name = Str::studly($name);
        foreach ($unit_folders as $key => $folder) {
            if(!folder_exist('base_path', "Core/$module_name/$folder")) {
                mkdir(base_path("Core/$module_name/$folder"), 0777, true);
            }
        }
    }
}