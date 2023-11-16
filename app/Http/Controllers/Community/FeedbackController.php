<?php

namespace App\Http\Controllers\Community;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Feedback;
use App\Models\FeedbackEvidence;
use App\Models\FeedbackResponse;
use App\Models\FeedbackRating;
use App\Models\Rating;
use App\Models\FeedbackOffice;
use App\Models\SettingOfficeCategory;
use App\Models\UserAdmin;
use App\Models\UserClient;

use App\Http\Requests\Community\FeedbackPostingRequest;
use App\Http\Requests\Community\FeedbackResponseRequest;
use App\Http\Requests\Community\FeedbackResponseRatingRequest;

use Carbon\Carbon;

use App\Mail\FeedbackOfficeMail;
use Illuminate\Support\Facades\Mail;

class FeedbackController extends Controller
{
    /**
     * 
     */
    public function index(Request $request, $id)
    {
        date_default_timezone_set('Asia/Manila');

        try {

            $feedbacks = Feedback::select('feedback.*', 'users.name', 'users.avatar')
                ->join('users', 'users.id', 'feedback.userID')
                ->where('users.id', $id)
                ->orderBy('feedback.created_at', 'DESC')
                ->get();

                $arr = [];

                foreach ($feedbacks as $key => $f_value) {
                    
                    $evidences = FeedbackEvidence::where('feedbackID', $f_value->id)
                        ->get();

                        $array = [
                            'name' => $f_value->name,
                            'avatar' => $f_value->avatar,
                            //
                            'id' => $f_value->id,
                            'status' => $f_value->status,
                            'content' => $f_value->content,
                            'created_at' => $f_value->created_at,
                            //
                            'photos' => $evidences
                        ];

                        array_push($arr, $array);

                }

                return response()->json($arr);

        } catch (\Exception $e) {

            logger('Message logged from FeedbackController.index', [$e->getMessage()]);
            return response()->json([
                'error' => 'Something went wrong getting record!',
                'data' => $e->getMessage()
            ], 400);

        }
    }
    /**
     * 
     */
    public function store(FeedbackPostingRequest $request)
    {
        date_default_timezone_set('Asia/Manila');

        try {

            $feedback = new Feedback;
            $feedback->userID = $request->get('userID');
            $feedback->categoryID = $request->get('categoryID');
            $feedback->content = $request->get('content');
            $feedback->expire_on = date ( 'Y-m-j' , strtotime ( '1 weekdays' ));
            // $feedback->expire_on = Carbon::now()->addWeekdays(1);
            $feedback->save();

            foreach ($request->get('photos') as $key => $e_value) {
                
                $photo = new FeedbackEvidence;
                $photo->feedbackID = $feedback->id;
                $photo->evidence = $e_value['photo'];
                $photo->save();

            }

            $settings = SettingOfficeCategory::where('categoryID', $request->get('categoryID'))
                ->where('isActive', TRUE)
                ->get();

                foreach ($settings as $key => $s_value) {
                    
                    $office = new FeedbackOffice;
                    $office->feedbackID = $feedback->id;
                    $office->officeID = $s_value->officeID;
                    $office->save();

                    $users = UserAdmin::select('users.*')
                        ->join('users', 'user_admins.userID', 'users.id')
                        ->where('user_admins.officeID', $s_value->officeID)
                        ->get();

                        foreach ($users as $key => $u_value) {
                            
                            Mail::to($u_value->email)->send(new FeedbackOfficeMail($u_value->name));

                        }

                }

                return response()->json([
                    'msg' => 'FEEDBACK SAVED!',
                    'data' => $feedback
                ], 200);

        } catch (\Exception $e) {

            logger('Message logged from FeedbackController.store', [$e->getMessage()]);
            return response()->json([
                'error' => 'Something went wrong storing record!',
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

            $responses = FeedbackResponse::select('feedback_responses.*', 'users.name', 'users.avatar', 'preference_offices.label AS office', 'user_roles.roleID')
                ->join('users', 'feedback_responses.userID', 'users.id')
                ->join('user_roles', 'users.id', 'user_roles.userID')
                ->leftJoin('user_admins', 'users.id', 'user_admins.userID')
                ->leftJoin('preference_offices', 'user_admins.officeID', 'preference_offices.id')
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
                            'office' => $r_value->office,
                            'avatar' => $r_value->avatar,
                            'roleID' => $r_value->roleID,
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
    public function response(FeedbackResponseRequest $request)
    {
        date_default_timezone_set('Asia/Manila');

        try {

            $response = new FeedbackResponse;
            $response->feedbackID = $request->get('feedbackID');
            $response->userID = $request->get('userID');
            $response->status = 2;
            $response->content = $request->get('content');
            $response->file = '';
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
    public function rating(FeedbackResponseRatingRequest $request)
    {
        date_default_timezone_set('Asia/Manila');
        
        try {

            $responses = FeedbackResponse::select('user_admins.officeID')
                ->join('user_admins', 'feedback_responses.userID', 'user_admins.userID')
                ->where('feedback_responses.id', $request->get('id'))
                ->get();

            $rating = new Rating;
            $rating->officeID = $responses[0]->officeID;
            $rating->rating = $request->get('rating');
            $rating->save();

            $feedback = new FeedbackRating;
            $feedback->responseID = $request->get('id');
            $feedback->rating = $request->get('rating');
            $feedback->save();

            return response()->json([
                'msg' => 'RECORD SAVED!',
                'data' => $rating
            ], 200);

        } catch (\Exception $e) {

            logger('Message logged from FeedbackController.rating', [$e->getMessage()]);
            return response()->json([
                'error' => 'Something went wrong storing record!',
                'data' => $e->getMessage()
            ], 400);

        } 
    }
}
