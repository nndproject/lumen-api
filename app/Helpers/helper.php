<?php
/*
                   _                   _           _   
                 | |                 (_)         | |  
  _ __  _ __   __| |  _ __  _ __ ___  _  ___  ___| |_ 
 | '_ \| '_ \ / _` | | '_ \| '__/ _ \| |/ _ \/ __| __|
 | | | | | | | (_| | | |_) | | | (_) | |  __/ (__| |_ 
 |_| |_|_| |_|\__,_| | .__/|_|  \___/| |\___|\___|\__|
                     | |            _/ |              
                     |_|           |__/               


php artisan queue:listen --queue=auth,high,default,low --memory=2
php artisan queue:work --daemon --queue=auth,high,default,low --memory=20 --max-time=240 --sleep=3  --tries=3 --stop-when-empty
*/

use Carbon\Carbon;

use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Crypt;

if(! function_exists('fWaNumber')){
    function fWaNumber($phone)
    {
        try {
            $noWA = str_replace('+','',$phone);
    
            if(!empty($noWA) && $noWA[0] == 0 ){
                $noWA = substr_replace($noWA, "62", 0, 1);
            }elseif(!empty($noWA) && $noWA[0] == 8 ){
                $noWA = '62'.$noWA;
            }
        } catch (\Throwable $th) {
            $noWA = $phone;
        }

        return $noWA;
    }
}

if( !function_exists("saveAndResizeImage") )
{
    function saveAndResizeImage( $image, $type, $dir_name, $width, $height, $old_image = null )
    {
        if( isset( $old_image) )
            unlinkFile( $old_image );

        $dir        =   $type . '/' . $dir_name;
    
        // Create directory first
        if(!File::exists( public_path( $dir ) ))
        {
            Storage::disk('public')->makeDirectory( $dir, 0755, true, true );
        }

        $file_name  =   uniqid() . '.' . $image->getClientOriginalExtension();
        $str_path   =   $dir . '/' . $file_name;
        $path       =   public_path( 'src/' . $str_path );
		// $request->file('file')->extension();


        // Create new Canvas and insert the image
        $img        =   Image::make( $image )->resize( $width, $height, function($constraint)
                        {
                            $constraint->aspectRatio();
                            $constraint->upsize();
                        });

        $img->save( $path );

        return $str_path;
    }
}

if( !function_exists('uploadFile') )
{
    function uploadFile( $fileType, $type, $dir_name, $file, $old_file = null)
    {
        if( isset( $old_file) )
            unlinkFile( $old_file );
        
        $file_path  =   $fileType . '/' . $type . '/' . $dir_name.'/';

        // Create directory first
        if(!File::exists( public_path( $file_path ) ))
        {
            Storage::disk('public')->makeDirectory( $file_path, 0755, true, true );
        }


        if( $file )
        {
            $file_name  =   uniqid() . '.' . $file->extension();
            $output     =   $file->move( public_path('src/' . $file_path), $file_name);
        }

        return $file_path . $file_name;
    }
}

if( !function_exists("unlinkFile"))
{
    function unlinkFile( $path )
    {
	if( empty($path) || !isset($path) || !$path )
            return false;

        if(File::exists(public_path( 'src/' . $path )))
        {
            File::delete(public_path( 'src/' . $path ));

            //Check if folder empty
            $files  =   File::files(public_path( 'src/' . dirname($path) ));
            if(empty($files))
                File::deleteDirectory( public_path( 'src/' . dirname($path)) );
        }
    }
}


if( !function_exists("fLogs"))
{
    function fLogs($str, $type='standard'){
        
        switch ($type) {
            case 'e': //error
                echo "\033[31m$str \033[0m\n";
            break;
            case 's': //success
                echo "\033[92m$str \033[0m\n";
            break;
            case 'w': //warning
                echo "\033[93m$str \033[0m\n";
            break;  
            case 'i': //info
                echo "\033[96m$str \033[0m\n";
            break;      
            default:
                echo $str."\n";
            break;
        }
    }
}

if(!function_exists('config_path'))
{
        /**
        * Return the path to config files
        * @param null $path
        * @return string
        */
        function config_path($path=null)
        {
                return app()->getConfigurationPath(rtrim($path, ".php"));
        }
}

if(!function_exists('public_path'))
{

        /**
        * Return the path to public dir
        * @param null $path
        * @return string
        */
        function public_path($path=null)
        {
                return rtrim(app()->basePath('public/'.$path), '/');
        }
}

if(!function_exists('storage_path'))
{

        /**
        * Return the path to storage dir
        * @param null $path
        * @return string
        */
        function storage_path($path=null)
        {
                return app()->storagePath($path);
        }
}

