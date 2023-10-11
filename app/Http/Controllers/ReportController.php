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
        try {

            $start = Carbon::create($request->get('from').' 00:00:00');
            $end = Carbon::create($request->get('to').' 23:59:59');

            $registrant = UserRole::where('roleID', 5)
                ->whereBetween('created_at', [$start, $end])
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

            $ors = Rating::whereNot('rating', 0)
                ->whereBetween('created_at', [$start, $end])
                ->sum('rating');
            $orc = Rating::whereNot('rating', 0)
                ->whereBetween('created_at', [$start, $end])
                ->count();

            $om = (3 * $orc);
            $ovr = ((($ors == 0 && $orc == 0) ? 0 : ($ors / $om)) * 100);

            $frs = FeedbackRating::whereNot('rating', 0)
                ->whereBetween('created_at', [$start, $end])
                ->sum('rating');
            $frc = FeedbackRating::whereNot('rating', 0)
                ->whereBetween('created_at', [$start, $end])
                ->count();

            $fm = (3 * $frc);
            $fr = ((($frs == 0 && $frc == 0) ? 0 : ($frs / $fm)) * 100);

            $ks_phy = KioskRating::whereNot('phyRating', 0)
                ->whereBetween('created_at', [$start, $end])
                ->sum('phyRating');
            $ks_ser = KioskRating::whereNot('serRating', 0)
                ->whereBetween('created_at', [$start, $end])
                ->sum('serRating');
            $ks_per = KioskRating::whereNot('perRating', 0)
                ->whereBetween('created_at', [$start, $end])
                ->sum('perRating');
            $ks_ovr = KioskRating::whereNot('ovrRating', 0)
                ->whereBetween('created_at', [$start, $end])
                ->sum('ovrRating');

            $kc_phy = KioskRating::whereNot('phyRating', 0)
                ->whereBetween('created_at', [$start, $end])
                ->count();
            $kc_ser = KioskRating::whereNot('serRating', 0)
                ->whereBetween('created_at', [$start, $end])
                ->count();
            $kc_per = KioskRating::whereNot('perRating', 0)
                ->whereBetween('created_at', [$start, $end])
                ->count();
            $kc_ovr = KioskRating::whereNot('ovrRating', 0)
                ->whereBetween('created_at', [$start, $end])
                ->count();

            $km_phy = (3 * $kc_phy);
            $kr_phy = ((($ks_phy == 0 && $kc_phy == 0) ? 0 : ($ks_phy / $km_phy)) * 100);

            $km_ser = (3 * $kc_ser);
            $kr_ser = ((($ks_ser == 0 && $kc_phy == 0) ? 0 : ($ks_ser / $km_ser)) * 100);

            $km_per = (3 * $kc_per);
            $kr_per = ((($ks_per == 0 && $kc_per == 0) ? 0 : ($ks_per / $km_per)) * 100);

            $km_ovr = (3 * $kc_ovr);
            $kr_ovr = ((($ks_ovr == 0 && $kc_ovr == 0) ? 0 : ($ks_ovr / $km_ovr)) * 100);

            $t_discussion = Discussion::whereBetween('created_at', [$start, $end])
                ->count();
            $a_discussion = Discussion::where('isActive', TRUE)
                ->whereBetween('created_at', [$start, $end])
                ->count();
            $i_discussion = Discussion::where('isActive', FALSE)
                ->whereBetween('created_at', [$start, $end])
                ->count();

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
        try {

            $start = Carbon::create($request->get('from').' 00:00:00');
            $end = Carbon::create($request->get('to').' 23:59:59');

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

            $ors = Rating::whereNot('rating', 0)
                ->whereBetween('created_at', [$start, $end])
                ->sum('rating');
                
            $orc = Rating::whereNot('rating', 0)
                ->whereBetween('created_at', [$start, $end])
                ->count();
                
            $om = (3 * $orc);
            $ovr = ((($ors == 0 && $orc == 0) ? 0 : ($ors / $om)) * 100);

            $frs = FeedbackRating::whereNot('rating', 0)
                ->whereBetween('created_at', [$start, $end])
                ->sum('rating');

            $frc = FeedbackRating::whereNot('rating', 0)
                ->whereBetween('created_at', [$start, $end])
                ->count();

            $fm = (3 * $frc);
            $fr = ((($frs == 0 && $frc == 0) ? 0 : ($frs / $fm)) * 100);

            $ks_phy = KioskRating::whereNot('phyRating', 0)
                ->whereBetween('created_at', [$start, $end])
                ->sum('phyRating');
            $ks_ser = KioskRating::whereNot('serRating', 0)
                ->whereBetween('created_at', [$start, $end])
                ->sum('serRating');
            $ks_per = KioskRating::whereNot('perRating', 0)
                ->whereBetween('created_at', [$start, $end])
                ->sum('perRating');
            $ks_ovr = KioskRating::whereNot('ovrRating', 0)
                ->whereBetween('created_at', [$start, $end])
                ->sum('ovrRating');

            $kc_phy = KioskRating::whereNot('phyRating', 0)
                ->whereBetween('created_at', [$start, $end])
                ->count();
            $kc_ser = KioskRating::whereNot('serRating', 0)
                ->whereBetween('created_at', [$start, $end])
                ->count();
            $kc_per = KioskRating::whereNot('perRating', 0)
                ->whereBetween('created_at', [$start, $end])
                ->count();
            $kc_ovr = KioskRating::whereNot('ovrRating', 0)
                ->whereBetween('created_at', [$start, $end])
                ->count();
                
            $km_phy = (3 * $kc_phy);
            $kr_phy = ((($ks_phy == 0 && $kc_phy == 0) ? 0 : ($ks_phy / $km_phy)) * 100);

            $km_ser = (3 * $kc_ser);
            $kr_ser = ((($ks_ser == 0 && $kc_phy == 0) ? 0 : ($ks_ser / $km_ser)) * 100);

            $km_per = (3 * $kc_per);
            $kr_per = ((($ks_per == 0 && $kc_per == 0) ? 0 : ($ks_per / $km_per)) * 100);

            $km_ovr = (3 * $kc_ovr);
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

                    $total = FeedbackOffice::where('officeID', $value->id)
                        ->whereBetween('created_at', [$start, $end])
                        ->count();

                    $oors = Rating::whereNot('rating', 0)
                        ->where('officeID', $value->id)
                        ->whereBetween('created_at', [$start, $end])
                        ->sum('rating');

                    $oorc = Rating::whereNot('rating', 0)
                        ->where('officeID', $value->id)
                        ->whereBetween('created_at', [$start, $end])
                        ->count();

                    $oom = (3 * $oorc);
                    $oovr = ((($oors == 0 && $oorc == 0) ? 0 : ($oors / $oom)) * 100);

                    $delay = FeedbackOffice::where('officeID', $value->id)
                        ->where('isDelayed', TRUE)
                        ->whereBetween('created_at', [$start, $end])
                        ->count();

                        $array = [
                            'label' => $value->label,
                            'total' => $total,
                            'rating' => number_format($oovr, 2),
                            'delay' => $delay
                        ];

                        array_push($offArr, $array);

                }

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
        try {

            $start = Carbon::create($request->get('from').' 00:00:00');
            $end = Carbon::create($request->get('to').' 23:59:59');

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

            $ors = Rating::whereNot('rating', 0)
                ->whereBetween('created_at', [$start, $end])
                ->sum('rating');

            $orc = Rating::whereNot('rating', 0)
                ->whereBetween('created_at', [$start, $end])
                ->count();

            $om = (3 * $orc);
            $ovr = ((($ors == 0 && $orc == 0) ? 0 : ($ors / $om)) * 100);

            $frs = FeedbackRating::whereNot('rating', 0)
                ->whereBetween('created_at', [$start, $end])
                ->sum('rating');

            $frc = FeedbackRating::whereNot('rating', 0)
                ->whereBetween('created_at', [$start, $end])
                ->count();

            $fm = (3 * $frc);
            $fr = ((($frs == 0 && $frc == 0) ? 0 : ($frs / $fm)) * 100);

            $ks_phy = KioskRating::whereNot('phyRating', 0)
                ->whereBetween('created_at', [$start, $end])
                ->sum('phyRating');
            $ks_ser = KioskRating::whereNot('serRating', 0)
                ->whereBetween('created_at', [$start, $end])
                ->sum('serRating');
            $ks_per = KioskRating::whereNot('perRating', 0)
                ->whereBetween('created_at', [$start, $end])
                ->sum('perRating');
            $ks_ovr = KioskRating::whereNot('ovrRating', 0)
                ->whereBetween('created_at', [$start, $end])
                ->sum('ovrRating');

            $kc_phy = KioskRating::whereNot('phyRating', 0)
                ->whereBetween('created_at', [$start, $end])
                ->count();
            $kc_ser = KioskRating::whereNot('serRating', 0)
                ->whereBetween('created_at', [$start, $end])
                ->count();
            $kc_per = KioskRating::whereNot('perRating', 0)
                ->whereBetween('created_at', [$start, $end])
                ->count();
            $kc_ovr = KioskRating::whereNot('ovrRating', 0)
                ->whereBetween('created_at', [$start, $end])
                ->count();

            $km_phy = (3 * $kc_phy);
            $kr_phy = ((($ks_phy == 0 && $kc_phy == 0) ? 0 : ($ks_phy / $km_phy)) * 100);

            $km_ser = (3 * $kc_ser);
            $kr_ser = ((($ks_ser == 0 && $kc_phy == 0) ? 0 : ($ks_ser / $km_ser)) * 100);

            $km_per = (3 * $kc_per);
            $kr_per = ((($ks_per == 0 && $kc_per == 0) ? 0 : ($ks_per / $km_per)) * 100);

            $km_ovr = (3 * $kc_ovr);
            $kr_ovr = ((($ks_ovr == 0 && $kc_ovr == 0) ? 0 : ($ks_ovr / $km_ovr)) * 100);

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

            $dtf = Carbon::create($request->get('from').' 08:00:00');
            $dtt = Carbon::create($request->get('to').' 17:00:00');
            $from = $dtf->toDayDateTimeString(); 
            $to = $dtt->toDayDateTimeString(); 

            $today = Carbon::now(+8);
            $now = $today->toDayDateTimeString(); 
            
            $pdf = PDF::setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true, 'debugPng' => true])->loadView('report.FeedbackCategoryReport', [
                'date' => $from.' to '.$to,
                'data' => $arr,
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
        try {

            $start = Carbon::create($request->get('from').' 00:00:00');
            $end = Carbon::create($request->get('to').' 23:59:59');

            $office = PreferenceOffice::where('isActive', TRUE)
                ->get();

                $arr = [];

                foreach ($office as $key => $value) {

                    $count = FeedbackOffice::where('officeID', $value->id)
                        ->whereBetween('created_at', [$start, $end])
                        ->count();
                        
                    $ors = Rating::whereNot('rating', 0)
                        ->where('officeID', $value->id)
                        ->whereBetween('created_at', [$start, $end])
                        ->sum('rating');
                        
                    $orc = Rating::whereNot('rating', 0)
                        ->where('officeID', $value->id)
                        ->whereBetween('created_at', [$start, $end])
                        ->count();
                        
                    $om = (3 * $orc);
                    $ovr = ((($ors == 0 && $orc == 0) ? 0 : ($ors / $om)) * 100);
                    
                    $frs = FeedbackRating::join('feedback_responses', 'feedback_ratings.responseID', 'feedback_responses.id')
                        ->join('feedback_offices', 'feedback_responses.feedbackID', 'feedback_offices.feedbackID')
                        ->where('feedback_offices.officeID', $value->id)
                        ->whereNot('feedback_ratings.rating', 0)
                        ->whereBetween('feedback_offices.created_at', [$start, $end])
                        ->sum('feedback_ratings.rating');
                        
                    $frc = FeedbackRating::join('feedback_responses', 'feedback_ratings.responseID', 'feedback_responses.id')
                        ->join('feedback_offices', 'feedback_responses.feedbackID', 'feedback_offices.feedbackID')
                        ->where('feedback_offices.officeID', $value->id)
                        ->whereNot('feedback_ratings.rating', 0)
                        ->whereBetween('feedback_offices.created_at', [$start, $end])
                        ->count();
                        
                    $fm = (3 * $frc);
                    $fr = ((($frs == 0 && $frc == 0) ? 0 : ($frs / $fm)) * 100);
                    
                    $ks_phy = KioskRating::join('preference_kiosks', 'kiosk_ratings.kioskID', 'preference_kiosks.id')
                        ->where('preference_kiosks.officeID', $value->id)
                        ->whereNot('kiosk_ratings.phyRating', 0)
                        ->whereBetween('kiosk_ratings.created_at', [$start, $end])
                        ->sum('kiosk_ratings.phyRating');
                    $ks_ser = KioskRating::join('preference_kiosks', 'kiosk_ratings.kioskID', 'preference_kiosks.id')
                        ->where('preference_kiosks.officeID', $value->id)
                        ->whereNot('kiosk_ratings.serRating', 0)
                        ->whereBetween('kiosk_ratings.created_at', [$start, $end])
                        ->sum('kiosk_ratings.serRating');
                    $ks_per = KioskRating::join('preference_kiosks', 'kiosk_ratings.kioskID', 'preference_kiosks.id')
                        ->where('preference_kiosks.officeID', $value->id)
                        ->whereNot('kiosk_ratings.perRating', 0)
                        ->whereBetween('kiosk_ratings.created_at', [$start, $end])
                        ->sum('kiosk_ratings.perRating');
                    $ks_ovr = KioskRating::join('preference_kiosks', 'kiosk_ratings.kioskID', 'preference_kiosks.id')
                        ->where('preference_kiosks.officeID', $value->id)
                        ->whereNot('kiosk_ratings.ovrRating', 0)
                        ->whereBetween('kiosk_ratings.created_at', [$start, $end])
                        ->sum('kiosk_ratings.ovrRating');
                        
                    $kc_phy = KioskRating::join('preference_kiosks', 'kiosk_ratings.kioskID', 'preference_kiosks.id')
                        ->where('preference_kiosks.officeID', $value->id)
                        ->whereNot('kiosk_ratings.phyRating', 0)
                        ->whereBetween('kiosk_ratings.created_at', [$start, $end])
                        ->count();
                    $kc_ser = KioskRating::join('preference_kiosks', 'kiosk_ratings.kioskID', 'preference_kiosks.id')
                        ->where('preference_kiosks.officeID', $value->id)
                        ->whereNot('kiosk_ratings.serRating', 0)
                        ->whereBetween('kiosk_ratings.created_at', [$start, $end])
                        ->count();
                    $kc_per = KioskRating::join('preference_kiosks', 'kiosk_ratings.kioskID', 'preference_kiosks.id')
                        ->where('preference_kiosks.officeID', $value->id)
                        ->whereNot('kiosk_ratings.perRating', 0)
                        ->whereBetween('kiosk_ratings.created_at', [$start, $end])
                        ->count();
                    $kc_ovr = KioskRating::join('preference_kiosks', 'kiosk_ratings.kioskID', 'preference_kiosks.id')
                        ->where('preference_kiosks.officeID', $value->id)
                        ->whereNot('kiosk_ratings.ovrRating', 0)
                        ->whereBetween('kiosk_ratings.created_at', [$start, $end])
                        ->count();
                        
                    $km_phy = (3 * $kc_phy);
                    $kr_phy = ((($ks_phy == 0 && $kc_phy == 0) ? 0 : ($ks_phy / $km_phy)) * 100);

                    $km_ser = (3 * $kc_ser);
                    $kr_ser = ((($ks_ser == 0 && $kc_phy == 0) ? 0 : ($ks_ser / $km_ser)) * 100);

                    $km_per = (3 * $kc_per);
                    $kr_per = ((($ks_per == 0 && $kc_per == 0) ? 0 : ($ks_per / $km_per)) * 100);

                    $km_ovr = (3 * $kc_ovr);
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

            $dtf = Carbon::create($request->get('from').' 08:00:00');
            $dtt = Carbon::create($request->get('to').' 17:00:00');
            $from = $dtf->toDayDateTimeString(); 
            $to = $dtt->toDayDateTimeString(); 

            $today = Carbon::now(+8);
            $now = $today->toDayDateTimeString(); 
            
            $pdf = PDF::setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true, 'debugPng' => true])->loadView('report.FeedbackOfficeReport', [
                'date' => $from.' to '.$to,
                'data' => $arr,
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
        try {

            $start = Carbon::create($request->get('from').' 00:00:00');
            $end = Carbon::create($request->get('to').' 23:59:59');

            $kiosk = PreferenceKiosk::select('preference_kiosks.*', 'preference_offices.code AS office', 'preference_positions.label AS position')
                ->join('preference_offices', 'preference_kiosks.officeID', 'preference_offices.id')
                ->join('preference_positions', 'preference_kiosks.positionID', 'preference_positions.id')
                ->where('preference_kiosks.isActive', TRUE)
                ->get();

                $arr = [];

                foreach ($kiosk as $key => $value) {

                    $ks_phy = KioskRating::where('kioskID', $value->id)
                        ->whereNot('phyRating', 0)
                        ->whereBetween('created_at', [$start, $end])
                        ->sum('phyRating');
                    $ks_ser = KioskRating::where('kioskID', $value->id)
                        ->whereNot('serRating', 0)
                        ->whereBetween('created_at', [$start, $end])
                        ->sum('serRating');
                    $ks_per = KioskRating::where('kioskID', $value->id)
                        ->whereNot('perRating', 0)
                        ->whereBetween('created_at', [$start, $end])
                        ->sum('perRating');
                    $ks_ovr = KioskRating::where('kioskID', $value->id)
                        ->whereNot('ovrRating', 0)
                        ->whereBetween('created_at', [$start, $end])
                        ->sum('ovrRating');

                    $kc_phy = KioskRating::where('kioskID', $value->id)
                        ->whereNot('phyRating', 0)
                        ->whereBetween('created_at', [$start, $end])
                        ->count();
                    $kc_ser = KioskRating::where('kioskID', $value->id)
                        ->whereNot('serRating', 0)
                        ->whereBetween('created_at', [$start, $end])
                        ->count();
                    $kc_per = KioskRating::where('kioskID', $value->id)
                        ->whereNot('perRating', 0)
                        ->whereBetween('created_at', [$start, $end])
                        ->count();
                    $kc_ovr = KioskRating::where('kioskID', $value->id)
                        ->whereNot('ovrRating', 0)
                        ->whereBetween('created_at', [$start, $end])
                        ->count();
                    $suggestions = KioskRating::where('kioskID', $value->id)
                        ->whereBetween('created_at', [$start, $end])
                        ->get();

                    $km_phy = (3 * $kc_phy);
                    $kr_phy = ((($ks_phy == 0 && $kc_phy == 0) ? 0 : ($ks_phy / $km_phy)) * 100);

                    $km_ser = (3 * $kc_ser);
                    $kr_ser = ((($ks_ser == 0 && $kc_phy == 0) ? 0 : ($ks_ser / $km_ser)) * 100);

                    $km_per = (3 * $kc_per);
                    $kr_per = ((($ks_per == 0 && $kc_per == 0) ? 0 : ($ks_per / $km_per)) * 100);

                    $km_ovr = (3 * $kc_ovr);
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

            $dtf = Carbon::create($request->get('from').' 08:00:00');
            $dtt = Carbon::create($request->get('to').' 17:00:00');
            $from = $dtf->toDayDateTimeString(); 
            $to = $dtt->toDayDateTimeString(); 

            $today = Carbon::now(+8);
            $now = $today->toDayDateTimeString(); 
            
            $pdf = PDF::setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true, 'debugPng' => true])->loadView('report.FeedbackKioskReport', [
                'date' => $from.' to '.$to,
                'data' => $arr,
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
    public function getDiscussionSummary(Request $request)
    {
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
