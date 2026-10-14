<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\DeviceRunningConfiguration;
use Illuminate\Http\Request;

class DeviceRunningConfigurationController extends Controller
{
    public function index(Request $request)
    {
        $query = DeviceRunningConfiguration::with('device:id,device_name,property_no_or_serial_no as property_no');

        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->where(function ($q) use ($search) {
                $q->where('file_name', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%")
                    ->orWhereHas('device', function ($dq) use ($search) {
                        $dq->where('device_name', 'like', "%{$search}%")
                            ->orWhere('property_no_or_serial_no', 'like', "%{$search}%");
                    });
            });
        }

        $sortField = $request->query('sort_field', 'created_at');
        $sortOrder = $request->query('sort_order', 'desc');

        if (! in_array($sortField, ['file_name', 'created_at', 'updated_at'])) {
            $sortField = 'created_at';
        }
        if (! in_array($sortOrder, ['asc', 'desc'])) {
            $sortOrder = 'desc';
        }

        return response()->json(
            $query->orderBy($sortField, $sortOrder)->paginate($request->query('per_page', 10))
        );
    }

    public function devices()
    {
        return response()->json(
            Device::whereNotIn('id', DeviceRunningConfiguration::select('device_id'))
                ->orderBy('device_name')
                ->get([
                    'id',
                    'device_name',
                    'property_no_or_serial_no as property_no',
                ])
        );
    }

    public function store(Request $request)
    {
        $configuration = DeviceRunningConfiguration::create($this->validated($request));

        return response()->json([
            'message' => 'Configuration saved successfully.',
            'configuration' => $configuration->load('device:id,device_name,property_no_or_serial_no as property_no'),
        ], 201);
    }

    public function show(DeviceRunningConfiguration $deviceRunningConfiguration)
    {
        return response()->json($deviceRunningConfiguration->load('device:id,device_name,property_no_or_serial_no as property_no'));
    }

    public function update(Request $request, DeviceRunningConfiguration $deviceRunningConfiguration)
    {
        $deviceRunningConfiguration->update($this->validated($request));

        return response()->json([
            'message' => 'Configuration updated successfully.',
            'configuration' => $deviceRunningConfiguration->fresh()->load('device:id,device_name,property_no_or_serial_no as property_no'),
        ]);
    }

    public function destroy(DeviceRunningConfiguration $deviceRunningConfiguration)
    {
        $deviceRunningConfiguration->delete();

        return response()->json(['message' => 'Configuration deleted successfully.']);
    }

    protected function validated(Request $request): array
    {
        return $request->validate([
            'device_id' => 'required|exists:devices,id',
            'file_name' => 'nullable|string|max:255',
            'content' => 'required|string|max:1000000',
        ]);
    }
}
