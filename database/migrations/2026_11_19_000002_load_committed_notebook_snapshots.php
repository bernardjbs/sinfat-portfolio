<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * The copies published before snapshots moved to the database, loaded as they are: six carry a
     * hand correction the factory originals lack, so they are never regenerated. The files stay in
     * resources/notebook as their record; new snapshots arrive through notebook:manage import.
     */
    public function up(): void
    {
        foreach (glob(resource_path('notebook/*.html')) as $path) {
            DB::table('notebook_snapshots')->insertOrIgnore([
                'stamp'      => basename($path, '.html'),
                'html'       => file_get_contents($path),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        $stamps = array_map(fn ($path) => basename($path, '.html'), glob(resource_path('notebook/*.html')));

        DB::table('notebook_snapshots')->whereIn('stamp', $stamps)->delete();
    }
};
