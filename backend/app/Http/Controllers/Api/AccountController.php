<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Account;
use Illuminate\Http\Request;
use App\Http\Resources\AccountResource;

class AccountController extends Controller
{


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Account  $account
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Account $account)
    {
        try {
            $account->fill($request->validated());
            if ($request->hasFile('logo')) {
                $path = $request->file('logo')->store('accounts/logos');
                $account->logo = $path;
            }
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
}
