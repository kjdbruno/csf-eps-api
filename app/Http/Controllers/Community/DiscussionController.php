<?php

namespace App\Http\Controllers\Community;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Discussion;
use App\Models\DiscussionThread;
use App\Models\DiscussionPoll;
use App\Models\DiscussionAnswer;

use App\Http\Requests\Community\DiscussionThreadRequest;
use App\Http\Requests\Community\DiscussionPollRequest;

use Illuminate\Support\Facades\DB;

class DiscussionController extends Controller
{
    /**
     * 
     */
    public function index()
    {
        date_default_timezone_set('Asia/Manila');

        try {

            $discussions = Discussion::select('discussions.*', 'preference_categories.label AS category')
                ->join('preference_categories', 'discussions.categoryID', 'preference_categories.id')
                ->where('discussions.isActive', TRUE)
                ->orderBy('discussions.created_at', 'DESC')
                ->get();

                return response()->json($discussions);

        } catch (\Exception $e) {

            logger('Message logged from DiscussionController.index', [$e->getMessage()]);
            return response()->json([
                'error' => 'Something went wrong getting record!',
                'data' => $e->getMessage()
            ], 400);

        }
    }

    /**
     * 
     */
    public function getThread($id)
    {
        date_default_timezone_set('Asia/Manila');

        try {

            $thread = DiscussionThread::select('discussion_threads.*', 'users.name', 'users.email', 'users.avatar', 'preference_offices.label AS office')
                ->join('users', 'discussion_threads.userID', 'users.id')
                ->leftJoin('user_admins', 'users.id', 'user_admins.userID')
                ->leftJoin('user_clients', 'users.id', 'user_clients.userID')
                ->leftJoin('preference_offices', 'user_admins.officeID', 'preference_offices.id')
                ->where('discussion_threads.discussionID', $id)
                ->orderBy('discussion_threads.created_at', 'DESC')
                ->get();

                return response()->json($thread);

        } catch (\Exception $e) {

            logger('Message logged from DiscussionController.getThread', [$e->getMessage()]);
            return response()->json([
                'error' => 'Something went wrong getting record!',
                'data' => $e->getMessage()
            ], 400);

        } 
    }

    /**
     * 
     */
    public function thread(DiscussionThreadRequest $request, $id)
    {
        date_default_timezone_set('Asia/Manila');

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
                'error' => 'Something went wrong storing record!',
                'data' => $e->getMessage()
            ], 400);

        }
    }

    /**
     * 
     */
    public function getPoll($discussionID, $id)
    {
        date_default_timezone_set('Asia/Manila');

        try {

            $discussions = DiscussionPoll::where('discussionID', $discussionID)
                ->where('isActive', TRUE)
                ->get();

                $count = DiscussionAnswer::where('userID', $id)
                    ->where('discussionID', $discussionID)
                    ->count();

                return response()->json([
                    'list' => $discussions,
                    'isDone' => ($count <= 0 ? false : true),
                    'msg' => 'poll already answered!'
                ]);

        } catch (\Exception $e) {

            logger('Message logged from DiscussionController.getPoll', [$e->getMessage()]);
            return response()->json([
                'error' => 'Something went wrong getting record!',
                'data' => $e->getMessage()
            ], 400);

        }
    }

    /**
     * 
     */
    public function answer(DiscussionPollRequest $request, $id)
    {
        date_default_timezone_set('Asia/Manila');
        try {

            $poll = new DiscussionAnswer;
            $poll->userID = $id;
            $poll->discussionID = $request->get('discussionID');
            $poll->answerID = $request->get('answerID');
            $poll->save();

            return response()->json([
                'msg' => 'RECORD STORED!',
                'data' => $poll
            ], 200);

        } catch (\Exception $e) {

            logger('Message logged from DiscussionController.answer', [$e->getMessage()]);
            return response()->json([
                'error' => 'Something went wrong storing record!',
                'data' => $e->getMessage()
            ], 400);

        }  
    }
}
