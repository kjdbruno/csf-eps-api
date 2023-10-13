<?php

namespace App\Http\Controllers;

use App\Models\PreferencePosition;
use Illuminate\Http\Request;

use App\Http\Requests\PreferencePositionRequest;

class PreferencePositionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        date_default_timezone_set('Asia/Manila');

        try {

            $preference = PreferencePosition::where('label', 'LIKE', '%'.$request->get('filter').'%')
                ->orderBy('created_at', 'DESC')
                ->get();
                return response()->json($preference);

        } catch (\Exception $e) {

            logger('Message logged from PreferencePositionController.index', [$e->getMessage()]);
            return response()->json([
                'error' => 'Something went wrong getting record',
                'msg' => $e->getMessage()
            ]);

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
     * @param  \Illuminate\Http\PreferencePositionRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PreferencePositionRequest $request)
    {
        date_default_timezone_set('Asia/Manila');

        try {

            $preference = new PreferencePosition;
            $preference->label = $request->get('label');
            $preference->save();

            return response()->json([
                'msg' => 'RECORD STORED!',
                'data' => $preference
            ], 200);

        } catch (\Exception $e) {
            logger('Message logged from PreferencePositionController.store', [$e->getMessage()]);
            return response()->json([
                'error' => 'Something went wrong storing record',
                'msg' => $e->getMessage()
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PreferencePosition  $preferencePosition
     * @return \Illuminate\Http\Response
     */
    public function show(PreferencePosition $preferencePosition)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\PreferencePosition  $preferencePosition
     * @return \Illuminate\Http\Response
     */
    public function edit(PreferencePosition $preferencePosition)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\PreferencePositionRequest  $request
     * @param  \App\Models\PreferencePosition  $preferencePosition
     * @return \Illuminate\Http\Response
     */
    public function update(PreferencePositionRequest $request, $id)
    {
        date_default_timezone_set('Asia/Manila');

        try {

            $preference = PreferencePosition::findOrFail($id);
            $preference->label = $request->get('label');
            $preference->save();

            return response()->json([
                'msg' => 'RECORD MODIFIED!',
                'data' => $preference
            ], 200);

        } catch (\Exception $e) {
            logger('Message logged from PreferencePositionController.update', [$e->getMessage()]);
            return response()->json([
                'error' => 'Something went wrong updating record',
                'msg' => $e->getMessage()
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PreferencePosition  $preferencePosition
     * @return \Illuminate\Http\Response
     */
    public function destroy(PreferencePosition $preferencePosition)
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
        date_default_timezone_set('Asia/Manila');

        try {

            $preference = PreferencePosition::findOrFail($id);
            $preference->isActive = FALSE;
            $preference->save();

            return response()->json([
                'msg' => 'RECORD DISABLED!',
                'data' => $preference
            ], 200);

        } catch (\Exception $e) {
            logger('Message logged from PreferencePositionController.disable', [$e->getMessage()]);
            return response()->json([
                'error' => 'Something went wrong disabling record',
                'msg' => $e->getMessage()
            ], $e->getCode());
        }
    }

    /**
     * Enable the specified resource from the storage
     *
     *
     */
    public function enable($id)
    {
        date_default_timezone_set('Asia/Manila');
        
        try {

            $preference = PreferencePosition::findOrFail($id);
            $preference->isActive = TRUE;
            $preference->save();

            return response()->json([
                'msg' => 'RECORD ENABLED!',
                'data' => $preference
            ], 200);

        } catch (\Exception $e) {
            logger('Message logged from PreferencePositionController.enable', [$e->getMessage()]);
            return response()->json([
                'error' => 'Something went wrong enabling record',
                'msg' => $e->getMessage()
            ], $e->getCode());
        }
    }
}
