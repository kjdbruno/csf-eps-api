<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use App\Models\UserAdmin;

use App\Http\Requests\MyAccountUpdateRequest;
use App\Http\Requests\MyAccountSettingRequest;
use App\Http\Requests\MyAccountResetRequest;

use Illuminate\Support\Facades\Hash;

class MyController extends Controller
{
    /**
     * Update the specified resource in storage.
     */
    public function updateUser(MyAccountUpdateRequest $request, $id)
    {
        date_default_timezone_set('Asia/Manila');

        try {

            $user = User::findOrFail($id);
            $user->name = $request->get('name');
            $user->save();

            $admin = UserAdmin::where('userID', $id)
                ->update([
                    'avatar' => $request->get('avatar')
                ]);

                return response()->json([
                    'msg' => 'RECORD MODIFIED!',
                    'data' => $user
                ], 200);

        } catch (\Exception $e) {

            logger('Message logged from MyController.updateUser', [$e->getMessage()]);
            return response()->json([
                'error' => 'Something went wrong updating record!',
                'data' => $e->getMessage()
            ], 400);

        } 
    }

    /**
     * Update the specified resource in storage.
     */
    public function settingUser(MyAccountSettingRequest $request, $id)
    {
        date_default_timezone_set('Asia/Manila');

        try {

            $admin = UserAdmin::where('userID', $id)
                ->update([
                    'yearID' => $request->get('yearID')
                ]);

                return response()->json([
                    'msg' => 'RECORD SET!',
                    'data' => $admin
                ], 200);

        } catch (\Exception $e) {

            logger('Message logged from MyController.settingUser', [$e->getMessage()]);
            return response()->json([
                'error' => 'Something went wrong setting record!',
                'data' => $e->getMessage()
            ], 400);

        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function resetUser(MyAccountResetRequest $request, $id)
    {
        date_default_timezone_set('Asia/Manila');
        
        try {

            $user = User::findOrFail($id);
            $user->password = Hash::make($request->get('password'));
            $user->save();

            return response()->json([
                'msg' => 'RECORD RESET!',
                'data' => $user
            ], 200);

        } catch (\Exception $e) {

            logger('Message logged from MyController.resetUser', [$e->getMessage()]);
            return response()->json([
                'error' => 'Something went wrong resetting record!',
                'data' => $e->getMessage()
            ], 400);

        } 
    }
}
