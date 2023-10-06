<?php

namespace App\Http\Controllers;

use App\Models\PreferenceYear;
use Illuminate\Http\Request;

use App\Http\Requests\PreferenceYearRequest;

class PreferenceYearController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {

            $preference = PreferenceYear::where('label', 'LIKE', '%'.$request->get('filter').'%')
                ->orderBy('created_at', 'DESC')
                ->get();
                return response()->json($preference);

        } catch (\Exception $e) {
            logger('Message logged from PreferenceYearController.index', [$e->getMessage()]);
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
     * @param  \Illuminate\Http\PreferenceYearRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PreferenceYearRequest $request)
    {
        try {
            /**
             * store year
             */
            $preference = new PreferenceYear;
            $preference->label = $request->get('label');
            $preference->save();
            /**
             * return response
             */
            return response()->json([
                'msg' => 'RECORD STORED!',
                'data' => $preference
            ], 200);

        } catch (\Exception $e) {
            logger('Message logged from PreferenceYearController.store', [$e->getMessage()]);
            return response()->json([
                'error' => 'Something went wrong storing record!',
                'data' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PreferenceYear  $preferenceYear
     * @return \Illuminate\Http\Response
     */
    public function show(PreferenceYear $preferenceYear)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\PreferenceYear  $preferenceYear
     * @return \Illuminate\Http\Response
     */
    public function edit(PreferenceYear $preferenceYear)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\PreferenceYearRequest  $request
     * @param  \App\Models\PreferenceYear  $preferenceYear
     * @return \Illuminate\Http\Response
     */
    public function update(PreferenceYearRequest $request, $id)
    {
        try {
            /**
             * update year
             */
            $preference = PreferenceYear::findOrFail($id);
            $preference->label = $request->get('label');
            $preference->save();
            /**
             * return response
             */
            return response()->json([
                'msg' => 'RECORD MODIFIED!',
                'data' => $preference
            ], 200);
        } catch (\Exception $e) {
            logger('Message logged from PreferenceYearController.update', [$e->getMessage()]);
            return response()->json([
                'error' => 'Something went wrong updating record!',
                'data' => $e->getMessage()
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PreferenceYear  $preferenceYear
     * @return \Illuminate\Http\Response
     */
    public function destroy(PreferenceYear $preferenceYear)
    {
        //
    }

    /**
     * Disable the specified resource from the storage
     */
    public function disable($id)
    {
        try {
            /**
             * disable year
             */
            $preference = PreferenceYear::findOrFail($id);
            $preference->isActive = FALSE;
            $preference->save();
            /**
             * return response
             */
            return response()->json([
                'msg' => 'RECORD DISABLED!',
                'data' => $preference
            ], 200);
        } catch (\Exception $e) {
            logger('Message logged from PreferenceYearController.disable', [$e->getMessage()]);
            return response()->json([
                'error' => 'Something went wrong disabling record!',
                'data' => $e->getMessage()
            ]);
        }
    }

    /**
     * Enable the specified resource from the storage
     */
    public function enable($id) {
        try {
            /**
             * enable year
             */
            $preference = PreferenceYear::findOrFail($id);
            $preference->isActive = TRUE;
            $preference->save();
            /**
             * return response
             */
            return response()->json([
                'msg' => 'RECORD ENABLED!',
                'data' => $preference
            ], 200);
        } catch (\Exception $e) {
            logger('Message logged from PreferenceYearController.enable', [$e->getMessage()]);
            return response()->json([
                'error' => 'Something went wrong enabling record!',
                'data' => $e->getMessage()
            ]);
        }
    }
}
