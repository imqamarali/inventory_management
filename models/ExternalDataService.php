<?php

namespace app\models;

use Yii;

/**
 * External Data Service - Fetch real-world data from APIs
 * Vehicles, Brands, Categories, Units
 */
class ExternalDataService
{
    const VEHICLE_MAKES_SOURCE = 'https://api.github.com/repos/rbhalla/automobile-specifications/contents/makes.json';
    const VEHICLE_MODELS_SOURCE = 'https://api.github.com/repos/rbhalla/automobile-specifications/contents/models.json';

    private $httpClient;
    private $timeout = 30;

    public function __construct()
    {
        $this->httpClient = new \yii\httpclient\Client();
    }

    /**
     * Get Popular Vehicle Makes with fallback data
     */
    public function getVehicleMakes()
    {
        $fallbackData = [
            ['make_name' => 'Toyota', 'make_code' => 'TYT', 'country' => 'Japan', 'website' => 'https://www.toyota.com'],
            ['make_name' => 'Honda', 'make_code' => 'HND', 'country' => 'Japan', 'website' => 'https://www.honda.com'],
            ['make_name' => 'Suzuki', 'make_code' => 'SUZ', 'country' => 'Japan', 'website' => 'https://www.suzuki.com'],
            ['make_name' => 'Hyundai', 'make_code' => 'HYU', 'country' => 'South Korea', 'website' => 'https://www.hyundai.com'],
            ['make_name' => 'Kia', 'make_code' => 'KIA', 'country' => 'South Korea', 'website' => 'https://www.kia.com'],
            ['make_name' => 'BMW', 'make_code' => 'BMW', 'country' => 'Germany', 'website' => 'https://www.bmw.com'],
            ['make_name' => 'Mercedes-Benz', 'make_code' => 'MBZ', 'country' => 'Germany', 'website' => 'https://www.mercedes-benz.com'],
            ['make_name' => 'Audi', 'make_code' => 'AUD', 'country' => 'Germany', 'website' => 'https://www.audi.com'],
            ['make_name' => 'Volkswagen', 'make_code' => 'VWG', 'country' => 'Germany', 'website' => 'https://www.volkswagen.com'],
            ['make_name' => 'Ford', 'make_code' => 'FRD', 'country' => 'USA', 'website' => 'https://www.ford.com'],
            ['make_name' => 'Chevrolet', 'make_code' => 'CHV', 'country' => 'USA', 'website' => 'https://www.chevrolet.com'],
            ['make_name' => 'Nissan', 'make_code' => 'NIS', 'country' => 'Japan', 'website' => 'https://www.nissan.com'],
            ['make_name' => 'Mazda', 'make_code' => 'MZD', 'country' => 'Japan', 'website' => 'https://www.mazda.com'],
            ['make_name' => 'Mitsubishi', 'make_code' => 'MTS', 'country' => 'Japan', 'website' => 'https://www.mitsubishi-motors.com'],
            ['make_name' => 'Daihatsu', 'make_code' => 'DIH', 'country' => 'Japan', 'website' => 'https://www.daihatsu.com'],
            ['make_name' => 'Isuzu', 'make_code' => 'ISZ', 'country' => 'Japan', 'website' => 'https://www.isuzu.com'],
            ['make_name' => 'Renault', 'make_code' => 'REN', 'country' => 'France', 'website' => 'https://www.renault.com'],
            ['make_name' => 'Peugeot', 'make_code' => 'PEU', 'country' => 'France', 'website' => 'https://www.peugeot.com'],
            ['make_name' => 'Citroen', 'make_code' => 'CIT', 'country' => 'France', 'website' => 'https://www.citroen.com'],
            ['make_name' => 'Fiat', 'make_code' => 'FIT', 'country' => 'Italy', 'website' => 'https://www.fiat.com'],
        ];

        try {
            // Try to fetch from API, but fallback to hardcoded data
            $response = @file_get_contents('https://raw.githubusercontent.com/rbhalla/automobile-specifications/master/makes.json', false, stream_context_create([
                'http' => ['timeout' => $this->timeout]
            ]));

            if ($response) {
                $data = json_decode($response, true);
                if (is_array($data)) {
                    return array_slice($data, 0, 50); // Limit to first 50
                }
            }
        } catch (\Exception $e) {
            // Fall through to fallback data
        }

        return $fallbackData;
    }

