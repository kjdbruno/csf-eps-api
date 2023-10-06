<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\UserRole;

class AuthController extends Controller
{
    public function getUser()
    {
        try {
            $role = UserRole::where('userID', auth()->user()->id)
                ->get();
            // if admin, supervisor, mgmt, encoder
            if ($role[0]->roleID != 5) {
                $user = User::select('users.*', 'user_roles.roleID', 'user_admins.isActive', 'user_admins.isVerified', 'user_admins.officeID', 'user_admins.positionID', 'user_admins.yearID', 'user_admins.employeeID', 'preference_offices.code AS office', 'preference_positions.label AS position', 'preference_years.label AS year', 'preference_roles.label AS role')
                    ->join('user_roles', 'users.id', 'user_roles.userID')
                    ->join('preference_roles', 'user_roles.roleID', 'preference_roles.id')
                    ->join('user_admins', 'users.id', 'user_admins.userID')
                    ->join('preference_offices', 'user_admins.officeID', 'preference_offices.id')
                    ->join('preference_positions', 'user_admins.positionID', 'preference_positions.id')
                    ->join('preference_years', 'user_admins.yearID', 'preference_years.id')
                    ->where('users.id', auth()->user()->id)
                    ->get();
                    return response()->json($user);
            // else citizen
            } else {
                $user = User::select('users.*', 'user_roles.roleID', 'user_clients.sexID', 'user_clients.number', 'user_clients.verification', 'user_clients.isVerified')
                    ->join('user_roles', 'users.id', 'user_roles.userID')
                    ->leftJoin('user_clients', 'users.id', 'user_clients.userID')
                    ->leftJoin('preference_sexes', 'user_clients.userID', 'preference_sexes.id')
                    ->where('users.id', auth()->user()->id)
                    ->get();
                    return response()->json($user);
            }
        } catch(\Exception $e) {

            logger('Message logged from AuthController.getUser', [$e->getMessage()]);
            return response()->json([
                'error' => 'Something went wrong getting record',
                'msg' => $e->getMessage()
            ], 400);

        } 
    }
}
