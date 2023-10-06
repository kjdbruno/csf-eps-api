<?php

namespace App\Http\Controllers;

use App\Models\PreferenceCategory;
use Illuminate\Http\Request;

use App\Http\Requests\PreferenceCategoryRequest;

class PreferenceCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {

            try {
                $preference= PreferenceCategory::Where('label', 'LIKE', '%'.$request->get('filter').'%')
                    ->orderBy('created_at', 'DESC')
                    ->get();
                    return response()->json($preference);
            } catch (\Exception $e) {
                
                logger('Message logged from PreferenceCategoryController.index', [$e->getMessage()]);
                return response()->json([
                    'error' => 'Something went wrong getting record',
                    'msg' => $e->getMessage()
                ], 400);

            }

        } catch (\Exception $e) {



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
     * @param  \Illuminate\Http\PreferenceCategoryRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PreferenceCategoryRequest $request)
    {
        try {

            $preference = new PreferenceCategory;
            $preference->label = $request->get('label');
            $preference->save();

            return response()->json([
                'msg' => 'RECORD STORED!',
                'data' => $preference
            ], 200);

        } catch (\Exception $e) {

            logger('Message logged from PreferenceCategoryController.store', [$e->getMessage()]);
            return response()->json([
                'error' => 'Something went wrong storing record',
                'msg' => $e->getMessage()
            ], 400);

        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PreferenceCategory  $preferenceCategory
     * @return \Illuminate\Http\Response
     */
    public function show(PreferenceCategory $preferenceCategory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\PreferenceCategory  $preferenceCategory
     * @return \Illuminate\Http\Response
     */
    public function edit(PreferenceCategory $preferenceCategory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\PreferenceCategoryRequest  $request
     * @param  \App\Models\PreferenceCategory  $preferenceCategory
     * @return \Illuminate\Http\Response
     */
    public function update(PreferenceCategoryRequest $request, $id)
    {
        try {

            $preference = PreferenceCategory::findOrFail($id);
            $preference->label = $request->get('label');
            $preference->save();

            return response()->json([
                'msg' => 'RECORD MODIFIED!',
                'data' => $preference
            ], 200);

        } catch (\Exception $e) {

            logger('Message logged from PreferenceCategoryController.update', [$e->getMessage()]);
            return response()->json([
                'error' => 'Something went wrong updating record',
                'msg' => $e->getMessage()
            ], 400);

        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PreferenceCategory  $preferenceCategory
     * @return \Illuminate\Http\Response
     */
    public function destroy(PreferenceCategory $preferenceCategory)
    {
        //
    }

    /**
     * Disable the specified resource from the storage
     */
    public function disable($id)
    {
        try {

            $preference = PreferenceCategory::findOrFail($id);
            $preference->isActive = FALSE;
            $preference->save();

            return response()->json([
                'msg' => 'RECORD DISABLED!',
                'data' => $preference
            ], 200);

        } catch (\Exception $e) {

            logger('Message logged from PreferenceCategoryController.disable', [$e->getMessage()]);
            return response()->json([
                'error' => 'Something went wrong disabling record!',
                'data' => $e->getMessage()
            ], 400);

        }
    }

    /**
     * Enable the specified resource from the storage
     */
    public function enable($id) {
        try {

            $preference = PreferenceCategory::findOrFail($id);
            $preference->isActive = TRUE;
            $preference->save();

            return response()->json([
                'msg' => 'RECORD ENABLED!',
                'data' => $preference
            ], 200);
            
        } catch (\Exception $e) {

            logger('Message logged from PreferenceCategoryController.enable', [$e->getMessage()]);
            return response()->json([
                'error' => 'Something went wrong enabling record!',
                'data' => $e->getMessage()
            ], 400);

        }
    }
}
