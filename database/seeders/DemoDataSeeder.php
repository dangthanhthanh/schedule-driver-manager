<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

use App\Models\{
    Driver,
    Truck,
    Location,
    Schedule,
    ScheduleTruck,
    DriverStatus,
    TruckStatus,
    DriverStatusLog,
    TruckStatusLog
};

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            // ----- 1. Seed lookup tables -----
            $driverStatuses  = $this->seedDriverStatuses();
            $truckStatuses   = $this->seedTruckStatuses();
            $locations       = $this->seedLocations();
            $trucks          = $this->seedTrucks();
            $drivers         = $this->seedDrivers($trucks);

            // ----- 2. Seed demo schedules -----
            $this->seedSchedules($drivers, $trucks, $locations);

            // ----- 3. Seed sample status logs -----
            $this->seedDriverStatusLogs($drivers, $driverStatuses);
            $this->seedTruckStatusLogs($trucks, $truckStatuses);
        });
    }

    /* ============================================================
     | Driver Statuses
     * ============================================================
     */
    protected function seedDriverStatuses()
    {
        $data = [
            ['name' => 'Đang chạy',  'color' => '#4caf50'],
            ['name' => 'Nghỉ phép',  'color' => '#f44336'],
            ['name' => 'Chuyến gần', 'color' => '#03a9f4'],
            ['name' => 'Chuyến xa',  'color' => '#9c27b0'],
        ];

        $out = [];
        foreach ($data as $row) {
            $out[$row['name']] = DriverStatus::firstOrCreate(
                ['name' => $row['name']],
                ['color' => $row['color']]
            );
        }
        return $out; // keyed by name
    }

    /* ============================================================
     | Truck Statuses
     * ============================================================
     */
    protected function seedTruckStatuses()
    {
        $data = [
            ['name' => 'Đang chạy', 'color' => '#4caf50'],
            ['name' => 'Bảo trì',   'color' => '#ff9800'],
            ['name' => 'Trống',     'color' => '#9e9e9e'],
            ['name' => 'Bận',       'color' => '#e91e63'],
        ];

        $out = [];
        foreach ($data as $row) {
            $out[$row['name']] = TruckStatus::firstOrCreate(
                ['name' => $row['name']],
                ['color' => $row['color']]
            );
        }
        return $out; // keyed by name
    }

    /* ============================================================
     | Locations
     * ============================================================
     */
    protected function seedLocations()
    {
        $data = [
            // route codes / bãi
            ['name' => 'HD2'],
            ['name' => 'QL1'],
            ['name' => 'TTX1'],
            ['name' => 'VT1'],
            ['name' => 'BD3'],
            // destination facilities
            ['name' => 'Trại BAF'],
            ['name' => 'RF Long Tân 1'],
            ['name' => 'Trại ANOVA - Dak Nong'],
            ['name' => 'Kho D6'],
            // generic fallback
            ['name' => 'N/A'],
        ];

        $out = [];
        foreach ($data as $row) {
            $loc = Location::firstOrCreate(
                ['name' => $row['name']],
                ['address' => $row['address'] ?? null, 'link' => $row['link'] ?? null]
            );
            $out[$loc->name] = $loc;
        }
        return $out; // keyed by name
    }

    /* ============================================================
     | Trucks
     * ============================================================
     */
    protected function seedTrucks()
    {
        $plates = [
            '61H 09265',
            '61C 18151',
            '61H 09206',
            '61H 09153',
            '61C 18205',
            '61H 09156',
            '61H 09119',
            '61H-07728',
            '61H-07635',
            '61H-04169',
            '61H-08343',
            '61H 09100',
            '61H 04132',
            '61H 05282',
            '61H 09101',
            '61C 42769',
        ];

        $out = [];
        foreach ($plates as $p) {
            $truck = Truck::firstOrCreate(
                ['truck_name' => $p],
                [
                    'status'      => null,
                    'project_id'  => null,
                    'floor'       => null,
                    'capacity'    => null,
                    'description' => null,
                ]
            );
            $out[$p] = $truck;
        }
        return $out; // keyed by truck_name
    }

    /* ============================================================
     | Drivers
     * ============================================================
     */
    protected function seedDrivers($trucks)
    {
        // helper to get truck id by plate (safe)
        $getTruckId = function (?string $plate) use ($trucks) {
            return isset($trucks[$plate]) ? $trucks[$plate]->id : null;
        };

        $rows = [
            // id, name, phone, truck_plate, is_main
            ['Hồ Văn Thiện',       '0396724390', '61H 09265', true],
            ['Võ Hửu Nghĩa',       '0962044495', '61C 18151', true],
            ['Đặng Quang Thân',    '0368104880', '61H 09206', true],
            ['Ngô Quốc Phận',      '0879652170', '61H 09119', true],
            ['Huỳnh Văn Thái',     '0937215412', '61H 09156', true],
            ['Nguyễn Quốc Thái',   '0944277866', '61H 09101', true],
            ['Thạch Sang',         '0342805335', '61H 09153', true],
            ['Trần Văn Hùng',      '0933382881', '61C 18205', true],
            ['Lê Phú Lâm',         '0333099919', '61H 09100', true],
            ['Nguyễn Minh Cường',  '0983030559', '61H-07728', true],
            ['Huỳnh Tiến Sang',    '0373774546', '61H-07635', true],
            ['Trần Trung Trọng',   '0387406454', '61H-04169', true],
            ['Trần Thế Hoàng Gia', '0394678977', '61H-08343', true],
            ['Ngô Văn Tiến',       null,         '61H 04132', true],
            ['Hà Văn Toàn',        null,         '61H 05282', true],
            ['Trần Ngọc Như',      null,         '61C 42769', true],
            // assistants that appear often but may not drive: create as non-main no-truck
            ['Nguyễn Trọng Nam',   null, null, false],
            ['Nguyễn Chí Linh',    null, null, false],
            ['Trần Thanh Thuận',   null, null, false],
            ['Trịnh Tú Thông',     null, null, false],
        ];

        $out = [];
        foreach ($rows as $r) {
            [$name, $phone, $plate, $isMain] = $r;
            $d = Driver::firstOrCreate(
                ['name' => $name],
                [
                    'phone'          => $phone,
                    'truck_id'       => $getTruckId($plate),
                    'is_main_driver' => $isMain,
                ]
            );

            // ensure truck assignment stays in sync if seed re-run
            if ($plate && $d->truck_id !== $getTruckId($plate)) {
                $d->truck_id = $getTruckId($plate);
                $d->save();
            }
            $out[$name] = $d;
        }
        return $out; // keyed by driver name
    }

    /* ============================================================
     | Schedules + ScheduleTrucks
     * ============================================================
     */
    protected function seedSchedules($drivers, $trucks, $locations)
    {
        // helper to safe-get id
        $truckId     = fn($plate)  => $trucks[$plate]->id  ?? null;
        $driverId    = fn($name)   => $drivers[$name]->id  ?? null;
        $locId       = fn($name)   => $locations[$name]->id ?? null;

        // MARK: dataset definitions ---------------------------
        // Each schedule array:
        // title, note, date (Y-m-d), items[] = [fromLoc, toLoc, truck_plate, driver_name|null, assistant|null, cargo_desc]
        $data = [

            // 21.07 Trại BAF
            [
                'title' => 'Trại BAF',
                'date'  => '2025-07-21',
                'note'  => '22h ngày 20.07 tới trại',
                'items' => [
                    ['HD2', 'Trại BAF', '61H 09265', 'Hồ Văn Thiện', 'Nguyễn Trọng Nam', '101 con 3M (20 con 60-79kg + 11 con 80-90kg + 70 con 115-130kg)'],
                    ['QL1', 'Trại BAF', '61C 18151', 'Võ Hửu Nghĩa', 'Nguyễn Chí Linh', '130 con 3M 90-115kg'],
                    ['QL1', 'Trại BAF', '61H 09206', 'Đặng Quang Thân', 'Nguyễn Chí Linh', '110 con 3M 90-115kg'],
                    ['QL1', 'Trại BAF', '61H 09153', 'Thạch Sang', 'Nguyễn Chí Linh', '110 con 3M 90-115kg'],
                    ['TTX1','Trại BAF', '61C 18205', 'Trần Văn Hùng', 'Trần Thanh Thuận', '150 con 2MD (3 con 60-79kg + 15 con 80-90kg + 82 con 90-115kg + 50 con 115-130kg)'],
                    ['VT1', 'Trại BAF', '61H 09156', 'Huỳnh Văn Thái', 'Trịnh Tú Thông', '60 con (3 HB + 1 nọc + 56 nái)'],
                ],
            ],

            // RF Long Tân 1
            [
                'title' => 'RF Long Tân 1',
                'date'  => '2025-07-21',
                'note'  => '04h nhận heo | 06h30 tới bãi JAPFA | 10h30 tới trại',
                'items' => [
                    [null, 'RF Long Tân 1', '61H-07728', 'Nguyễn Minh Cường', 'Trần Phương Đại', '160 con 2MD'],
                    [null, 'RF Long Tân 1', '61H-07635', 'Huỳnh Tiến Sang',   'Trần Phương Đại', '160 con 2MD'],
                    [null, 'RF Long Tân 1', '61H-04169', 'Trần Trung Trọng',  'Trần Phương Đại', '110 con 3M'],
                    [null, 'RF Long Tân 1', '61H-08343', 'Trần Thế Hoàng Gia','Trần Phương Đại', '50 con 3M'],
                ],
            ],

            // Trại ANOVA -> Kho D6
            [
                'title' => 'Trại ANOVA - Dak Nong => Kho D6',
                'date'  => '2025-07-21',
                'note'  => '05h30 tới trại',
                'items' => [
                    [null, 'Kho D6', '61H 09100', 'Lê Phú Lâm', null, '172 con'],
                    [null, 'Kho D6', '61H 04132', 'Ngô Văn Tiến', null, '172 con'],
                    [null, 'Kho D6', '61H 05282', 'Hà Văn Toàn', null, '172 con'],
                    [null, 'Kho D6', '61H 09101', 'Nguyễn Quốc Thái', null, '110 con'],
                    [null, 'Kho D6', '61C 42769', 'Trần Ngọc Như', null, '80 con'],
                ],
            ],

            // Kế hoạch BD3/HD2
            [
                'title' => 'Kế hoạch BD3/HD2',
                'date'  => '2025-07-21',
                'note'  => null,
                'items' => [
                    ['BD3','N/A','61H 09119','Ngô Quốc Phận',null,'100 con 3M 115-130kg'],
                    ['HD2','N/A','61C 18151','Võ Hửu Nghĩa',null,'150 con 3M 90-115kg'],
                    ['HD2','N/A','61C 18205','Trần Văn Hùng',null,'150 con 3M 90-115kg'],
                    ['HD2','N/A','61H 09265','Hồ Văn Thiện',null,'105 con 3M 90-115kg'],
                ],
            ],
        ];

        foreach ($data as $schedData) {
            $schedule = Schedule::create([
                'title'        => $schedData['title'],
                'date'         => $schedData['date'],
                'general_note' => $schedData['note'],
            ]);

            foreach ($schedData['items'] as $item) {
                [$from, $to, $plate, $driverName, $assistant, $cargo] = $item;

                ScheduleTruck::create([
                    'schedule_id'      => $schedule->id,
                    'truck_id'         => $truckId($plate),
                    'driver_id'        => $driverId($driverName),
                    'from_location_id' => $from ? $locId($from) : null,
                    'to_location_id'   => $to   ? $locId($to)   : null,
                    'assistant'        => $assistant,
                    'cargo_desc'       => $cargo,
                ]);
            }
        }
    }

    /* ============================================================
     | Driver Status Logs (sample)
     * ============================================================
     */
    protected function seedDriverStatusLogs($drivers, $driverStatuses)
    {
        $today = Carbon::parse('2025-07-21');

        $sample = [
            // driver_name => status_name
            'Hồ Văn Thiện'       => 'Đang chạy',
            'Võ Hửu Nghĩa'       => 'Đang chạy',
            'Đặng Quang Thân'    => 'Đang chạy',
            'Nguyễn Minh Cường'  => 'Nghỉ phép', // ví dụ
            'Huỳnh Tiến Sang'    => 'Đang chạy',
        ];

        foreach ($sample as $driverName => $statusName) {
            if (!isset($drivers[$driverName], $driverStatuses[$statusName])) {
                continue;
            }

            DriverStatusLog::create([
                'driver_id' => $drivers[$driverName]->id,
                'status_id' => $driverStatuses[$statusName]->id,
                'date'      => $today,
                'time_unit' => 1,
            ]);
        }
    }

    /* ============================================================
     | Truck Status Logs (sample)
     * ============================================================
     */
    protected function seedTruckStatusLogs($trucks, $truckStatuses)
    {
        $today = Carbon::parse('2025-07-21');

        $sample = [
            // plate => status_name
            '61H 09265'  => 'Đang chạy',
            '61C 18151'  => 'Đang chạy',
            '61H 09206'  => 'Bảo trì',
            '61C 18205'  => 'Đang chạy',
            '61H 09156'  => 'Trống',
        ];

        foreach ($sample as $plate => $statusName) {
            if (!isset($trucks[$plate], $truckStatuses[$statusName])) {
                continue;
            }

            TruckStatusLog::create([
                'truck_id'  => $trucks[$plate]->id,
                'status_id' => $truckStatuses[$statusName]->id,
                'date'      => $today,
                'time_unit' => 1,
            ]);
        }
    }
}
