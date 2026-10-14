<?php

use App\Models\Device;
use App\Models\DeviceRunningConfiguration;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

test('device running configuration endpoints use the device property number column', function () {
    Sanctum::actingAs(User::create([
        'first_name' => 'Test',
        'last_name' => 'User',
        'username' => 'configuration-test-user',
        'name' => 'Test User',
        'email' => 'configuration-test@example.com',
        'mobile_number' => '1000000000',
        'password' => 'password',
    ]));

    $device = Device::create([
        'device_name' => 'Core Switch',
        'property_no_or_serial_no' => 'NET-001',
        'category' => 'switch',
    ]);
    $unconfiguredDevice = Device::create([
        'device_name' => 'Edge Router',
        'property_no_or_serial_no' => 'NET-002',
        'category' => 'router',
    ]);

    $response = $this->getJson('/api/device-running-configuration-devices');

    $response
        ->assertOk()
        ->assertExactJson([
            [
                'id' => $device->id,
                'device_name' => 'Core Switch',
                'property_no' => 'NET-001',
            ],
            [
                'id' => $unconfiguredDevice->id,
                'device_name' => 'Edge Router',
                'property_no' => 'NET-002',
            ],
        ]);

    DeviceRunningConfiguration::create([
        'device_id' => $device->id,
        'file_name' => 'running-config.txt',
        'content' => 'hostname core-switch',
    ]);

    $this->getJson('/api/device-running-configuration-devices')
        ->assertOk()
        ->assertExactJson([
            [
                'id' => $unconfiguredDevice->id,
                'device_name' => 'Edge Router',
                'property_no' => 'NET-002',
            ],
        ]);

    $this->getJson('/api/device-running-configurations?search=NET-001')
        ->assertOk()
        ->assertJsonPath('data.0.device.property_no', 'NET-001');
});
