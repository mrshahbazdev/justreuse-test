<?php

namespace App\Exports;

use App\Models\Feature;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;


class FeaturesExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Feature::all();
    }

    /**
     * @param $feature
     * @return array
     */

    public function map($feature): array
    {
        // Decode the JSON other_features into an array
        $other_features = json_decode($feature->other_features, true);

        return [
            $feature->id,
            $feature->brand_id,
            $feature->cat_id,
            $feature->make,
            $feature->model,
            $feature->generation,
            $feature->year_from,
            $feature->year_to,
            $feature->series,
            // Other features from the JSON column
            $other_features['trim'] ?? null,
            $other_features['body_type'] ?? null,
            $other_features['load_height_mm'] ?? null,
            $other_features['number_of_seats'] ?? null,
            $other_features['length_mm'] ?? null,
            $other_features['width_mm'] ?? null,
            $other_features['height_mm'] ?? null,
            $other_features['wheelbase_mm'] ?? null,
            $other_features['front_track_mm'] ?? null,
            $other_features['rear_track_mm'] ?? null,
            $other_features['curb_weight_kg'] ?? null,
            $other_features['wheel_size_r14'] ?? null,
            $other_features['ground_clearance_mm'] ?? null,
            $other_features['trailer_load_with_brakes_kg'] ?? null,
            $other_features['payload_kg'] ?? null,
            $other_features['back_track_width_mm'] ?? null,
            $other_features['front_track_width_mm'] ?? null,
            $other_features['clearance_mm'] ?? null,
            $other_features['full_weight_kg'] ?? null,
            $other_features['front_rear_axle_load_kg'] ?? null,
            $other_features['max_trunk_capacity_l'] ?? null,
            $other_features['cargo_compartment_length_width_height_mm'] ?? null,
            $other_features['cargo_volume_m3'] ?? null,
            $other_features['minimum_trunk_capacity_l'] ?? null,
            $other_features['maximum_torque_n_m'] ?? null,
            $other_features['injection_type'] ?? null,
            $other_features['overhead_camshaft'] ?? null,
            $other_features['cylinder_layout'] ?? null,
            $other_features['number_of_cylinders'] ?? null,
            $other_features['compression_ratio'] ?? null,
            $other_features['engine_type'] ?? null,
            $other_features['valves_per_cylinder'] ?? null,
            $other_features['boost_type'] ?? null,
            $other_features['cylinder_bore_mm'] ?? null,
            $other_features['stroke_cycle_mm'] ?? null,
            $other_features['engine_placement'] ?? null,
            $other_features['cylinder_bore_and_stroke_cycle_mm'] ?? null,
            $other_features['turnover_of_maximum_torque_rpm'] ?? null,
            $other_features['max_power_kw'] ?? null,
            $other_features['presence_of_intercooler'] ?? null,
            $other_features['70 to 350HP'] ?? null,
            $other_features['engine_hp'] ?? null,
            $other_features['engine_hp_rpm'] ?? null,
            $other_features['drive_wheels'] ?? null,
            $other_features['bore_stroke_ratio'] ?? null,
            $other_features['number_of_gears'] ?? null,
            $other_features['turning_circle_m'] ?? null,
            $other_features['transmission'] ?? null,
            $other_features['mixed_fuel_consumption_per_100_km_l'] ?? null,
            $other_features['range_km'] ?? null,
            $other_features['emission_standards'] ?? null,
            $other_features['fuel_tank_capacity_l'] ?? null,
            $other_features['acceleration_0_100_km/h_s'] ?? null,
            $other_features['max_speed_km_per_h'] ?? null,
            $other_features['city_fuel_per_100km_l'] ?? null,
            $other_features['CO2_emissions_g/km'] ?? null,
            $other_features['fuel_grade'] ?? null,
            $other_features['highway_fuel_per_100km_l'] ?? null,
            $other_features['back_suspension'] ?? null,
            $other_features['rear_brakes'] ?? null,
            $other_features['front_brakes'] ?? null,
            $other_features['front_suspension'] ?? null,
            $other_features['steering_type'] ?? null,
           

            // Add more fields as necessary from the JSON
        ];
    }

    /**
     * Define headings for the exported file.
     * 
     * @return array
     */
    public function headings(): array
    {
        return [

            "id",
            "brand_id",
            "cat_id",
            "Make",
            "Model",
            "Generation",
            "Year_from",
            "Year_to",
            "Series",
            "Trim",
            "Body_type",
            "load_height_mm",
            "number_of_seats",
            "length_mm",
            "width_mm",
            "height_mm",
            "wheelbase_mm",
            "front_track_mm",
            "rear_track_mm",
            "curb_weight_kg",
            "wheel_size_r14",
            "ground_clearance_mm",
            "trailer_load_with_brakes_kg",
            "payload_kg",
            "back_track_width_mm",
            "front_track_width_mm",
            "clearance_mm",
            "full_weight_kg",
            "front_rear_axle_load_kg",
            "max_trunk_capacity_l",
            "cargo_compartment_length_width_height_mm",
            "cargo_volume_m3",
            "minimum_trunk_capacity_l",
            "maximum_torque_n_m",
            "injection_type",
            "overhead_camshaft",
            "cylinder_layout",
            "number_of_cylinders",
            "compression_ratio",
            "engine_type",
            "valves_per_cylinder",
            "boost_type",
            "cylinder_bore_mm",
            "stroke_cycle_mm",
            "engine_placement",
            "cylinder_bore_and_stroke_cycle_mm",
            "turnover_of_maximum_torque_rpm",
            "max_power_kw",
            "presence_of_intercooler",
            "engine_hp_range_70_to_350HP",
            "engine_hp",
            "engine_hp_rpm",
            "drive_wheels",
            "bore_stroke_ratio",
            "number_of_gears",
            "turning_circle_m",
            "transmission",
            "mixed_fuel_consumption_per_100_km_l",
            "range_km",
            "emission_standards",
            "fuel_tank_capacity_l",
            "acceleration_0_100_km/h_s",
            "max_speed_km_per_h",
            "city_fuel_per_100km_l",
            "CO2_emissions_g/km",
            "fuel_grade",
            "highway_fuel_per_100km_l",
            "back_suspension",
            "rear_brakes",
            "front_brakes",
            "front_suspension",
            "steering_type"
            
        ];
    }
}
