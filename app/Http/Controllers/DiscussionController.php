<?php

namespace App\Http\Controllers;

use App\Models\Discussion;
use App\Models\DiscussionThread;
use App\Models\DiscussionPoll;
use App\Models\DiscussionAnswer;
use App\Models\User;
use App\Models\SettingOfficeCategory;
use Illuminate\Http\Request;

use App\Http\Requests\DiscussionRequest;
use App\Http\Requests\DiscussionThreadRequest;
use App\Http\Requests\DiscussionPollRequest;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use PDF;

class DiscussionController extends Controller
{
    /**
     * 
     */
    public function getOverview($id)
    {
        try {

            $users = User::select('users.*', 'user_roles.roleID', 'preference_years.label AS year', 'user_admins.officeID')
                ->join('user_admins', 'users.id', 'user_admins.userID')
                ->join('user_roles', 'users.id', 'user_roles.userID')
                ->join('preference_years', 'user_admins.yearID', 'preference_years.id')
                ->where('users.id', $id)
                ->get();

                if ($users[0]->roleID == 1 OR $users[0]->roleID == 2 OR $users[0]->roleID == 3) {

                    $total = Discussion::whereYear('created_at', $users[0]->year)
                        ->count();

                    $active = Discussion::whereYear('created_at', $users[0]->year)
                        ->where('isActive', TRUE)
                        ->count();

                    $inactive = Discussion::whereYear('created_at', $users[0]->year)
                        ->where('isActive', FALSE)
                        ->count();

                    $joiner = DiscussionThread::join('discussions', 'discussion_threads.discussionID', 'discussions.id')
                        ->whereYear('discussions.created_at', $users[0]->year)
                        ->count();

                    $thread = Discussion::select('discussions.*', 'discussions.id as dID', DB::raw("(SELECT COUNT(*) FROM discussion_threads WHERE discussionID = dID) AS count"))
                        ->whereYear('created_at', $users[0]->year)
                        ->orderBy('count', 'DESC')
                        ->limit(5)
                        ->get();

                        return response()->json([
                            'totalDiscussion' => $total,
                            'totalActive' => $active,
                            'totalInactive' => $inactive,
                            'totalJoiner' => $joiner,
                            'joiner' => $thread
                        ]);

                } else {

                    $total = Discussion::join('setting_office_categories', 'discussions.categoryID', 'setting_office_categories.categoryID')
                        ->whereYear('discussions.created_at', $users[0]->year)
                        ->where('setting_office_categories.officeID', $users[0]->officeID)
                        ->count();

                    $active = Discussion::join('setting_office_categories', 'discussions.categoryID', 'setting_office_categories.categoryID')
                        ->whereYear('discussions.created_at', $users[0]->year)
                        ->where('setting_office_categories.officeID', $users[0]->officeID)
                        ->where('discussions.isActive', TRUE)
                        ->count();

                    $inactive = Discussion::join('setting_office_categories', 'discussions.categoryID', 'setting_office_categories.categoryID')
                        ->whereYear('discussions.created_at', $users[0]->year)
                        ->where('setting_office_categories.officeID', $users[0]->officeID)
                        ->where('discussions.isActive', FALSE)
                        ->count();

                    $joiner = DiscussionThread::join('discussions', 'discussion_threads.discussionID', 'discussions.id')
                        ->join('setting_office_categories', 'discussions.categoryID', 'setting_office_categories.categoryID')
                        ->where('setting_office_categories.officeID', $users[0]->officeID)
                        ->whereYear('discussions.created_at', $users[0]->year)
                        ->count();

                    $thread = Discussion::select('discussions.*', 'discussions.id as dID', DB::raw("(SELECT COUNT(*) FROM discussion_threads WHERE discussionID = dID) AS count"))
                        ->join('setting_office_categories', 'discussions.categoryID', 'setting_office_categories.categoryID')
                        ->where('setting_office_categories.officeID', $users[0]->officeID)
                        ->whereYear('discussions.created_at', $users[0]->year)
                        ->orderBy('count', 'DESC')
                        ->limit(5)
                        ->get();

                        return response()->json([
                            'totalDiscussion' => $total,
                            'totalActive' => $active,
                            'totalInactive' => $inactive,
                            'totalJoiner' => $joiner,
                            'joiner' => $thread
                        ]);

                }

        } catch (\Exception $e) {

            logger('Message logged from DiscussionController.getOverview', [$e->getMessage()]);
            return response()->json([
                'error' => 'Something went wrong getting record',
                'msg' => $e->getMessage()
            ], 400);

        }
    }

