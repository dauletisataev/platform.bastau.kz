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
    public function save(Request $request, $id)
    {
        $validData = $request->validate([
            'name' => 'required|string'
        ]);
        // $trainer = BusinessTrainer::updateOr
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