    /**
     * Get Popular Automotive Brands with fallback data
     */
    public function getAutomotiveBrands()
    {
        $fallbackData = [
            ['brand_name' => 'Bosch', 'brand_code' => 'BOSCH', 'website' => 'https://www.bosch.com', 'email' => 'info@bosch.com'],
            ['brand_name' => 'Valeo', 'brand_code' => 'VALEO', 'website' => 'https://www.valeo.com', 'email' => 'contact@valeo.com'],
            ['brand_name' => 'Denso', 'brand_code' => 'DENSO', 'website' => 'https://www.denso.com', 'email' => 'info@denso.com'],
            ['brand_name' => 'MAHLE', 'brand_code' => 'MAHLE', 'website' => 'https://www.mahle.com', 'email' => 'info@mahle.com'],
            ['brand_name' => 'Brembo', 'brand_code' => 'BREMBO', 'website' => 'https://www.brembo.com', 'email' => 'info@brembo.com'],
            ['brand_name' => 'SKF', 'brand_code' => 'SKF', 'website' => 'https://www.skf.com', 'email' => 'contact@skf.com'],
            ['brand_name' => 'Sachs', 'brand_code' => 'SACHS', 'website' => 'https://www.sachs.de', 'email' => 'info@sachs.de'],
            ['brand_name' => 'Meyle', 'brand_code' => 'MEYLE', 'website' => 'https://www.meyle.com', 'email' => 'info@meyle.com'],
            ['brand_name' => 'Bilstein', 'brand_code' => 'BILSTEIN', 'website' => 'https://www.bilstein.com', 'email' => 'info@bilstein.com'],
            ['brand_name' => 'Akebono', 'brand_code' => 'AKEBONO', 'website' => 'https://www.akebono.com', 'email' => 'info@akebono.com'],
            ['brand_name' => 'Ferodo', 'brand_code' => 'FERODO', 'website' => 'https://www.ferodo.com', 'email' => 'info@ferodo.com'],
            ['brand_name' => 'Motul', 'brand_code' => 'MOTUL', 'website' => 'https://www.motul.com', 'email' => 'info@motul.com'],
            ['brand_name' => 'Castrol', 'brand_code' => 'CASTROL', 'website' => 'https://www.castrol.com', 'email' => 'info@castrol.com'],
            ['brand_name' => 'Shell', 'brand_code' => 'SHELL', 'website' => 'https://www.shell.com', 'email' => 'info@shell.com'],
            ['brand_name' => 'Mobil', 'brand_code' => 'MOBIL', 'website' => 'https://www.mobil.com', 'email' => 'info@mobil.com'],
            ['brand_name' => 'ZF', 'brand_code' => 'ZF', 'website' => 'https://www.zf.com', 'email' => 'info@zf.com'],
            ['brand_name' => 'Aisin', 'brand_code' => 'AISIN', 'website' => 'https://www.aisin.com', 'email' => 'info@aisin.com'],
            ['brand_name' => 'Continental', 'brand_code' => 'CONT', 'website' => 'https://www.continental.com', 'email' => 'info@continental.com'],
            ['brand_name' => 'Michelin', 'brand_code' => 'MICHELIN', 'website' => 'https://www.michelin.com', 'email' => 'info@michelin.com'],
            ['brand_name' => 'Goodyear', 'brand_code' => 'GOODYEAR', 'website' => 'https://www.goodyear.com', 'email' => 'info@goodyear.com'],
        ];

        return $fallbackData;
    }

