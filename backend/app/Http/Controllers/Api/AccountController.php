<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Account;
use Illuminate\Http\Request;
use App\Http\Requests\AccountRequest;
use App\Http\Resources\AccountResource;
use App\Services\NfseCertificateService;
use Illuminate\Support\Facades\Storage;

class AccountController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Account $account)
    {
        $this->authorizeOwnAccount($account);

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
        $this->authorizeOwnAccount($account);

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
        $this->authorizeOwnAccount($account);

        if ($request->hasFile('logo')) {
            if ($account->logo) {
                Storage::disk('public')->delete($account->logo);
            }
            $logo = $request->file('logo');
            $path = $logo->store('img/accounts/logos', 'public');
            $account->logo = $path;
            $account->save();
        }

        return AccountResource::make($account);
    }

    /**
     * Envia o certificado digital A1 (e-CNPJ) usado na emissão de NFS-e
     *
     * @param  \Illuminate\Http\Request  $request
     * @param \App\Models\Account  $account
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadNfseCertificate(Request $request, Account $account)
    {
        $this->authorizeOwnAccount($account);

        $request->validate([
            'certificate' => 'required|file|max:100',
            'password' => 'required|string',
        ], [
            'certificate.required' => 'Selecione o arquivo do certificado (.pfx ou .p12).',
            'certificate.max' => 'O arquivo do certificado não pode passar de 100KB.',
            'password.required' => 'Informe a senha do certificado.',
        ]);

        $extension = strtolower($request->file('certificate')->getClientOriginalExtension());
        if (!in_array($extension, ['pfx', 'p12'])) {
            return response()->json([
                'message' => 'Erro de validação',
                'errors' => ['certificate' => ['O certificado deve ser um arquivo .pfx ou .p12 (certificado A1).']],
            ], 422);
        }

        try {
            $account = NfseCertificateService::store($account, $request->file('certificate'), $request->input('password'));
        } catch (\DomainException $exception) {
            return response()->json([
                'message' => 'Erro de validação',
                'errors' => ['certificate' => [$exception->getMessage()]],
            ], 422);
        }

        return AccountResource::make($account);
    }

    /**
     * Remove o certificado digital da conta
     *
     * @param \App\Models\Account  $account
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteNfseCertificate(Account $account)
    {
        $this->authorizeOwnAccount($account);

        return AccountResource::make(NfseCertificateService::remove($account));
    }

    private function authorizeOwnAccount(Account $account)
    {
        abort_unless((int) auth()->user()->account_id === (int) $account->id, 403);
    }
}
