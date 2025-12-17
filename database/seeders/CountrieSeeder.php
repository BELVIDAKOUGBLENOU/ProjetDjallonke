<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CountrieSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tables = [
            "countries" => Country::class,

        ];
        foreach ($tables as $key => $className) {
            if ($className::count() > 0) {
                continue;
            }
            $jsonData = json_decode(file_get_contents(database_path("json/{$key}.json")), true);

            foreach ($jsonData as $data) {
                // var_dump($data);
                $fillableData = collect($data)
                    ->only((new $className)->getFillable())
                    ->toArray();
                $className::updateOrCreate(
                    ['id' => $data['id']],
                    $fillableData
                );
            }
        }
        Country::whereNotIn('code_iso', ['BJ', 'BF',])->update(['is_active' => false]);
        Country::whereIn('code_iso', ['BJ', 'BF',])->update(['is_active' => true]);
    }
}
