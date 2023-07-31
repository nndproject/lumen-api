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

if(! function_exists('fLinkOriginAuction')){
    function fLinkOriginAuction($auction_code)
    {
        try {
            $linkToOrigin = config('access.domain').'/eproc4/lelang/'.$auction_code; 
        } catch (\Throwable $th) {
            $linkToOrigin = 'https://www.datalpse.com/eproc4/lelang/'.$auction_code; 
        }

        return $linkToOrigin;
    }
}

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

if(! function_exists('fdayToMonth')){
	function fdayToMonth($d)
	{
        if($d > 30){
            $months = floor($d / 30);
            $days = $d - ($months*30);
            if($days > 0){
                return  $months ." Bulan " . $days ." Hari";
            }else{
                return  $months ." Bulan";
            }
        } else{ return $d.' Hari'; }
    }
}

if(! function_exists('fSession')){
    function fSession()
    {
        $layanan = session()->get('auth.services');
       
        return $layanan;
    }
}

if(! function_exists('fencryptIt')){
    function fencryptIt($text)
    {
        /* $expected  = crypt('12345', '$2a$07$usesomesillystringforsalt$');
        $correct   = crypt('12345', '$2a$07$usesomesillystringforsalt$');
        $incorrect = crypt('apple', '$2a$07$usesomesillystringforsalt$');

        var_dump(hash_equals($expected, $correct));
        var_dump(hash_equals($expected, $incorrect)); */

        $cryptKey           = 'qJB0rGtIn5UB1xG03efyCp';
        $encryptionMethod   = "AES-256-CBC"; 
        // $qEncoded           = openssl_encrypt($text, $encryptionMethod, $cryptKey);
        $qEncoded           = Crypt::encryptString($text);
        return( $qEncoded );
    }

}

if(! function_exists('fdecryptIt')){
    function fdecryptIt($text)
    {
        /* $cryptKey           = 'qJB0rGtIn5UB1xG03efyCp';
        $encryptionMethod   = "AES-256-CBC"; 
        $qDecoded      = openssl_decrypt($text, $encryptionMethod, $secretHash);
        return( $qDecoded ); */

       return Crypt::decryptString($text);
    }

}

if(! function_exists('fstatusAkun')){
    function fstatusAkun($status)
    {
         if($status =='0'){
            return '<span class="badge badge-light txt-dark"><i class="fa fa-clock-o txt-dark"></i> Pending</span>';
         }elseif($status =='1'){
            return '<span class="badge badge-success fw-bold"><i class="fa fa-check txt-white"></i> Aktif</span>';
         }elseif($status =='2'){
            return '<span class="badge badge-warning fw-bold"><i class="fa fa-minus-circle"></i> Hold</span>';
         }elseif($status =='3'){
            return '<span class="badge badge-danger fw-bold"><i class="fa fa-warning txt-white"></i> Renewal</span>';
         }

        return $status;
    }

}

if(! function_exists('fspanservice')){
    function fspanservice($status)
    {
         if($status =='Waiting Payment'){
            return '<span class="badge badge-light txt-dark"><i class="fa fa-clock-o txt-dark"></i> '.$status.'</span>';
         }elseif($status =='Paid'){
            return '<span class="badge badge-warning fw-bold"><i class="fa fa-check txt-white"></i> '.$status.'</span>';
         }elseif($status =='Payment Confirmed'){
            return '<span class="badge badge-success fw-bold"><i class="fa fa-check-circle txt-white"></i> '.$status.'</span>';
         }elseif($status =='Payment Declined'){
            return '<span class="badge badge-danger fw-bold"><i class="fa fa-warning txt-white"></i> '.$status.'</span>';
         }elseif($status =='Due Date'){
            return '<span class="badge badge-dark fw-bold"><i class="fa fa-warning txt-white"></i> '.$status.'</span>';
         }elseif($status =='Non Actived'){
            return '<span class="badge badge-dark fw-bold"><i class="fa fa-times-circle-o txt-white"></i> '.$status.'</span>';
         }

        return $status;
    }

}

if(! function_exists('fConvertDaysToMonth')){
    function fConvertDaysToMonth($d)
    {
        $convert = '29'; // days you want to convert

		$years = ($convert / 365) ; // days / 365 days
		$years = floor($years); // Remove all decimals

		$month = ($convert % 365) / 30; // I choose 30 for Month (30,31) ;)
		$month = floor($month); // Remove all decimals

		$days = ($convert % 365) % 30; // the rest of days

		// Echo all information set
        $dreturn = $d." Hari";
        if($years >= 1 ){
            $dreturn        = $years.' Tahun ';
            if($month>=1)
                $dreturn    .= $month.' Bulan ';
            if($days>=1)
                $dreturn    .= $days.' Hari ';
        }elseif($month >= 1){
            $dreturn        = $month.' Bulan ';
            if($days>=1)
                $dreturn    .= $days.' Hari ';
        }
        return $dreturn;
    }
}


