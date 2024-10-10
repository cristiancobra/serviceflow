<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Account;
use Illuminate\Http\Request;
use App\Http\Requests\AccountRequest;
use App\Http\Resources\AccountResource;

class AccountController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Account $account)
    {
        return AccountResource::make($account);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\AccountRequest;
     * @param  \App\Models\Account  $account
     * @return \Illuminate\Http\Response
     */
    public function update(AccountRequest $request, Account $account)
    {
        try {
            $account->fill($request->validated());
            $account->save();

            return AccountResource::make($account);
        } catch (ValidationException $validationException) {
            return response()->json([
                'message' => "Erro de validação",
                'errors' => $validationException->errors(),
            ], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Account  $account
     * @return \Illuminate\Http\Response
     */
    public function destroy(Account $account)
    {
        //
    }

    /**
     * Upload logo
     *
     * @param  \Illuminate\Http\Request  $request
     * @param \App\Models\Account  $account
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadLogo(Request $request, Account $account)
    {
        if ($request->hasFile('logo')) {
            $logo = $request->file('logo');
            $fileName = time().'.'.$logo->getClientOriginalExtension();
            $logo->move(public_path('img/logos'), $fileName);
            $account->logo = 'img/logos/'.$fileName;
            $account->save();
        }

        return AccountResource::make($account);
    }
}
