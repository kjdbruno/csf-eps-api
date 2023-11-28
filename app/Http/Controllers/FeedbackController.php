<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use App\Models\FeedbackOffice;
use App\Models\FeedbackResponse;
use App\Models\FeedbackEvidence;
use App\Models\FeedbackRating;
use App\Models\User;
use App\Models\UserClient;
use App\Models\UserAdmin;
use App\Models\UserRole;
use App\Models\SettingOfficeCategory;
use App\Models\KioskRating;
use App\Models\Rating;
use Illuminate\Http\Request;

use App\Http\Requests\FeedbackReceiveRequest;
use App\Http\Requests\FeedbackResponseRequest;
use App\Http\Requests\FeedbackEntryOfflineRequest;
use App\Http\Requests\FeedbackEntryKioskRequest;
use App\Http\Requests\FeedbackCompleteRequest;
use App\Http\Requests\FeedbackCancelRequest;

use Illuminate\Support\Facades\Hash;

use App\Mail\FeedbackEntryOfflineUserRegistrationMail;
use App\Mail\FeedbackOfficeMail;
use App\Mail\FeedbackResponseMail;
use Illuminate\Support\Facades\Mail;

use Carbon\Carbon;

USE PDF;

class FeedbackController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function getOverview(Request $request, $id)
    {
        date_default_timezone_set('Asia/Manila');

        try {

            $users = User::select('users.*', 'user_roles.roleID', 'preference_years.label AS year', 'user_admins.officeID')
                ->join('user_admins', 'users.id', 'user_admins.userID')
                ->join('user_roles', 'users.id', 'user_roles.userID')
                ->join('preference_years', 'user_admins.yearID', 'preference_years.id')
                ->where('users.id', $id)
                ->get();

            $now = NOW();
            $today = date_format($now, 'Y-m-d H:i:s');

            $feedbacks = Feedback::where('expire_on', '<', $today)
                ->get();

                foreach ($feedbacks as $key => $f_value) {
                    
                    $offices = FeedbackOffice::where('feedbackID', $f_value->id)
                        ->where('isReceived', FALSE)
                        ->where('isActive', TRUE)
                        ->update([
                            'isDelayed' => TRUE
                        ]);

                }

                if ($users[0]->roleID == 1 OR $users[0]->roleID == 2 OR $users[0]->roleID == 3) {

                    $rating_sum = FeedbackRating::whereNot('rating', 0)
                        ->whereYear('created_at', $users[0]->year)
                        ->sum('rating');

                    $rating_count = FeedbackRating::whereNot('rating', 0)
                        ->whereYear('created_at', $users[0]->year)
                        ->count();

                    $maximum = (5 * $rating_count);
                    $satisfaction = ((($rating_sum == 0 && $rating_count == 0) ? 0 : ($rating_sum / $maximum)) * 100);

                    $total = Feedback::whereYear('created_at', $users[0]->year)
                        ->count();

                    $pending = Feedback::whereYear('created_at', $users[0]->year)
                        ->where('status', 1)
                        ->count();

                    $ongoing = Feedback::whereYear('created_at', $users[0]->year)
                        ->where('status', 2)
                        ->count();

                    $completed = Feedback::whereYear('created_at', $users[0]->year)
                        ->where('status', 3)
                        ->count();

                    $delayed = FeedbackOffice::whereYear('created_at', $users[0]->year)
                        ->where('isDelayed', TRUE)
                        ->count();

                    $cancelled = Feedback::whereYear('created_at', $users[0]->year)
                        ->where('isActive', FALSE)
                        ->count();

                        return response()->json([
                            'totalFeedback' => $total,
                            'totalPending' => $pending,
                            'totalOngoing' => $ongoing,
                            'totalCompleted' => $completed,
                            'totalDelayed' => $delayed,
                            'totalCancelled' => $cancelled,
                            'satisfactionRate' => number_format($satisfaction, 2)
                            
                        ]);

                } else {
                    $rating_sum = FeedbackRating::join('feedback_responses', 'feedback_ratings.responseID', 'feedback_responses.id')
                        ->join('feedback_offices', 'feedback_responses.feedbackID', 'feedback_offices.feedbackID')
                        ->join('feedback', 'feedback_offices.feedbackID', 'feedback.id')
                        ->whereNot('feedback_ratings.rating', 0)
                        ->where('feedback_offices.officeID', $users[0]->officeID)
                        ->whereYear('feedback.created_at', $users[0]->year)
                        ->sum('feedback_ratings.rating');

                    $rating_count = FeedbackRating::join('feedback_responses', 'feedback_ratings.responseID', 'feedback_responses.id')
                        ->join('feedback_offices', 'feedback_responses.feedbackID', 'feedback_offices.feedbackID')
                        ->join('feedback', 'feedback_offices.feedbackID', 'feedback.id')
                        ->whereNot('feedback_ratings.rating', 0)
                        ->where('feedback_offices.officeID', $users[0]->officeID)
                        ->whereYear('feedback.created_at', $users[0]->year)
                        ->count();

                    $maximum = (5 * $rating_count);
                    $satisfaction = ((($rating_sum == 0 && $rating_count == 0) ? 0 : ($rating_sum / $maximum)) * 100);

                    $total = Feedback::join('feedback_offices', 'feedback.id', 'feedback_offices.feedbackID')
                        ->where('feedback_offices.officeID', $users[0]->officeID)
                        ->whereYear('feedback.created_at', $users[0]->year)
                        ->count();

                    $pending = Feedback::join('feedback_offices', 'feedback.id', 'feedback_offices.feedbackID')
                        ->where('feedback_offices.officeID', $users[0]->officeID)
                        ->whereYear('feedback.created_at', $users[0]->year)
                        ->where('feedback.status', 1)
                        ->count();

                    $ongoing = Feedback::join('feedback_offices', 'feedback.id', 'feedback_offices.feedbackID')
                        ->where('feedback_offices.officeID', $users[0]->officeID)
                        ->whereYear('feedback.created_at', $users[0]->year)
                        ->where('feedback.status', 2)
                        ->count();

                    $completed = Feedback::join('feedback_offices', 'feedback.id', 'feedback_offices.feedbackID')
                        ->where('feedback_offices.officeID', $users[0]->officeID)
                        ->whereYear('feedback.created_at', $users[0]->year)
                        ->where('feedback.status', 3)
                        ->count();

                    $delayed = FeedbackOffice::whereYear('created_at', $users[0]->year)
                        ->where('officeID', $users[0]->officeID)
                        ->where('isDelayed', TRUE)
                        ->count();

                    $cancelled = Feedback::join('feedback_offices', 'feedback.id', 'feedback_offices.feedbackID')
                        ->where('feedback.isActive', FALSE)
                        ->where('feedback_offices.officeID', $users[0]->officeID)
                        ->whereYear('feedback.created_at', $users[0]->year)
                        ->count();

                        return response()->json([
                            'totalFeedback' => $total,
                            'totalPending' => $pending,
                            'totalOngoing' => $ongoing,
                            'totalCompleted' => $completed,
                            'totalDelayed' => $delayed,
                            'totalCancelled' => $cancelled,
                            'satisfactionRate' => number_format($satisfaction, 2)
                        ]);
                }

        } catch (\Exception $e) {

            logger('Message logged from FeedbackController.getOverview', [$e->getMessage()]);
            return response()->json([
                'error' => 'Something went wrong getting record!',
                'data' => $e->getMessage()
            ], 400);

        }
    }

    /**
     * Display a listing of the resource.
     */
    public function getList(Request $request, $id)
    {
        date_default_timezone_set('Asia/Manila');

        try {

            $users = User::select('users.*', 'user_roles.roleID', 'preference_years.label AS year', 'user_admins.officeID')
                ->join('user_admins', 'users.id', 'user_admins.userID')
                ->join('user_roles', 'users.id', 'user_roles.userID')
                ->join('preference_years', 'user_admins.yearID', 'preference_years.id')
                ->where('users.id', $id)
                ->get();

                if ($users[0]->roleID == 1 OR $users[0]->roleID == 2 OR $users[0]->roleID == 3) {

                    $feedbacks = Feedback::select('feedback.*', 'users.name', 'users.email', 'users.avatar', 'preference_categories.label AS category')
                        ->join('users', 'feedback.userID', 'users.id')
                        ->join('user_clients', 'users.id', 'user_clients.userID')
                        ->join('preference_categories', 'feedback.categoryID', 'preference_categories.id')
                        ->whereYear('feedback.created_at', $users[0]->year)
                        ->where('preference_categories.label', 'LIKE', '%'.$request->get('filter').'%')
                        ->orderBy('feedback.created_at', 'DESC')
                        ->get();

                        $arr = [];
                        foreach ($feedbacks as $key => $f_value) {
                            
                            $receive = FeedbackOffice::where('feedbackID', $f_value->id)
                                ->where('isReceived', TRUE)
                                ->count();

                            $offices = FeedbackOffice::select('preference_offices.code AS office', 'feedback_offices.isReceived', 'feedback_offices.isDelayed')
                                ->join('preference_offices', 'feedback_offices.officeID', 'preference_offices.id')
                                ->where('feedback_offices.feedbackID', $f_value->id)
                                ->get();

                                $array = [
                                    'id' => $f_value->id,
                                    'complainant' => $f_value->name,
                                    'email' => $f_value->email,
                                    'avatar' => $f_value->avatar,
                                    'category' => $f_value->category,
                                    'isActive' => $f_value->isActive,
                                    'status' => $f_value->status,
                                    'rCount' => $receive,
                                    'offices' => $offices
                                ];

                                array_push($arr, $array);
                        }

                        return response()->json($arr);

                } else {

                    $feedbacks = Feedback::select('feedback.*', 'users.name', 'users.email', 'users.avatar', 'preference_categories.label AS category')
                        ->join('users', 'feedback.userID', 'users.id')
                        ->join('user_clients', 'users.id', 'user_clients.userID')
                        ->join('preference_categories', 'feedback.categoryID', 'preference_categories.id')
                        ->join('feedback_offices', 'feedback.id', 'feedback_offices.feedbackID')
                        ->whereYear('feedback.created_at', $users[0]->year)
                        ->where('feedback_offices.officeID', $users[0]->officeID)
                        ->where('preference_categories.label', 'LIKE', '%'.$request->get('filter').'%')
                        ->orderBy('feedback.created_at', 'DESC')
                        ->get();

                        $arr = [];
                        foreach ($feedbacks as $key => $f_value) {
                            
                            $receive = FeedbackOffice::where('feedbackID', $f_value->id)
                                ->where('isReceived', TRUE)
                                ->count();

                            $offices = FeedbackOffice::select('preference_offices.code AS office', 'feedback_offices.isReceived', 'feedback_offices.isDelayed')
                                ->join('preference_offices', 'feedback_offices.officeID', 'preference_offices.id')
                                ->where('feedback_offices.feedbackID', $f_value->id)
                                ->get();

                                $array = [
                                    'id' => $f_value->id,
                                    'complainant' => $f_value->name,
                                    'email' => $f_value->email,
                                    'avatar' => $f_value->avatar,
                                    'category' => $f_value->category,
                                    'isActive' => $f_value->isActive,
                                    'status' => $f_value->status,
                                    'rCount' => $receive,
                                    'offices' => $offices
                                ];

                                array_push($arr, $array);
                        }

                        return response()->json($arr);

                }

        } catch (\Exception $e) {

            logger('Message logged from FeedbackController.getList', [$e->getMessage()]);
            return response()->json([
                'error' => 'Something went wrong getting record!',
                'data' => $e->getMessage()
            ], 400);

        }
    }

    /**
     * 
     */
    public function receive(FeedbackReceiveRequest $request, $id)
    {
        date_default_timezone_set('Asia/Manila');

        try {

            $client = Feedback::join('users', 'feedback.userID', 'users.id')
                ->where('feedback.id', $request->get('feedbackID'))
                ->get();

            Mail::to($client[0]->email)->send(new FeedbackResponseMail($client[0]->name));

            $users = User::select('users.*', 'user_roles.roleID', 'preference_years.label AS year', 'user_admins.officeID')
                ->join('user_admins', 'users.id', 'user_admins.userID')
                ->join('user_roles', 'users.id', 'user_roles.userID')
                ->join('preference_years', 'user_admins.yearID', 'preference_years.id')
                ->where('users.id', $id)
                ->get();

                $office = FeedbackOffice::where('feedbackID', $request->get('feedbackID'))
                    ->where('officeID', $users[0]->officeID)
                    ->update(['isReceived' => TRUE]);

                $feedback = Feedback::where('id', $request->get('feedbackID'))
                    ->update([
                        'status' => 2
                    ]);

                $response = new FeedbackResponse;
                $response->feedbackID = $request->get('feedbackID');
                $response->userID = $id;
                $response->content = 'Your feedback has been received by concern office. Please wait for futher updates.';
                $response->file = '';
                $response->save();

                return response()->json([
                    'msg' => 'RECORD RECEIVED!',
                    'data' => $response
                ], 200);

        } catch (\Exception $e) {

            logger('Message logged from FeedbackController.receive', [$e->getMessage()]);
            return response()->json([
                'error' => 'Something went wrong receiving record!',
                'data' => $e->getMessage()
            ], 400);

        } 
    }

    /**
     * 
     */
    public function getDetail($id)
    {
        date_default_timezone_set('Asia/Manila');

        try {

            $feedbacks = Feedback::select('feedback.*', 'users.name', 'users.email', 'users.avatar')
                ->join('users', 'feedback.userID', 'users.id')
                ->join('user_clients', 'users.id', 'user_clients.userID')
                ->where('feedback.id', $id)
                ->get();

            $rating_sum = FeedbackRating::join('feedback_responses', 'feedback_ratings.responseID', 'feedback_responses.id')
                ->whereNot('feedback_ratings.rating', 0)
                ->where('feedback_responses.feedbackID', $id)
                ->sum('feedback_ratings.rating');

            $rating_count = FeedbackRating::join('feedback_responses', 'feedback_ratings.responseID', 'feedback_responses.id')
                ->whereNot('feedback_ratings.rating', 0)
                ->where('feedback_responses.feedbackID', $id)
                ->count();

            $maximum = (5 * $rating_count);
            $rating = ((($rating_sum == 0 && $rating_count == 0) ? 0 : ($rating_sum / $maximum)) * 100);

            $arr = [];

            foreach ($feedbacks as $key => $f_value) {
                
                $offices = FeedbackOffice::select('preference_offices.code AS office', 'feedback_offices.isReceived', 'feedback_offices.isDelayed')
                    ->join('preference_offices', 'feedback_offices.officeID', 'preference_offices.id')
                    ->where('feedback_offices.feedbackID', $id)
                    ->get();

                $evidences = FeedbackEvidence::where('feedbackID', $id)
                    ->get();

                    $array = [
                        'id' => $f_value->id,
                        'content' => $f_value->content,
                        'status' => $f_value->status,
                        'created_at' => $f_value->created_at,
                        'photos' => $evidences,
                        //
                        'complainant' => $f_value->name,
                        'email' => $f_value->email,
                        'avatar' => $f_value->avatar,
                        //
                        'offices' => $offices,
                        'rating' => number_format($rating, 2)
                    ];

                    array_push($arr, $array);

            }

            return response()->json($arr);

        } catch (\Exception $e) {

            logger('Message logged from FeedbackController.getDetail', [$e->getMessage()]);
            return response()->json([
                'error' => 'Something went wrong getting record!',
                'data' => $e->getMessage()
            ], 400);

        } 
    }

    /**
     * 
     */
    public function getResponse($id)
    {
        date_default_timezone_set('Asia/Manila');

        try {

            $responses = FeedbackResponse::select('feedback_responses.*', 'users.name', 'users.avatar', 'user_roles.roleID')
                ->join('users', 'feedback_responses.userID', 'users.id')
                ->join('user_roles', 'users.id', 'user_roles.userID')
                ->where('feedback_responses.feedbackID', $id)
                ->orderBy('feedback_responses.created_at', 'DESC')
                ->get();

                $arr = [];

                foreach ($responses as $key => $r_value) {

                    $rating = FeedbackRating::where('responseID', $r_value->id)
                        ->get();

                        $array = [
                            'id' => $r_value->id,
                            'content' => $r_value->content,
                            'file' => $r_value->file,
                            'name' => $r_value->name,
                            'avatar' => $r_value->avatar,
                            'created_at' => $r_value->created_at,
                            'rating' => (count($rating) == 0 ? 0 : $rating[0]->rating)
                        ];

                        array_push($arr, $array);

                }

                return response()->json($arr);

        } catch (\Exception $e) {

            logger('Message logged from FeedbackController.getResponse', [$e->getMessage()]);
            return response()->json([
                'error' => 'Something went wrong getting record!',
                'data' => $e->getMessage()
            ], 400);

        } 
    }

    /**
     * 
     */
    public function response(FeedbackResponseRequest $request, $id)
    {
        date_default_timezone_set('Asia/Manila');

        try {

            $client = Feedback::join('users', 'feedback.userID', 'users.id')
                ->where('feedback.id', $request->get('feedbackID'))
                ->get();

            Mail::to($client[0]->email)->send(new FeedbackResponseMail($client[0]->name));

            $response = new FeedbackResponse;
            $response->feedbackID = $request->get('feedbackID');
            $response->userID = $id;
            $response->status = 2;
            $response->content = $request->get('content');
            $response->file = $request->get('file');
            $response->save();

            return response()->json([
                'msg' => 'RECORD SAVED!',
                'data' => $response
            ], 200);

        } catch (\Exception $e) {

            logger('Message logged from FeedbackController.response', [$e->getMessage()]);
            return response()->json([
                'error' => 'Something went wrong storing record!',
                'data' => $e->getMessage()
            ], 400);

        } 
    }

    /**
     * 
     */
    public function complete(FeedbackCompleteRequest $request, $id)
    {
        date_default_timezone_set('Asia/Manila');

        try {

            $feedback = Feedback::where('id', $request->get('feedbackID'))
                ->update(['status' => 3]);

            $response = new FeedbackResponse;
            $response->feedbackID = $request->get('feedbackID');
            $response->userID = $id;
            $response->status = 3;
            $response->content = 'This feedback is mark as completed. Thank You!';
            $response->file = '';
            $response->save();

            return response()->json([
                'msg' => 'RECORD COMPLETED!',
                'data' => $response
            ], 200);

        } catch (\Exception $e) {

            logger('Message logged from FeedbackController.complete', [$e->getMessage()]);
            return response()->json([
                'error' => 'Something went wrong completing record!',
                'data' => $e->getMessage()
            ], 400);

        }
    }

    /**
     * 
     */
    public function cancel($id)
    {
        date_default_timezone_set('Asia/Manila');

        try {

            $feedback = Feedback::where('id', $id)
                ->update(['isActive' => FALSE]);

                return response()->json([
                    'msg' => 'RECORD CANCELLED!',
                    'data' => $feedback
                ], 200);

        } catch (\Exception $e) {

            logger('Message logged from FeedbackController.cancel', [$e->getMessage()]);
            return response()->json([
                'error' => 'Something went wrong cancelling record!',
                'data' => $e->getMessage()
            ], 400);

        }
    }

    /**
     * 
     */
    public function offline(FeedbackEntryOfflineRequest $request)
    {
        date_default_timezone_set('Asia/Manila');

        // Generate random verification code
        $code = random_int(100000, 999999);
        // avatar
        $avatar = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAXQAAAF1CAYAAAD4PxH2AAAACXBIWXMAAAsTAAALEwEAmpwYAAA7rWlUWHRYTUw6Y29tLmFkb2JlLnhtcAAAAAAAPD94cGFja2V0IGJlZ2luPSLvu78iIGlkPSJXNU0wTXBDZWhpSHpyZVN6TlRjemtjOWQiPz4KPHg6eG1wbWV0YSB4bWxuczp4PSJhZG9iZTpuczptZXRhLyIgeDp4bXB0az0iQWRvYmUgWE1QIENvcmUgNS42LWMwNjcgNzkuMTU3NzQ3LCAyMDE1LzAzLzMwLTIzOjQwOjQyICAgICAgICAiPgogICA8cmRmOlJERiB4bWxuczpyZGY9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkvMDIvMjItcmRmLXN5bnRheC1ucyMiPgogICAgICA8cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0iIgogICAgICAgICAgICB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iCiAgICAgICAgICAgIHhtbG5zOmRjPSJodHRwOi8vcHVybC5vcmcvZGMvZWxlbWVudHMvMS4xLyIKICAgICAgICAgICAgeG1sbnM6cGhvdG9zaG9wPSJodHRwOi8vbnMuYWRvYmUuY29tL3Bob3Rvc2hvcC8xLjAvIgogICAgICAgICAgICB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIKICAgICAgICAgICAgeG1sbnM6c3RFdnQ9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZUV2ZW50IyIKICAgICAgICAgICAgeG1sbnM6dGlmZj0iaHR0cDovL25zLmFkb2JlLmNvbS90aWZmLzEuMC8iCiAgICAgICAgICAgIHhtbG5zOmV4aWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20vZXhpZi8xLjAvIj4KICAgICAgICAgPHhtcDpDcmVhdG9yVG9vbD5BZG9iZSBQaG90b3Nob3AgQ0MgMjAxNSAoTWFjaW50b3NoKTwveG1wOkNyZWF0b3JUb29sPgogICAgICAgICA8eG1wOkNyZWF0ZURhdGU+MjAxOC0wNS0xMFQxODoxNzo0MSswMzowMDwveG1wOkNyZWF0ZURhdGU+CiAgICAgICAgIDx4bXA6TW9kaWZ5RGF0ZT4yMDE4LTA1LTEwVDE4OjE5OjQzKzAzOjAwPC94bXA6TW9kaWZ5RGF0ZT4KICAgICAgICAgPHhtcDpNZXRhZGF0YURhdGU+MjAxOC0wNS0xMFQxODoxOTo0MyswMzowMDwveG1wOk1ldGFkYXRhRGF0ZT4KICAgICAgICAgPGRjOmZvcm1hdD5pbWFnZS9wbmc8L2RjOmZvcm1hdD4KICAgICAgICAgPHBob3Rvc2hvcDpDb2xvck1vZGU+MzwvcGhvdG9zaG9wOkNvbG9yTW9kZT4KICAgICAgICAgPHhtcE1NOkluc3RhbmNlSUQ+eG1wLmlpZDo2Y2JhMzMyYi0yZGQyLTQ4MjAtYmM5ZS1kYmRlZmI4NDljNjE8L3htcE1NOkluc3RhbmNlSUQ+CiAgICAgICAgIDx4bXBNTTpEb2N1bWVudElEPmFkb2JlOmRvY2lkOnBob3Rvc2hvcDpmNTZmZTJiZS05NGYzLTExN2ItYjkzZC1mMWQwZDBiYWJkOTU8L3htcE1NOkRvY3VtZW50SUQ+CiAgICAgICAgIDx4bXBNTTpPcmlnaW5hbERvY3VtZW50SUQ+eG1wLmRpZDo1MGM0YjNhMy03M2FkLTQyMDQtYjkzMC1jZDgwYWVkNGJkNWU8L3htcE1NOk9yaWdpbmFsRG9jdW1lbnRJRD4KICAgICAgICAgPHhtcE1NOkhpc3Rvcnk+CiAgICAgICAgICAgIDxyZGY6U2VxPgogICAgICAgICAgICAgICA8cmRmOmxpIHJkZjpwYXJzZVR5cGU9IlJlc291cmNlIj4KICAgICAgICAgICAgICAgICAgPHN0RXZ0OmFjdGlvbj5jcmVhdGVkPC9zdEV2dDphY3Rpb24+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDppbnN0YW5jZUlEPnhtcC5paWQ6NTBjNGIzYTMtNzNhZC00MjA0LWI5MzAtY2Q4MGFlZDRiZDVlPC9zdEV2dDppbnN0YW5jZUlEPgogICAgICAgICAgICAgICAgICA8c3RFdnQ6d2hlbj4yMDE4LTA1LTEwVDE4OjE3OjQxKzAzOjAwPC9zdEV2dDp3aGVuPgogICAgICAgICAgICAgICAgICA8c3RFdnQ6c29mdHdhcmVBZ2VudD5BZG9iZSBQaG90b3Nob3AgQ0MgMjAxNSAoTWFjaW50b3NoKTwvc3RFdnQ6c29mdHdhcmVBZ2VudD4KICAgICAgICAgICAgICAgPC9yZGY6bGk+CiAgICAgICAgICAgICAgIDxyZGY6bGkgcmRmOnBhcnNlVHlwZT0iUmVzb3VyY2UiPgogICAgICAgICAgICAgICAgICA8c3RFdnQ6YWN0aW9uPnNhdmVkPC9zdEV2dDphY3Rpb24+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDppbnN0YW5jZUlEPnhtcC5paWQ6MWRhNmIzOWItMjJlYS00ZGY5LWEzOTItODExMjMwMzE2NWNhPC9zdEV2dDppbnN0YW5jZUlEPgogICAgICAgICAgICAgICAgICA8c3RFdnQ6d2hlbj4yMDE4LTA1LTEwVDE4OjE5KzAzOjAwPC9zdEV2dDp3aGVuPgogICAgICAgICAgICAgICAgICA8c3RFdnQ6c29mdHdhcmVBZ2VudD5BZG9iZSBQaG90b3Nob3AgQ0MgMjAxNSAoTWFjaW50b3NoKTwvc3RFdnQ6c29mdHdhcmVBZ2VudD4KICAgICAgICAgICAgICAgICAgPHN0RXZ0OmNoYW5nZWQ+Lzwvc3RFdnQ6Y2hhbmdlZD4KICAgICAgICAgICAgICAgPC9yZGY6bGk+CiAgICAgICAgICAgICAgIDxyZGY6bGkgcmRmOnBhcnNlVHlwZT0iUmVzb3VyY2UiPgogICAgICAgICAgICAgICAgICA8c3RFdnQ6YWN0aW9uPnNhdmVkPC9zdEV2dDphY3Rpb24+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDppbnN0YW5jZUlEPnhtcC5paWQ6NmNiYTMzMmItMmRkMi00ODIwLWJjOWUtZGJkZWZiODQ5YzYxPC9zdEV2dDppbnN0YW5jZUlEPgogICAgICAgICAgICAgICAgICA8c3RFdnQ6d2hlbj4yMDE4LTA1LTEwVDE4OjE5OjQzKzAzOjAwPC9zdEV2dDp3aGVuPgogICAgICAgICAgICAgICAgICA8c3RFdnQ6c29mdHdhcmVBZ2VudD5BZG9iZSBQaG90b3Nob3AgQ0MgMjAxNSAoTWFjaW50b3NoKTwvc3RFdnQ6c29mdHdhcmVBZ2VudD4KICAgICAgICAgICAgICAgICAgPHN0RXZ0OmNoYW5nZWQ+Lzwvc3RFdnQ6Y2hhbmdlZD4KICAgICAgICAgICAgICAgPC9yZGY6bGk+CiAgICAgICAgICAgIDwvcmRmOlNlcT4KICAgICAgICAgPC94bXBNTTpIaXN0b3J5PgogICAgICAgICA8dGlmZjpPcmllbnRhdGlvbj4xPC90aWZmOk9yaWVudGF0aW9uPgogICAgICAgICA8dGlmZjpYUmVzb2x1dGlvbj43MjAwMDAvMTAwMDA8L3RpZmY6WFJlc29sdXRpb24+CiAgICAgICAgIDx0aWZmOllSZXNvbHV0aW9uPjcyMDAwMC8xMDAwMDwvdGlmZjpZUmVzb2x1dGlvbj4KICAgICAgICAgPHRpZmY6UmVzb2x1dGlvblVuaXQ+MjwvdGlmZjpSZXNvbHV0aW9uVW5pdD4KICAgICAgICAgPGV4aWY6Q29sb3JTcGFjZT42NTUzNTwvZXhpZjpDb2xvclNwYWNlPgogICAgICAgICA8ZXhpZjpQaXhlbFhEaW1lbnNpb24+MzcyPC9leGlmOlBpeGVsWERpbWVuc2lvbj4KICAgICAgICAgPGV4aWY6UGl4ZWxZRGltZW5zaW9uPjM3MzwvZXhpZjpQaXhlbFlEaW1lbnNpb24+CiAgICAgIDwvcmRmOkRlc2NyaXB0aW9uPgogICA8L3JkZjpSREY+CjwveDp4bXBtZXRhPgogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAg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";

        try {

            $users = User::select('users.*', 'user_roles.roleID')
                ->join('user_roles', 'users.id', 'user_roles.userID')
                ->where('users.email', $request->get('email'))
                ->get();

                if ($users->isEmpty()) {

                    Mail::to($request->get('email'))->send(new FeedbackEntryOfflineUserRegistrationMail($request->get('name'), $code));
                    
                    $user = new User;
                    $user->name = $request->get('name');
                    $user->email = $request->get('email');
                    $user->password = Hash::make($code);
                    $user->avatar = $avatar;
                    $user->save();

                    $role = new UserRole;
                    $role->userID = $user->id;
                    $role->roleID = 5;
                    $role->save();

                    $client = new UserClient;
                    $client->userID = $user->id;
                    $client->sexID = $request->get('sexID');
                    $client->number = $request->get('number');
                    $client->verification = $code;
                    $client->save();

                    $feedback = new Feedback;
                    $feedback->userID = $user->id;
                    $feedback->categoryID = $request->get('categoryID');
                    $feedback->content = $request->get('content');
                    $feedback->expire_on = date ( 'Y-m-d H:i:s' , strtotime ( '1 weekdays' ));
                    $feedback->save();

                    foreach ($request->get('photos') as $key => $p_value) {
                        
                        $evidence = new FeedbackEvidence;
                        $evidence->feedbackID = $feedback->id;
                        $evidence->evidence = $p_value['photo'];
                        $evidence->save();

                    }

                    $settings = SettingOfficeCategory::where('categoryID', $request->get('categoryID'))
                        ->where('isActive', TRUE)
                        ->get();

                        foreach ($settings as $key => $s_value) {
                            
                            $office = new FeedbackOffice;
                            $office->feedbackID = $feedback->id;
                            $office->officeID = $s_value->officeID;
                            $office->save();

                            $admins = UserAdmin::select('users.*')
                                ->join('users', 'user_admins.userID', 'users.id')
                                ->where('user_admins.officeID', $s_value->officeID)
                                ->get();

                                foreach ($admins as $key => $u_value) {
                                    
                                    Mail::to($u_value->email)->send(new FeedbackOfficeMail($u_value->name));

                                }

                        }

                        return response()->json([
                            'msg' => 'RECORD SAVED!',
                            'data' => $feedback
                        ], 200);

                } else {

                    if ($users[0]->roleID != 5) {
                        
                        return response()->json([
                            'isAdmin' => TRUE,
                            'msg' => 'EMAIL IS ADMIN ACCOUNT!',
                        ], 400);

                    } else {

                        $feedback = new Feedback;
                        $feedback->userID = $users[0]->id;
                        $feedback->categoryID = $request->get('categoryID');
                        $feedback->content = $request->get('content');
                        $feedback->expire_on = date ( 'Y-m-d H:i:s' , strtotime ( '1 weekdays' ));
                        $feedback->save();

                        foreach ($request->get('photos') as $key => $p_value) {
                            
                            $evidence = new FeedbackEvidence;
                            $evidence->feedbackID = $feedback->id;
                            $evidence->evidence = $p_value['photo'];
                            $evidence->save();

                        }

                        $settings = SettingOfficeCategory::where('categoryID', $request->get('categoryID'))
                            ->where('isActive', TRUE)
                            ->get();

                            foreach ($settings as $key => $s_value) {
                                
                                $office = new FeedbackOffice;
                                $office->feedbackID = $feedback->id;
                                $office->officeID = $s_value->officeID;
                                $office->save();

                                $admins = UserAdmin::select('users.*')
                                    ->join('users', 'user_admins.userID', 'users.id')
                                    ->where('user_admins.officeID', $s_value->officeID)
                                    ->get();

                                    foreach ($admins as $key => $u_value) {
                                        
                                        Mail::to($u_value->email)->send(new FeedbackOfficeMail($u_value->name));

                                    }

                            }

                            return response()->json([
                                'msg' => 'RECORD SAVED!',
                                'data' => $feedback
                            ], 200);

                    }

                }

        } catch (\Exception $e) {

            logger('Message logged from FeedbackController.offline', [$e->getMessage()]);
            return response()->json([
                'error' => 'Something went wrong storing record!',
                'data' => $e->getMessage()
            ], 400);

        }
    }

    /**
     * 
     */
    public function getReport($id)
    {
        try {

            $feedbacks = Feedback::select('feedback.*', 'users.name', 'users.email', 'users.avatar', 'preference_categories.label AS category')
                ->join('users', 'feedback.userID', 'users.id')
                ->join('user_clients', 'users.id', 'user_clients.userID')
                ->join('preference_categories', 'feedback.categoryID', 'preference_categories.id')
                ->where('feedback.id', $id)
                ->get();

            $rating_sum = FeedbackRating::join('feedback_responses', 'feedback_ratings.responseID', 'feedback_responses.id')
                ->whereNot('feedback_ratings.rating', 0)
                ->where('feedback_responses.feedbackID', $id)
                ->sum('feedback_ratings.rating');

            $rating_count = FeedbackRating::join('feedback_responses', 'feedback_ratings.responseID', 'feedback_responses.id')
                ->whereNot('feedback_ratings.rating', 0)
                ->where('feedback_responses.feedbackID', $id)
                ->count();

            $maximum = (5 * $rating_count);
            $rating = ((($rating_sum == 0 && $rating_count == 0) ? 0 : ($rating_sum / $maximum)) * 100);

            $offices = FeedbackOffice::select('preference_offices.code AS office', 'feedback_offices.isReceived', 'feedback_offices.isDelayed')
                    ->join('preference_offices', 'feedback_offices.officeID', 'preference_offices.id')
                    ->where('feedback_offices.feedbackID', $id)
                    ->get();

            $evidences = FeedbackEvidence::where('feedbackID', $id)
                ->get();

            $responses = FeedbackResponse::select('feedback_responses.*', 'users.name', 'users.avatar', 'user_roles.roleID', 'preference_offices.label AS office')
                ->join('users', 'feedback_responses.userID', 'users.id')
                ->join('user_roles', 'users.id', 'user_roles.userID')
                ->leftJoin('user_admins', 'users.id', 'user_admins.userID')
                ->leftJoin('preference_offices', 'user_admins.officeID', 'preference_offices.id')
                ->where('feedback_responses.feedbackID', $id)
                ->orderBy('feedback_responses.created_at', 'DESC')
                ->get();

            $account = User::where('id', auth()->user()->id)
                ->get();
            
            $today = Carbon::now(+8);
            $now = $today->toDayDateTimeString(); 

            $pdf = PDF::setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true, 'debugPng' => true])->loadView('report.FeedbackDetailReport', [
                'feedbacks' => $feedbacks,
                'evidences' => $evidences,
                'rating' => number_format($rating, 2),
                'responses' => $responses,
                'name' => $account[0]->name,
                'now' => $now
            ])->setPaper('a4', 'portrait');

            return $pdf->stream();

            return response()->json($arr);

        } catch (\Exception $e) {

            logger('Message logged from FeedbackController.getReport', [$e->getMessage()]);
            return response()->json([
                'error' => 'Something went wrong getting record!',
                'data' => $e->getMessage()
            ], 400);

        } 
    }

    /**
     * 
     */
    public function kiosk(FeedbackEntryKioskRequest $request)
    {
        date_default_timezone_set('Asia/Manila');
        
        try {

            $kiosk = new KioskRating;
            $kiosk->name = $request->get('name');
            $kiosk->number = $request->get('number');
            $kiosk->email = $request->get('email');
            $kiosk->officeID = $request->get('officeID');
            $kiosk->phyRating = $request->get('phyRating');
            $kiosk->serRating = $request->get('serRating');
            $kiosk->perRating = $request->get('perRating');
            $kiosk->ovrRating = $request->get('ovrRating');
            $kiosk->content = $request->get('suggestion');
            $kiosk->date = $request->get('date');
            $kiosk->save();
            
            $divisor = 0;
            if ($request->get('phyRating') != 0) {
                $phys = $divisor + 1;
                $divisor = $phys;
            }
            if ($request->get('serRating') != 0) {
                $sers = $divisor + 1;
                $divisor = $sers;
            }
            if ($request->get('perRating') != 0) {
                $pers = $divisor + 1;
                $divisor = $pers;
            }
            if ($request->get('ovrRating') != 0) {
                $ovrs = $divisor + 1;
                $divisor = $ovrs;
            }
            $rate = ($divisor == 0 ? 0 : (($request->get('phyRating') + $request->get('serRating') + $request->get('perRating') + $request->get('ovrRating')) / $divisor));

            $rating = new Rating;
            $rating->officeID = $request->get('officeID');
            $rating->rating = $rate;
            $rating->save();

            return response()->json([
                'msg' => 'RATING SAVED',
                'data' => $kiosk
            ], 200);

        } catch (\Exception $e) {

            logger('Message logged from FeedbackController.kiosk', [$e->getMessage()]);
            return response()->json([
                'error' => 'Something went wrong storing record!',
                'data' => $e->getMessage()
            ], 400);

        }
    }

    /**
     * 
     */
    public function kioskEndpoint(FeedbackEntryKioskRequest $request)
    {
        date_default_timezone_set('Asia/Manila');
        
        try {

            $kiosk = new KioskRating;
            $kiosk->name = $request->get('name');
            $kiosk->number = $request->get('number');
            $kiosk->email = $request->get('email');
            $kiosk->kioskID = $request->get('personnelID');
            $kiosk->phyRating = $request->get('phyRating');
            $kiosk->serRating = $request->get('serRating');
            $kiosk->perRating = $request->get('perRating');
            $kiosk->ovrRating = $request->get('ovrRating');
            $kiosk->content = $request->get('suggestion');
            $kiosk->save();
            
            $divisor = 0;
            if ($request->get('phyRating') != 0) {
                $phys = $divisor + 1;
                $divisor = $phys;
            }
            if ($request->get('serRating') != 0) {
                $sers = $divisor + 1;
                $divisor = $sers;
            }
            if ($request->get('perRating') != 0) {
                $pers = $divisor + 1;
                $divisor = $pers;
            }
            if ($request->get('ovrRating') != 0) {
                $ovrs = $divisor + 1;
                $divisor = $ovrs;
            }
            $rate = ($divisor == 0 ? 0 : (($request->get('phyRating') + $request->get('serRating') + $request->get('perRating') + $request->get('ovrRating')) / $divisor));

            $rating = new Rating;
            $rating->officeID = $request->get('officeID');
            $rating->rating = $rate;
            $rating->save();

            return response()->json([
                'msg' => 'RATING SAVED',
                'data' => $kiosk
            ], 200);

        } catch (\Exception $e) {

            logger('Message logged from FeedbackController.kiosk', [$e->getMessage()]);
            return response()->json([
                'error' => 'Something went wrong storing record!',
                'data' => $e->getMessage()
            ], 400);

        }
    }
}
