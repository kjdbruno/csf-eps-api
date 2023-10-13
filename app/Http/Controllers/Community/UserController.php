<?php

namespace App\Http\Controllers\Community;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\UserClient;
use App\Models\User;

use App\Mail\UserVerificationMail;
use Illuminate\Support\Facades\Mail;

use App\Http\Requests\Community\UserVerificationRequest;
use App\Http\Requests\Community\UserUpdateRequest;
use App\Http\Requests\Community\UserResetRequest;

use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * 
     */
    public function verifyUser(UserVerificationRequest $request, $id)
    {
        try {
            $user = User::where('id', $id)
                ->get();

                if (UserClient::where('userID', $id)->where('verification', $request->get('code'))->exists()) {

                    // send email
                    Mail::to($user[0]->email)->send(new UserVerificationMail($user[0]->name));

                    $client = UserClient::where('userID', $id)
                        ->where('verification', $request->get('code'))
                        ->update([
                            'sexID' => $request->get('sexID'),
                            'number' => $request->get('number'),
                            'isVerified' => TRUE
                        ]);

                        return response()->json([
                            'msg' => 'RECORD VERIFIED!',
                            'data' => $client
                        ], 200);

                } else {
                    return response()->json([
                        'msg' => 'ACCOUNT DOES NOT MATCH!',
                        'exist' => true
                    ], 400);
                }

        } catch (\Exception $e) {

            logger('Message logged from UserController.verifyUser', [$e->getMessage()]);
            return response()->json([
                'error' => 'Something went wrong verifying record!',
                'data' => $e->getMessage()
            ], 400);

        }
    }

    /**
     * 
     */
    public function updateUser(UserUpdateRequest $request, $id)
    {
        try {

            $user = User::findOrFail($id);
            $user->name = $request->get('name');
            $user->avatar = $request->get('avatar');
            $user->save();

            $client = UserClient::where('userID', $id)
                ->update([
                    'sexID' => $request->get('sexID'),
                    'number' => $request->get('number')
                ]);

                return response()->json([
                    'msg' => 'RECORD MODIFIED!',
                    'data' => $client
                ], 200);

        } catch (\Exception $e) {

            logger('Message logged from UserController.updateUser', [$e->getMessage()]);
            return response()->json([
                'error' => 'Something went wrong modifying record!',
                'data' => $e->getMessage()
            ], 400);

        }
    }

    /**
     * 
     */
    public function resetUser(UserResetRequest $request, $id)
    {
        try {

            $user = User::findOrFail($id);
            $user->password = Hash::make($request->get('password'));
            $user->save();

            return response()->json([
                'msg' => 'RECORD RESET!',
                'data' => $user
            ], 200);

        } catch (\Exception $e) {

            logger('Message logged from UserController.resetUser', [$e->getMessage()]);
            return response()->json([
                'error' => 'Something went wrong resetting record!',
                'data' => $e->getMessage()
            ], 400);

        } 
    }
}
