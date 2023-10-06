<?php

namespace App\Http\Controllers;

use App\Models\PreferenceMessage;
use Illuminate\Http\Request;

use App\Http\Requests\PreferenceMessageRequest;

class PreferenceMessageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {

            $preference= PreferenceMessage::where('title', 'LIKE', '%'.$request->get('filter').'%')
                ->orWhere('content', 'LIKE', '%'.$request->get('filter').'%')
                ->orderBy('created_at', 'DESC')
                ->get();
                return response()->json($preference);
                
        } catch (\Exception $e) {

            logger('Message logged from PreferenceMessageController.index', [$e->getMessage()]);
            return response()->json([
                'error' => 'Something went wrong getting record',
                'msg' => $e->getMessage()
            ], 400);

        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\PreferenceMessageRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PreferenceMessageRequest $request)
    {
        try {

            $preference = new PreferenceMessage;
            $preference->title = $request->get('title');
            $preference->content = $request->get('content');
            $preference->save();

            return response()->json([
                'msg' => 'RECORD STORED!',
                'data' => $preference
            ], 200);

        } catch (\Exception $e) {

            logger('Message logged from PreferenceMessageController.store', [$e->getMessage()]);
            return response()->json([
                'error' => 'Something went wrong storing record',
                'msg' => $e->getMessage()
            ], 400);

        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PreferenceMessage  $preferenceMessage
     * @return \Illuminate\Http\Response
     */
    public function show(PreferenceMessage $preferenceMessage)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\PreferenceMessage  $preferenceMessage
     * @return \Illuminate\Http\Response
     */
    public function edit(PreferenceMessage $preferenceMessage)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\PreferenceMessageRequest  $request
     * @param  \App\Models\PreferenceMessage  $preferenceMessage
     * @return \Illuminate\Http\Response
     */
    public function update(PreferenceMessageRequest $request, $id)
    {
        try {

            $preference = PreferenceMessage::findOrfail($id);
            $preference->title = $request->get('title');
            $preference->content = $request->get('content');
            $preference->save();

            return response()->json([
                'msg' => 'RECORD MODIFIED!',
                'data' => $preference
            ], 200);

        } catch (\Exception $e) {

            logger('Message logged from PreferenceMessageController.update', [$e->getMessage()]);
            return response()->json([
                'error' => 'Something went wrong updating record',
                'msg' => $e->getMessage()
            ], 400);

        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PreferenceMessage  $preferenceMessage
     * @return \Illuminate\Http\Response
     */
    public function destroy(PreferenceMessage $preferenceMessage)
    {
        //
    }

    /**
     * Disable the specified resource from the storage
     */
    public function disable($id)
    {
        try {

            $preference = PreferenceMessage::findOrfail($id);
            $preference->isActive = FALSE;
            $preference->save();

            return response()->json([
                'msg' => 'RECORD DISABLE!',
                'data' => $preference
            ], 200);

        } catch (\Exception $e) {

            logger('Message logged from PreferenceMessageController.disable', [$e->getMessage()]);
            return response()->json([
                'error' => 'Something went wrong disabling record',
                'msg' => $e->getMessage()
            ], 400);

        }
    }

    /**
     * Enable the specified resource from the storage
     */
    public function enable($id)
    {
        try {

            $preference = PreferenceMessage::findOrfail($id);
            $preference->isActive = TRUE;
            $preference->save();

            return response()->json([
                'msg' => 'RECORD ENABLE!',
                'data' => $preference
            ], 200);

        } catch (\Exception $e) {

            logger('Message logged from PreferenceMessageController.enable', [$e->getMessage()]);
            return response()->json([
                'error' => 'Something went wrong enabling record',
                'msg' => $e->getMessage()
            ], 400);

        }    
    }
}
