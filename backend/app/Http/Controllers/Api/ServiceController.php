<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Http\Resources\ServicesResource;
use App\Http\Resources\ServiceResource;
use App\Http\Requests\ServiceRequest;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $services = Service::where('account_id', auth()->user()->account_id)
            ->orderBy('name', 'asc')
            ->paginate(500);

        return ServicesResource::collection($services);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $service = new Service;

            $service->fill($request->all());
            // $service->account_id = 1;
            // $service->user_id = 1;
            $service->labor_hours = $request->labor_hours;
            $service->labor_hourly_rate = $this->convertMoneyBrToDefault($request->labor_hourly_rate);
            $service->labor_hourly_total = $this->calculateLaborHourlyTotal($service);
            $service->profit_percentage = $request->profit_percentage;
            $service->profit = $this->calculateProfit($service);
            $service->price = $this->calculatePrice($service);
            $service->save();

            return response()->json([
                'message' => "Serviço $service->name atualizado",
                'service' => $service,
            ]);

        } catch (ValidationException $validationException) {
            return response()->json([
                'message' => "Erro de validação",
                'errors' => $validationException->errors(),
            ], 422);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function show(Service $service)
    {
        return new ServiceResource(Service::find($service->id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function update(ServiceRequest $request, Service $service)
    {
        try {
            $validatedData = $request->validated();
            
            $laborHours = $validatedData['labor_hours'] ?? $service->labor_hours;
            $laborHours = $laborHours / 3600;
            $laborHourlyRate = $validatedData['labor_hourly_rate'] ?? $service->labor_hourly_rate;
            $laborHourlyTotal = $laborHours * $laborHourlyRate;
            $validatedData['labor_hourly_total'] = $laborHourlyTotal;

            $profitPercentage = $validatedData['profit_percentage'] ?? $service->profit_percentage;
            $totalCost = $validatedData['labor_hourly_total']; // depois adicionar custos de terceiros
            $validatedData['price'] = $totalCost / (1 - ($profitPercentage / 100));
            $validatedData['profit'] = $validatedData['price'] - $totalCost;
    
            $service->fill($validatedData);
            $service->save();

            return ServiceResource::make($service);
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
     * @param  \App\Models\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function destroy(Service $service)
    {
        //
    }

    private function convertDecimalBrToDefault($value)
    {
        // Remove pontos e espaços do valor para evitar conflitos
        $valueWithoutPoints = str_replace(['.', ' '], '', $value);

        $valueWithPoint = str_replace(',', '.', $valueWithoutPoints);
        $valueFormatted = number_format($valueWithPoint, 1);

        return $valueFormatted;
    }

    private function convertMoneyBrToDefault($value)
    {
        // Remove pontos e espaços do valor para evitar conflitos
        $valueWithoutPoints = str_replace(['.', ' '], '', $value);

        // Substitui a vírgula pelo ponto
        $valueFormatted = str_replace(',', '.', $valueWithoutPoints);

        return $valueFormatted;
    }
}