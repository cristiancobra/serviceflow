<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cost;
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
            ->with('costs') // Carrega os custos relacionados ao serviço
            ->orderBy('name', 'asc')
            ->paginate(500);

        foreach ($services->getCollection() as $service) {
            $productionCosts = $service->costs->sum('price');
            $service->final_price = $service->price + $productionCosts;
        }

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

            if ($request->has('costs')) {
                $costs = [];
                foreach ($request->input('costs') as $cost) {
                    $costs[$cost['id']] = [
                        'quantity' => $cost['quantity'],
                        'price' => $cost['price'],
                        'total_price' => $cost['total_price'],
                    ];
                }
                $service->costs()->sync($costs);
            }
    
            // Recarrega os custos associados ao serviço
            $service->load('costs');
    
            // Calcula os custos de produção
            $productionCosts = $service->costs->sum('price');
    
            // Calcula o preço final
            
            // Salva os valores calculados no serviço
            $service->save();
            $service->production_costs = $productionCosts;
            $service->final_price = $service->price + $productionCosts;

            return new ServiceResource($service->load('costs'));
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

        $service->load('costs');
        $service->production_costs = $service->costs->sum('price');
        $service->final_price = $service->price + $service->production_costs;

        return new ServiceResource($service);
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

            $serviceData = [
                'labor_hours' => $serviceValidated['labor_hours'] ?? $service->labor_hours,
                'labor_hourly_rate' => $serviceValidated['labor_hourly_rate'] ?? $service->labor_hourly_rate,
                'price' => $serviceValidated['price'] ?? $service->price,
                'profit_percentage' => $serviceValidated['profit_percentage'] ?? $service->profit_percentage,
                'profit' => $serviceValidated['profit'] ?? $service->profit,
                'final_price' => null,
            ];

            $laborHours = $serviceData['labor_hours'] / 3600;
            $laborHourlyTotal = $laborHours * $serviceData['labor_hourly_rate'];
            $operationalCost = $laborHourlyTotal;

            // Atualiza ou adiciona os custos associados ao serviço
            if ($request->has('serviceCosts')) {
                $serviceCosts = [];
                foreach ($request->input('serviceCosts') as $cost) {
                    $serviceCosts[$cost['id']] = [
                        'quantity' => $cost['quantity'],
                        'price' => $cost['price'],
                        'total_price' => $cost['quantity'] * $cost['price'], // Calcula o total_price
                    ];
                }
                $service->costs()->syncWithoutDetaching($serviceCosts); // Adiciona ou atualiza os custos sem remover os existentes
            }

            // Recarrega os custos associados ao serviço
            $service->load('costs');

            // Recalcula os custos de produção
            $productionCosts = $service->costs->sum('price');
            $totalCost = $operationalCost + $productionCosts;

            // Recalcula o preço e a margem de lucro
            if (isset($serviceValidated['profit_percentage'])) {
                $serviceData['final_price'] = $totalCost / (1 - ($serviceValidated['profit_percentage'] / 100));
                $serviceData['profit'] = $serviceData['final_price'] - $totalCost;
                $serviceData['price'] = $operationalCost + $serviceData['profit'];
            } elseif (isset($serviceValidated['profit'])) {
                $serviceData['final_price'] = $totalCost + $serviceValidated['profit'];
                $serviceData['profit_percentage'] = ($serviceValidated['profit'] / $serviceData['final_price']) * 100;
                $serviceData['price'] = $operationalCost + $serviceValidated['profit'];
            } else {
                $serviceData['final_price'] = $totalCost + $serviceData['profit'];
                $serviceData['price'] = $operationalCost + $serviceData['profit'];
                $serviceData['profit_percentage'] = ($serviceData['profit'] / $serviceData['final_price']) * 100;
            }

            // Atualiza os dados do serviço
            $serviceData['labor_hourly_total'] = $laborHourlyTotal;
            $service->fill($serviceData);
            $service->save();

            // Recalcula os custos de produção e o preço final
            $service->production_costs = $productionCosts;
            $service->final_price = $serviceData['final_price'];

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

    /**
     * Detach a cost from a service.
     *
     * @param  \App\Models\Service  $service
     * @param  \App\Models\Cost  $cost
     * @return \Illuminate\Http\Response
     * */
    public function detachCost(Service $service, Cost $cost)
    {
        try {
            // Remove a associação do custo ao serviço
            $service->costs()->detach($cost->id);

            return response()->json([
                'message' => 'Custo desassociado com sucesso.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao desassociar o custo.',
                'error' => $e->getMessage(),
            ], 500);
        }
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
