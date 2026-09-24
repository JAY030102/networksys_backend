<?php

namespace App\Http\Controllers;

use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DeviceController extends Controller
{
    protected array $categories = ['router', 'switch', 'server', 'printer', 'workstation', 'access_point', 'firewall', 'other'];
    protected array $statuses = ['active', 'inactive', 'maintenance', 'decommissioned'];
    protected array $manufacturers = ['Cisco', 'HP', 'Dell', 'Juniper', 'TP-Link', 'Ubiquiti', 'Lenovo', 'Other'];

    public function index(Request $request)
    {
        $query = Device::with('assignedTo:id,name,email');

        if ($request->filled('search')) {
        $search = $request->query('search');
        $query->where(function ($q) use ($search) {
            $q->where('device_name', 'like', "%{$search}%")
            ->orWhere('category', 'like', "%{$search}%")
            ->orWhere('status', 'like', "%{$search}%")
            ->orWhere('ip_address', 'like', "%{$search}%")
            ->orWhere('mac_address', 'like', "%{$search}%")
            ->orWhere('manufacturer', 'like', "%{$search}%")
            ->orWhere('model', 'like', "%{$search}%")
            ->orWhere('serial_number', 'like', "%{$search}%")
            ->orWhere('port', 'like', "%{$search}%")
            ->orWhere('location', 'like', "%{$search}%")
            ->orWhereHas('assignedTo', function ($uq) use ($search) {
            $uq->where('name', 'like', "%{$search}%");
            });
        });
        }

        if ($category = $request->query('category')) {
            $query->where('category', $category);
        }

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        return response()->json(
            $query->orderByDesc('created_at')->paginate($request->query('per_page', 10))
        );
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);

        $device = Device::create($validated);

        return response()->json([
            'message' => 'Device created successfully.',
            'device' => $device->load('assignedTo:id,name,email'),
        ], 201);
    }

    public function show(Device $device)
    {
        return response()->json($device->load('assignedTo:id,name,email'));
    }

    public function update(Request $request, Device $device)
    {
        $validated = $this->validated($request, $device->id);

        $device->update($validated);

        return response()->json([
            'message' => 'Device updated successfully.',
            'device' => $device->fresh()->load('assignedTo:id,name,email'),
        ]);
    }

    public function destroy(Device $device)
    {
        $device->delete();

        return response()->json(['message' => 'Device deleted successfully.']);
    }

    public function options()
    {
        return response()->json([
            'categories' => $this->categories,
            'statuses' => $this->statuses,
            'manufacturers' => $this->manufacturers,
        ]);
    }

    protected function validated(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'device_name' => 'required|string|max:255',
            'category' => 'required|exists:device_categories,name',
            'status' => 'required|exists:device_statuses,name',
            'ip_address' => ['nullable', 'ip', Rule::unique('devices', 'ip_address')->ignore($ignoreId)],
            'mac_address' => ['nullable', 'string', 'max:255', Rule::unique('devices', 'mac_address')->ignore($ignoreId)],
            'vlan' => 'nullable|string|max:255',
            'manufacturer' => 'nullable|exists:device_manufacturers,name',
            'model' => [
            'nullable', 'string', 'max:255',
            function ($attribute, $value, $fail) use ($request) {
                if (! $value) return;
                $manufacturer = \App\Models\DeviceManufacturer::where('name', $request->manufacturer)->first();
                if (! $manufacturer || ! \App\Models\DeviceModel::where('manufacturer_id', $manufacturer->id)->where('name', $value)->exists()) {
                    $fail('The selected model does not belong to the selected manufacturer.');
                }
            },
        ],
            'serial_number' => ['nullable', 'string', 'max:255', Rule::unique('devices', 'serial_number')->ignore($ignoreId)],
            'location' => 'nullable|string|max:255',
            'rack' => 'nullable|string|max:255',
            'port' => 'nullable|string|max:255',
            'firmware' => 'nullable|string|max:255',
            'assigned_to' => 'nullable|exists:users,id',
            'purchase_date' => 'nullable|date',
            'warranty_expiry' => 'nullable|date|after_or_equal:purchase_date',
            'notes' => 'nullable|string',
        ]);
    }
}