    /**
     * Get Auto Parts Categories with fallback data
     */
    public function getAutoPartsCategories()
    {
        return [
            ['category_name' => 'Engine & Engine Covers', 'category_code' => 'ENGINE', 'parent_id' => null, 'description' => 'Engine components and covers'],
            ['category_name' => 'Suspension & Steering', 'category_code' => 'SUSPENSION', 'parent_id' => null, 'description' => 'Suspension and steering components'],
            ['category_name' => 'Brake System', 'category_code' => 'BRAKES', 'parent_id' => null, 'description' => 'Brake components and parts'],
            ['category_name' => 'Cooling System', 'category_code' => 'COOLING', 'parent_id' => null, 'description' => 'Radiators and cooling components'],
            ['category_name' => 'Electrical', 'category_code' => 'ELECTRICAL', 'parent_id' => null, 'description' => 'Electrical components'],
            ['category_name' => 'Transmission & Drive', 'category_code' => 'TRANSMISSION', 'parent_id' => null, 'description' => 'Transmission and drive components'],
            ['category_name' => 'Fuel System', 'category_code' => 'FUEL', 'parent_id' => null, 'description' => 'Fuel system components'],
            ['category_name' => 'Exhaust System', 'category_code' => 'EXHAUST', 'parent_id' => null, 'description' => 'Exhaust system components'],
            ['category_name' => 'Lighting & Mirrors', 'category_code' => 'LIGHTING', 'parent_id' => null, 'description' => 'Lighting and mirror components'],
            ['category_name' => 'Filters', 'category_code' => 'FILTERS', 'parent_id' => null, 'description' => 'Various types of filters'],
            ['category_name' => 'Oils & Lubricants', 'category_code' => 'OILS', 'parent_id' => null, 'description' => 'Oils and lubricants'],
            ['category_name' => 'Interior Parts', 'category_code' => 'INTERIOR', 'parent_id' => null, 'description' => 'Interior components'],
            ['category_name' => 'Exterior Parts', 'category_code' => 'EXTERIOR', 'parent_id' => null, 'description' => 'Exterior components'],
            ['category_name' => 'Tools & Accessories', 'category_code' => 'TOOLS', 'parent_id' => null, 'description' => 'Tools and accessories'],
        ];
    }

    /**
     * Get Standard Units with fallback data
     */
    public function getStandardUnits()
    {
        return [
            ['unit_name' => 'Piece', 'short_name' => 'PCS', 'description' => 'Single item'],
            ['unit_name' => 'Set', 'short_name' => 'SET', 'description' => 'Complete set'],
            ['unit_name' => 'Pair', 'short_name' => 'PAIR', 'description' => 'Two items'],
            ['unit_name' => 'Box', 'short_name' => 'BOX', 'description' => 'Box packing'],
            ['unit_name' => 'Bottle', 'short_name' => 'BOT', 'description' => 'Liquid bottle'],
            ['unit_name' => 'Liter', 'short_name' => 'LTR', 'description' => 'Liquid measurement'],
            ['unit_name' => 'Milliliter', 'short_name' => 'ML', 'description' => 'Small liquid measurement'],
            ['unit_name' => 'Kilogram', 'short_name' => 'KG', 'description' => 'Weight measurement'],
            ['unit_name' => 'Gram', 'short_name' => 'GM', 'description' => 'Small weight measurement'],
            ['unit_name' => 'Roll', 'short_name' => 'ROLL', 'description' => 'Roll packing'],
            ['unit_name' => 'Pack', 'short_name' => 'PACK', 'description' => 'Package packing'],
            ['unit_name' => 'Carton', 'short_name' => 'CTN', 'description' => 'Carton packing'],
            ['unit_name' => 'Meter', 'short_name' => 'MTR', 'description' => 'Length measurement'],
            ['unit_name' => 'Square Meter', 'short_name' => 'SQM', 'description' => 'Area measurement'],
        ];
    }

