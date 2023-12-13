<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use App\Models\UserClient;
use App\Models\UserRole;
use App\Models\Feedback;
use App\Models\Rating;
use App\Models\FeedbackRating;
use App\Models\FeedbackOffice;
use App\Models\KioskRating;
use App\Models\Discussion;
use App\Models\PreferenceSex;
use App\Models\PreferenceCategory;
use App\Models\PreferenceOffice;
use App\Models\PreferenceKiosk;

use PDF;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * 
     */
    public function getSummary(Request $request)
    {
        date_default_timezone_set('Asia/Manila');

        try {

            $start = Carbon::create($request->get('from').' 00:00:00');
            $end = Carbon::create($request->get('to').' 23:59:59');

            $k_start = Carbon::create($request->get('from'));
            $k_end = Carbon::create($request->get('to'));

            $registrant = UserClient::join('user_roles', 'user_clients.userID', 'user_roles.userID')
                ->where('user_roles.roleID', 5)
                ->where('user_clients.isVerified', TRUE)
                ->whereBetween('user_roles.created_at', [$start, $end])
                ->count();

            $demographics = PreferenceSex::where('isActive', TRUE)
                ->get();

                $demArr = [];

                foreach ($demographics as $key => $value) {

                    $count = UserClient::where('sexID', $value->id)
                        ->whereBetween('created_at', [$start, $end])
                        ->count();

                        $array = [
                            'label' => $value->label,
                            'count' => $count
                        ];

                        array_push($demArr, $array);

                }
            
            $t_feedback = Feedback::whereBetween('created_at', [$start, $end])
                ->count();

            $levelArr = [];

            for ($i=1; $i <= 3; $i++) { 
                
                $count = Feedback::where('status', $i)
                        ->whereBetween('created_at', [$start, $end])
                        ->count();

                        $array = [
                            'label' => ($i == 1 ? 'Pending' : ($i == 2 ? 'Ongoing' : ($i == 3 ? 'Completed' : null))),
                            'count' => $count
                        ];

                        array_push($levelArr, $array);

            }

            $ors = Rating::whereNot('rating', '0.00')
                ->whereBetween('created_at', [$start, $end])
                ->sum('rating');
            $orc = Rating::whereNot('rating', '0.00')
                ->whereBetween('created_at', [$start, $end])
                ->count();

            $om = (5 * $orc);
            $ovr = ((($ors == '0.00' && $orc == '0.00') ? 0 : ($ors / $om)) * 100);

            $frs = FeedbackRating::whereNot('rating', 0)
                ->whereBetween('created_at', [$start, $end])
                ->sum('rating');
            $frc = FeedbackRating::whereNot('rating', 0)
                ->whereBetween('created_at', [$start, $end])
                ->count();

            $fm = (5 * $frc);
            $fr = ((($frs == 0 && $frc == 0) ? 0 : ($frs / $fm)) * 100);

            $ks_phy = KioskRating::whereNot('phyRating', 0)
                ->whereBetween('date', [$k_start, $k_end])
                ->sum('phyRating');
            $ks_ser = KioskRating::whereNot('serRating', 0)
                ->whereBetween('date', [$k_start, $k_end])
                ->sum('serRating');
            $ks_per = KioskRating::whereNot('perRating', 0)
                ->whereBetween('date', [$k_start, $k_end])
                ->sum('perRating');
            $ks_ovr = KioskRating::whereNot('ovrRating', 0)
                ->whereBetween('date', [$k_start, $k_end])
                ->sum('ovrRating');

            $kc_phy = KioskRating::whereNot('phyRating', 0)
                ->whereBetween('date', [$k_start, $k_end])
                ->count();
            $kc_ser = KioskRating::whereNot('serRating', 0)
                ->whereBetween('date', [$k_start, $k_end])
                ->count();
            $kc_per = KioskRating::whereNot('perRating', 0)
                ->whereBetween('date', [$k_start, $k_end])
                ->count();
            $kc_ovr = KioskRating::whereNot('ovrRating', 0)
                ->whereBetween('date', [$k_start, $k_end])
                ->count();

            $km_phy = (5 * $kc_phy);
            $kr_phy = ((($ks_phy == 0 && $kc_phy == 0) ? 0 : ($ks_phy / $km_phy)) * 100);

            $km_ser = (5 * $kc_ser);
            $kr_ser = ((($ks_ser == 0 && $kc_phy == 0) ? 0 : ($ks_ser / $km_ser)) * 100);

            $km_per = (5 * $kc_per);
            $kr_per = ((($ks_per == 0 && $kc_per == 0) ? 0 : ($ks_per / $km_per)) * 100);

            $km_ovr = (5 * $kc_ovr);
            $kr_ovr = ((($ks_ovr == 0 && $kc_ovr == 0) ? 0 : ($ks_ovr / $km_ovr)) * 100);

            $t_discussion = Discussion::whereBetween('created_at', [$start, $end])
                ->count();
            $a_discussion = Discussion::where('isActive', TRUE)
                ->whereBetween('created_at', [$start, $end])
                ->count();
            $i_discussion = Discussion::where('isActive', FALSE)
                ->whereBetween('created_at', [$start, $end])
                ->count();

            $account = User::where('id', auth()->user()->id)
                ->get();

            $dtf = Carbon::create($request->get('from').' 08:00:00');
            $dtt = Carbon::create($request->get('to').' 17:00:00');
            $from = $dtf->toDayDateTimeString(); 
            $to = $dtt->toDayDateTimeString();

            $today = Carbon::now(+8);
            $now = $today->toDayDateTimeString(); 

            $pdf = PDF::setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true, 'debugPng' => true])->loadView('report.SummaryReport', [
                'date' => $from.' to '.$to,
                'totalRegistrant' => $registrant,
                'demArr' => $demArr,
                'totalFeedback' => $t_feedback,
                'levelArr' => $levelArr,
                'or' => number_format($ovr, 2),
                'fr' => number_format($fr, 2),
                'kr_phy' => number_format($kr_phy, 2),
                'kr_ser' => number_format($kr_ser, 2),
                'kr_per' => number_format($kr_per, 2),
                'kr_ovr' => number_format($kr_ovr, 2),
                'totalDiscussion' => $t_discussion,
                'activeDiscussion' => $a_discussion,
                'inactiveDiscussion' => $i_discussion,
                'name' => $account[0]->name,
                'now' => $now
            ])->setPaper('a4', 'portrait');

            return $pdf->stream();

        } catch (\Exception $e) {

            logger('Message logged from ReportController.getSummary', [$e->getMessage()]);
            return response()->json([
                'error' => 'Something went wrong getting summary report!',
                'data' => $e->getMessage()
            ], 400);

        }
    }

    /**
     * 
     */
    public function getPerformance(Request $request)
    {
        date_default_timezone_set('Asia/Manila');

        try {

            $start = Carbon::create($request->get('from').' 00:00:00');
            $end = Carbon::create($request->get('to').' 23:59:59');

            $k_start = Carbon::create($request->get('from'));
            $k_end = Carbon::create($request->get('to'));

            $t_feedback = Feedback::whereBetween('created_at', [$start, $end])
                ->count();

            $levelArr = [];

            for ($i=1; $i <= 3; $i++) { 
                
                $count = Feedback::where('status', $i)
                        ->whereBetween('created_at', [$start, $end])
                        ->count();

                        $array = [
                            'label' => ($i == 1 ? 'Pending' : ($i == 2 ? 'Ongoing' : ($i == 3 ? 'Completed' : null))),
                            'count' => $count
                        ];

                        array_push($levelArr, $array);

            }

            $ors = Rating::whereNot('rating', '0.00')
                ->whereBetween('created_at', [$start, $end])
                ->sum('rating');
                
            $orc = Rating::whereNot('rating', '0.00')
                ->whereBetween('created_at', [$start, $end])
                ->count();
                
            $om = (5 * $orc);
            $ovr = ((($ors == '0.00' && $orc == '0.00') ? 0 : ($ors / $om)) * 100);

            $frs = FeedbackRating::whereNot('rating', 0)
                ->whereBetween('created_at', [$start, $end])
                ->sum('rating');

            $frc = FeedbackRating::whereNot('rating', 0)
                ->whereBetween('created_at', [$start, $end])
                ->count();

            $fm = (5 * $frc);
            $fr = ((($frs == 0 && $frc == 0) ? 0 : ($frs / $fm)) * 100);

            $ks_phy = KioskRating::whereNot('phyRating', 0)
                ->whereBetween('date', [$k_start, $k_end])
                ->sum('phyRating');
            $ks_ser = KioskRating::whereNot('serRating', 0)
                ->whereBetween('date', [$k_start, $k_end])
                ->sum('serRating');
            $ks_per = KioskRating::whereNot('perRating', 0)
                ->whereBetween('date', [$k_start, $k_end])
                ->sum('perRating');
            $ks_ovr = KioskRating::whereNot('ovrRating', 0)
                ->whereBetween('date', [$k_start, $k_end])
                ->sum('ovrRating');

            $kc_phy = KioskRating::whereNot('phyRating', 0)
                ->whereBetween('date', [$k_start, $k_end])
                ->count();
            $kc_ser = KioskRating::whereNot('serRating', 0)
                ->whereBetween('date', [$k_start, $k_end])
                ->count();
            $kc_per = KioskRating::whereNot('perRating', 0)
                ->whereBetween('date', [$k_start, $k_end])
                ->count();
            $kc_ovr = KioskRating::whereNot('ovrRating', 0)
                ->whereBetween('date', [$k_start, $k_end])
                ->count();
                
            $km_phy = (5 * $kc_phy);
            $kr_phy = ((($ks_phy == 0 && $kc_phy == 0) ? 0 : ($ks_phy / $km_phy)) * 100);

            $km_ser = (5 * $kc_ser);
            $kr_ser = ((($ks_ser == 0 && $kc_phy == 0) ? 0 : ($ks_ser / $km_ser)) * 100);

            $km_per = (5 * $kc_per);
            $kr_per = ((($ks_per == 0 && $kc_per == 0) ? 0 : ($ks_per / $km_per)) * 100);

            $km_ovr = (5 * $kc_ovr);
            $kr_ovr = ((($ks_ovr == 0 && $kc_ovr == 0) ? 0 : ($ks_ovr / $km_ovr)) * 100);

            $category = PreferenceCategory::where('isActive', TRUE)
                ->get();

                $catArr = [];

                foreach ($category as $key => $value) {

                    $total = Feedback::where('categoryID', $value->id)
                        ->whereBetween('created_at', [$start, $end])
                        ->count();

                    $pending = Feedback::where('categoryID', $value->id)
                        ->where('status', 1)
                        ->whereBetween('created_at', [$start, $end])
                        ->count();

                    $ongoing = Feedback::where('categoryID', $value->id)
                        ->where('status', 2)
                        ->whereBetween('created_at', [$start, $end])
                        ->count();

                    $completed = Feedback::where('categoryID', $value->id)
                        ->where('status', 3)
                        ->whereBetween('created_at', [$start, $end])
                        ->count();

                    $delayed = FeedbackOffice::join('feedback', 'feedback_offices.feedbackID', 'feedback.id')
                        ->where('feedback.categoryID', $value->id)
                        ->where('isDelayed', TRUE)
                        ->count();

                        $array = [
                            'label'=> $value->label,
                            'tf' => $total,
                            'pf' => $pending,
                            'of' => $ongoing,
                            'cf' => $completed,
                            'df' => $delayed
                        ];

                        array_push($catArr, $array);

                }
            
            $office = PreferenceOffice::where('isActive', TRUE)
                ->get();

                $offArr = [];
                
                foreach ($office as $key => $value) {

                    $oors = Rating::whereNot('rating', '0.00')
                        ->where('officeID', $value->id)
                        ->whereBetween('created_at', [$start, $end])
                        ->sum('rating');

                    $oorc = Rating::whereNot('rating', '0.00')
                        ->where('officeID', $value->id)
                        ->whereBetween('created_at', [$start, $end])
                        ->count();

                    $oom = (5 * $oorc);
                    $oovr = ((($oors == 0 && $oorc == 0) ? 0 : ($oors / $oom)) * 100);

                        $array = [
                            'label' => $value->label,
                            'rating' => number_format($oovr, 2),
                        ];

                        array_push($offArr, $array);

                }

            $account = User::where('id', auth()->user()->id)
                ->get();

            $dtf = Carbon::create($request->get('from').' 08:00:00');
            $dtt = Carbon::create($request->get('to').' 17:00:00');
            $from = $dtf->toDayDateTimeString(); 
            $to = $dtt->toDayDateTimeString();

            $today = Carbon::now(+8);
            $now = $today->toDayDateTimeString(); 
            
            $pdf = PDF::setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true, 'debugPng' => true])->loadView('report.PerformanceReport', [
                'date' => $from.' to '.$to,
                'totalFeedback' => $t_feedback,
                'levelArr' => $levelArr,
                'or' => number_format($ovr, 2),
                'fr' => number_format($fr, 2),
                'kr_phy' => number_format($kr_phy, 2),
                'kr_ser' => number_format($kr_ser, 2),
                'kr_per' => number_format($kr_per, 2),
                'kr_ovr' => number_format($kr_ovr, 2),
                'catArr' => $catArr,
                'offArr' => $offArr,
                'name' => $account[0]->name,
                'now' => $now
            ])->setPaper('a4', 'portrait');

            return $pdf->stream();

        } catch (\Exception $e) {

            logger('Message logged from ReportController.getPerformance', [$e->getMessage()]);
            return response()->json([
                'error' => 'Something went wrong getting performance report!',
                'data' => $e->getMessage()
            ], 400);

        }
    }

    /**
     * 
     */
    public function getFeedbackSummary(Request $request)
    {
        date_default_timezone_set('Asia/Manila');

        try {

            $start = Carbon::create($request->get('from').' 00:00:00');
            $end = Carbon::create($request->get('to').' 23:59:59');

            $k_start = Carbon::create($request->get('from'));
            $k_end = Carbon::create($request->get('to'));

            $t_feedback = Feedback::whereBetween('created_at', [$start, $end])
                ->count();

            $levelArr = [];

            for ($i=1; $i <= 3; $i++) { 
                
                $count = Feedback::where('status', $i)
                        ->whereBetween('created_at', [$start, $end])
                        ->count();

                        $array = [
                            'label' => ($i == 1 ? 'Pending' : ($i == 2 ? 'Ongoing' : ($i == 3 ? 'Completed' : null))),
                            'count' => $count
                        ];

                        array_push($levelArr, $array);

            }

            $ors = Rating::whereNot('rating', '0.00')
                ->whereBetween('created_at', [$start, $end])
                ->sum('rating');

            $orc = Rating::whereNot('rating', '0.00')
                ->whereBetween('created_at', [$start, $end])
                ->count();

            $om = (5 * $orc);
            $ovr = ((($ors == '0.00' && $orc == '0.00') ? 0 : ($ors / $om)) * 100);

            $frs = FeedbackRating::whereNot('rating', 0)
                ->whereBetween('created_at', [$start, $end])
                ->sum('rating');

            $frc = FeedbackRating::whereNot('rating', 0)
                ->whereBetween('created_at', [$start, $end])
                ->count();

            $fm = (5 * $frc);
            $fr = ((($frs == 0 && $frc == 0) ? 0 : ($frs / $fm)) * 100);

            $ks_phy = KioskRating::whereNot('phyRating', 0)
                ->whereBetween('date', [$k_start, $k_end])
                ->sum('phyRating');
            $ks_ser = KioskRating::whereNot('serRating', 0)
                ->whereBetween('date', [$k_start, $k_end])
                ->sum('serRating');
            $ks_per = KioskRating::whereNot('perRating', 0)
                ->whereBetween('date', [$k_start, $k_end])
                ->sum('perRating');
            $ks_ovr = KioskRating::whereNot('ovrRating', 0)
                ->whereBetween('date', [$k_start, $k_end])
                ->sum('ovrRating');

            $kc_phy = KioskRating::whereNot('phyRating', 0)
                ->whereBetween('date', [$k_start, $k_end])
                ->count();
            $kc_ser = KioskRating::whereNot('serRating', 0)
                ->whereBetween('date', [$k_start, $k_end])
                ->count();
            $kc_per = KioskRating::whereNot('perRating', 0)
                ->whereBetween('date', [$k_start, $k_end])
                ->count();
            $kc_ovr = KioskRating::whereNot('ovrRating', 0)
                ->whereBetween('date', [$k_start, $k_end])
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

            $dtf = Carbon::create($request->get('from').' 08:00:00');
            $dtt = Carbon::create($request->get('to').' 17:00:00');
            $from = $dtf->toDayDateTimeString(); 
            $to = $dtt->toDayDateTimeString(); 

            $today = Carbon::now(+8);
            $now = $today->toDayDateTimeString(); 
            
            $pdf = PDF::setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true, 'debugPng' => true])->loadView('report.FeedbackSummaryReport', [
                'date' => $from.' to '.$to,
                'totalFeedback' => $t_feedback,
                'levelArr' => $levelArr,
                'or' => number_format($ovr, 2),
                'fr' => number_format($fr, 2),
                'kr_phy' => number_format($kr_phy, 2),
                'kr_ser' => number_format($kr_ser, 2),
                'kr_per' => number_format($kr_per, 2),
                'kr_ovr' => number_format($kr_ovr, 2),
                'name' => $account[0]->name,
                'now' => $now
            ])->setPaper('a4', 'portrait');

            return $pdf->stream();

        } catch (\Exception $e) {

            logger('Message logged from ReportController.getFeedbackSummary', [$e->getMessage()]);
            return response()->json([
                'error' => 'Something went wrong getting feedback summary report!',
                'data' => $e->getMessage()
            ], 400);

        }
    }

    /**
     * 
     */
    public function getFeedbackCategory(Request $request)
    {
        date_default_timezone_set('Asia/Manila');

        try {

            $start = Carbon::create($request->get('from').' 00:00:00');
            $end = Carbon::create($request->get('to').' 23:59:59');

            $category = PreferenceCategory::where('isActive', TRUE)
                ->get();

                $arr = [];

                foreach ($category as $key => $value) {

                    $total = Feedback::where('categoryID', $value->id)
                        ->whereBetween('created_at', [$start, $end])
                        ->count();

                    $pending = Feedback::where('categoryID', $value->id)
                        ->where('status', 1)
                        ->whereBetween('created_at', [$start, $end])
                        ->count();

                    $ongoing = Feedback::where('categoryID', $value->id)
                        ->where('status', 2)
                        ->whereBetween('created_at', [$start, $end])
                        ->count();

                    $completed = Feedback::where('categoryID', $value->id)
                        ->where('status', 3)
                        ->whereBetween('created_at', [$request->get('from'), $request->get('to')])
                        ->count();

                        $array = [
                            'label' => $value->label,
                            'total' => $total,
                            'pending' => $pending,
                            'ongoing' => $ongoing,
                            'completed' => $completed
                        ];

                        array_push($arr, $array);
                }

            $account = User::where('id', auth()->user()->id)
                ->get();

            $dtf = Carbon::create($request->get('from').' 08:00:00');
            $dtt = Carbon::create($request->get('to').' 17:00:00');
            $from = $dtf->toDayDateTimeString(); 
            $to = $dtt->toDayDateTimeString(); 

            $today = Carbon::now(+8);
            $now = $today->toDayDateTimeString(); 
            
            $pdf = PDF::setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true, 'debugPng' => true])->loadView('report.FeedbackCategoryReport', [
                'date' => $from.' to '.$to,
                'data' => $arr,
                'name' => $account[0]->name,
                'now' => $now
            ])->setPaper('a4', 'portrait');
            return $pdf->stream();

        } catch (\Exception $e) {

            logger('Message logged from ReportController.getFeedbackCategory', [$e->getMessage()]);
            return response()->json([
                'error' => 'Something went wrong getting feedback category report!',
                'data' => $e->getMessage()
            ], 400);

        }
    }

    /**
     * 
     */
    public function getFeedbackStatus(Request $request)
    {
        date_default_timezone_set('Asia/Manila');

        try {

            $start = Carbon::create($request->get('from').' 00:00:00');
            $end = Carbon::create($request->get('to').' 23:59:59');

            $levelArr = [];

            for ($i=1; $i <= 3; $i++) { 
                
                $count = Feedback::where('status', $i)
                    ->whereBetween('created_at', [$start, $end])
                    ->count();

                    $array = [
                        'label' => ($i == 1 ? 'Pending' : ($i == 2 ? 'Ongoing' : ($i == 3 ? 'Completed' : null))),
                        'count' => $count
                    ];

                    array_push($levelArr, $array);

            }

            $cancel = Feedback::where('isActive', FALSE)
                ->whereBetween('created_at', [$start, $end])
                ->count();

            $delay = FeedbackOffice::where('isDelayed', TRUE)
                ->whereBetween('created_at', [$start, $end])
                ->count();

            $account = User::where('id', auth()->user()->id)
                ->get();

            $dtf = Carbon::create($request->get('from').' 08:00:00');
            $dtt = Carbon::create($request->get('to').' 17:00:00');
            $from = $dtf->toDayDateTimeString(); 
            $to = $dtt->toDayDateTimeString(); 

            $today = Carbon::now(+8);
            $now = $today->toDayDateTimeString(); 
            
            $pdf = PDF::setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true, 'debugPng' => true])->loadView('report.FeedbackStatusReport', [
                'date' => $from.' to '.$to,
                'data' => $levelArr,
                'cancel' => $cancel,
                'delay' => $delay,
                'name' => $account[0]->name,
                'now' => $now
            ])->setPaper('a4', 'portrait');

            return $pdf->stream();


        } catch (\Exception $e) {

            logger('Message logged from ReportController.getFeedbackStatus', [$e->getMessage()]);
            return response()->json([
                'error' => 'Something went wrong getting feedback status report!',
                'data' => $e->getMessage()
            ], 400);

        }
    }

    /**
     * 
     */
    public function getFeedbackOffice(Request $request)
    {
        date_default_timezone_set('Asia/Manila');

        try {

            $start = Carbon::create($request->get('from').' 00:00:00');
            $end = Carbon::create($request->get('to').' 23:59:59');

            $k_start = Carbon::create($request->get('from'));
            $k_end = Carbon::create($request->get('to'));

            $office = PreferenceOffice::where('isActive', TRUE)
                ->get();

                $arr = [];

                foreach ($office as $key => $value) {

                    $count = FeedbackOffice::where('officeID', $value->id)
                        ->whereBetween('created_at', [$start, $end])
                        ->count();
                        
                    $ors = Rating::whereNot('rating', '0.00')
                        ->where('officeID', $value->id)
                        ->whereBetween('created_at', [$start, $end])
                        ->sum('rating');
                        
                    $orc = Rating::whereNot('rating', '0.00')
                        ->where('officeID', $value->id)
                        ->whereBetween('created_at', [$start, $end])
                        ->count();
                        
                    $om = (5 * $orc);
                    $ovr = ((($ors == '0.00' && $orc == '0.00') ? 0 : ($ors / $om)) * 100);
                    
                    $frs = FeedbackRating::join('feedback_responses', 'feedback_ratings.responseID', 'feedback_responses.id')
                        ->join('feedback', 'feedback_responses.feedbackID', 'feedback.id')
                        ->join('feedback_offices', 'feedback.id', 'feedback_offices.feedbackID')
                        ->where('feedback_offices.officeID', $value->id)
                        ->whereNot('feedback_ratings.rating', 0)
                        ->whereBetween('feedback.created_at', [$start, $end])
                        ->sum('feedback_ratings.rating');
                        
                    $frc = FeedbackRating::join('feedback_responses', 'feedback_ratings.responseID', 'feedback_responses.id')
                        ->join('feedback', 'feedback_responses.feedbackID', 'feedback.id')
                        ->join('feedback_offices', 'feedback.id', 'feedback_offices.feedbackID')
                        ->where('feedback_offices.officeID', $value->id)
                        ->whereNot('feedback_ratings.rating', 0)
                        ->whereBetween('feedback.created_at', [$start, $end])
                        ->whereBetween('feedback_offices.created_at', [$start, $end])
                        ->count();
                        
                    $fm = (5 * $frc);
                    $fr = ((($frs == 0 && $frc == 0) ? 0 : ($frs / $fm)) * 100);
                    
                    $ks_phy = KioskRating::where('kiosk_ratings.officeID', $value->id)
                        ->whereNot('kiosk_ratings.phyRating', 0)
                        ->whereBetween('kiosk_ratings.date', [$k_start, $k_end])
                        ->sum('kiosk_ratings.phyRating');
                    $ks_ser = KioskRating::where('kiosk_ratings.officeID', $value->id)
                        ->whereNot('kiosk_ratings.serRating', 0)
                        ->whereBetween('kiosk_ratings.date', [$k_start, $k_end])
                        ->sum('kiosk_ratings.serRating');
                    $ks_per = KioskRating::where('kiosk_ratings.officeID', $value->id)
                        ->whereNot('kiosk_ratings.perRating', 0)
                        ->whereBetween('kiosk_ratings.date', [$k_start, $k_end])
                        ->sum('kiosk_ratings.perRating');
                    $ks_ovr = KioskRating::where('kiosk_ratings.officeID', $value->id)
                        ->whereNot('kiosk_ratings.ovrRating', 0)
                        ->whereBetween('kiosk_ratings.date', [$k_start, $k_end])
                        ->sum('kiosk_ratings.ovrRating');
                        
                    $kc_phy = KioskRating::where('kiosk_ratings.officeID', $value->id)
                        ->whereNot('kiosk_ratings.phyRating', 0)
                        ->whereBetween('kiosk_ratings.date', [$k_start, $k_end])
                        ->count();
                    $kc_ser = KioskRating::where('kiosk_ratings.officeID', $value->id)
                        ->whereNot('kiosk_ratings.serRating', 0)
                        ->whereBetween('kiosk_ratings.date', [$k_start, $k_end])
                        ->count();
                    $kc_per = KioskRating::where('kiosk_ratings.officeID', $value->id)
                        ->whereNot('kiosk_ratings.perRating', 0)
                        ->whereBetween('kiosk_ratings.date', [$k_start, $k_end])
                        ->count();
                    $kc_ovr = KioskRating::where('kiosk_ratings.officeID', $value->id)
                        ->whereNot('kiosk_ratings.ovrRating', 0)
                        ->whereBetween('kiosk_ratings.date', [$k_start, $k_end])
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
                        'label' => $value->label,
                        'count' => $count,
                        'ovr' => number_format($ovr, 2),
                        'fr' => number_format($fr, 2),
                        'kr_phy' => number_format($kr_phy, 2),
                        'kr_ser' => number_format($kr_ser, 2),
                        'kr_per' => number_format($kr_per, 2),
                        'kr_ovr' => number_format($kr_ovr, 2),
                    ];
                    
                    array_push($arr, $array);
                }

            $account = User::where('id', auth()->user()->id)
                ->get();

            $dtf = Carbon::create($request->get('from').' 08:00:00');
            $dtt = Carbon::create($request->get('to').' 17:00:00');
            $from = $dtf->toDayDateTimeString(); 
            $to = $dtt->toDayDateTimeString(); 

            $today = Carbon::now(+8);
            $now = $today->toDayDateTimeString(); 
            
            $pdf = PDF::setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true, 'debugPng' => true])->loadView('report.FeedbackOfficeReport', [
                'date' => $from.' to '.$to,
                'data' => $arr,
                'name' => $account[0]->name,
                'now' => $now
            ])->setPaper('a4', 'landscape');

            return $pdf->stream();

        } catch (\Exception $e) {

            logger('Message logged from ReportController.getFeedbackOffice', [$e->getMessage()]);
            return response()->json([
                'error' => 'Something went wrong getting feedback office report!',
                'data' => $e->getMessage()
            ], 400);

        }
    }

    /**
     * 
     */
    public function getFeedbackKiosk(Request $request)
    {
        date_default_timezone_set('Asia/Manila');

        try {

            $start = Carbon::create($request->get('from').' 00:00:00');
            $end = Carbon::create($request->get('to').' 23:59:59');

            $k_start = Carbon::create($request->get('from'));
            $k_end = Carbon::create($request->get('to'));

            $kiosk = PreferenceKiosk::select('preference_kiosks.*', 'preference_offices.code AS office', 'preference_positions.label AS position')
                ->join('preference_offices', 'preference_kiosks.officeID', 'preference_offices.id')
                ->join('preference_positions', 'preference_kiosks.positionID', 'preference_positions.id')
                ->where('preference_kiosks.isActive', TRUE)
                ->get();

                $arr = [];

                foreach ($kiosk as $key => $value) {

                    $ks_phy = KioskRating::where('kioskID', $value->id)
                        ->whereNot('phyRating', 0)
                        ->whereBetween('date', [$k_start, $k_end])
                        ->sum('phyRating');
                    $ks_ser = KioskRating::where('kioskID', $value->id)
                        ->whereNot('serRating', 0)
                        ->whereBetween('date', [$k_start, $k_end])
                        ->sum('serRating');
                    $ks_per = KioskRating::where('kioskID', $value->id)
                        ->whereNot('perRating', 0)
                        ->whereBetween('date', [$k_start, $k_end])
                        ->sum('perRating');
                    $ks_ovr = KioskRating::where('kioskID', $value->id)
                        ->whereNot('ovrRating', 0)
                        ->whereBetween('date', [$k_start, $k_end])
                        ->sum('ovrRating');

                    $kc_phy = KioskRating::where('kioskID', $value->id)
                        ->whereNot('phyRating', 0)
                        ->whereBetween('date', [$k_start, $k_end])
                        ->count();
                    $kc_ser = KioskRating::where('kioskID', $value->id)
                        ->whereNot('serRating', 0)
                        ->whereBetween('date', [$k_start, $k_end])
                        ->count();
                    $kc_per = KioskRating::where('kioskID', $value->id)
                        ->whereNot('perRating', 0)
                        ->whereBetween('date', [$k_start, $k_end])
                        ->count();
                    $kc_ovr = KioskRating::where('kioskID', $value->id)
                        ->whereNot('ovrRating', 0)
                        ->whereBetween('date', [$k_start, $k_end])
                        ->count();
                    $suggestions = KioskRating::where('kioskID', $value->id)
                        ->whereBetween('created_at', [$start, $end])
                        ->get();

                    $km_phy = (5 * $kc_phy);
                    $kr_phy = ((($ks_phy == 0 && $kc_phy == 0) ? 0 : ($ks_phy / $km_phy)) * 100);

                    $km_ser = (5 * $kc_ser);
                    $kr_ser = ((($ks_ser == 0 && $kc_phy == 0) ? 0 : ($ks_ser / $km_ser)) * 100);

                    $km_per = (5 * $kc_per);
                    $kr_per = ((($ks_per == 0 && $kc_per == 0) ? 0 : ($ks_per / $km_per)) * 100);

                    $km_ovr = (5 * $kc_ovr);
                    $kr_ovr = ((($ks_ovr == 0 && $kc_ovr == 0) ? 0 : ($ks_ovr / $km_ovr)) * 100);

                    $array = [
                        'name' => $value->name,
                        'office' => $value->office,
                        'position' => $value->position,
                        'kr_phy' => number_format($kr_phy, 2),
                        'kr_ser' => number_format($kr_ser, 2),
                        'kr_per' => number_format($kr_per, 2),
                        'kr_ovr' => number_format($kr_ovr, 2),
                        'suggestions' => $suggestions
                    ];
                    array_push($arr, $array);
                }

            $account = User::where('id', auth()->user()->id)
                ->get();

            $dtf = Carbon::create($request->get('from').' 08:00:00');
            $dtt = Carbon::create($request->get('to').' 17:00:00');
            $from = $dtf->toDayDateTimeString(); 
            $to = $dtt->toDayDateTimeString(); 

            $today = Carbon::now(+8);
            $now = $today->toDayDateTimeString(); 
            
            $pdf = PDF::setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true, 'debugPng' => true])->loadView('report.FeedbackKioskReport', [
                'date' => $from.' to '.$to,
                'data' => $arr,
                'name' => $account[0]->name,
                'now' => $now
            ])->setPaper('a4', 'landscape');

            return $pdf->stream();

        } catch (\Exception $e) {

            logger('Message logged from ReportController.getFeedbackKiosk', [$e->getMessage()]);
            return response()->json([
                'error' => 'Something went wrong getting feedback kiosk report!',
                'data' => $e->getMessage()
            ], 400);

        }
    }

    /**
     * 
     */
    public function getFeedbackKioskOffice(Request $request)
    {
        date_default_timezone_set('Asia/Manila');

        try {

            $start = Carbon::create($request->get('from').' 00:00:00');
            $end = Carbon::create($request->get('to').' 23:59:59');

            $k_start = Carbon::create($request->get('from'));
            $k_end = Carbon::create($request->get('to'));

            $offices = PreferenceOffice::get();

            $arr = [];

            foreach ($offices as $key => $o_value) {
                
                $ks_phy = KioskRating::where('kiosk_ratings.officeID', $o_value->id)
                    ->whereNot('kiosk_ratings.phyRating', 0)
                    ->whereBetween('kiosk_ratings.date', [$k_start, $k_end])
                    ->sum('kiosk_ratings.phyRating');
                $ks_ser = KioskRating::where('kiosk_ratings.officeID', $o_value->id)
                    ->whereNot('kiosk_ratings.serRating', 0)
                    ->whereBetween('kiosk_ratings.date', [$k_start, $k_end])
                    ->sum('kiosk_ratings.serRating');
                $ks_per = KioskRating::where('kiosk_ratings.officeID', $o_value->id)
                    ->whereNot('kiosk_ratings.perRating', 0)
                    ->whereBetween('kiosk_ratings.date', [$k_start, $k_end])
                    ->sum('kiosk_ratings.perRating');
                $ks_ovr = KioskRating::where('kiosk_ratings.officeID', $o_value->id)
                    ->whereNot('kiosk_ratings.ovrRating', 0)
                    ->whereBetween('kiosk_ratings.date', [$k_start, $k_end])
                    ->sum('kiosk_ratings.ovrRating');

                $kc_phy = KioskRating::where('kiosk_ratings.officeID', $o_value->id)
                    ->whereNot('kiosk_ratings.phyRating', 0)
                    ->whereBetween('kiosk_ratings.date', [$k_start, $k_end])
                    ->count();
                $kc_ser = KioskRating::where('kiosk_ratings.officeID', $o_value->id)
                    ->whereNot('kiosk_ratings.serRating', 0)
                    ->whereBetween('kiosk_ratings.date', [$k_start, $k_end])
                    ->count();
                $kc_per = KioskRating::where('kiosk_ratings.officeID', $o_value->id)
                    ->whereNot('kiosk_ratings.perRating', 0)
                    ->whereBetween('kiosk_ratings.date', [$k_start, $k_end])
                    ->count();
                $kc_ovr = KioskRating::where('kiosk_ratings.officeID', $o_value->id)
                    ->whereNot('kiosk_ratings.ovrRating', 0)
                    ->whereBetween('kiosk_ratings.date', [$k_start, $k_end])
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
                    'office' => $o_value->label,
                    'phyRating' => number_format($kr_phy, 2),
                    'serRating' => number_format($kr_ser, 2),
                    'perRating' => number_format($kr_per, 2),
                    'ovrRating' => number_format($kr_ovr, 2),
                ];

                array_push($arr, $array);

            }

            $account = User::where('id', auth()->user()->id)
                ->get();

            $dtf = Carbon::create($request->get('from').' 08:00:00');
            $dtt = Carbon::create($request->get('to').' 17:00:00');
            $from = $dtf->toDayDateTimeString(); 
            $to = $dtt->toDayDateTimeString(); 

            $today = Carbon::now(+8);
            $now = $today->toDayDateTimeString(); 

            $pdf = PDF::setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true, 'debugPng' => true])->loadView('report.FeedbackKioskOfficeReport', [
                'date' => $from.' to '.$to,
                'data' => $arr,
                'name' => $account[0]->name,
                'now' => $now
            ])->setPaper('a4', 'landscape');

            return $pdf->stream();

        } catch (\Exception $e) {

            logger('Message logged from ReportController.getFeedbackKioskOffice', [$e->getMessage()]);
            return response()->json([
                'error' => 'Something went wrong getting feedback kiosk office report!',
                'data' => $e->getMessage()
            ], 400);

        }
    }

    /**
     * 
     */
    public function getDiscussionSummary(Request $request)
    {
        date_default_timezone_set('Asia/Manila');
        
        try {

            $start = Carbon::create($request->get('from').' 00:00:00');
            $end = Carbon::create($request->get('to').' 23:59:59');

            $t_discussion = Discussion::whereBetween('created_at', [$start, $end])
                ->count();

            $a_discussion = Discussion::where('isActive', TRUE)
                ->whereBetween('created_at', [$start, $end])
                ->count();

            $i_discussion = Discussion::where('isActive', FALSE)
                ->whereBetween('created_at', [$start, $end])
                ->count();

            $category = PreferenceCategory::where('isActive', TRUE)
                ->get();

                $catArr = [];

                foreach ($category as $key => $value) {

                    $count = Discussion::where('categoryID', $value->id)
                        ->whereBetween('created_at', [$start, $end])
                        ->count();

                        $array = [
                            'label' => $value->label,
                            'count' => $count
                        ];

                        array_push($catArr, $array);
                }

            $account = User::where('id', auth()->user()->id)
                ->get();

            $dtf = Carbon::create($request->get('from').' 08:00:00');
            $dtt = Carbon::create($request->get('to').' 17:00:00');
            $from = $dtf->toDayDateTimeString(); 
            $to = $dtt->toDayDateTimeString(); 

            $today = Carbon::now(+8);
            $now = $today->toDayDateTimeString(); 
            
            $pdf = PDF::setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true, 'debugPng' => true])->loadView('report.DiscussionSummaryReport', [
                'date' => $from.' to '.$to,
                'totalDiscussion' => $t_discussion,
                'totalActive' => $a_discussion,
                'totalInactive' => $i_discussion,
                'data' => $catArr,
                'name' => $account[0]->name,
                'now' => $now
            ])->setPaper('a4', 'portrait');

            return $pdf->stream();

        } catch (\Exception $e) {

            logger('Message logged from ReportController.getDiscussionSummary', [$e->getMessage()]);
            return response()->json([
                'error' => 'Something went wrong getting discussion summary report!',
                'data' => $e->getMessage()
            ], 400);

        } 
    }
}
