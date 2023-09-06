<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Http\Resources\ServicesResource;
use App\Http\Resources\ServiceResource;
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
        return ServicesResource::collection(Service::all());
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
            $service->labor_hourly_rate_total = $this->calculateLaborHourlyRateTotal($service);
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
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function edit(Service $service)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Service $service)
    {
        //
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

    private function calculateLaborHourlyRateTotal($service)
    {
        $hours = $service->labor_hours / 3600;
        $total = $hours * $service->labor_hourly_rate;
        $formattedTotal = number_format($total, 2, '.', '');
        
        return $formattedTotal;
    }


    private function calculatePrice($service)
    {
        return $service->labor_hourly_rate_total + $service->profit;
    }

    private function calculateProfit($service)
    {
        $divisionFactorPercentual = 100 - $service->profit_percentage;
        $divisionFactorDecimal = $divisionFactorPercentual / 100;

        $profit = $service->labor_hourly_rate_total / $divisionFactorDecimal - $service->labor_hourly_rate_total;
        $profit = number_format($profit, 2);
        
        return $profit;
    }

    /**
     * nao utilizada
     */
    private function formatLaborHours($request)
    {
        // $hours = $this->convertDecimalBrToDefault($request->labor_hours);

        // if(!$request->minutes) {
        //     return $hours;    
        // }

        // // $minutes = $this->convertDecimalBrToDefault($request->minutes);

        // return $hours + $request->minutes;
    }
}
