<?php

namespace App\Http\Controllers;

use App\Models\DeviceCategory;
use App\Models\DeviceManufacturer;
use App\Models\DeviceModel;
use App\Models\DeviceStatus;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DeviceSelectionController extends Controller
{
    protected function resolveModel(string $type): string
    {
        return match ($type) {
            'categories' => DeviceCategory::class,
            'statuses' => DeviceStatus::class,
            'manufacturers' => DeviceManufacturer::class,
            default => abort(404),
        };
    }

    public function index(string $type)
    {
        $model = $this->resolveModel($type);
        return response()->json($model::orderBy('name')->get());
    }

    public function store(Request $request, string $type)
    {
        $model = $this->resolveModel($type);
        $table = (new $model())->getTable();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique($table, 'name')],
        ]);

        $record = $model::create($validated);

        return response()->json(['message' => 'Created successfully.', 'data' => $record], 201);
    }

    public function update(Request $request, string $type, int $id)
    {
        $model = $this->resolveModel($type);
        $table = (new $model())->getTable();
        $record = $model::findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique($table, 'name')->ignore($id)],
        ]);

        $record->update($validated);

        return response()->json(['message' => 'Updated successfully.', 'data' => $record]);
    }

    public function destroy(string $type, int $id)
    {
        $model = $this->resolveModel($type);
        $model::findOrFail($id)->delete();

        return response()->json(['message' => 'Deleted successfully.']);
    }

    // Models (tied to a manufacturer) — separate since it has a foreign key
    public function models(Request $request)
    {
        $query = DeviceModel::with('manufacturer:id,name');

        if ($manufacturerId = $request->query('manufacturer_id')) {
            $query->where('manufacturer_id', $manufacturerId);
        }

        return response()->json($query->orderBy('name')->get());
    }

    public function storeModel(Request $request)
    {
        $validated = $request->validate([
            'manufacturer_id' => 'required|exists:device_manufacturers,id',
            'name' => 'required|string|max:255',
        ]);

        $model = DeviceModel::create($validated);

        return response()->json(['message' => 'Model created successfully.', 'data' => $model->load('manufacturer:id,name')], 201);
    }

    public function updateModel(Request $request, int $id)
    {
        $model = DeviceModel::findOrFail($id);

        $validated = $request->validate([
            'manufacturer_id' => 'required|exists:device_manufacturers,id',
            'name' => 'required|string|max:255',
        ]);

        $model->update($validated);

        return response()->json(['message' => 'Model updated successfully.', 'data' => $model->fresh()->load('manufacturer:id,name')]);
    }

    public function destroyModel(int $id)
    {
        DeviceModel::findOrFail($id)->delete();

        return response()->json(['message' => 'Model deleted successfully.']);
    }
}
