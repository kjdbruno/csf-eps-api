<?php

namespace App\Http\Controllers;

use App\Models\PreferenceOffice;
use Illuminate\Http\Request;

use App\Http\Requests\PreferenceOfficeRequest;

class PreferenceOfficeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {

            $preference= PreferenceOffice::where('code', 'LIKE', '%'.$request->get('filter').'%')
                ->orWhere('label', 'LIKE', '%'.$request->get('filter').'%')
                ->orderBy('created_at', 'DESC')
                ->get();
                return response()->json($preference);
                
        } catch (\Exception $e) {

            logger('Message logged from PreferenceOfficeController.index', [$e->getMessage()]);
            return response()->json([
                'error' => 'Something went wrong getting your record',
                'msg' => $e->getMessage()
            ], 400);

        }
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
     * @param  \Illuminate\Http\PreferenceOfficeRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PreferenceOfficeRequest $request)
    {
        try {

            $preference = new PreferenceOffice;
            $preference->label = $request->get('label');
            $preference->code = $request->get('code');
            $preference->save();

            return response()->json([
                'msg' => 'RECORD STORED!',
                'data' => $preference
            ], 200);

        } catch (\Exception $e) {

            logger('Message logged from PreferenceOfficeController.store', [$e->getMessage()]);
            return response()->json([
                'error' => 'Something went wrong storing record',
                'msg' => $e->getMessage()
            ]);

        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PreferenceOffice  $preferenceOffice
     * @return \Illuminate\Http\Response
     */
    public function show(PreferenceOffice $preferenceOffice)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\PreferenceOffice  $preferenceOffice
     * @return \Illuminate\Http\Response
     */
    public function edit(PreferenceOffice $preferenceOffice)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\PreferenceOfficeRequest  $request
     * @param  \App\Models\PreferenceOffice  $preferenceOffice
     * @return \Illuminate\Http\Response
     */
    public function update(PreferenceOfficeRequest $request, $id)
    {
        try {

            $preference = PreferenceOffice::findOrFail($id);
            $preference->label = $request->get('label');
            $preference->code = $request->get('code');
            $preference->save();

            return response()->json([
                'msg' => 'RECORD MODIFIED!',
                'data' => $preference
            ], 200);

        } catch (\Exception $e) {
            logger('Message logged from PreferenceOfficeController.update', [$e->getMessage()]);
            return response()->json([
                'error' => 'Something went wrong updating record',
                'msg' => $e->getMessage()
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PreferenceOffice  $preferenceOffice
     * @return \Illuminate\Http\Response
     */
    public function destroy(PreferenceOffice $preferenceOffice)
    {
        //
    }

     /**
     * Disable the specified resource from the storage
     *
     *
     */
    public function disable($id)
    {
        try {

            $preference = PreferenceOffice::findOrFail($id);
            $preference->isActive = FALSE;
            $preference->save();

            return response()->json([
                'msg' => 'RECORD DISABLED!',
                'data' => $preference
            ], 200);

        } catch (\Exception $e) {
            logger('Message logged from PreferenceOfficeController.disable', [$e->getMessage()]);
            return response()->json([
                'error' => 'Something went wrong disabling record',
                'msg' => $e->getMessage()
            ]);
        }
    }

    /**
     * Enable the specified resource from the storage
     *
     *
     */
    public function enable($id)
    {
        try {

            $preference = PreferenceOffice::findOrFail($id);
            $preference->isActive = TRUE;
            $preference->save();

            return response()->json([
                'msg' => 'RECORD ENABLED!',
                'data' => $preference
            ], 200);

        } catch (\Exception $e) {
            logger('Message logged from PreferenceOfficeController.enable', [$e->getMessage()]);
            return response()->json([
                'error' => 'Something went wrong enabling record',
                'msg' => $e->getMessage()
            ]);
        }
    }
}