if(! function_exists('fwarnaPercent')){
    function fwarnaPercent($percent)
    {
    if($percent >= 95)
    {
        return 'txt-danger';
    }elseif ($percent >=90 && $percent <95) {
        return 'txt-warning';
    }elseif ($percent >=85 && $percent <90) {
        return 'txt-primary';
    }elseif ($percent >=80 && $percent <85) {
        return 'txt-success';
    }elseif (!empty($percent) && $percent < 80 ) {
        return 'text-info';
    }else{
        return 'txt-dark';
    }
    }
}

if(! function_exists('fnumber_format_short')){
	function fnumber_format_short($n ,  $precision = 1)
	{ 
        // $n = (0+str_replace(",", "", $n));

        // is this a number?
        if (!is_numeric($n)) return $n;
        
        if ($n < 900) {
            // 0 - 900
            $n_format = number_format($n, $precision);
            $suffix = '';
        } else if ($n < 900000) {
            // 0.9k-850k
            $n_format = number_format($n / 1000, $precision);
            $suffix = 'Rb';
        } else if ($n < 900000000) {
            // 0.9m-850m
            $n_format = number_format($n / 1000000, $precision);
            $suffix = 'Jt';
        } else if ($n < 900000000000) {
            // 0.9b-850b
            $n_format = number_format($n / 1000000000, $precision);
            $suffix = 'M';
        } else {
            // 0.9t+
            $n_format = number_format($n / 1000000000000, $precision);
            $suffix = 'T';
        }
    
      // Remove unecessary zeroes after decimal. "1.0" -> "1"; "1.00" -> "1"
      // Intentionally does not affect partials, eg "1.50" -> "1.50"
        if ( $precision > 0 ) {
            $dotzero = '.' . str_repeat( '0', $precision );
            $n_format = str_replace( $dotzero, ' ', $n_format );
        }
    
        return $n_format . $suffix;
	}
}
if(! function_exists('linkfrontend')){
	function linkfrontend()
	{ 
        return 'http://pengadaan.info/';
	}
}
if(! function_exists('fspanTahap')){
	function fspanTahap($tahap)
    {
    if($tahap =='' || empty($tahap)){
        $datareturn = '<span class="badge badge-danger">Tahap Lelang Kosong</span>';
    }elseif($tahap == 'Lelang Sudah Selesai' || $tahap == 'Tender Sudah Selesai'){
        $datareturn = '<span class="badge badge-success"> <i class="fa fa-check-square"></i> '.ucwords($tahap).'</span>';
    }else{
        $datareturn = '<span class="badge badge-primary"> <i class="fa fa-clock-o"></i> '.ucwords($tahap).'</span>';
    }
    
    return $datareturn;
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

        $file_name  =   uniqid() . '_' . $width . 'x' . $height . '.' . $image->getClientOriginalExtension();
        $str_path   =   $dir . '/' . $file_name;
        $path       =   public_path( 'storage/' . $str_path );
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
            $output     =   $file->move( public_path('storage/' . $file_path), $file_name);
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

        if(File::exists(public_path( 'storage/' . $path )))
        {
            File::delete(public_path( 'storage/' . $path ));

            //Check if folder empty
            $files  =   File::files(public_path( 'storage/' . dirname($path) ));
            if(empty($files))
                File::deleteDirectory( public_path( 'storage/' . dirname($path)) );
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

/*
    select concat(val,' ',cnt) as result from(
    select (substring_index(substring_index(t.title, ' ', n.n), ' ', -1)) val,count(*) as cnt
        from auction t cross join(
         select a.n + b.n * 10 + 1 n
         from 
                (select 0 as n union all select 1 union all select 2 union all select 3 
                        union all select 4 union all select 5 union all select 6 
                        union all select 7 union all select 8 union all select 9) a,
                (select 0 as n union all select 1 union all select 2 union all select 3 
                        union all select 4 union all select 5 union all select 6 
                        union all select 7 union all select 8 union all select 9) b
                order by n 
        ) n
    where n.n <= 1 + (length(t.title) - length(replace(t.title, ' ', '')))
    group by val
    order by cnt desc
) as x
 */