<?php

namespace App\Http\Controllers;

use App\Models\PreferenceKiosk;
use App\Models\PreferenceOffice;
use App\Models\KioskRating;
use App\Models\User;
use Illuminate\Http\Request;

use App\Http\Requests\PreferenceKioskRequest;

use PDF;
use Carbon\Carbon;

class PreferenceKioskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $id)
    {
        date_default_timezone_set('Asia/Manila');

        try {

            $users = User::select('users.*', 'user_roles.roleID', 'preference_years.label AS year', 'user_admins.officeID')
                ->join('user_admins', 'users.id', 'user_admins.userID')
                ->join('user_roles', 'users.id', 'user_roles.userID')
                ->join('preference_years', 'user_admins.yearID', 'preference_years.id')
                ->where('users.id', $id)
                ->get();

            $kiosks = 
            PreferenceOffice::select('preference_offices.*')
                ->where('preference_offices.label', 'LIKE', '%'.$request->get('filter').'%')
                ->get();

                $arr = [];

                foreach ($kiosks as $key => $k_value) {
                    
                    $ks_phy = KioskRating::where('kiosk_ratings.officeID', $k_value->id)
                        ->whereNot('kiosk_ratings.phyRating', 0)
                        ->whereYear('kiosk_ratings.created_at', $users[0]->year)
                        ->sum('kiosk_ratings.phyRating');
                    $ks_ser = KioskRating::where('kiosk_ratings.officeID', $k_value->id)
                        ->whereNot('kiosk_ratings.serRating', 0)
                        ->whereYear('kiosk_ratings.created_at', $users[0]->year)
                        ->sum('kiosk_ratings.serRating');
                    $ks_per = KioskRating::where('kiosk_ratings.officeID', $k_value->id)
                        ->whereNot('kiosk_ratings.perRating', 0)
                        ->whereYear('kiosk_ratings.created_at', $users[0]->year)
                        ->sum('kiosk_ratings.perRating');
                    $ks_ovr = KioskRating::where('kiosk_ratings.officeID', $k_value->id)
                        ->whereNot('kiosk_ratings.ovrRating', 0)
                        ->whereYear('kiosk_ratings.created_at', $users[0]->year)
                        ->sum('kiosk_ratings.ovrRating');

                    $kc_phy = KioskRating::where('kiosk_ratings.officeID', $k_value->id)
                        ->whereNot('kiosk_ratings.phyRating', 0)
                        ->whereYear('kiosk_ratings.created_at', $users[0]->year)
                        ->count();
                    $kc_ser = KioskRating::where('kiosk_ratings.officeID', $k_value->id)
                        ->whereNot('kiosk_ratings.serRating', 0)
                        ->whereYear('kiosk_ratings.created_at', $users[0]->year)
                        ->count();
                    $kc_per = KioskRating::where('kiosk_ratings.officeID', $k_value->id)
                        ->whereNot('kiosk_ratings.perRating', 0)
                        ->whereYear('kiosk_ratings.created_at', $users[0]->year)
                        ->count();
                    $kc_ovr = KioskRating::where('kiosk_ratings.officeID', $k_value->id)
                        ->whereNot('kiosk_ratings.ovrRating', 0)
                        ->whereYear('kiosk_ratings.created_at', $users[0]->year)
                        ->count();

                    $km_phy = (5 * $kc_phy);
                    $kr_phy = ((($ks_phy == 0 && $kc_phy == 0) ? 0 : ($ks_phy / $km_phy)) * 100);

                    $km_ser = (5 * $kc_ser);
                    $kr_ser = ((($ks_ser == 0 && $kc_phy == 0) ? 0 : ($ks_ser / $km_ser)) * 100);

                    $km_per = (5 * $kc_per);
                    $kr_per = ((($ks_per == 0 && $kc_per == 0) ? 0 : ($ks_per / $km_per)) * 100);

                    $km_ovr = (5 * $kc_ovr);
                    $kr_ovr = ((($ks_ovr == 0 && $kc_ovr == 0) ? 0 : ($ks_ovr / $km_ovr)) * 100);

                    $array = [
                        'id' => $k_value->id,
                        'office' => $k_value->label,
                        'code' => $k_value->code,
                        //
                        'kr_phy' => number_format($kr_phy, 2),
                        'kr_ser' => number_format($kr_ser, 2),
                        'kr_per' => number_format($kr_per, 2),
                        'kr_ovr' => number_format($kr_ovr, 2)
                    ];

                    array_push($arr, $array);

                }

                return response()->json($arr);

        } catch (\Exception $e) {

            logger('Message logged from PreferenceKioskController.index', [$e->getMessage()]);
            return response()->json([
                'error' => 'Something went wrong getting record',
                'msg' => $e->getMessage()
            ], 400);

        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\PreferenceKioskRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PreferenceKioskRequest $request)
    {
        date_default_timezone_set('Asia/Manila');

        try {

            $preference = new PreferenceKiosk;
            $preference->name = $request->get('name');
            $preference->officeID = $request->get('officeID');
            $preference->positionID = $request->get('positionID');
            $preference->description = $request->get('description');
            $preference->photo = $request->get('photo');
            $preference->save();

            return response()->json([
                'msg' => 'RECORD STORED!',
                'data' => $preference
            ], 200);

        } catch (\Exception $e) {

            logger('Message logged from PreferenceKioskController.store', [$e->getMessage()]);
            return response()->json([
                'error' => 'Something went wrong storing record',
                'msg' => $e->getMessage()
            ], 400);

        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\PreferenceKioskRequest  $request
     * @param  \App\Models\PreferenceKiosk  $preferenceKiosk
     * @return \Illuminate\Http\Response
     */
    public function update(PreferenceKioskRequest $request, $id)
    {
        date_default_timezone_set('Asia/Manila');

        try {

            $preference = PreferenceKiosk::findOrFail($id);
            $preference->name = $request->get('name');
            $preference->officeID = $request->get('officeID');
            $preference->positionID = $request->get('positionID');
            $preference->description = $request->get('description');
            $preference->photo = $request->get('photo');
            $preference->save();

            return response()->json([
                'msg' => 'RECORD MODIFIED!',
                'data' => $preference
            ], 200);

        } catch (\Exception $e) {

            logger('Message logged from PreferenceKioskController.update', [$e->getMessage()]);
            return response()->json([
                'error' => 'Something went wrong updating record',
                'msg' => $e->getMessage()
            ], 400);

        }
    }

    /**
     * 
     */
    public function disable($id)
    {
        date_default_timezone_set('Asia/Manila');

        try {

            $preference = PreferenceKiosk::findOrFail($id);
            $preference->isActive = FALSE;
            $preference->save();

            return response()->json([
                'msg' => 'RECORD DISABLED!',
                'data' => $preference
            ], 200);

        } catch (\Exception $e) {

            logger('Message logged from PreferenceKioskController.disable', [$e->getMessage()]);
            return response()->json([
                'error' => 'Something went wrong disabling record',
                'msg' => $e->getMessage()
            ], 400);

        }
    }

    /**
     * 
     */
    public function enable($id)
    {
        date_default_timezone_set('Asia/Manila');

        try {

            $preference = PreferenceKiosk::findOrFail($id);
            $preference->isActive = TRUE;
            $preference->save();

            return response()->json([
                'msg' => 'RECORD ENABLED!',
                'data' => $preference
            ], 200);

        } catch (\Exception $e) {

            logger('Message logged from PreferenceKioskController.enable', [$e->getMessage()]);
            return response()->json([
                'error' => 'Something went wrong enabling record',
                'msg' => $e->getMessage()
            ], 400);

        }
    }

    /**
     * 
     */
    public function getKiosk($id)
    {
        date_default_timezone_set('Asia/Manila');

        $userID = auth()->user()->id;

        $users = User::select('users.*', 'user_roles.roleID', 'preference_years.label AS year', 'user_admins.officeID')
            ->join('user_admins', 'users.id', 'user_admins.userID')
            ->join('user_roles', 'users.id', 'user_roles.userID')
            ->join('preference_years', 'user_admins.yearID', 'preference_years.id')
            ->where('users.id', $userID)
            ->get();

        try {

            $office = PreferenceOffice::where('id', $id)
                ->get();

            $ks_phy = KioskRating::where('kiosk_ratings.officeID', $id)
                ->whereYear('kiosk_ratings.created_at', $users[0]->year)
                ->whereNot('kiosk_ratings.phyRating', 0)
                ->sum('kiosk_ratings.phyRating');
            $ks_ser = KioskRating::where('kiosk_ratings.officeID', $id)
                ->whereYear('kiosk_ratings.created_at', $users[0]->year)
                ->whereNot('kiosk_ratings.serRating', 0)
                ->sum('kiosk_ratings.serRating');
            $ks_per = KioskRating::where('kiosk_ratings.officeID', $id)
                ->whereYear('kiosk_ratings.created_at', $users[0]->year)
                ->whereNot('kiosk_ratings.perRating', 0)
                ->sum('kiosk_ratings.perRating');
            $ks_ovr = KioskRating::where('kiosk_ratings.officeID', $id)
                ->whereYear('kiosk_ratings.created_at', $users[0]->year)
                ->whereNot('kiosk_ratings.ovrRating', 0)
                ->sum('kiosk_ratings.ovrRating');

            $kc_phy = KioskRating::where('kiosk_ratings.officeID', $id)
                ->whereYear('kiosk_ratings.created_at', $users[0]->year)
                ->whereNot('kiosk_ratings.phyRating', 0)
                ->count();
            $kc_ser = KioskRating::where('kiosk_ratings.officeID', $id)
                ->whereYear('kiosk_ratings.created_at', $users[0]->year)
                ->whereNot('kiosk_ratings.serRating', 0)
                ->count();
            $kc_per = KioskRating::where('kiosk_ratings.officeID', $id)
                ->whereYear('kiosk_ratings.created_at', $users[0]->year)
                ->whereNot('kiosk_ratings.perRating', 0)
                ->count();
            $kc_ovr = KioskRating::where('kiosk_ratings.officeID', $id)
                ->whereYear('kiosk_ratings.created_at', $users[0]->year)
                ->whereNot('kiosk_ratings.ovrRating', 0)
                ->count();

            $km_phy = (5 * $kc_phy);
            $kr_phy = ((($ks_phy == 0 && $kc_phy == 0) ? 0 : ($ks_phy / $km_phy)) * 100);

            $km_ser = (5 * $kc_ser);
            $kr_ser = ((($ks_ser == 0 && $kc_phy == 0) ? 0 : ($ks_ser / $km_ser)) * 100);

            $km_per = (5 * $kc_per);
            $kr_per = ((($ks_per == 0 && $kc_per == 0) ? 0 : ($ks_per / $km_per)) * 100);

            $km_ovr = (5 * $kc_ovr);
            $kr_ovr = ((($ks_ovr == 0 && $kc_ovr == 0) ? 0 : ($ks_ovr / $km_ovr)) * 100);

            $arr = [];

            $rating = [
                'phyRating' => number_format($kr_phy, 2),
                'serRating' => number_format($kr_ser, 2),
                'perRating' => number_format($kr_per, 2),
                'ovrRating' => number_format($kr_ovr, 2)
            ];

            array_push($arr, $rating);

            $list = KioskRating::select('kiosk_ratings.id AS rID', 'kiosk_ratings.phyRating', 'kiosk_ratings.serRating', 'kiosk_ratings.perRating', 'kiosk_ratings.ovrRating')
                ->where('kiosk_ratings.officeID', $id)
                ->whereYear('kiosk_ratings.created_at', $users[0]->year)
                ->orderBy('kiosk_ratings.created_at', 'DESC')
                ->get();

            // $employees = PreferenceKiosk::where('officeID', $id)
            //     ->get();

                return response()->json([
                    'detail' => $office,
                    'rating' => $arr,
                    'list' => $list,
                    // 'employees' => $employees
                ]);

        } catch (\Exception $e) {

            logger('Message logged from FeedbackController.getKiosk', [$e->getMessage()]);
            return response()->json([
                'error' => 'Something went wrong getting record!',
                'data' => $e->getMessage()
            ], 400);

        } 
    }

    /**
     * 
     */
    public function getReport($id)
    {
        date_default_timezone_set('Asia/Manila');
        
        try {

            $kiosks = KioskRating::select('kiosk_ratings.*', 'preference_offices.label AS office')
                ->join('preference_offices', 'kiosk_ratings.officeID', 'preference_offices.id')
                ->where('kiosk_ratings.id', $id)
                ->get();

            $list = KioskRating::where('id', $id)
                ->orderBy('created_at', 'DESC')
                ->get();
                
            $today = Carbon::now(+8);
            $now = $today->toDayDateTimeString(); 

            $account = User::where('id', auth()->user()->id)
                ->get();

            $pdf = PDF::loadView('report.KioskDetailReport', [
                'kiosks' => $kiosks,
                'list' => $list,
                'name' => $account[0]->name,
                'now' => $now
            ])->setPaper('a4', 'portrait');

            return $pdf->stream();

        } catch (\Exception $e) {

            logger('Message logged from FeedbackController.getReport', [$e->getMessage()]);
            return response()->json([
                'error' => 'Something went wrong reporting record!',
                'data' => $e->getMessage()
            ], 400);

        }  
    }

    /**
     * 
     */
    public function getOfficeReport($id)
    {
        date_default_timezone_set('Asia/Manila');
        
        try {

            $office = PreferenceOffice::where('id', $id)
                ->get();

            $ks_phy = KioskRating::where('kiosk_ratings.officeID', $id)
                ->whereNot('kiosk_ratings.phyRating', 0)
                ->sum('kiosk_ratings.phyRating');
            $ks_ser = KioskRating::where('kiosk_ratings.officeID', $id)
                ->whereNot('kiosk_ratings.serRating', 0)
                ->sum('kiosk_ratings.serRating');
            $ks_per = KioskRating::where('kiosk_ratings.officeID', $id)
                ->whereNot('kiosk_ratings.perRating', 0)
                ->sum('kiosk_ratings.perRating');
            $ks_ovr = KioskRating::where('kiosk_ratings.officeID', $id)
                ->whereNot('kiosk_ratings.ovrRating', 0)
                ->sum('kiosk_ratings.ovrRating');

            $kc_phy = KioskRating::where('kiosk_ratings.officeID', $id)
                ->whereNot('kiosk_ratings.phyRating', 0)
                ->count();
            $kc_ser = KioskRating::where('kiosk_ratings.officeID', $id)
                ->whereNot('kiosk_ratings.serRating', 0)
                ->count();
            $kc_per = KioskRating::where('kiosk_ratings.officeID', $id)
                ->whereNot('kiosk_ratings.perRating', 0)
                ->count();
            $kc_ovr = KioskRating::where('kiosk_ratings.officeID', $id)
                ->whereNot('kiosk_ratings.ovrRating', 0)
                ->count();

            $km_phy = (5 * $kc_phy);
            $kr_phy = ((($ks_phy == 0 && $kc_phy == 0) ? 0 : ($ks_phy / $km_phy)) * 100);

            $km_ser = (5 * $kc_ser);
            $kr_ser = ((($ks_ser == 0 && $kc_phy == 0) ? 0 : ($ks_ser / $km_ser)) * 100);

            $km_per = (5 * $kc_per);
            $kr_per = ((($ks_per == 0 && $kc_per == 0) ? 0 : ($ks_per / $km_per)) * 100);

            $km_ovr = (5 * $kc_ovr);
            $kr_ovr = ((($ks_ovr == 0 && $kc_ovr == 0) ? 0 : ($ks_ovr / $km_ovr)) * 100);
            
            $account = User::where('id', auth()->user()->id)
                ->get();

            $today = Carbon::now(+8);
            $now = $today->toDayDateTimeString(); 

            $year = $today->year;

            $pdf = PDF::loadView('report.KioskOfficeReport', [
                'office' => $office[0]->label,
                'phyRating' => number_format($kr_phy, 2),
                'serRating' => number_format($kr_ser, 2),
                'perRating' => number_format($kr_per, 2),
                'ovrRating' => number_format($kr_ovr, 2),
                'year' => $year,
                'name' => $account[0]->name,
                'now' => $now
            ])->setPaper('a4', 'portrait');

            return $pdf->stream();

        } catch (\Exception $e) {
            logger('Message logged from FeedbackController.getOfficeReport', [$e->getMessage()]);
            return response()->json([
                'error' => 'Something went wrong reporting record!',
                'data' => $e->getMessage()
            ], 400);
        }
    }
}
