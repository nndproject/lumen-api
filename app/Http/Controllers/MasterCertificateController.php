<?php

namespace App\Http\Controllers;

use App\Models\MasterCertificate;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MasterCertificateController extends Controller
{
    
    public function __construct()
    {
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = MasterCertificate::latest()->paginate(5);

        return response()->json([
            "success"   => true,
            "message"   => "Data Found",
            "data"      => $data
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function search($param)
    {
        $data = MasterCertificate::where('event_name', 'like', '%'. $param .'%')
        ->orWhere('event_name', 'like', '%'. $param .'%')
        ->orWhere('event_description', 'like', '%'. $param .'%')
        ->orWhere('status', 'like', '%'. $param .'%')
        ->paginate(5);

        return response()->json([
            "success"   => true,
            "message"   => "Data Found",
            "data"      => $data
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'event_name' => 'required',
            'event_description' => 'required',
            'event_date' => 'required',
            'event_status' => 'required',
            'ttd_instansi' => 'required|array',
            'ttd_nama' => 'required|array',
            'ttd_jabatan' => 'required|array',
            'ttd_img.*' => 'required|mimes:jpg,jpeg,png,bmp|max:20000',
        ]);

        $ttd_signed = array(); 
        // format array : 
        // ['instansi' => 'nama_instansi', 'nama' =>'nama penandatangan', 'jabatan' => 'jabatan, 'img' => 'png_ttd]
        for ($i=0; $i < sizeof($request->ttd_instansi); $i++) {
            try {
                // upload image ttd
                $files = $request->ttd_img[$i];
                $tempname = saveAndResizeImage( $files, 'master-cert', Str::slug($request->event_name), 500, 500 );
            } catch (\Throwable $th) {
                $tempname = "";
            }

            $temp = array(
                'instansi'  => $request->ttd_instansi[$i],
                'nama'      => $request->ttd_nama[$i],
                'jabatan'   => $request->ttd_jabatan[$i],
                'img'       => $tempname,
            );

            \array_push($ttd_signed, $temp);
        }

        try {
            $data = new MasterCertificate();
            $data->event_name           = $request->event_name;
            $data->event_description    = $request->event_description;
            $data->event_date           = Carbon::parse($request->event_date)->format('Y-m-d');
            $data->event_signed         = \json_encode($ttd_signed);
            $data->status               = $request->event_status;
            $data->post_by              = auth()->user()->id ?? 0;
            $data->save();
        } catch (\Throwable $th) {
            Log::error($th);
            return response()->json([
                "success"   => false,
                "message"   => "Error creating data",
                "data"      => $th->getMessage()
            ]);
        }

        return response()->json([
            "success"   => true,
            "message"   => "The item was created successfully",
            "data"      => array( "request"   => $request->all() )
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = MasterCertificate::findOrFail($id);

        return response()->json([
            "success"   => true,
            "message"   => "Data Found",
            "data"      => $data
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'event_name' => 'required',
            'event_description' => 'required',
            'event_date' => 'required',
            'event_status' => 'required',
            'ttd_instansi' => 'nullable|array',
            'ttd_nama' => 'required_with:ttd_instansi|array',
            'ttd_jabatan' => 'required_with:ttd_instansi|array',
            'ttd_img_change'    => 'required_with:ttd_instansi|array',
            'ttd_img.*'    => "bail|required_unless:ttd_img_change,=,0|mimes:jpg,jpeg,png,bmp|max:20000",
        ]);

        
        $data = MasterCertificate::findOrFail($id);

        $ttd_signed = array(); 
        // format array : 
        // ['instansi' => 'nama_instansi', 'nama' =>'nama penandatangan', 'jabatan' => 'jabatan, 'img' => 'png_ttd]
        
        $oldData = json_decode($data->event_signed);
        for ($i=0; $i < sizeof($request->ttd_instansi); $i++) {
            try {
                // upload image ttd
                $files = $request->ttd_img[$i];

                if($request->ttd_img_change[$i] == '1'){
                    $tempname = saveAndResizeImage( $files, 'master-cert', Str::slug($request->event_name), 500, 500, $oldData[$i]->img);
                }else{
                    $tempname = $oldData[$i]->img;
                }
            } catch (\Throwable $th) {
                $tempname = "";
            }

            $temp = array(
                'instansi'  => $request->ttd_instansi[$i],
                'nama'      => $request->ttd_nama[$i],
                'jabatan'   => $request->ttd_jabatan[$i],
                'img'       => $tempname,
            );

            \array_push($ttd_signed, $temp);
        }

        try {
            $data->event_name        = $request->event_name;
            $data->event_description = $request->event_description;
            $data->event_date        = Carbon::parse($request->event_date)->format('Y-m-d');
            $data->event_signed      = \json_encode($ttd_signed);
            $data->status            = $request->event_status;
            $data->post_by           = auth()->user()->id ?? 0;
            $data->save();
        } catch (\Throwable $th) {
            Log::error($th);
            return response()->json([
                "success"   => false,
                "message"   => "Error updating data",
                "data"      => $th->getMessage()
            ]);
        }

        return response()->json([
            "success"   => true,
            "message"   => "The item was updated successfully",
            "data"      => $request->all()
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $data = MasterCertificate::find($id)->delete();
        } catch (\Throwable $th) {
            //throw $th;
        }
        return response()->json([
            "success"   => true,
            "message"   => "The item was deleted successfully",
            "data"      => array()
        ]);
    }
}
