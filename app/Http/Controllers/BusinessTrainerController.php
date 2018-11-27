<?php

namespace App\Http\Controllers;

use App\BusinessTrainer;
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
        return BusinessTrainer::with('user')->get();
    }

    /**
    * If resource exists update its fields and relationship.
    * Otherwise create new and fill from request.
    * @param Request $request, $id
    * @return \Illuminate\Http\Response
    */
    public function save(Request $request)
    {
        // $result = $request->session()->all(); //получаем данные из сессии
        // $result = $request->all();
        // set X-CSRF-TOKEN Cookie in Header
        // $token = $result['_token'];
        // return $token;

        $id = $request->get('id') ? $request->get('id') : 0;

        // $this->validate($request, [
        //     'name' => 'required|string|max:255',
        //     'password' => 'nullable|string|min:6|confirmed',
        //     'email' => 'nullable|email',
        //     'phone' => 'required',
        //     'photo' => 'image64:jpeg,jpg,png',
        //     'role_id' => 'required',
        // ]);

        $trainer = $id ? BusinessTrainer::find($id) : new BusinessTrainer();
        $user = $id ? $trainer->user()->first() : new \App\User();

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
        return $user;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\BusinessTrainer  $trainer
     * @return \Illuminate\Http\Response
     */
    public function item(BusinessTrainer $trainer)
    {
        return $trainer::with('user')->first();
    }

    /**
     * Show item with less info.
     *
     * @param  \App\BusinessTrainer  $trainer
     * @return \Illuminate\Http\Response
     */
    public function light_item(BusinessTrainer $trainer)
    {
        $trainer->update($request->all());
    }


    /**
     * Archive object to BTrainerHistory.
     *
     * @param  \App\BusinessTrainer  $trainer
     * @return \Illuminate\Http\Response
     */
    public function softDelete(BusinessTrainer $trainer)
    {
        $trainer->delete();
    }

    /**
    * Full delete of object from both BusinessTrainer BTrainerHistory.
    *
    * @param \App\BusinessTrainer $trainer
    * @return \Illuminate\Http\Response
    */
    public function hardDelete(BusinessTrainer $trainer)
    {

    }
}
