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
use App\Models\User;
use App\Models\UserRole;
use App\Models\UserClient;
use App\Models\Rating;
use App\Models\Feedback;
use App\Models\FeedbackRating;
use App\Models\Discussion;
use App\Models\KioskRating;
use App\Models\PreferenceMonth;
use App\Models\PreferenceMessage;

use Carbon\Carbon;
use Carbon\CarbonPeriod;

class PreferenceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function getOverview($id)
    {
        date_default_timezone_set('Asia/Manila');

        try {

            $users = User::select('users.*', 'user_roles.roleID', 'preference_years.label AS year', 'user_admins.officeID')
                ->join('user_admins', 'users.id', 'user_admins.userID')
                ->join('user_roles', 'users.id', 'user_roles.userID')
                ->join('preference_years', 'user_admins.yearID', 'preference_years.id')
                ->where('users.id', $id)
                ->get();

                $scr_dt = Carbon::create($users[0]->year, 1, 01, 0);
                $ecr_dt = Carbon::create($users[0]->year, 12, 31, 0);

                $spr_dt = Carbon::create($users[0]->year, 1, 01, 0)->subYear();
                $epr_dt = Carbon::create($users[0]->year, 12, 31, 0)->subYear();

                if ($users[0]->roleID == 1 OR $users[0]->roleID == 2 OR $users[0]->roleID == 3) {

                    $cos = Rating::whereNot('rating', 0.00)
                        ->whereBetween('created_at', [$scr_dt, $ecr_dt])
                        ->sum('rating');
                        
                    $coc = Rating::whereNot('rating', 0.00)
                        ->whereBetween('created_at', [$scr_dt, $ecr_dt])
                        ->count();

                    $com = (5 * $coc);
                    $cor = ((($cos == 0 && $coc == 0) ? 0 : ($cos / $com)) * 100);

                    $pos = Rating::whereNot('rating', 0.00)
                        ->whereBetween('created_at', [$spr_dt, $epr_dt])
                        ->sum('rating');
                        
                    $poc = Rating::whereNot('rating', 0.00)
                        ->whereBetween('created_at', [$spr_dt, $epr_dt])
                        ->count();

                    $pom = (5 * $poc);
                    $por = ((($pos == 0 && $poc == 0) ? 0 : ($pos / $pom)) * 100);

                    $or_percent = ($cor - $por);

                    $cfs = FeedbackRating::whereNot('rating', 0)
                        ->whereBetween('created_at', [$scr_dt, $ecr_dt])
                        ->sum('rating');

                    $cfc = FeedbackRating::whereNot('rating', 0)
                        ->whereBetween('created_at', [$scr_dt, $ecr_dt])
                        ->count();
                        
                    $cfm = (5 * $cfc);
                    $cfr = ((($cfs == 0 && $cfc == 0) ? 0 : ($cfs / $cfm)) * 100);

                    $pfs = FeedbackRating::whereNot('rating', 0)
                        ->whereBetween('created_at', [$spr_dt, $epr_dt])
                        ->sum('rating');
                        
                    $pfc = FeedbackRating::whereNot('rating', 0)
                        ->whereBetween('created_at', [$spr_dt, $epr_dt])
                        ->count();
                        
                    $pfm = (5 * $pfc);
                    $pfr = ((($pfs == 0 && $pfc == 0) ? 0 : ($pfs / $pfm)) * 100);

                    $fb_percent = ($cfr - $pfr);

                    $ks_phy = KioskRating::whereNot('phyRating', 0)
                        ->whereBetween('created_at', [$scr_dt, $ecr_dt])
                        ->sum('phyRating');

                    $ks_ser = KioskRating::whereNot('serRating', 0)
                        ->whereBetween('created_at', [$scr_dt, $ecr_dt])
                        ->sum('serRating');

                    $ks_per = KioskRating::whereNot('perRating', 0)
                        ->whereBetween('created_at', [$scr_dt, $ecr_dt])
                        ->sum('perRating');

                    $ks_ovr = KioskRating::whereNot('ovrRating', 0)
                        ->whereBetween('created_at', [$scr_dt, $ecr_dt])
                        ->sum('ovrRating');
                        
                    $kc_phy = KioskRating::whereNot('phyRating', 0)
                        ->whereBetween('created_at', [$scr_dt, $ecr_dt])
                        ->count();

                    $kc_ser = KioskRating::whereNot('serRating', 0)
                        ->whereBetween('created_at', [$scr_dt, $ecr_dt])
                        ->count();

                    $kc_per = KioskRating::whereNot('perRating', 0)
                        ->whereBetween('created_at', [$scr_dt, $ecr_dt])
                        ->count();

                    $kc_ovr = KioskRating::whereNot('ovrRating', 0)
                        ->whereBetween('created_at', [$scr_dt, $ecr_dt])
                        ->count();
                        
                    $km_phy = (5 * $kc_phy);
                    $kr_phy = ((($ks_phy == 0 && $kc_phy == 0) ? 0 : ($ks_phy / $km_phy)) * 100);

                    $km_ser = (5 * $kc_ser);
                    $kr_ser = ((($ks_ser == 0 && $kc_phy == 0) ? 0 : ($ks_ser / $km_ser)) * 100);

                    $km_per = (5 * $kc_per);
                    $kr_per = ((($ks_per == 0 && $kc_per == 0) ? 0 : ($ks_per / $km_per)) * 100);

                    $km_ovr = (5 * $kc_ovr);
                    $kr_ovr = ((($ks_ovr == 0 && $kc_ovr == 0) ? 0 : ($ks_ovr / $km_ovr)) * 100);

                    $registrants = PreferenceMonth::where('isActive', TRUE)
                        ->get();

                        $regArr = [];

                        foreach ($registrants as $key => $value) {

                            $user = UserRole::where('roleID', 5)
                                ->whereMonth('created_at', $value->code)
                                ->whereYear('created_at', $users[0]->year)
                                ->count();

                                $array = [
                                    'label' => $value->label,
                                    'count' => $user
                                ];

                                array_push($regArr, $array);

                        }
                    
                    $demographic = PreferenceSex::where('isActive', TRUE)
                        ->get();

                        $demArr = [];

                        foreach ($demographic as $key => $value) {

                            $user = UserClient::Where('sexID', $value->id)
                                ->whereYear('created_at', $users[0]->year)
                                ->count();

                                $array = [
                                    'label' => $value->label,
                                    'count' => $user
                                ];

                                array_push($demArr, $array);

                        }

                    $month = PreferenceMonth::where('isActive', TRUE)
                        ->get();

                        $monthlyArr = [];

                        foreach ($month as $key => $value) {

                            $count = Feedback::whereMonth('created_at', $value->code)
                                ->whereYear('created_at', $users[0]->year)
                                ->count();

                                $array = [
                                    'label' => $value->label,
                                    'count' => $count,
                                ];

                                array_push($monthlyArr, $array);

                        }

                    $office = PreferenceOffice::where('isActive', TRUE)
                        ->get();

                        $officeArr = [];

                        foreach ($office as $key => $value) {

                            $pending = Feedback::join('feedback_offices', 'feedback.id', 'feedback_offices.feedbackID')
                                ->where('feedback_offices.officeID', $value->id)
                                ->where('feedback.status', 1)
                                ->whereYear('feedback.created_at', $users[0]->year)
                                ->count();

                            $ongoing = Feedback::join('feedback_offices', 'feedback.id', 'feedback_offices.feedbackID')
                                ->where('feedback_offices.officeID', $value->id)
                                ->where('feedback.status', 2)
                                ->whereYear('feedback.created_at', $users[0]->year)
                                ->count();

                            $completed = Feedback::join('feedback_offices', 'feedback.id', 'feedback_offices.feedbackID')
                                ->where('feedback_offices.officeID', $value->id)
                                ->where('feedback.status', 3)
                                ->whereYear('feedback.created_at', $users[0]->year)
                                ->count();

                                $array = [
                                    'label' => $value->code,
                                    'pending' => $pending,
                                    'ongoing' => $ongoing,
                                    'completed' => $completed
                                ];

                                array_push($officeArr, $array);

                        }

                    $period = CarbonPeriod::create($scr_dt, $ecr_dt);

                    $perArr = [];

                    foreach ($period as $date) {

                        $d_from = $date->format('Y-m-d 00:00:00');
                        $d_to = $date->format('Y-m-d 23:59:59');

                        $count = Feedback::whereBetween('created_at', [$d_from, $d_to])
                            ->count();

                            $array = [
                                'date' => $date->format('m-d'),
                                'count' => $count
                            ];

                            array_push($perArr, $array);

                    }

                    $registrant = UserRole::where('roleID', 5)
                        ->whereYear('created_at', $users[0]->year)
                        ->count();

                    $feedback = Feedback::whereYear('created_at', $users[0]->year)
                        ->count();

                    $discussion = Discussion::whereYear('created_at', $users[0]->year)
                        ->count();

                        return response()->json(
                            [
                                'totalRegistrant' => $registrant,
                                'totalFeedback' => $feedback,
                                'totalDiscussion' => $discussion,
                                'or_current' => number_format($cor, 2),
                                'or_prev' => number_format($por, 2),
                                'or_percent' => number_format(abs($or_percent), 2),
        
                                'fb_current' => number_format($cfr, 2),
                                'fb_prev' => number_format($pfr, 2),
                                'fb_percent' => number_format(abs($fb_percent), 2),
        
                                'kr_phy' => number_format($kr_phy, 2),
                                'kr_ser' => number_format($kr_ser, 2),
                                'kr_per' => number_format($kr_per, 2),
                                'kr_ovr' => number_format($kr_ovr, 2),
        
                                'registrant' => $regArr,
                                'demographic' => $demArr,
                                'monthlyFeedback' => $monthlyArr,
                                'officeFeedback' => $officeArr,
                                'periodFeedback' => $perArr
                            ]
                        );

                } else {

                    $cos = Rating::whereNot('rating', 0.00)
                        ->where('officeID', $users[0]->officeID)
                        ->whereBetween('created_at', [$scr_dt, $ecr_dt])
                        ->sum('rating');

                    $coc = Rating::whereNot('rating', 0.00)
                        ->where('officeID', $users[0]->officeID)
                        ->whereBetween('created_at', [$scr_dt, $ecr_dt])
                        ->count();
                        
                    $com = (5 * $coc);
                    $cor = ((($cos == 0 && $coc == 0) ? 0 : ($cos / $com)) * 100);
                    
                    $pos = Rating::whereNot('rating', 0.00)
                        ->where('officeID', $users[0]->officeID)
                        ->whereBetween('created_at', [$spr_dt, $epr_dt])
                        ->sum('rating');
                        
                    $poc = Rating::whereNot('rating', 0.00)
                        ->where('officeID', $users[0]->officeID)
                        ->whereBetween('created_at', [$spr_dt, $epr_dt])
                        ->count();
                        
                    $pom = (5 * $poc);
                    $por = ((($pos == 0 && $poc == 0) ? 0 : ($pos / $pom)) * 100);
                    
                    $or_percent = ($cor - $por);

                    $cfs = FeedbackRating::join('feedback_responses', 'feedback_ratings.responseID', 'feedback_responses.id')
                        ->join('feedback_offices', 'feedback_responses.feedbackID', 'feedback_offices.feedbackID')
                        ->join('feedback', 'feedback_offices.feedbackID', 'feedback.id')
                        ->whereNot('feedback_ratings.rating', 0)
                        ->where('feedback_offices.officeID', $users[0]->officeID)
                        ->whereBetween('feedback.created_at', [$scr_dt, $ecr_dt])
                        ->sum('feedback_ratings.rating');
                        
                    $cfc = FeedbackRating::join('feedback_responses', 'feedback_ratings.responseID', 'feedback_responses.id')
                        ->join('feedback_offices', 'feedback_responses.feedbackID', 'feedback_offices.feedbackID')
                        ->join('feedback', 'feedback_offices.feedbackID', 'feedback.id')
                        ->whereNot('feedback_ratings.rating', 0)
                        ->where('feedback_offices.officeID', $users[0]->officeID)
                        ->whereBetween('feedback.created_at', [$scr_dt, $ecr_dt])
                        ->count();
                        
                    $cfm = (5 * $cfc);
                    $cfr = ((($cfs == 0 && $cfc == 0) ? 0 : ($cfs / $cfm)) * 100);
                    
                    $pfs = FeedbackRating::join('feedback_responses', 'feedback_ratings.responseID', 'feedback_responses.id')
                        ->join('feedback_offices', 'feedback_responses.feedbackID', 'feedback_offices.feedbackID')
                        ->join('feedback', 'feedback_offices.feedbackID', 'feedback.id')
                        ->whereNot('feedback_ratings.rating', 0)
                        ->where('feedback_offices.officeID', $users[0]->officeID)
                        ->whereBetween('feedback.created_at', [$spr_dt, $epr_dt])
                        ->sum('feedback_ratings.rating');
                        
                    $pfc = FeedbackRating::join('feedback_responses', 'feedback_ratings.responseID', 'feedback_responses.id')
                        ->join('feedback_offices', 'feedback_responses.feedbackID', 'feedback_offices.feedbackID')
                        ->join('feedback', 'feedback_offices.feedbackID', 'feedback.id')
                        ->whereNot('feedback_ratings.rating', 0)
                        ->where('feedback_offices.officeID', $users[0]->officeID)
                        ->whereBetween('feedback.created_at', [$spr_dt, $epr_dt])
                        ->count();
                        
                    $pfm = (5 * $pfc);
                    $pfr = ((($pfs == 0 && $pfc == 0) ? 0 : ($pfs / $pfm)) * 100);
                    
                    $fb_percent = ($cfr - $pfr);

                    $ks_phy = KioskRating::where('kiosk_ratings.officeID', $users[0]->officeID)
                        ->whereNot('kiosk_ratings.phyRating', 0)
                        ->whereBetween('kiosk_ratings.created_at', [$scr_dt, $ecr_dt])
                        ->sum('kiosk_ratings.phyRating');

                    $ks_ser = KioskRating::where('kiosk_ratings.officeID', $users[0]->officeID)
                        ->whereNot('kiosk_ratings.serRating', 0)
                        ->whereBetween('kiosk_ratings.created_at', [$scr_dt, $ecr_dt])
                        ->sum('kiosk_ratings.serRating');

                    $ks_per = KioskRating::where('kiosk_ratings.officeID', $users[0]->officeID)
                        ->whereNot('kiosk_ratings.perRating', 0)
                        ->whereBetween('kiosk_ratings.created_at', [$scr_dt, $ecr_dt])
                        ->sum('kiosk_ratings.perRating');

                    $ks_ovr = KioskRating::where('kiosk_ratings.officeID', $users[0]->officeID)
                        ->whereNot('kiosk_ratings.ovrRating', 0)
                        ->whereBetween('kiosk_ratings.created_at', [$scr_dt, $ecr_dt])
                        ->sum('kiosk_ratings.ovrRating');
                        
                    $kc_phy = KioskRating::where('kiosk_ratings.officeID', $users[0]->officeID)
                        ->whereNot('kiosk_ratings.phyRating', 0)
                        ->whereBetween('kiosk_ratings.created_at', [$scr_dt, $ecr_dt])
                        ->count();
                        
                    $kc_ser = KioskRating::where('kiosk_ratings.officeID', $users[0]->officeID)
                        ->whereNot('kiosk_ratings.serRating', 0)
                        ->whereBetween('kiosk_ratings.created_at', [$scr_dt, $ecr_dt])
                        ->count();

                    $kc_per = KioskRating::where('kiosk_ratings.officeID', $users[0]->officeID)
                        ->whereNot('kiosk_ratings.perRating', 0)
                        ->whereBetween('kiosk_ratings.created_at', [$scr_dt, $ecr_dt])
                        ->count();

                    $kc_ovr = KioskRating::where('kiosk_ratings.officeID', $users[0]->officeID)
                        ->whereNot('kiosk_ratings.ovrRating', 0)
                        ->whereBetween('kiosk_ratings.created_at', [$scr_dt, $ecr_dt])
                        ->count();
                        
                    $km_phy = (5 * $kc_phy);
                    $kr_phy = ((($ks_phy == 0 && $kc_phy == 0) ? 0 : ($ks_phy / $km_phy)) * 100);

                    $km_ser = (5 * $kc_ser);
                    $kr_ser = ((($ks_ser == 0 && $kc_phy == 0) ? 0 : ($ks_ser / $km_ser)) * 100);

                    $km_per = (5 * $kc_per);
                    $kr_per = ((($ks_per == 0 && $kc_per == 0) ? 0 : ($ks_per / $km_per)) * 100);

                    $km_ovr = (5 * $kc_ovr);
                    $kr_ovr = ((($ks_ovr == 0 && $kc_ovr == 0) ? 0 : ($ks_ovr / $km_ovr)) * 100);

                    $registrants = PreferenceMonth::where('isActive', TRUE)
                        ->get();

                        $regArr = [];

                        foreach ($registrants as $key => $value) {

                            $count = UserRole::where('roleID', 5)
                                ->whereMonth('created_at', $value->code)
                                ->whereYear('created_at', $users[0]->year)
                                ->count();

                                $array = [
                                    'label' => $value->label,
                                    'count' => $count
                                ];

                                array_push($regArr, $array);

                        }

                    $demographic = PreferenceSex::where('isActive', TRUE)
                        ->get();

                        $demArr = [];

                        foreach ($demographic as $key => $value) {

                            $count = UserClient::where('sexID', $value->id)
                                ->whereYear('created_at', $users[0]->year)
                                ->count();

                                $array = [
                                    'label' => $value->label,
                                    'count' => $count
                                ];

                                array_push($demArr, $array);

                        }

                    $month = PreferenceMonth::get();

                        $monthlyArr = [];

                        foreach ($month as $key => $value) {

                            $count = Feedback::join('feedback_offices', 'feedback.id', 'feedback_offices.feedbackID')
                                ->where('feedback_offices.officeID', $users[0]->officeID)
                                ->whereMonth('feedback.created_at', $value->code)
                                ->whereYear('feedback.created_at', $users[0]->year)
                                ->count();

                                $array = [
                                    'label' => $value->label,
                                    'count' => $count,
                                ];

                                array_push($monthlyArr, $array);

                        }

                    $office = PreferenceOffice::where('isActive', TRUE)
                        ->get();

                        $officeArr = [];

                        foreach ($office as $key => $value) {

                            $pending = Feedback::join('feedback_offices', 'feedback.id', 'feedback_offices.feedbackID')
                                ->where('feedback_offices.officeID', $users[0]->officeID)
                                ->where('feedback_offices.officeID', $value->id)
                                ->where('feedback.status', 1)
                                ->whereYear('feedback.created_at', $users[0]->year)
                                ->count();

                            $ongoing = Feedback::join('feedback_offices', 'feedback.id', 'feedback_offices.feedbackID')
                                ->where('feedback_offices.officeID', $users[0]->officeID)
                                ->where('feedback_offices.officeID', $value->id)
                                ->where('feedback.status', 2)
                                ->whereYear('feedback.created_at', $users[0]->year)
                                ->count();

                            $completed = Feedback::join('feedback_offices', 'feedback.id', 'feedback_offices.feedbackID')
                                ->where('feedback_offices.officeID', $users[0]->officeID)
                                ->where('feedback_offices.officeID', $value->id)
                                ->where('feedback.status', 3)
                                ->whereYear('feedback.created_at', $users[0]->year)
                                ->count();

                                $array = [
                                    'label' => $value->code,
                                    'pending' => $pending,
                                    'ongoing' => $ongoing,
                                    'completed' => $completed
                                ];

                                array_push($officeArr, $array);

                        }

                    $period = CarbonPeriod::create($scr_dt, $ecr_dt);

                        $perArr = [];

                        foreach ($period as $date) {

                            $d_from = $date->format('Y-m-d 00:00:00');
                            $d_to = $date->format('Y-m-d 23:59:59');

                            $count = Feedback::join('feedback_offices', 'feedback.id', 'feedback_offices.feedbackID')
                                ->where('feedback_offices.officeID', $users[0]->officeID)
                                ->whereBetween('feedback.created_at', [$d_from, $d_to])
                                ->count();

                                $array = [
                                    'date' => $date->format('m-d'),
                                    'count' => $count
                                ];

                                array_push($perArr, $array);
                        }

                    $registrant = UserRole::where('roleID', 5)
                        ->whereYear('created_at', $users[0]->year)
                        ->count();

                    $feedback = Feedback::join('feedback_offices', 'feedback.id', 'feedback_offices.feedbackID')
                        ->where('feedback_offices.officeID', $users[0]->officeID)
                        ->whereYear('feedback.created_at', $users[0]->year)
                        ->count();

                    $discussion = Discussion::join('setting_office_categories', 'discussions.categoryID', 'setting_office_categories.categoryID')
                        ->where('setting_office_categories.officeID', $users[0]->officeID)
                        ->whereYear('discussions.created_at', $users[0]->year)
                        ->count();

                        return response()->json(
                            [
                                'totalRegistrant' => $registrant,
                                'totalFeedback' => $feedback,
                                'totalDiscussion' => $discussion,

                                'or_current' => number_format($cor, 2),
                                'or_prev' => number_format($por, 2),
                                'or_percent' => number_format(abs($or_percent), 2),
        
                                'fb_current' => number_format($cfr, 2),
                                'fb_prev' => number_format($pfr, 2),
                                'fb_percent' => number_format(abs($fb_percent), 2),
        
                                'kr_phy' => number_format($kr_phy, 2),
                                'kr_ser' => number_format($kr_ser, 2),
                                'kr_per' => number_format($kr_per, 2),
                                'kr_ovr' => number_format($kr_ovr, 2),
        
                                'registrant' => $regArr,
                                'demographic' => $demArr,
                                'monthlyFeedback' => $monthlyArr,
                                'officeFeedback' => $officeArr,
                                'periodFeedback' => $perArr
                            ]
                        );
                }

        } catch (\Exception $e) {

            logger('Message logged from PreferenceController.getOverview', [$e->getMessage()]);
            return response()->json([
                'error' => 'Something went wrong getting record',
                'msg' => $e->getMessage()
            ], $e->getCode());

        }
    }
    /**
     * Display a listing of the resource.
     */
    public function getYear()
    {
        date_default_timezone_set('Asia/Manila');

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
        date_default_timezone_set('Asia/Manila');

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
        date_default_timezone_set('Asia/Manila');

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
        date_default_timezone_set('Asia/Manila');

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
        date_default_timezone_set('Asia/Manila');

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
        date_default_timezone_set('Asia/Manila');

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
     * Display a isting of the resource
     */
    public function getMessage()
    {
        date_default_timezone_set('Asia/Manila');

        try {

            return response()->json(
                PreferenceMessage::select('id AS value', 'content AS label')
                    ->where('isActive', TRUE)
                    ->get()
                );

        } catch (\Exception $e) {

            logger('Message logged from PreferenceController.getMessage', [$e->getMessage()]);
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
        date_default_timezone_set('Asia/Manila');

        try {
            return response()->json(
                PreferenceKiosk::select('preference_kiosks.id', 'preference_kiosks.name AS label', 'preference_kiosks.description', 'preference_kiosks.photo', 'preference_positions.label AS position', 'preference_offices.code AS office')
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

    /**
     * Display a listing of the resource.
     */
    public function getOfficeEndpoint()
    {
        date_default_timezone_set('Asia/Manila');

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
     * Display listing of resource
     */
    public function getPersonnelEndpoint($id)
    {
        date_default_timezone_set('Asia/Manila');

        try {
            return response()->json(
                PreferenceKiosk::select('preference_kiosks.id', 'preference_kiosks.name AS label', 'preference_kiosks.description', 'preference_kiosks.photo', 'preference_positions.label AS position', 'preference_offices.code AS office')
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
