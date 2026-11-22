<?php

namespace Database\Seeders;

use App\Models\AccessLevel;
use App\Models\City;
use App\Models\Country;
use App\Models\OrganizationUnit;
use App\Models\OrganizationUnitClosure;
use App\Models\Outlet;
use App\Models\Permission;
use App\Models\Position;
use App\Models\Province;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrganizationAndAccessControlSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Seeds initial master data defined in PRD 0.
     */
    public function run(): void
    {
        DB::transaction(function () {
            // 1. Master Wilayah
            $indonesia = Country::firstOrCreate(
                ['code' => 'ID'],
                ['name' => 'Indonesia', 'is_active' => true]
            );

            $sulsel = Province::firstOrCreate(
                ['code' => '73'],
                ['country_id' => $indonesia->id, 'name' => 'Sulawesi Selatan', 'is_active' => true]
            );

            $sultra = Province::firstOrCreate(
                ['code' => '74'],
                ['country_id' => $indonesia->id, 'name' => 'Sulawesi Tenggara', 'is_active' => true]
            );

            $makassar = City::firstOrCreate(
                ['code' => '7371'],
                ['province_id' => $sulsel->id, 'name' => 'Kota Makassar', 'type' => 'kota', 'is_active' => true]
            );

            $gowa = City::firstOrCreate(
                ['code' => '7306'],
                ['province_id' => $sulsel->id, 'name' => 'Kabupaten Gowa', 'type' => 'kabupaten', 'is_active' => true]
            );

            $baubau = City::firstOrCreate(
                ['code' => '7472'],
                ['province_id' => $sultra->id, 'name' => 'Kota Baubau', 'type' => 'kota', 'is_active' => true]
            );

            $outletMks01 = Outlet::firstOrCreate(
                ['code' => 'MKS-01'],
                ['city_id' => $makassar->id, 'name' => 'Gerai Makassar 01', 'address' => 'Jl. Antang Raya No. 1', 'is_active' => true]
            );

            $outletMks02 = Outlet::firstOrCreate(
                ['code' => 'MKS-02'],
                ['city_id' => $makassar->id, 'name' => 'Gerai Makassar 02', 'address' => 'Jl. Perintis Kemerdekaan', 'is_active' => true]
            );

            $outletBaubau = Outlet::firstOrCreate(
                ['code' => 'BAU-01'],
                ['city_id' => $baubau->id, 'name' => 'Gerai Baubau', 'address' => 'Jl. Murhum No. 10', 'is_active' => true]
            );

            // 2. Master Organisasi & Hierarki (BR-001)
            $orgNational = OrganizationUnit::firstOrCreate(
                ['code' => 'WIZ-PUSAT'],
                [
                    'parent_id' => null,
                    'unit_type' => 'country',
                    'name' => 'Wahdah Inspirasi Zakat Nasional',
                    'country_id' => $indonesia->id,
                    'is_active' => true,
                ]
            );

            $orgSulsel = OrganizationUnit::firstOrCreate(
                ['code' => 'WIZ-SULSEL'],
                [
                    'parent_id' => $orgNational->id,
                    'unit_type' => 'province',
                    'name' => 'WIZ Perwakilan Sulawesi Selatan',
                    'country_id' => $indonesia->id,
                    'province_id' => $sulsel->id,
                    'is_active' => true,
                ]
            );

            $orgMakassar = OrganizationUnit::firstOrCreate(
                ['code' => 'WIZ-MAKASSAR'],
                [
                    'parent_id' => $orgSulsel->id,
                    'unit_type' => 'city',
                    'name' => 'WIZ Daerah Kota Makassar',
                    'country_id' => $indonesia->id,
                    'province_id' => $sulsel->id,
                    'city_id' => $makassar->id,
                    'is_active' => true,
                ]
            );

            $orgOutlet01 = OrganizationUnit::firstOrCreate(
                ['code' => 'WIZ-GERAI-MKS01'],
                [
                    'parent_id' => $orgMakassar->id,
                    'unit_type' => 'outlet',
                    'name' => 'WIZ Gerai Makassar 01',
                    'country_id' => $indonesia->id,
                    'province_id' => $sulsel->id,
                    'city_id' => $makassar->id,
                    'outlet_id' => $outletMks01->id,
                    'is_active' => true,
                ]
            );

            $orgOutlet02 = OrganizationUnit::firstOrCreate(
                ['code' => 'WIZ-GERAI-MKS02'],
                [
                    'parent_id' => $orgMakassar->id,
                    'unit_type' => 'outlet',
                    'name' => 'WIZ Gerai Makassar 02',
                    'country_id' => $indonesia->id,
                    'province_id' => $sulsel->id,
                    'city_id' => $makassar->id,
                    'outlet_id' => $outletMks02->id,
                    'is_active' => true,
                ]
            );

            // Rebuild closure table entries for the organization units
            $units = OrganizationUnit::all();
            foreach ($units as $unit) {
                // Self reference (depth 0)
                OrganizationUnitClosure::firstOrCreate([
                    'ancestor_id' => $unit->id,
                    'descendant_id' => $unit->id,
                    'depth' => 0,
                ]);

                // Ancestors of parent
                if ($unit->parent_id) {
                    $parentClosures = OrganizationUnitClosure::where('descendant_id', $unit->parent_id)->get();
                    foreach ($parentClosures as $pc) {
                        OrganizationUnitClosure::firstOrCreate([
                            'ancestor_id' => $pc->ancestor_id,
                            'descendant_id' => $unit->id,
                            'depth' => $pc->depth + 1,
                        ]);
                    }
                }
            }

            // 3. Positions (Jabatan)
            $positions = [
                ['code' => 'KETUA', 'name' => 'Ketua', 'description' => 'Pimpinan unit organisasi'],
                ['code' => 'WAKIL_KETUA', 'name' => 'Wakil Ketua', 'description' => 'Wakil pimpinan unit organisasi'],
                ['code' => 'SEKRETARIS', 'name' => 'Sekretaris', 'description' => 'Sekretaris unit'],
                ['code' => 'BENDAHARA', 'name' => 'Bendahara', 'description' => 'Pengelola keuangan unit'],
                ['code' => 'STAFF_PENGHIMPUNAN', 'name' => 'Staff Penghimpunan', 'description' => 'Operasional penghimpunan dana ziswaf'],
                ['code' => 'STAFF_PENYALURAN', 'name' => 'Staff Penyaluran', 'description' => 'Operasional penyaluran dan pendayagunaan'],
                ['code' => 'AMIL', 'name' => 'Amil Pelaksana', 'description' => 'Amil pelaksana operasional'],
            ];

            foreach ($positions as $pos) {
                Position::firstOrCreate(['code' => $pos['code']], $pos);
            }

            // 4. Access Levels (BR-005, FR-004)
            $accessLevels = [
                [
                    'level' => 1,
                    'code' => 'ALL',
                    'name' => 'All Access',
                    'description' => 'Akses seluruh organisasi tanpa batasan wilayah (tidak membutuhkan organization_id)',
                ],
                [
                    'level' => 2,
                    'code' => 'COUNTRY',
                    'name' => 'Country Access',
                    'description' => 'Akses data tingkat negara dan seluruh organisasi di bawahnya',
                ],
                [
                    'level' => 3,
                    'code' => 'PROVINCE',
                    'name' => 'Province Access',
                    'description' => 'Akses data tingkat provinsi dan seluruh kota/gerai di bawahnya',
                ],
                [
                    'level' => 4,
                    'code' => 'LOCAL',
                    'name' => 'City / Outlet Access',
                    'description' => 'Akses data spesifik kota atau gerai tertentu',
                ],
            ];

            foreach ($accessLevels as $al) {
                AccessLevel::firstOrCreate(['level' => $al['level']], $al);
            }

            // 5. Roles (FR-005)
            $roles = [
                ['code' => 'super_admin', 'name' => 'Super Admin', 'description' => 'Mengelola seluruh konfigurasi sistem'],
                ['code' => 'admin_nasional', 'name' => 'Admin Nasional', 'description' => 'Mengelola organisasi dan data tingkat negara'],
                ['code' => 'admin_provinsi', 'name' => 'Admin Provinsi', 'description' => 'Mengelola organisasi dan data dalam provinsi'],
                ['code' => 'admin_kota', 'name' => 'Admin Kota', 'description' => 'Mengelola organisasi dan data dalam kota'],
                ['code' => 'admin_gerai', 'name' => 'Admin Gerai', 'description' => 'Mengelola aktivitas operasional gerai'],
                ['code' => 'finance', 'name' => 'Finance', 'description' => 'Mengelola keuangan dan verifikasi transaksi'],
                ['code' => 'staff_penghimpunan', 'name' => 'Staff Penghimpunan', 'description' => 'Menginput dan mengelola penghimpunan zakat/infaq'],
                ['code' => 'staff_penyaluran', 'name' => 'Staff Penyaluran', 'description' => 'Menginput dan mengelola penyaluran mustahik'],
            ];

            $createdRoles = [];
            foreach ($roles as $r) {
                $createdRoles[$r['code']] = Role::firstOrCreate(['code' => $r['code']], $r);
            }

            // 6. Permissions (FR-006)
            $permissions = [
                // Donations
                ['code' => 'donations.view', 'name' => 'Melihat data donasi/transaksi', 'group' => 'donations'],
                ['code' => 'donations.create', 'name' => 'Membuat transaksi donasi baru', 'group' => 'donations'],
                ['code' => 'donations.update', 'name' => 'Mengubah data transaksi donasi', 'group' => 'donations'],
                ['code' => 'donations.delete', 'name' => 'Menghapus transaksi donasi', 'group' => 'donations'],
                ['code' => 'donations.approve', 'name' => 'Menyetujui/memvalidasi transaksi', 'group' => 'donations'],
                ['code' => 'donations.export', 'name' => 'Mengekspor data transaksi donasi', 'group' => 'donations'],

                // Members
                ['code' => 'members.view', 'name' => 'Melihat data anggota', 'group' => 'members'],
                ['code' => 'members.create', 'name' => 'Menambahkan anggota baru', 'group' => 'members'],
                ['code' => 'members.update', 'name' => 'Mengubah data anggota', 'group' => 'members'],
                ['code' => 'members.delete', 'name' => 'Menghapus data anggota', 'group' => 'members'],

                // Reports
                ['code' => 'reports.view', 'name' => 'Melihat laporan statistik & keuangan', 'group' => 'reports'],
                ['code' => 'reports.export', 'name' => 'Mengekspor laporan', 'group' => 'reports'],

                // Organizations
                ['code' => 'organizations.view', 'name' => 'Melihat struktur organisasi', 'group' => 'organizations'],
                ['code' => 'organizations.manage', 'name' => 'Mengelola konfigurasi organisasi', 'group' => 'organizations'],
            ];

            $createdPermissions = [];
            foreach ($permissions as $p) {
                $createdPermissions[$p['code']] = Permission::firstOrCreate(['code' => $p['code']], $p);
            }

            // 7. Role Permission Mapping
            // Super Admin: all permissions
            $allPermissionIds = array_map(fn ($p) => $p->id, $createdPermissions);
            $createdRoles['super_admin']->permissions()->sync($allPermissionIds);

            // Admin Provinsi
            $provPermissions = Permission::whereIn('code', [
                'donations.view', 'donations.create', 'donations.update', 'donations.approve', 'donations.export',
                'members.view', 'members.create', 'members.update',
                'reports.view', 'reports.export',
                'organizations.view',
            ])->pluck('id');
            $createdRoles['admin_provinsi']->permissions()->sync($provPermissions);

            // Staff Penghimpunan
            $staffPenghimpunanPermissions = Permission::whereIn('code', [
                'donations.view', 'donations.create', 'donations.export',
                'reports.view',
            ])->pluck('id');
            $createdRoles['staff_penghimpunan']->permissions()->sync($staffPenghimpunanPermissions);
        });
    }
}