    /**
     * 
     */
    public function getList(Request $request, $id)
    {
        try {

            $users = User::select('users.*', 'user_roles.roleID', 'preference_years.label AS year', 'user_admins.officeID')
                ->join('user_admins', 'users.id', 'user_admins.userID')
                ->join('user_roles', 'users.id', 'user_roles.userID')
                ->join('preference_years', 'user_admins.yearID', 'preference_years.id')
                ->where('users.id', $id)
                ->get();

                if ($users[0]->roleID == 1 OR $users[0]->roleID == 2 OR $users[0]->roleID == 3) {

                    $discussions = Discussion::select('discussions.*', 'preference_categories.label AS category')
                        ->join('preference_categories', 'discussions.categoryID', 'preference_categories.id')
                        ->whereYear('discussions.created_at', $users[0]->year)
                        ->where('discussions.title', 'LIKE', '%'.$request->get('filter').'%')
                        ->get();

                        $arr = [];

                        foreach ($discussions as $key => $d_value) {
                            
                            $offices = SettingOfficeCategory::select('preference_offices.code AS office')
                                ->join('preference_offices', 'setting_office_categories.officeID', 'preference_offices.id')
                                ->where('setting_office_categories.categoryID', $d_value->categoryID)
                                ->where('setting_office_categories.isActive', TRUE)
                                ->get();

                                $expiry = Carbon::createFromDate($d_value->expire_on);
                                $today = Carbon::now();

                                $array = [
                                    'id' => $d_value->id,
                                    'categoryID' => $d_value->categoryID,
                                    'title' => $d_value->title,
                                    'content' => $d_value->content,
                                    'file' => $d_value->file,
                                    'day' => $expiry->diffInDays($today),
                                    'expiry_on' => $expiry->toFormattedDateString(),
                                    'isActive' => $d_value->isActive,
                                    'category' => $d_value->category,
                                    'offices' => $offices
                                ];

                                array_push($arr, $array);

                        }

                        return response()->json($arr);

                } else {

                    $discussions = Discussion::select('discussions.*', 'preference_categories.label AS category')
                        ->join('preference_categories', 'discussions.categoryID', 'preference_categories.id')
                        ->join('setting_office_categories', 'preference_categories.id', 'setting_office_categories.categoryID')
                        ->where('setting_office_categories.officeID', $users[0]->officeID)
                        ->whereYear('discussions.created_at', $users[0]->year)
                        ->where('discussions.title', 'LIKE', '%'.$request->get('filter').'%')
                        ->get();

                        $arr = [];

                        foreach ($discussions as $key => $d_value) {
                            
                            $offices = SettingOfficeCategory::select('preference_offices.code AS office')
                                ->join('preference_offices', 'setting_office_categories.officeID', 'preference_offices.id')
                                ->where('setting_office_categories.categoryID', $d_value->categoryID)
                                ->where('setting_office_categories.isActive', TRUE)
                                ->get();

                                $expiry = Carbon::createFromDate($d_value->expire_on);
                                $today = Carbon::now();

                                $array = [
                                    'id' => $d_value->id,
                                    'categoryID' => $d_value->categoryID,
                                    'title' => $d_value->title,
                                    'content' => $d_value->content,
                                    'file' => $d_value->file,
                                    'day' => $expiry->diffInDays($today),
                                    'expiry_on' => $expiry->toFormattedDateString(),
                                    'isActive' => $d_value->isActive,
                                    'category' => $d_value->category,
                                    'offices' => $offices
                                ];

                                array_push($arr, $array);

                        }

                        return response()->json($arr);

                }

        } catch (\Exception $e) {

            logger('Message logged from DiscussionController.getList', [$e->getMessage()]);
            return response()->json([
                'error' => 'Something went wrong getting record',
                'msg' => $e->getMessage()
            ], 400);

        }
    }

    /**
     * 
     */
    public function store(DiscussionRequest $request)
    {
        try {

            $discussion = new Discussion;
            $discussion->title = $request->get('title');
            $discussion->content = $request->get('content');
            $discussion->categoryID = $request->get('categoryID');
            $discussion->expire_on = Carbon::now()->addDays($request->get('day'));
            $discussion->file = $request->get('photo');
            $discussion->save();

            return response()->json([
                'msg' => 'RECORD STORED!',
                'data' => $discussion
            ], 200);

        } catch (\Exception $e) {

            logger('Message logged from DiscussionController.store', [$e->getMessage()]);
            return response()->json([
                'error' => 'Something went wrong storing record',
                'msg' => $e->getMessage()
            ], 400);

        }
    }

