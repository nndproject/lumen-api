<?php

namespace App\Http\Controllers;

use App\Models\MasterCertificate;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MasterCertificateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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

        try {
            $data = new MasterCertificate();
            $data->event_name = $request->event_name;
            $data->event_description = $request->event_description;
            $data->event_date = Carbon::parse($request->event_date)->format('Y-m-d');
            $data->event_signed = $request->event_name;
            $data->status = $request->event_status;
            $data->post_by      = auth()->user()->id ?? 0;
            $data->save();
        } catch (\Throwable $th) {
            Log::error($th);
            return response()->json([
                "success"   => false,
                "message"   => "Error creating data",
                "data"      => $th->getMessage()
            ])
            ->withHeaders([
                'X-Header-Author' => '#nndproject - fernandoferry',
            ]);
        }

        return response()->json([
            "success"   => true,
            "message"   => "The item was created successfully",
            "data"      => json_encode(array())
        ])
        ->withHeaders([
            'X-Header-Author' => '#nndproject - fernandoferry',
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
