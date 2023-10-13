<?php

namespace App\Http\Controllers;

use App\Models\SettingOfficeCategory;
use Illuminate\Http\Request;

use App\Http\Requests\SettingOfficeCategoryRequest;

class SettingOfficeCategoryController extends Controller
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

            $preference = SettingOfficeCategory::select('setting_office_categories.*', 'preference_offices.label AS office', 'preference_offices.code AS code', 'preference_categories.label AS category')
                ->join('preference_categories', 'setting_office_categories.categoryID', 'preference_categories.id')
                ->join('preference_offices', 'setting_office_categories.officeID', 'preference_offices.id')
                ->where('preference_categories.label', 'LIKE', '%'.$request->get('filter').'%')
                ->where('preference_offices.label', 'LIKE', '%'.$request->get('filter').'%')
                ->orderBy('setting_office_categories.created_at', 'DESC')
                ->get();
                
                return response()->json($preference);

        } catch (\Exception $e) {

            logger('Message logged from SettingOfficeCategoryController.index', [$e->getMessage()]);
            return response()->json([
                'error' => 'Something went wrong getting record',
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
     * @param  \Illuminate\Http\SettingOfficeCategoryRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SettingOfficeCategoryRequest $request)
    {
        date_default_timezone_set('Asia/Manila');

        try {

            $setting = new SettingOfficeCategory;
            $setting->categoryID = $request->get('categoryID');
            $setting->officeID = $request->get('officeID');
            $setting->save();

            return response()->json([
                'msg' => 'RECORD STORED!',
                'data' => $setting
            ], 200);

        } catch (\Exception $e) {

            logger('Message logged from SettingOfficeCategoryController.store', [$e->getMessage()]);
            return response()->json([
                'error' => 'Something went wrong storing record',
                'msg' => $e->getMessage()
            ], 400);

        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\SettingOfficeCategory  $settingOfficeCategory
     * @return \Illuminate\Http\Response
     */
    public function show(SettingOfficeCategory $settingOfficeCategory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\SettingOfficeCategory  $settingOfficeCategory
     * @return \Illuminate\Http\Response
     */
    public function edit(SettingOfficeCategory $settingOfficeCategory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\SettingOfficeCategoryRequest  $request
     * @param  \App\Models\SettingOfficeCategory  $settingOfficeCategory
     * @return \Illuminate\Http\Response
     */
    public function update(SettingOfficeCategoryRequest $request, $id)
    {
        date_default_timezone_set('Asia/Manila');

        try {

            $setting = SettingOfficeCategory::findOrFail($id);
            $setting->categoryID = $request->get('categoryID');
            $setting->officeID = $request->get('officeID');
            $setting->save();

            return response()->json([
                'msg' => 'RECORD MODIFIED!',
                'data' => $setting
            ], 200);

        } catch (\Exception $e) {

            logger('Message logged from SettingOfficeCategoryController.update', [$e->getMessage()]);
            return response()->json([
                'error' => 'Something went wrong updating record',
                'msg' => $e->getMessage()
            ], 400);

        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SettingOfficeCategory  $settingOfficeCategory
     * @return \Illuminate\Http\Response
     */
    public function destroy(SettingOfficeCategory $settingOfficeCategory)
    {
        //
    }

    /**
     * Disable the specified resource from the storage
     */
    public function disable($id)
    {
        date_default_timezone_set('Asia/Manila');

        try {

            $setting = SettingOfficeCategory::findOrFail($id);
            $setting->isActive = FALSE;
            $setting->save();

            return response()->json([
                'msg' => 'RECORD DISABLED!',
                'data' => $setting
            ], 200);

        } catch (\Exception $e) {

            logger('Message logged from SettingOfficeCategoryController.disable', [$e->getMessage()]);
            return response()->json([
                'error' => 'Something went wrong disabling record',
                'msg' => $e->getMessage()
            ], 400);

        }
    }

    /**
     * Enable the specified resource from the storage
     */
    public function enable($id)
    {
        date_default_timezone_set('Asia/Manila');
        
        try {

            $setting = SettingOfficeCategory::findOrFail($id);
            $setting->isActive = TRUE;
            $setting->save();

            return response()->json([
                'msg' => 'RECORD ENABLED!',
                'data' => $setting
            ], 200);

        } catch (\Exception $e) {

            logger('Message logged from SettingOfficeCategoryController.enable', [$e->getMessage()]);
            return response()->json([
                'error' => 'Something went wrong enabling record',
                'msg' => $e->getMessage()
            ], 400);

        }
    }
}
