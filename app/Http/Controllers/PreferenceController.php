<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\PreferenceYear;
use App\Models\PreferenceSex;
use App\Models\PreferenceRole;
use App\models\PreferencePosition;
use App\Models\PreferenceOffice;
use App\Models\PreferenceCategory;
use App\Models\PreferenceKiosk;

class PreferenceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function getYear()
    {
        try {
            return response()->json(
                PreferenceYear::select('id AS value', 'label')
                    ->where('isActive', TRUE)
                    ->get()
            );
        } catch (\Exception $e) {
            logger('Message logged from PreferenceController.getYear', [$e->getMessage()]);
            return response()->json([
                'error' => 'Something went wrong getting record',
                'msg' => $e->getMessage()
            ], $e->getCode());
        } 
    }

    /**
     * Display a listing of the resource.
     */
    public function getSex()
    {
        try {
            return response()->json(
                PreferenceSex::select('id AS value', 'label')
                    ->where('isActive', TRUE)
                    ->get()
            );
        } catch (\Exception $e) {
            logger('Message logged from PreferenceController.getSex', [$e->getMessage()]);
            return response()->json([
                'error' => 'Something went wrong getting record',
                'msg' => $e->getMessage()
            ], $e->getCode());
        } 
    }

    /**
     * Display a listing of the resource.
     */
    public function getRole()
    {
        try {
            return response()->json(
                PreferenceRole::select('id AS value', 'label')
                    ->get()
            );
        } catch (\Exception $e) {
            logger('Message logged from PreferenceController.getRole', [$e->getMessage()]);
            return response()->json([
                'error' => 'Something went wrong getting record',
                'msg' => $e->getMessage()
            ], $e->getCode());
        } 
    }

    /**
     * Display a listing of the resource.
     */
    public function getPosition()
    {
        try {
            return response()->json(
                PreferencePosition::select('id AS value', 'label')
                    ->where('isActive', TRUE)
                    ->get()
            );
        } catch (\Exception $e) {
            logger('Message logged from PreferenceController.getPosition', [$e->getMessage()]);
            return response()->json([
                'error' => 'Something went wrong getting record',
                'msg' => $e->getMessage()
            ], $e->getCode());
        } 
    }

    /**
     * Display a listing of the resource.
     */
    public function getOffice()
    {
        try {
            return response()->json(
                PreferenceOffice::select('id AS value', 'label')
                    ->where('isActive', TRUE)
                    ->get()
            );
        } catch (\Exception $e) {
            logger('Message logged from PreferenceController.getPosition', [$e->getMessage()]);
            return response()->json([
                'error' => 'Something went wrong getting record',
                'msg' => $e->getMessage()
            ], $e->getCode());
        } 
    }

    /**
     * Display a listing of the resource.
     */
    public function getCategory()
    {
        try {
            return response()->json(
                PreferenceCategory::select('id AS value', 'label')
                    ->where('isActive', TRUE)
                    ->get()
            );
        } catch (\Exception $e) {
            logger('Message logged from PreferenceController.getCategory', [$e->getMessage()]);
            return response()->json([
                'error' => 'Something went wrong getting record',
                'msg' => $e->getMessage()
            ], $e->getCode());
        } 
    }

    /**
     * Display listing of resource
     */
    public function getPersonnel($id)
    {
        try {
            return response()->json(
                PreferenceKiosk::select('preference_kiosks.id', 'preference_kiosks.name AS label', 'preference_kiosks.description', 'preference_positions.label AS position', 'preference_offices.code AS office')
                    ->join('preference_positions', 'preference_kiosks.positionID', 'preference_positions.id')
                    ->join('preference_offices', 'preference_kiosks.officeID', 'preference_offices.id')
                    ->where('preference_kiosks.officeID', $id)
                    ->where('preference_kiosks.isActive', TRUE)
                    ->get()
            );
        } catch (\Exception $e) {
            logger('Message logged from PreferenceController.getPersonnel', [$e->getMessage()]);
            return response()->json([
                'error' => 'Something went wrong getting record',
                'msg' => $e->getMessage()
            ], $e->getCode());
        } 
    }
}