    /**
     * Get Vehicle Models for a specific make
     */
    public function getModelsForMake($makeName)
    {
        $models = [
            'Toyota' => [
                ['model_name' => 'Corolla', 'model_year' => '2023', 'fuel_type' => 'Petrol', 'transmission' => 'Manual'],
                ['model_name' => 'Corolla', 'model_year' => '2023', 'fuel_type' => 'Petrol', 'transmission' => 'Automatic'],
                ['model_name' => 'Civic', 'model_year' => '2023', 'fuel_type' => 'Petrol', 'transmission' => 'Manual'],
                ['model_name' => 'Camry', 'model_year' => '2023', 'fuel_type' => 'Petrol', 'transmission' => 'Automatic'],
                ['model_name' => 'Land Cruiser', 'model_year' => '2023', 'fuel_type' => 'Diesel', 'transmission' => 'Automatic'],
                ['model_name' => 'Fortuner', 'model_year' => '2023', 'fuel_type' => 'Diesel', 'transmission' => 'Automatic'],
            ],
            'Honda' => [
                ['model_name' => 'Civic', 'model_year' => '2023', 'fuel_type' => 'Petrol', 'transmission' => 'Manual'],
                ['model_name' => 'Civic', 'model_year' => '2023', 'fuel_type' => 'Petrol', 'transmission' => 'Automatic'],
                ['model_name' => 'CR-V', 'model_year' => '2023', 'fuel_type' => 'Petrol', 'transmission' => 'Automatic'],
                ['model_name' => 'Accord', 'model_year' => '2023', 'fuel_type' => 'Petrol', 'transmission' => 'Automatic'],
                ['model_name' => 'City', 'model_year' => '2023', 'fuel_type' => 'Petrol', 'transmission' => 'Manual'],
            ],
            'Suzuki' => [
                ['model_name' => 'Swift', 'model_year' => '2023', 'fuel_type' => 'Petrol', 'transmission' => 'Manual'],
                ['model_name' => 'Alto', 'model_year' => '2023', 'fuel_type' => 'Petrol', 'transmission' => 'Manual'],
                ['model_name' => 'Vitara', 'model_year' => '2023', 'fuel_type' => 'Petrol', 'transmission' => 'Automatic'],
            ],
            'Hyundai' => [
                ['model_name' => 'Elantra', 'model_year' => '2023', 'fuel_type' => 'Petrol', 'transmission' => 'Manual'],
                ['model_name' => 'Tucson', 'model_year' => '2023', 'fuel_type' => 'Petrol', 'transmission' => 'Automatic'],
                ['model_name' => 'Creta', 'model_year' => '2023', 'fuel_type' => 'Diesel', 'transmission' => 'Manual'],
            ],
            'Kia' => [
                ['model_name' => 'Sportage', 'model_year' => '2023', 'fuel_type' => 'Petrol', 'transmission' => 'Automatic'],
                ['model_name' => 'Sorento', 'model_year' => '2023', 'fuel_type' => 'Diesel', 'transmission' => 'Automatic'],
            ],
            'BMW' => [
                ['model_name' => '3-Series', 'model_year' => '2023', 'fuel_type' => 'Petrol', 'transmission' => 'Automatic'],
                ['model_name' => '5-Series', 'model_year' => '2023', 'fuel_type' => 'Diesel', 'transmission' => 'Automatic'],
                ['model_name' => 'X5', 'model_year' => '2023', 'fuel_type' => 'Diesel', 'transmission' => 'Automatic'],
            ],
            'Mercedes-Benz' => [
                ['model_name' => 'C-Class', 'model_year' => '2023', 'fuel_type' => 'Petrol', 'transmission' => 'Automatic'],
                ['model_name' => 'E-Class', 'model_year' => '2023', 'fuel_type' => 'Diesel', 'transmission' => 'Automatic'],
                ['model_name' => 'GLE', 'model_year' => '2023', 'fuel_type' => 'Diesel', 'transmission' => 'Automatic'],
            ],
        ];

        return $models[$makeName] ?? [];
    }
}
