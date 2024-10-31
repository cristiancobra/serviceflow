<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Http\Resources\ServiceResource;
use App\Http\Requests\ServiceRequest;

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

        return ServiceResource::collection($services);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ServiceRequest $request)
    {
        try {
            $service = new Service;

            $service->fill($request->validated());

            $service->save();

            return ServiceResource::make($service);

            // $service->fill($request->all());
            // // $service->account_id = 1;
            // // $service->user_id = 1;
            // $service->labor_hours = $request->labor_hours;
            // $service->labor_hourly_rate = $this->convertMoneyBrToDefault($request->labor_hourly_rate);
            // $service->labor_hourly_total = $service->calculateLaborHourlyTotal($service);
            // $service->profit_percentage = $request->profit_percentage;
            // $service->profit = $service->calculateProfit($service);
            // $service->price = $service->calculatePrice($service);
            // $service->save();

            // return response()->json([
            //     'message' => "Serviço $service->name atualizado",
            //     'service' => $service,
            // ]);

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
            $serviceValidated = $request->validated();
            
            $laborHours = $serviceValidated['labor_hours'] ?? $service->labor_hours;
            $laborHours = $laborHours / 3600;
            $laborHourlyRate = $serviceValidated['labor_hourly_rate'] ?? $service->labor_hourly_rate;
            $laborHourlyTotal = $laborHours * $laborHourlyRate;
            $serviceValidated['labor_hourly_total'] = $laborHourlyTotal;
            $operationalCost = $serviceValidated['labor_hourly_total'];
            
            if(isset($serviceValidated['profit_percentage'])) {
                $serviceValidated['price'] = $operationalCost / (1 - ($serviceValidated['profit_percentage'] / 100));
                $serviceValidated['profit'] = $serviceValidated['price'] - $operationalCost;
            }
            
            if(isset($serviceValidated['profit'])) {
                $serviceValidated['price'] = $operationalCost + $serviceValidated['profit'];
                $serviceValidated['profit_percentage'] = ($serviceValidated['profit'] / $serviceValidated['price']) * 100;
            }
            // $profitPercentage = $serviceValidated['profit_percentage'] ?? $service->profit_percentage;
    
            $service->fill($serviceValidated);
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
        $service->delete();

        return response()->json([
            'message' => "Serviço $service->name deletado",
        ]);
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