    /**
     * 
     */
    public function update(Request $request, $id)
    {
        try {

            $discussion = Discussion::findOrFail($id);
            $discussion->title = $request->get('title');
            $discussion->content = $request->get('content');
            $discussion->categoryID = $request->get('categoryID');
            $discussion->expire_on = Carbon::now()->addDays($request->get('day'));
            $discussion->file = $request->get('photo');
            $discussion->save();

            return response()->json([
                'msg' => 'RECORD MODIFIED!',
                'data' => $discussion
            ], 200);

        } catch (\Exception $e) {

            logger('Message logged from DiscussionController.update', [$e->getMessage()]);
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
        try {

            $discussion = Discussion::findOrFail($id);
            $discussion->isActive = FALSE;
            $discussion->save();

            return response()->json([
                'msg' => 'RECORD DISABLE!',
                'data' => $discussion
            ], 200);

        } catch (\Exception $e) {

            logger('Message logged from DiscussionController.disable', [$e->getMessage()]);
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
        try {

            $discussion = Discussion::findOrFail($id);
            $discussion->isActive = TRUE;
            $discussion->save();

            return response()->json([
                'msg' => 'RECORD ENABLE!',
                'data' => $discussion
            ], 200);

        } catch (\Exception $e) {

            logger('Message logged from DiscussionController.enable', [$e->getMessage()]);
            return response()->json([
                'error' => 'Something went wrong enabling record',
                'msg' => $e->getMessage()
            ], 400);

        }
    }

    /**
     * 
     */
    public function getDetail($id)
    {
        try {

            $discussions = Discussion::select('discussions.*', 'preference_categories.label AS category')
                ->join('preference_categories', 'discussions.categoryID', 'preference_categories.id')
                ->where('discussions.id', $id)
                ->get();

                return response()->json($discussions);

        } catch (\Exception $e) {

            logger('Message logged from DiscussionController.getDetail', [$e->getMessage()]);
            return response()->json([
                'error' => 'Something went wrong getting record',
                'msg' => $e->getMessage()
            ], 400);

        }
    }

    /**
     * 
     */
    public function getThread($id)
    {
        try {

            $discussions = Discussionthread::select('discussion_threads.*', 'users.name', 'users.email', 'users.avatar', 'preference_offices.label AS office')
                ->join('users', 'discussion_threads.userID', 'users.id')
                ->leftJoin('user_admins', 'users.id', 'user_admins.userID')
                ->leftJoin('user_clients', 'users.id', 'user_clients.userID')
                ->leftJoin('preference_offices', 'user_admins.officeID', 'preference_offices.id')
                ->where('discussion_threads.discussionID', $id)
                ->orderBy('discussion_threads.created_at', 'DESC')
                ->get();

                return response()->json($discussions);

        } catch (\Exception $e) {

            logger('Message logged from DiscussionController.getThread', [$e->getMessage()]);
            return response()->json([
                'error' => 'Something went wrong getting record',
                'msg' => $e->getMessage()
            ], 400);

        }
    }

    /**
     * 
     */
    public function getAnswer($id)
    {
        try {

            $discussions = DiscussionPoll::where('discussionID', $id)
                ->get();

                $arr = [];

                foreach ($discussions as $key => $d_value) {

                    $count = DiscussionAnswer::where('answerID', $d_value->id)
                        ->count();

                        $array = [
                            'id' => $d_value->id,
                            'label' => $d_value->label,
                            'isActive' => $d_value->isActive,
                            'count' => $count
                        ];

                        array_push($arr, $array);

                }

                return response()->json($arr);

        } catch (\Exception $e) {

            logger('Message logged from DiscussionController.getAnswer', [$e->getMessage()]);
            return response()->json([
                'error' => 'Something went wrong getting record',
                'msg' => $e->getMessage()
            ], 400);

        }
    }

    /**
     * 
     */
    public function thread(DiscussionThreadRequest $request, $id)
    {
        try {

            $thread = new DiscussionThread;
            $thread->discussionID = $request->get('discussionID');
            $thread->userID= $id;
            $thread->content = $request->get('content');
            $thread->save();

            return response()->json([
                'msg' => 'RECORD STORED!',
                'data' => $thread
            ], 200);

        } catch (\Exception $e) {

            logger('Message logged from DiscussionController.thread', [$e->getMessage()]);
            return response()->json([
                'error' => 'Something went wrong storing record',
                'msg' => $e->getMessage()
            ], 400);

        }
    }

    /**
     * 
     */
    public function answer(DiscussionPollRequest $request)
    {
        try {
            
            $answer = new DiscussionPoll;
            $answer->discussionID = $request->get('discussionID');
            $answer->label = $request->get('content');
            $answer->save();

            return response()->json([
                'msg' => 'RECORD SAVED!',
                'data' => $answer
            ], 200);

        } catch (\Exception $e) {

            logger('Message logged from DiscussionController.answer', [$e->getMessage()]);
            return response()->json([
                'error' => 'Something went wrong storing record',
                'msg' => $e->getMessage()
            ], 400);

        }
    }

    /**
     * 
     */
    public function disableThread($id)
    {
        try {

            $thread = DiscussionThread::findOrFail($id);
            $thread->isActive = FALSE;
            $thread->save();

            return response()->json([
                'msg' => 'RECORD DISABLED!',
                'data' => $thread
            ], 200);

        } catch (\Exception $e) {

            logger('Message logged from DiscussionController.disableThread', [$e->getMessage()]);
            return response()->json([
                'error' => 'Something went wrong disabling record',
                'msg' => $e->getMessage()
            ], 400);

        }
    }

    /**
     * 
     */
    public function enableThread($id)
    {
        try {

            $thread = DiscussionThread::findOrFail($id);
            $thread->isActive = TRUE;
            $thread->save();

            return response()->json([
                'msg' => 'RECORD ENABLED!',
                'data' => $thread
            ], 200);

        } catch (\Exception $e) {

            logger('Message logged from DiscussionController.enableThread', [$e->getMessage()]);
            return response()->json([
                'error' => 'Something went wrong enabling record',
                'msg' => $e->getMessage()
            ], 400);

        }
    }

    /**
     * 
     */
    public function disableAnswer($id)
    {
        try {

            $answer = DiscussionPoll::findOrFail($id);
            $answer->isActive = FALSE;
            $answer->save();

            return response()->json([
                'msg' => 'RECORD DISABLED!',
                'data' => $answer
            ], 200);

        } catch (\Exception $e) {

            logger('Message logged from DiscussionController.disableAnswer', [$e->getMessage()]);
            return response()->json([
                'error' => 'Something went wrong disabling record',
                'msg' => $e->getMessage()
            ], 400);

        }
    }

    /**
     * 
     */
    public function enableAnswer($id)
    {
        try {

            $answer = DiscussionPoll::findOrFail($id);
            $answer->isActive = TRUE;
            $answer->save();

            return response()->json([
                'msg' => 'RECORD ENABLED!',
                'data' => $answer
            ], 200);

        } catch (\Exception $e) {

            logger('Message logged from DiscussionController.enableAnswer', [$e->getMessage()]);
            return response()->json([
                'error' => 'Something went wrong enabling record',
                'msg' => $e->getMessage()
            ], 400);

        }
    }

    /**
     * 
     */
    public function getReport($id)
    {
        try {

            $discussions = Discussion::select('discussions.*', 'preference_categories.label AS category')
                ->join('preference_categories', 'discussions.categoryID', 'preference_categories.id')
                ->where('discussions.id', $id)
                ->get();

            $threads = Discussionthread::select('discussion_threads.*', 'users.name', 'users.email', 'users.avatar', 'preference_offices.label AS office')
                ->join('users', 'discussion_threads.userID', 'users.id')
                ->leftJoin('user_admins', 'users.id', 'user_admins.userID')
                ->leftJoin('user_clients', 'users.id', 'user_clients.userID')
                ->leftJoin('preference_offices', 'user_admins.officeID', 'preference_offices.id')
                ->where('discussion_threads.discussionID', $id)
                ->orderBy('discussion_threads.created_at', 'DESC')
                ->get();

            $answers = DiscussionPoll::where('isActive', TRUE)
                ->where('discussionID', $id)
                ->get();

                $arr = [];

                foreach ($answers as $key => $a_value) {
                    
                    $count = DiscussionAnswer::where('answerID', $a_value->id)
                        ->count();

                        $array = [
                            'label' => $a_value->label,
                            'count' => $count
                        ];

                        array_push($arr, $array);

                }

            $today = Carbon::now(+8);
            $now = $today->toDayDateTimeString(); 

            $pdf = PDF::loadView('report.DiscussionDetailReport', [
                'discussions' => $discussions,
                'threads' => $threads,
                'poll' => $arr,
                'now' => $now
            ])->setPaper('a4', 'portrait');

            return $pdf->stream();

        } catch (\Exception $e) {

            logger('Message logged from DiscussionController.report', [$e->getMessage()]);
            return response()->json([
                'error' => 'Something went wrong reporting record',
                'msg' => $e->getMessage()
            ], 400);

        } 
    }
}