if(!function_exists('database_path'))
{

        /**
        * Return the path to database dir
        * @param null $path
        * @return string
        */
        function database_path($path=null)
        {
                return app()->databasePath($path);
        }
}

if(!function_exists('resource_path'))
{

        /**
        * Return the path to resource dir
        * @param null $path
        * @return string
        */
        function resource_path($path=null)
        {
                return app()->resourcePath($path);
        }
}

if(!function_exists('lang_path'))
{

        /**
        * Return the path to lang dir
        * @param null $str
        * @return string
        */
        function lang_path($path=null)
        {
                return app()->getLanguagePath($path);
        }
}

if ( ! function_exists('asset'))
{
    /**
     * Generate an asset path for the application.
     *
     * @param  string  $path
     * @param  bool    $secure
     * @return string
     */
    function asset($path, $secure = null)
    {
        return app('url')->asset($path, $secure);
    }
}

if ( ! function_exists('elixir'))
{
    /**
     * Get the path to a versioned Elixir file.
     *
     * @param  string  $file
     * @return string
     */
    function elixir($file)
    {
        static $manifest = null;
        if (is_null($manifest))
        {
            $manifest = json_decode(file_get_contents(public_path().'/build/rev-manifest.json'), true);
        }
        if (isset($manifest[$file]))
        {
            return '/build/'.$manifest[$file];
        }
        throw new InvalidArgumentException("File {$file} not defined in asset manifest.");
    }
}

if ( ! function_exists('auth'))
{
    /**
     * Get the available auth instance.
     *
     * @return \Illuminate\Contracts\Auth\Guard
     */
    function auth()
    {
        return app('Illuminate\Contracts\Auth\Guard');
    }
}

if ( ! function_exists('bcrypt'))
{
    /**
     * Hash the given value.
     *
     * @param  string  $value
     * @param  array   $options
     * @return string
     */
    function bcrypt($value, $options = array())
    {
        return app('hash')->make($value, $options);
    }
}

if ( ! function_exists('redirect'))
{
    /**
     * Get an instance of the redirector.
     *
     * @param  string|null  $to
     * @param  int     $status
     * @param  array   $headers
     * @param  bool    $secure
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    function redirect($to = null, $status = 302, $headers = array(), $secure = null)
    {
        if (is_null($to)) return app('redirect');
        return app('redirect')->to($to, $status, $headers, $secure);
    }
}

if ( ! function_exists('response'))
{
    /**
     * Return a new response from the application.
     *
     * @param  string  $content
     * @param  int     $status
     * @param  array   $headers
     * @return \Symfony\Component\HttpFoundation\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    function response($content = '', $status = 200, array $headers = array())
    {
        $factory = app('Illuminate\Contracts\Routing\ResponseFactory');
        if (func_num_args() === 0)
        {
            return $factory;
        }
        return $factory->make($content, $status, $headers);
    }
}

if ( ! function_exists('secure_asset'))
{
    /**
     * Generate an asset path for the application.
     *
     * @param  string  $path
     * @return string
     */
    function secure_asset($path)
    {
        return asset($path, true);
    }
}

if ( ! function_exists('secure_url'))
{
    /**
     * Generate a HTTPS url for the application.
     *
     * @param  string  $path
     * @param  mixed   $parameters
     * @return string
     */
    function secure_url($path, $parameters = array())
    {
        return url($path, $parameters, true);
    }
}


if ( ! function_exists('session'))
{
    /**
     * Get / set the specified session value.
     *
     * If an array is passed as the key, we will assume you want to set an array of values.
     *
     * @param  array|string  $key
     * @param  mixed  $default
     * @return mixed
     */
    function session($key = null, $default = null)
    {
        if (is_null($key)) return app('session');
        if (is_array($key)) return app('session')->put($key);
        return app('session')->get($key, $default);
    }
}


if ( ! function_exists('cookie'))
{
    /**
     * Create a new cookie instance.
     *
     * @param  string  $name
     * @param  string  $value
     * @param  int     $minutes
     * @param  string  $path
     * @param  string  $domain
     * @param  bool    $secure
     * @param  bool    $httpOnly
     * @return \Symfony\Component\HttpFoundation\Cookie
     */
    function cookie($name = null, $value = null, $minutes = 0, $path = null, $domain = null, $secure = false, $httpOnly = true)
    {
        $cookie = app('Illuminate\Contracts\Cookie\Factory');
        if (is_null($name))
        {
            return $cookie;
        }
        return $cookie->make($name, $value, $minutes, $path, $domain, $secure, $httpOnly);
    }
}