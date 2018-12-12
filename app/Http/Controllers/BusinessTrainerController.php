<?php

namespace App\Http\Controllers;

use App\BusinessTrainer;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class BusinessTrainerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function items(request $request)
    {
        $trainers =  BusinessTrainer::with('user')->filter(Input::all()); 
        return $trainers->paginate(20);
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
        // dd($request->all());
        // return NULL;
        $id = $request->get('id') ? $request->get('id') : 0;
        $user = $id ? User::find($id) : new User();
        $trainer = $id ? $user->trainer()->first() : new BusinessTrainer;

        // Update doesn't work because of validation
        $this->validate($request, [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'patronymic' => 'required|string|max:255',
            'gender' => 'string',
            'iin' => 'required|string|max:12',
            'password' => 'nullable|string|min:6|confirmed',
            'email' => 'nullable|email',
            'phone' => 'required',
            'photo' => 'image64:jpeg,jpg,png',
            // 'region_id' => 'required|integer',
            'locality_id' => 'required|integer',
            // 'district_id' => 'required|integer',
        ]);

        // dd($request);
        
        $user->first_name = $request->get('first_name');
        $user->last_name = $request->get('last_name');
        $user->patronymic = $request->get('patronymic');
        $user->gender = $request->get('gender');
        $user->iin = $request->get('iin');
        $user->email = $request->get('email');
        $user->phone = $request->get('phone');
        $user->role_id = $request->get('role_id');
        $user->locality_id = $request->get('locality_id');
        if ($request->get('photo')) {
            $user->photo = $request->get('photo');
        }
        if ($request->get('password')) {
            $user->password = bcrypt($request->get('password'));
        }
        $nocrypt = $request->get('password');
        $user->save();
        $user->trainer()->save($trainer);
        // BTrainerHistory::insert()
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\BusinessTrainer  $trainer
     * @return \Illuminate\Http\Response
     */
    public function item($id)
    {
        return BusinessTrainer::where('id',$id)->with(['user', 'histories'])->first();
        
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
    public function archive(Request $request, $id)
    {
        $archive_reason = $request->get('archive_reason');
        $trainer = BusinessTrainer::find($id)->first();
        $trainer->in_archive = true;
        $trainer->archive_reason_id = $archive_reason;
        $trainer->save();
    }


    public function restore($id)
    {
        $trainer = BusinessTrainer::find($id)->first();
        $trainer->in_archive = false;
        $trainer->archive_reason_id = NULL;
        $trainer->save();
    }

    /**
    * Full delete of object from both BusinessTrainer BTrainerHistory.
    *
    * @param \App\BusinessTrainer $trainer
    * @return \Illuminate\Http\Response
    */
    public function delete($id)
    {   
        return BusinessTrainer::destroy($id);
    }

}
