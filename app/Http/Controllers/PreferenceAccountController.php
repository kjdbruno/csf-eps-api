<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\UserAdmin;
use App\Models\UserRole;
use App\Models\PreferenceRole;
use App\Models\PreferenceOffice;
use App\Models\PreferencePosition;
use Illuminate\Http\Request;

use App\Http\Requests\PreferenceAccountRequest;
use App\Http\Requests\PreferenceAccountVerifyRequest;
use App\Http\Requests\PreferenceAccountModifyRequest;
use App\Http\Requests\PreferenceAccountResetRequest;

use App\Mail\PreferenceAccountRegistrationMail;
use App\Mail\PreferenceAccountVerificationMail;
use Illuminate\Support\Facades\Mail;

class PreferenceAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        date_default_timezone_set('Asia/Manila');

        try {

            $preference = User::select('users.*', 'users.id AS uID', 'user_admins.officeID', 'user_admins.positionID', 'user_admins.employeeID', 'user_admins.yearID', 'preference_roles.label AS role','preference_offices.code AS office', 'preference_positions.label AS position', 'user_admins.isActive', 'user_admins.isVerified', 'user_roles.roleID', DB::raw("(SELECT COUNT(*) FROM user_admins WHERE userID = uID AND isActive = TRUE) AS adm"))
                ->leftJoin('user_admins', 'users.id', 'user_admins.userID')
                ->leftJoin('user_roles', 'users.id', 'user_roles.userID')
                ->leftJoin('preference_roles', 'user_roles.roleID', 'preference_roles.id')
                ->leftJoin('preference_offices', 'user_admins.officeID', 'preference_offices.id')
                ->leftJoin('preference_positions', 'user_admins.positionID', 'preference_positions.id')
                ->orderBy('users.created_at', 'DESC')
                ->where('users.name', 'LIKE','%'.$request->get('filter').'%')
                // ->whereNot('user_roles.roleID', 5)
                ->get();
                return response()->json($preference);

        } catch (\Exception $e) {

            logger('Message logged from PreferenceAccountController.index', [$e->getMessage()]);
            return response()->json([
                'error' => 'Something went wrong getting record',
                'msg' => $e->getMessage()
            ], 400);

        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\PreferenceAccountRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PreferenceAccountRequest $request)
    {
        date_default_timezone_set('Asia/Manila');

        try {

            Mail::to($request->get('email'))->send(new PreferenceAccountRegistrationMail($request->get('name')));

            $preference = new User;
            $preference->name = $request->get('name');
            $preference->email = $request->get('email');
            $preference->password = Hash::make($request->get('password'));
            $preference->avatar = $request->get('avatar');
            $preference->save();

            $role = new UserRole;
            $role->userID = $preference->id;
            $role->roleID = $request->get('roleID');
            $role->save();

            return response()->json([
                'msg' => 'RECORD STORED!',
                'data' => $preference
            ], 200);

        } catch (\Exception $e) {

            logger('Message logged from PreferenceAccountController.store', [$e->getMessage()]);
            return response()->json([
                'error' => 'Something went wrong storing record',
                'msg' => $e->getMessage()
            ], 400);

        }
    }

    /**
     * Verify created resource in storage.
     */
    public function verify(PreferenceAccountVerifyRequest $request, $id)
    {
        date_default_timezone_set('Asia/Manila');

        try {

            $admin = new UserAdmin;
            $admin->userID = $id;
            $admin->officeID = $request->get('officeID');
            $admin->positionID = $request->get('positionID');
            $admin->yearID = $request->get('yearID');
            $admin->employeeID = $request->get('employeeID');
            $admin->isVerified = TRUE;
            $admin->save();

            $account = User::select('users.name', 'users.email', 'user_admins.employeeID', 'preference_offices.label AS office', 'preference_roles.label AS role', 'preference_positions.label AS position')
                ->join('user_admins', 'users.id', 'user_admins.userID')
                ->join('user_roles', 'users.id', 'user_roles.userID')
                ->join('preference_offices', 'user_admins.officeID', 'preference_offices.id')
                ->join('preference_positions', 'user_admins.positionID', 'preference_positions.id')
                ->join('preference_roles', 'user_roles.roleID', 'preference_roles.id')
                ->where('users.id', $id)
                ->get();

            Mail::to($account[0]->email)->send(new PreferenceAccountVerificationMail($account[0]->name, $account[0]->employeeID, $account[0]->role, $account[0]->office, $account[0]->position));

            return response()->json([
                'msg' => 'RECORD VERIFIED!',
                'data' => $admin
            ], 200);

        } catch(\Exception $e) {

            logger('Message logged from PreferenceAccountController.verify', [$e->getMessage()]);
            return response()->json([
                'error' => 'Something went wrong verifying record',
                'msg' => $e->getMessage()
            ], 400);

        } 
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PreferenceAccountModifyRequest $request, $id)
    {
        date_default_timezone_set('Asia/Manila');

        try {

            $user = User::findOrFail($id);
            $user->name = $request->get('name');
            $user->email = $request->get('email');
            $user->save();

            $admin = UserAdmin::where('userID', $id)
                ->update([
                    'officeID' => $request->get('officeID'),
                    'positionID' => $request->get('positionID'),
                    'yearID' => $request->get('yearID'),
                    'employeeID' => $request->get('employeeID'),
                    'avatar' => $request->get('avatar')
                ]);
            
            $role = UserRole::where('userID', $id)
                ->update([
                    'roleID' => $request->get('roleID')
                ]);

                return response()->json([
                    'msg' => 'RECORD MODIFIED!',
                    'data' => $user
                ], 200);

        } catch (\Exception $e) {

            logger('Message logged from PreferenceAccountController.update', [$e->getMessage()]);
            return response()->json([
                'error' => 'Something went wrong updating record',
                'msg' => $e->getMessage()
            ], 400);

        }
    }

    /**
     * Reset the specified resource in storage.
     */
    public function reset(PreferenceAccountResetRequest $request, $id)
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

        } catch (\Exception $request) {

            logger('Message logged from PreferenceAccountController.reset', [$e->getMessage()]);
            return response()->json([
                'error' => 'Something went wrong resetting record',
                'msg' => $e->getMessage()
            ], 400);

        }
    }

    /**
     * Disable the specified resource in storage.
     */
    public function disable($id)
    {
        date_default_timezone_set('Asia/Manila');

        try {

            $admin = UserAdmin::where('userID', $id)
                ->update([
                    'isActive' => FALSE
                ]);

                return response()->json([
                    'msg' => 'RECORD DISABLED!',
                    'data' => $admin
                ], 200);

        } catch (\Exception $e) {

            logger('Message logged from PreferenceAccountController.disable', [$e->getMessage()]);
            return response()->json([
                'error' => 'Something went wrong disabling record',
                'msg' => $e->getMessage()
            ], 400);

        }
    }

    /**
     * Enable the specified resource in storage.
     */
    public function enable($id)
    {
        date_default_timezone_set('Asia/Manila');
        
        try {

            $admin = UserAdmin::where('userID', $id)
                ->update([
                    'isActive' => TRUE
                ]);

                return response()->json([
                    'msg' => 'RECORD ENABLED!',
                    'data' => $admin
                ], 200);

        } catch (\Exception $e) {

            logger('Message logged from PreferenceAccountController.enable', [$e->getMessage()]);
            return response()->json([
                'error' => 'Something went wrong enabling record',
                'msg' => $e->getMessage()
            ], 400);

        }
    }
}
