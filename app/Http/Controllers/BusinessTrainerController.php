<?php

namespace App\Http\Controllers;

use App\BusinessTrainer;
use App\User;
use Illuminate\Http\Request;

class BusinessTrainerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function items()
    {
        return BusinessTrainer::with('user')->paginate(20);
    }

    /**
    * If resource exists update its fields and relationship.
    * Otherwise create new and fill from request.
    * @param Request $request, $id
    * @return \Illuminate\Http\Response
    */
    public function save(Request $request)
    {
        // $result = $request->session()->all(); // Get data from ssesion
        // set X-CSRF-TOKEN Cookie in Header
        // $token = $result['_token'];
        // return $token;

        $id = $request->get('id') ? $request->get('id') : 0;
        $user = $id ? User::find($id) : new User();
        $trainer = $id ? $user->trainer()->first() : new BusinessTrainer;

        // Update doesn't work because of validation
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'password' => 'nullable|string|min:6|confirmed',
            'email' => 'nullable|email',
            'phone' => 'required',
            'photo' => 'image64:jpeg,jpg,png',
        ]);

        $user->name = $request->get('name');
        $user->email = $request->get('email');
        $user->phone = $request->get('phone');
        $user->role_id = $request->get('role_id');
        if ($request->get('photo')) {
            $user->photo = $request->get('photo');
        }
        if ($request->get('password')) {
            $user->password = bcrypt($request->get('password'));
        }
        $nocrypt = $request->get('password');
        $user->save();
        $user->trainer()->save($trainer);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\BusinessTrainer  $trainer
     * @return \Illuminate\Http\Response
     */
    public function item($id)
    {
        $trainer = BusinessTrainer::find($id);
        if ($trainer) {
            return $trainer->user()->get()->first();
        } else {
            return response()->json(["status" => "404", "message" => "Not Found"]);
        }
    }

    /**
     * Show item with less info.
     *
     * @param  \App\BusinessTrainer  $trainer
     * @return \Illuminate\Http\Response
     */
    // public function light_item(BusinessTrainer $trainer)
    // {
        
    // }


    /**
     * Archive object to BTrainerHistory.
     *
     * @param  \App\BusinessTrainer  $trainer
     * @return \Illuminate\Http\Response
     */
    public function archive(Request $request, BusinessTrainer $trainer)
    {   
        // $result = $request->session()->all(); // Get data from ssesion
        // set X-CSRF-TOKEN Cookie in Header
        // $token = $result['_token'];
        // return $token;

        return $trainer->archive();
    }

    /**
    * Full delete of object from both BusinessTrainer BTrainerHistory.
    *
    * @param \App\BusinessTrainer $trainer
    * @return \Illuminate\Http\Response
    */
    public function hardDelete(BusinessTrainer $trainer)
    {
        $trainer->full_delete();
    }

}
